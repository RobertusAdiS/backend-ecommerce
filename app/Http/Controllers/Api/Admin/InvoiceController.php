<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Invoice;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;

class InvoiceController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $invoice = Invoice::with('customer')->when(request()->q, function($invoice) {
            $invoice->where('invoice', 'like', '%'.request()->q.'%');
        })->latest()->paginate(5);

        //return with api resource
        return new InvoiceResource(true, 'success', $invoice);
    }

    public function show($id)
    {
        $invoice = Invoice::with('orders.product','customer', 'city', 'province')->where($id)->first();

        if ($invoice) {
            //return success with api resource
            return new InvoiceResource(true, 'Detail Data Invoice', $invoice);
        } else {
            //return failed with api resource
            return new InvoiceResource(false, 'Detail Data Invoice Tidak Ditemukan', null);
        }
    }
}
