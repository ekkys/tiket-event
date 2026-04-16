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
        $height = 950;
        $image = imagecreatetruecolor($width, $height);
        
        // Colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $navy = imagecolorallocate($image, 92, 59, 254); // Purple Brand Primary
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
                
                // Center Crop Logic
                $targetRatio = $width / $bannerDisplayH;
                $srcRatio = $srcW / $srcH;

                if ($srcRatio > $targetRatio) {
                    // Source is wider than target
                    $cropW = (int)($srcH * $targetRatio);
                    $cropH = $srcH;
                    $srcX = (int)(($srcW - $cropW) / 2);
                    $srcY = 0;
                } else {
                    // Source is taller than target
                    $cropW = $srcW;
                    $cropH = (int)($srcW / $targetRatio);
                    $srcX = 0;
                    $srcY = (int)(($srcH - $cropH) / 2);
                }

                imagecopyresampled($image, $srcBanner, 0, 0, $srcX, $srcY, $width, $bannerDisplayH, $cropW, $cropH);
                imagedestroy($srcBanner);
            }
        } else {
            // Placeholder color if no banner
            imagefilledrectangle($image, 0, 0, $width, $bannerDisplayH, $navy);
        }

        // 3. Info Section
        $font = base_path('vendor/endroid/qr-code/assets/open_sans.ttf');
        $currentY = $bannerDisplayH + 60;

        // Event Name
        $eventName = $ticket->registration->event->name;
        imagettftext($image, 24, 0, 40, $currentY, $navy, $font, $eventName);
        $currentY += 50;

        // Date & Location
        $eventDate = $ticket->registration->event->event_date->isoFormat('dddd, D MMMM Y');
        imagettftext($image, 14, 0, 40, $currentY, $textMuted, $font, $eventDate);
        $currentY += 80;

        // Attendee Name
        imagettftext($image, 12, 0, 40, $currentY, $textMuted, $font, "PEMEGANG TIKET");
        $currentY += 40;
        imagettftext($image, 20, 0, 40, $currentY, $textDark, $font, $ticket->registration->full_name);
        $currentY += 40;
        imagettftext($image, 12, 0, 40, $currentY, $textMuted, $font, $ticket->registration->email);
        
        // 4. QR Code
        if (Storage::disk('public')->exists($ticket->qr_code_path)) {
            $qrPath = Storage::disk('public')->path($ticket->qr_code_path);
            $qrSrc = imagecreatefrompng($qrPath);
            if ($qrSrc) {
                $qrSize = 250;
                $qrX = ($width - $qrSize) / 2;
                $qrY = $height - $qrSize - 100;

                // QR Container Box
                imagefilledrectangle($image, $qrX - 10, $qrY - 10, $qrX + $qrSize + 10, $qrY + $qrSize + 10, $grayLight);
                imagecopyresampled($image, $qrSrc, $qrX, $qrY, 0, 0, $qrSize, $qrSize, imagesx($qrSrc), imagesy($qrSrc));
                imagedestroy($qrSrc);

                // Registration Code
                $regCode = $ticket->registration->registration_code;
                $currentY = $qrY + $qrSize + 40;
                imagettftext($image, 14, 0, $qrX + 45, $currentY, $navy, $font, $regCode);
            }
        }

        // 5. Output
        $fileName = 'Ticket-' . $ticket->registration->registration_code . '.jpg';
        
        header('Content-Type: image/jpeg');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        imagejpeg($image, null, 90);
        imagedestroy($image);
        exit;
    }
}
