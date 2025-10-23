<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class InvoicePdfController extends Controller
{
    public function __invoke(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        $order->load('items', 'payments');
        // Use dompdf if available
        if (! class_exists(\Dompdf\Dompdf::class)) {
            return response()->json(['error' => 'pdf_library_missing'], 500);
        }
        $html = view('front.account.partials.invoice_pdf', compact('order'))->render();
        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice-'.$order->id.'.pdf"',
        ]);
    }
}
