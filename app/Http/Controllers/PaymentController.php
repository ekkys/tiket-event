<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Jobs\GenerateTicketJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function show($code)
    {
        $registration = Registration::where('registration_code', $code)
            ->with('event')
            ->firstOrFail();

        if ($registration->isPaid()) {
            return redirect()->route('ticket.show', $registration->ticket->token);
        }

        // Generate Snap Token Midtrans
        \Midtrans\Config::$serverKey       = config('midtrans.server_key');
        \Midtrans\Config::$isProduction    = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized     = true;
        \Midtrans\Config::$is3ds           = true;

        $orderId = $registration->registration_code . '-' . time();
        
        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $registration->event->price,
            ],
            'customer_details' => [
                'first_name' => $registration->full_name,
                'email'      => $registration->email,
                'phone'      => $registration->phone,
            ],
            'enabled_payments' => ['gopay', 'qris', 'bank_transfer'],
            'expiry' => [
                'unit'     => 'hours',
                'duration' => 2,
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $registration->update(['order_id' => $orderId]);

        return view('payment.show', compact('registration', 'snapToken'));
    }

    // Webhook dari Midtrans (harus di-exclude dari CSRF)
    public function webhook(Request $request)
    {
        $notif = new \Midtrans\Notification();
        
        $transactionStatus = $notif->transaction_status;
        $orderId           = $notif->order_id;
        $paymentType       = $notif->payment_type;
        $fraudStatus       = $notif->fraud_status ?? null;

        Log::info('Midtrans webhook', compact('transactionStatus', 'orderId', 'paymentType'));

        $registration = Registration::where('order_id', $orderId)->first();
        if (!$registration) return response('OK', 200);

        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            $this->markAsPaid($registration, $paymentType);
        } elseif ($transactionStatus === 'settlement') {
            $this->markAsPaid($registration, $paymentType);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $registration->update(['payment_status' => 'failed']);
        }

        return response('OK', 200);
    }

    private function markAsPaid(Registration $registration, string $paymentType): void
    {
        if ($registration->payment_status === 'paid') return; // idempotent

        $registration->update([
            'payment_status' => 'paid',
            'payment_method' => $paymentType,
            'paid_at'        => now(),
        ]);

        GenerateTicketJob::dispatch($registration);
    }

    // Cek status pembayaran (polling dari frontend)
    public function checkStatus($code)
    {
        $registration = Registration::where('registration_code', $code)
            ->select('payment_status', 'registration_code')
            ->with('ticket:id,registration_id,token')
            ->firstOrFail();

        return response()->json([
            'status'      => $registration->payment_status,
            'ticket_url'  => $registration->ticket
                ? route('ticket.show', $registration->ticket->token)
                : null,
        ]);
    }
}
