<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function show($token)
    {
        $ticket = Ticket::where('token', $token)
            ->with(['registration.event'])
            ->firstOrFail();

        if (!$ticket->registration->isPaid()) {
            return view('ticket.not-paid', compact('ticket'));
        }

        return view('ticket.show', compact('ticket'));
    }

    public function download($token)
    {
        $ticket = Ticket::where('token', $token)->with(['registration.event'])->firstOrFail();
        
        // 1. Setup Canvas
        $width = 600;
        $height = 1250;
        $image = imagecreatetruecolor($width, $height);
        
        // Colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $navy = imagecolorallocate($image, 92, 59, 254);
        $textDark = imagecolorallocate($image, 15, 23, 42);
        $textMuted = imagecolorallocate($image, 100, 116, 139);
        $grayLight = imagecolorallocate($image, 248, 250, 252);
        
        imagefill($image, 0, 0, $white);
        
        // 2. Banner
        $bannerDisplayH = 300;
        if ($ticket->registration->event->image_path && Storage::disk('public')->exists($ticket->registration->event->image_path)) {
            $bannerPath = Storage::disk('public')->path($ticket->registration->event->image_path);
            $ext = strtolower(pathinfo($bannerPath, PATHINFO_EXTENSION));
            $srcBanner = ($ext === 'png') ? imagecreatefrompng($bannerPath) : imagecreatefromjpeg($bannerPath);
            
            if ($srcBanner) {
                $srcW = imagesx($srcBanner);
                $srcH = imagesy($srcBanner);
                $targetRatio = $width / $bannerDisplayH;
                $srcRatio = $srcW / $srcH;

                if ($srcRatio > $targetRatio) {
                    $cropW = (int)($srcH * $targetRatio);
                    $cropH = $srcH;
                    $srcX = (int)(($srcW - $cropW) / 2);
                    $srcY = 0;
                } else {
                    $cropW = $srcW;
                    $cropH = (int)($srcW / $targetRatio);
                    $srcX = 0;
                    $srcY = (int)(($srcH - $cropH) / 2);
                }

                imagecopyresampled($image, $srcBanner, 0, 0, $srcX, $srcY, $width, $bannerDisplayH, $cropW, $cropH);
                imagedestroy($srcBanner);
            }
        } else {
            imagefilledrectangle($image, 0, 0, $width, $bannerDisplayH, $navy);
        }

        // 3. Info Section
        $font = base_path('vendor/endroid/qr-code/assets/open_sans.ttf');
        $currentY = $bannerDisplayH + 50;

        // Event Name
        imagettftext($image, 22, 0, 40, $currentY, $navy, $font, $ticket->registration->event->name);
        $currentY += 40;

        // Date & Location
        $eventDate = $ticket->registration->event->event_date->isoFormat('dddd, D MMMM Y');
        $location = $ticket->registration->event->location_name ?: ($ticket->registration->event->location ?? '-');
        imagettftext($image, 12, 0, 40, $currentY, $textMuted, $font, $eventDate . ' | ' . $location);
        $currentY += 60;

        // Divider
        imageline($image, 40, $currentY, $width - 40, $currentY, $grayLight);
        $currentY += 50;

        // Attendee Info Grid
        $fields = [
            'NAMA PESERTA' => $ticket->registration->full_name,
            'NIK' => $ticket->registration->id_number,
            'EMAIL' => $ticket->registration->email,
            'NO. TELEPON' => $ticket->registration->phone,
            'JENIS KELAMIN' => $ticket->registration->gender === 'male' ? 'Laki-laki' : 'Perempuan',
            'INSTANSI' => $ticket->registration->institution ?: '-',
        ];

        foreach ($fields as $label => $value) {
            imagettftext($image, 10, 0, 40, $currentY, $textMuted, $font, $label);
            $currentY += 30;
            imagettftext($image, 14, 0, 40, $currentY, $textDark, $font, $value);
            $currentY += 50;
        }

        // Alamat (Special word wrap)
        imagettftext($image, 10, 0, 40, $currentY, $textMuted, $font, 'ALAMAT');
        $currentY += 30;
        $address = $ticket->registration->address ?: '-';
        $words = explode(' ', $address);
        $line = '';
        foreach ($words as $word) {
            $testLine = $line . ($line ? ' ' : '') . $word;
            $bbox = imagettfbbox(14, 0, $font, $testLine);
            if (($bbox[2] - $bbox[0]) > 520 && $line !== '') {
                imagettftext($image, 14, 0, 40, $currentY, $textDark, $font, $line);
                $currentY += 30;
                $line = $word;
            } else {
                $line = $testLine;
            }
        }
        imagettftext($image, 14, 0, 40, $currentY, $textDark, $font, $line);
        
        // 4. QR Code
        if (Storage::disk('public')->exists($ticket->qr_code_path)) {
            $qrPath = Storage::disk('public')->path($ticket->qr_code_path);
            $qrSrc = imagecreatefrompng($qrPath);
            if ($qrSrc) {
                $qrSize = 180;
                $qrX = ($width - $qrSize) / 2;
                $qrY = $height - $qrSize - 80;

                imagefilledrectangle($image, $qrX - 10, $qrY - 10, $qrX + $qrSize + 10, $qrY + $qrSize + 10, $grayLight);
                imagecopyresampled($image, $qrSrc, $qrX, $qrY, 0, 0, $qrSize, $qrSize, imagesx($qrSrc), imagesy($qrSrc));
                imagedestroy($qrSrc);

                $regCode = $ticket->registration->registration_code;
                imagettftext($image, 14, 0, $qrX + 15, $qrY + $qrSize + 40, $navy, $font, $regCode);
            }
        }

        $fileName = 'Ticket-' . $ticket->registration->registration_code . '.jpg';
        header('Content-Type: image/jpeg');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        imagejpeg($image, null, 90);
        imagedestroy($image);
        exit;
    }
}
