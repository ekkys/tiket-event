<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\Ticket;
use App\Models\ScanLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

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

        $registrations = $query->paginate(50)->withQueryString();
        return view('admin.registrations', compact('registrations'));
    }

    public function export()
    {
        $userId = auth()->id();
        $registrations = Registration::whereHas('event', fn($q) => $q->where('user_id', $userId))
            ->with(['ticket', 'event'])
            ->whereIn('payment_status', ['paid', 'free'])
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=registrations.csv',
        ];

        $callback = function () use ($registrations) {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['No', 'Kode', 'Nama', 'Email', 'HP', 'NIK', 'Alamat', 'Institusi', 'Status', 'Sudah Scan', 'Tgl Daftar']);
            foreach ($registrations as $i => $r) {
                fputcsv($f, [
                    $i + 1,
                    $r->registration_code,
                    $r->full_name,
                    $r->email,
                    $r->phone,
                    $r->id_number,
                    $r->address,
                    $r->institution,
                    $r->payment_status,
                    $r->ticket?->is_used ? 'Ya' : 'Tidak',
                    $r->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function scanLogs()
    {
        $userId = auth()->id();
        $logs = ScanLog::whereHas('ticket.registration.event', fn($q) => $q->where('user_id', $userId))
            ->with(['ticket.registration.event'])
            ->latest()
            ->paginate(100);
        return view('admin.scan-logs', compact('logs'));
    }

    // ---- Event CRUD ----

    public function events()
    {
        $events = Event::where('user_id', auth()->id())->latest()->get();
        return view('admin.events.index', compact('events'));
    }

    public function createEvent()
    {
        return view('admin.events.create');
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'event_date' => 'required|date',
            'location'   => 'nullable|string|max:255',
            'price'      => 'required|numeric|min:0',
            'quota'      => 'required|integer|min:1',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['is_free']   = $request->has('is_free');
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $validated['image_path'] = $path;
        }

        $validated['user_id'] = auth()->id();

        Event::create($validated);

        return redirect()->route('admin.events')->with('success', 'Event berhasil dibuat');
    }

    public function editEvent(Event $event)
    {
        $this->authorizeOwner($event);
        return view('admin.events.edit', compact('event'));
    }

    public function updateEvent(Request $request, Event $event)
    {
        $this->authorizeOwner($event);
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'event_date' => 'required|date',
            'location'   => 'nullable|string|max:255',
            'price'      => 'required|numeric|min:0',
            'quota'      => 'required|integer|min:1',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['is_free']   = $request->has('is_free');
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            $path = $request->file('image')->store('events', 'public');
            $validated['image_path'] = $path;
        }

        $event->update($validated);

        return redirect()->route('admin.events')->with('success', 'Event berhasil diupdate');
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

    private function authorizeOwner(Event $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke event ini.');
        }
    }
}
