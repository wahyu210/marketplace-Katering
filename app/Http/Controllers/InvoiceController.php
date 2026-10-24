<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/** Shared by both portals: each party sees the same invoice. */
class InvoiceController extends Controller
{
    public function show(Invoice $invoice): View
    {
        Gate::authorize('view', $invoice);

        return view('invoices.show', ['invoice' => $this->load($invoice)]);
    }

    public function download(Invoice $invoice): Response
    {
        Gate::authorize('view', $invoice);

        $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $this->load($invoice)])->setPaper('a4');

        return $pdf->download($invoice->urlKey().'.pdf');
    }

    public function markPaid(Request $request, Invoice $invoice): RedirectResponse
    {
        Gate::authorize('markPaid', $invoice);

        $invoice->update(['status' => InvoiceStatus::Paid, 'paid_at' => now()]);

        return back()->with('status', "Invoice {$invoice->invoice_number} ditandai lunas.");
    }

    private function load(Invoice $invoice): Invoice
    {
        return $invoice->load(['order.items', 'order.customer.user', 'order.merchant.user']);
    }
}
