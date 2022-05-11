<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Session;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $provider = $request->get('furnizor-select');
        // $number = $request->get('document-date');
        // $document_date = $request->get('document-date');
        // $due_date = $request->get('due-date');
        // $discount_procent = $request->get('discount-procent');
        // $discount_value = $request->get('discount-value');
        // $total = $request->get('total-value');

        // Invoice::create([
        //     'provider_id' => $request->$provider,
        //     'number' => $request->$number,
        //     'document_date' => $request->$document_date,
        //     'due_date' => $request->$due_date,
        //     'discount_procent' => $request->$discount_procent,
        //     'discount_value' => $request->$discount_value,
        //     'total' => $request->$total
        // ]);

        // return redirect('/intrare-factura');



        $this->validate($request, array(
            'furnizor-select' => 'required',
            'document-number' => 'required',
            'document-date' => 'required',
            'due-date' => 'required',
            'discount-procent' => 'required',
            'discount-value' => 'required',
            'total-value' => 'required',
            'nir-number' => 'nullable'
        ));
    
        $invoice = new \App\Models\Invoice();
        $invoice->provider_id = $request->input('furnizor-select');
        $invoice->number = $request->input('document-number');
        $invoice->document_date = $request->input('document-date');
        $invoice->due_date = $request->input('due-date');
        $invoice->discount_procent = $request->input('discount-procent');
        $invoice->discount_value = $request->input('discount-value');
        $invoice->total = $request->input('total-value');
        $invoice->save();

        // $medicament = new \App\Models\ItemStock();
        // $medicament->id = 1;
        // $medicament->inventory_id = 1;
        // $medicament->save();

        // Invoice($products, $factura);

        //$item_stock = new \App\Models\ItemStock();
        //dd($request->input('product'));

        foreach($request->input('product') as $productPost) {
            $invoice_item = new \App\Models\InvoiceItem();
            
            //dd($productPost);
            //$invoice_item->invoice_id = $productPost['lot']; // lot or productCode
            //$invoice_item->invoice_id = $request->input('nir-number'); // lot or productCode
            $invoice_item->invoice_id = Invoice::all()->last()->id;
            $invoice_item->item_id = $productPost['productId'];
            $invoice_item->cim_code = $productPost['productCim'];
            $invoice_item->product_code = $productPost['productCode'];
            $invoice_item->quantity = $productPost['productQty'];
            $invoice_item->exp_date = $productPost['productExp'];
            $invoice_item->lot = $productPost['productLot'];
            $invoice_item->measure_unit_id = $productPost['productUm'];
            $invoice_item->price = $productPost['productPrice'];
            $invoice_item->tva = $productPost['productTva'];
            $invoice_item->tva_price = $productPost['productTvaPrice'];
            $invoice_item->value = $productPost['productValue'];
            $invoice_item->save();

            $item_stock = new \App\Models\ItemStock();
            $item_stock->item_id = $productPost['productId'];
            $item_stock->inventory_id = 1;
            $item_stock->invoice_id = Invoice::all()->last()->id;
            $item_stock->quantity = $productPost['productQty'];
            $item_stock->save();
            
        }


        //$item_stock->save();

        // Session::flash('success', 'Here is your success message');
    
        return redirect('/operatiuni/intrare-factura')
            ->with('success', 'Factura inregistrata cu succes!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
