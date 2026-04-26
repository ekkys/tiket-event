<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\Ticket;
use App\Models\ScanLog;
use App\Exports\RegistrationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

class AdminController extends Controller
{
    public function dashboard()
    {
        $userId = auth()->id();
        $stats = [
            'total'           => Registration::whereHas('event', fn($q) => $q->where('user_id', $userId))->count(),
            'paid'            => Registration::whereHas('event', fn($q) => $q->where('user_id', $userId))->whereIn('payment_status', ['paid', 'free'])->count(),
            'pending'         => Registration::whereHas('event', fn($q) => $q->where('user_id', $userId))->where('payment_status', 'pending')->count(),
            'checked_in'      => Ticket::whereHas('registration.event', fn($q) => $q->where('user_id', $userId))->where('is_used', true)->count(),
            'tickets'         => Ticket::whereHas('registration.event', fn($q) => $q->where('user_id', $userId))->count(),
            'today_reg'       => Registration::whereHas('event', fn($q) => $q->where('user_id', $userId))->whereDate('created_at', today())->count(),
            'remaining_quota' => Event::where('user_id', $userId)->sum('quota') - Registration::whereHas('event', fn($q) => $q->where('user_id', $userId))->count(),
        ];

        $recentRegistrations = Registration::whereHas('event', fn($q) => $q->where('user_id', $userId))
            ->with(['ticket', 'event'])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentRegistrations'));
    }

    public function registrations(Request $request)
    {
        $userId = auth()->id();
        $query = Registration::whereHas('event', fn($q) => $q->where('user_id', $userId))
            ->with(['ticket', 'event'])
            ->latest();

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('registration_code', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->status) {
            $query->where('payment_status', $request->status);
        }

        $registrations = $query->paginate(15)->withQueryString();
        return view('admin.registrations', compact('registrations'));
    }

    public function export(Request $request)
    {
        $fileName = 'registrations-' . now()->format('Y-m-d-His') . '.xlsx';
        return Excel::download(new RegistrationsExport($request->only(['search', 'status'])), $fileName);
    }

    public function scanLogs(Request $request)
    {
        $userId = auth()->id();
        $query = ScanLog::whereHas('ticket.registration.event', fn($q) => $q->where('user_id', $userId))
            ->with(['ticket.registration.event'])
            ->latest();

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('token', 'like', "%{$s}%")
                  ->orWhere('scanner_name', 'like', "%{$s}%")
                  ->orWhere('message', 'like', "%{$s}%")
                  ->orWhereHas('ticket.registration', function($sq) use ($s) {
                      $sq->where('full_name', 'like', "%{$s}%")
                        ->orWhere('registration_code', 'like', "%{$s}%");
                  });
            });
        }

        $logs = $query->paginate(15)->withQueryString();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('admin.partials.scan-log-rows', compact('logs'))->render();
        }

        return view('admin.scan-logs', compact('logs'));
    }

    // ---- Event CRUD ----

    public function events()
    {
        $events = Event::where('user_id', auth()->id())->latest()->paginate(15)->withQueryString();
        return view('admin.events.index', compact('events'));
    }

    public function createEvent()
    {
        return view('admin.events.create');
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'event_date'          => 'required|date',
            'booking_starts_at'   => 'nullable|date',
            'booking_ends_at'     => 'nullable|date',
            'location'            => 'nullable|string|max:255',
            'location_name'       => 'nullable|string|max:255',
            'location_link'       => 'nullable|string|max:2000',
            'price'               => 'required|numeric|min:0',
            'quota'               => 'required|integer|min:1',
            'description'         => 'nullable|string',
            'highlights'          => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'image'               => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['is_free']   = $request->has('is_free');
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $validated['image_path'] = $path;
        }

        $validated['user_id'] = auth()->id();

        $event = Event::create($validated);

        return redirect()->route('admin.events.show', $event->id)->with('success', 'Event berhasil dibuat! Silakan bagikan link atau download flyer di bawah.');
    }

    public function showEvent(Event $event)
    {
        $this->authorizeOwner($event);
        
        $url = route('events.show', $event->id);
        
        // v6 configuration via constructor
        $qrCode = new QrCode(
            data: $url,
            size: 300,
            margin: 10
        );
        
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $qrCodeDataUri = $result->getDataUri();

        return view('admin.events.show', compact('event', 'qrCodeDataUri', 'url'));
    }

    public function editEvent(Event $event)
    {
        $this->authorizeOwner($event);
        return view('admin.events.edit', compact('event'));
    }

    public function updateEvent(Request $request, Event $event)
    {
        $this->authorizeOwner($event);
        $rules = [
            'name'                => 'required|string|max:255',
            'event_date'          => 'required|date',
            'booking_starts_at'   => 'nullable|date',
            'booking_ends_at'     => 'nullable|date',
            'location'            => 'nullable|string|max:255',
            'location_name'       => 'nullable|string|max:255',
            'location_link'       => 'nullable|string|max:2000',
            'price'               => 'required|numeric|min:0',
            'description'         => 'nullable|string',
            'highlights'          => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'image'               => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Cumulative quota logic
        if ($request->filled('added_quota') && (int)$request->added_quota > 0) {
            $rules['added_quota'] = 'integer|min:0';
        } else {
            $rules['quota'] = 'required|integer|min:1';
        }

        $validated = $request->validate($rules);

        if ($request->filled('added_quota') && (int)$request->added_quota > 0) {
            $validated['quota'] = $event->quota + $request->added_quota;
            unset($validated['added_quota']);
        }

        $validated['is_free']   = $request->has('is_free');
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            $path = $request->file('image')->store('events', 'public');
            $validated['image_path'] = $path;
        }

        $event->update($validated);

        return redirect()->route('admin.events.show', $event->id)->with('success', 'Event berhasil diupdate');
    }

    public function downloadFlyer(Event $event)
    {
        $this->authorizeOwner($event);

        if (!$event->image_path || !Storage::disk('public')->exists($event->image_path)) {
            return back()->with('error', 'Event ini tidak memiliki banner untuk dijadikan flyer.');
        }

        // 1. Prepare Base Dimensions
        $canvasW = 800;
        $canvasH = 1200; // Total height for the flyer
        $flyer = imagecreatetruecolor($canvasW, $canvasH);
        
        // Colors
        $white = imagecolorallocate($flyer, 255, 255, 255);
        $black = imagecolorallocate($flyer, 26, 16, 51); // Dark theme text
        $primary = imagecolorallocate($flyer, 92, 59, 254); // Primary theme color
        $muted = imagecolorallocate($flyer, 123, 122, 142); // Gray text
        
        imagefill($flyer, 0, 0, $white);

        // 2. Load and Scale Banner
        $bannerPath = Storage::disk('public')->path($event->image_path);
        $ext = strtolower(pathinfo($bannerPath, PATHINFO_EXTENSION));
        $srcBanner = ($ext === 'png') ? imagecreatefrompng($bannerPath) : imagecreatefromjpeg($bannerPath);
        
        if (!$srcBanner) return back()->with('error', 'Gagal memproses gambar banner.');

        $srcW = imagesx($srcBanner);
        $srcH = imagesy($srcBanner);
        $bannerDisplayH = (int)($srcH * ($canvasW / $srcW)); // Keep aspect ratio
        
        // Ensure banner doesn't take too much space (max 50% of height)
        if ($bannerDisplayH > 600) {
            $bannerDisplayH = 600;
            // Crop or scale? Let's scale and use a filled bg if needed, or just scale.
        }

        imagecopyresampled($flyer, $srcBanner, 0, 0, 0, 0, $canvasW, $bannerDisplayH, $srcW, $srcH);
        imagedestroy($srcBanner);

        // 3. Render Event Data (Text)
        $fontBold = base_path('vendor/endroid/qr-code/assets/open_sans.ttf'); // Assuming this exists from my search
        
        $currentY = $bannerDisplayH + 60;

        // Event Name
        $nameLines = explode("\n", wordwrap($event->name, 25, "\n"));
        foreach($nameLines as $line) {
            imagettftext($flyer, 32, 0, 60, $currentY, $primary, $fontBold, $line);
            $currentY += 50;
        }

        $currentY += 40;

        // Details Panel
        imagettftext($flyer, 14, 0, 60, $currentY, $muted, $fontBold, "TANGGAL & WAKTU");
        $currentY += 35;
        imagettftext($flyer, 18, 0, 60, $currentY, $black, $fontBold, $event->event_date->isoFormat('dddd, D MMMM Y'));
        $currentY += 30;
        imagettftext($flyer, 18, 0, 60, $currentY, $black, $fontBold, $event->event_date->format('H:i') . ' WIB');

        $currentY += 60;
        imagettftext($flyer, 14, 0, 60, $currentY, $muted, $fontBold, "LOKASI");
        $currentY += 35;
        $locLabel = $event->location_name ?: ($event->location ?? '-');
        $locLines = explode("\n", wordwrap($locLabel, 35, "\n"));
        foreach($locLines as $line) {
            imagettftext($flyer, 18, 0, 60, $currentY, $black, $fontBold, $line);
            $currentY += 30;
        }

        // 4. Generate QR Code
        $url = route('events.show', $event->id);
        $qrCode = new QrCode(data: $url, size: 220, margin: 0);
        $writer = new PngWriter();
        $qrResult = $writer->write($qrCode);
        $qrImage = imagecreatefromstring($qrResult->getString());
        
        // Draw white card for QR
        $qrBgSize = 260;
        $qrX = $canvasW - $qrBgSize - 60;
        $qrY = 1200 - $qrBgSize - 100; // Fixed near bottom
        
        // Background for QR
        imagefilledrectangle($flyer, $qrX, $qrY, $qrX + $qrBgSize, $qrY + $qrBgSize, $white);
        // Draw light border for QR card
        $lightGray = imagecolorallocate($flyer, 241, 245, 249);
        imagerectangle($flyer, $qrX, $qrY, $qrX + $qrBgSize, $qrY + $qrBgSize, $lightGray);

        imagecopy($flyer, $qrImage, $qrX + 20, $qrY + 20, 0, 0, 220, 220);
        
        // Footer text near QR
        imagettftext($flyer, 12, 0, $qrX + 45, $qrY + $qrBgSize + 30, $muted, $fontBold, "SCAN UNTUK DAFTAR");

        // 5. Output
        header('Content-Type: image/jpeg');
        header('Content-Disposition: attachment; filename="invitation-' . \Illuminate\Support\Str::slug($event->name) . '.jpg"');
        imagejpeg($flyer, null, 90);
        
        imagedestroy($flyer);
        imagedestroy($qrImage);
        exit;
    }

    public function deleteEvent(Event $event)
    {
        $this->authorizeOwner($event);
        if ($event->registrations()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus event yang sudah memiliki pendaftar');
        }
        
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }

        $event->delete();
        return redirect()->route('admin.events')->with('success', 'Event berhasil dihapus');
    }

    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ], [
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'email.required' => 'Email wajib diisi.',
            'name.required' => 'Nama wajib diisi.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'current_password.current_password' => 'Password saat ini yang Anda masukkan salah.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.'
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    private function authorizeOwner(Event $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke event ini.');
        }
    }
}
