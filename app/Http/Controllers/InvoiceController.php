<?php

namespace App\Http\Controllers;

use \App\Models\Invoice;
use \App\Models\Institution;
use \App\Models\Provider;
use \App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Session;
use PDF;
use Auth;

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

        $invoices = Invoice::all();
        $invoice_id = $invoices->last()->id;
        $user = Auth::user();

        $old_date = $request->input('document-date');
        $new_date = date("d-m-Y", strtotime($old_date));  
        $provider_id = $request->input('furnizor-select');
        $provider = Provider::where('id', $provider_id)->get();
        $invoice_number = $request->input('document-number');
        $old_due_date = $request->input('due-date');
        $new_due_date = date("d-m-Y", strtotime($old_due_date));  

        $institution = Institution::all();

        $filename = 'pdfs/nir'.$invoice_id.'.pdf';

        $html = '<html>
                <head>
                <style>
                td, th {border: 2px solid black;}
                </style>
                </head>
                ';

        $html .= ' <span style="font-weight: bold; float: left;">'. $institution[0]->name .'</span>
                <br>
                <span style="float: left;">Utilizator: '. $user->name .'</span>
                <h2 style="font-weight:bold; text-align: center;">NOTA DE INTRARE RECEPTIE</h2>
                <br>
                <span style="font-weight: bold; float: right;">Numar document: '. $invoice_id . ' / ' . $new_date .'</span>
                <br>
                <span style="font-weight: bold; float: right;">Furnizor: '. $provider->first()->name .'</span>
                <br>
                <span style="font-weight: bold; float: right;">Gestiune: DEPOZIT FARMACIE</span>
                <br>
                <span style="font-weight: bold; float: right;">Document intrare: Factura fiscala - '. $invoice_number .'</span>
                <br>
                <span style="font-weight: bold; float: right;">Data scadenta: '. $new_due_date .'</span>
                <br>
                <br>
                <br>
        ';

        $html .= '
        <table>
        <tr>
          <th style="font-weight: bold; text-align: center;">Cod CIM</th>
          <th style="font-weight: bold; text-align: center;">Cod Produs</th>
          <th style="font-weight: bold; text-align: center;">Nume</th>
          <th style="font-weight: bold; text-align: center;">Lot</th>
          <th style="font-weight: bold; text-align: center;">Data Exp.</th>
          <th style="font-weight: bold; text-align: center;">UM</th>
          <th style="font-weight: bold; text-align: center;">Cantitate</th>
          <th style="font-weight: bold; text-align: center;">Pret Unitar</th>
          <th style="font-weight: bold; text-align: center;">Pret cu TVA</th>
          <th style="font-weight: bold; text-align: center;">Valoare (RON)</th>
        </tr>
        ';

        $total_value = 0;

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

            $total_value = $total_value + $productPost['productValue'];

            $html.= '<tr>
                <td style="font-weight: bold; text-align: center;">'. $productPost['productCim'] .'</td>
                <td style="font-weight: bold; text-align: center;">'. $productPost['productCode'] .'</td>
                <td style="font-weight: bold; text-align: center;">'. $productPost['productName'] .'</td>
                <td style="font-weight: bold; text-align: center;">'. $productPost['productLot'] .'</td>
                <td style="font-weight: bold; text-align: center;">'. date("d-m-Y", strtotime($productPost['productExp'])) .'</td>
                <td style="font-weight: bold; text-align: center;">'. $productPost['productUmText'] .'</td>
                <td style="font-weight: bold; text-align: center;">'. $productPost['productQty'] .'</td>
                <td style="font-weight: bold; text-align: center;">'. $productPost['productPrice'] .'</td>
                <td style="font-weight: bold; text-align: center;">'. $productPost['productTvaPrice'] .'</td>
                <td style="font-weight: bold; text-align: center;">'. $productPost['productValue'] .'</td>
            </tr>';

            $item_stock = new \App\Models\ItemStock();
            $item_stock->item_id = $productPost['productId'];
            $item_stock->inventory_id = 1;
            $item_stock->invoice_item_id = InvoiceItem::all()->last()->id;
            $item_stock->quantity = $productPost['productQty'];
            $item_stock->save();
            
        }

        $html .= '<br>
                    Total valoare: '. $total_value .'';

        // $invoices = Invoice::all();
        // $invoice_id = $invoices->last()->id;
        // $user = Auth::user();

        // $old_date = $request->input('document-date');
        // $new_date = date("d-m-Y", strtotime($old_date));  
        // $provider_id = $request->input('furnizor-select');
        // $provider = Provider::where('id', $provider_id)->get();
        // $invoice_number = $request->input('document-number');
        // $old_due_date = $request->input('due-date');
        // $new_due_date = date("d-m-Y", strtotime($old_due_date));  

        // $institution = Institution::all();

        // $filename = 'pdfs/nir'.$invoice_id.'.pdf';

        // $html = '<html>
        //         <head>
        //         <style>
        //         td, th {border: 2px solid black;}
        //         </style>
        //         </head>
        //         ';

        // $html .= ' <span style="font-weight: bold; float: left;">'. $institution[0]->name .'</span>
        //         <br>
        //         <span style="float: left;">Utilizator: '. $user->name .'</span>
        //         <h2 style="font-weight:bold; text-align: center;">NOTA DE INTRARE RECEPTIE</h2>
        //         <br>
        //         <span style="font-weight: bold; float: right;">Numar document: '. $invoice_id . ' / ' . $new_date .'</span>
        //         <br>
        //         <span style="font-weight: bold; float: right;">Furnizor: '. $provider->first()->name .'</span>
        //         <br>
        //         <span style="font-weight: bold; float: right;">Gestiune: DEPOZIT FARMACIE</span>
        //         <br>
        //         <span style="font-weight: bold; float: right;">Document intrare: Factura fiscala - '. $invoice_number .'</span>
        //         <br>
        //         <span style="font-weight: bold; float: right;">Data scadenta: '. $new_due_date .'</span>
        //         <br>
        //         <br>
        //         <br>
        // ';

        // $html .= '
        // <table>
        // <tr>
        //   <th style="font-weight: bold; text-align: center;">Cod CIM</th>
        //   <th style="font-weight: bold; text-align: center;">Cod Produs</th>
        //   <th style="font-weight: bold; text-align: center;">Lot</th>
        //   <th style="font-weight: bold; text-align: center;">Data Exp.</th>
        //   <th style="font-weight: bold; text-align: center;">UM</th>
        //   <th style="font-weight: bold; text-align: center;">Cantitate</th>
        //   <th style="font-weight: bold; text-align: center;">Pret Unitar</th>
        //   <th style="font-weight: bold; text-align: center;">Pret cu TVA</th>
        //   <th style="font-weight: bold; text-align: center;">Valoare (RON)</th>
        // </tr>
        // ';
        
        $html .= '';

        $html .= '</table>';

        $html .= '</html>';


        
        PDF::SetTitle('NIR');
        PDF::AddPage('L', 'A4');
        PDF::writeHTML($html, true, false, true, false, '');

        //PDF::Output(public_path($filename), 'D');

        //return response()->download(public_path($filename));

        //$item_stock->save();

        // Session::flash('success', 'Here is your success message');
        
        PDF::Output(public_path($filename), 'F');

        Session::flash('fileToDownload', url($filename));

        return redirect('/operatiuni/intrare-factura')
            ->with('success', 'Factura inregistrata cu succes!')->with('download',);
    
        //return redirect('/operatiuni/intrare-factura')
          //  ->with('success', 'Factura inregistrata cu succes!');
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