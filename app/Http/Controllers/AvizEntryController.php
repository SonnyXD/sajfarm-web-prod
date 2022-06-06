<?php

namespace App\Http\Controllers;

use App\Models\AvizEntry;
use App\Models\AvizEntryItem;
use \App\Models\Invoice;
use \App\Models\Institution;
use \App\Models\Provider;
use \App\Models\InvoiceItem;
use \App\Models\Inventory;
use Illuminate\Http\Request;
use Auth;
use PDF;
use Session;

class AvizEntryController extends Controller
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
        $this->validate($request, array(
            'type' => 'required',
            'furnizor-select' => 'required',
            'document-number' => 'required',
            'document-date' => 'required',
            'due-date' => 'required',
            'discount-procent' => 'required',
            'discount-value' => 'required',
            'total-value' => 'required',
            'nir-number' => 'nullable'
        ));
    
        $aviz = new \App\Models\AvizEntry();
        $aviz->type = $request->input('type');
        $aviz->provider_id = $request->input('furnizor-select');
        $aviz->number = $request->input('document-number');
        $aviz->document_date = $request->input('document-date');
        $aviz->due_date = $request->input('due-date');
        $aviz->discount_procent = $request->input('discount-procent');
        $aviz->discount_value = $request->input('discount-value');
        $aviz->total = $request->input('total-value');
        $aviz->save();
    
        $avize = AvizEntry::all();
        $aviz_id = $avize->last()->id;
        $user = Auth::user();

        $old_date = $request->input('document-date');
        $new_date = date("d-m-Y", strtotime($old_date));  
        $provider_id = $request->input('furnizor-select');
        $provider = Provider::where('id', $provider_id)->get();
        $aviz_number = $request->input('document-number');
        $old_due_date = $request->input('due-date');
        $new_due_date = date("d-m-Y", strtotime($old_due_date));  

        $institution = Institution::all();

        $filename = 'pdfs/aviz'.$aviz_id.'.pdf';

        $html = '<html>
                <head>
                <style>
                td, th {border: 1px solid black;}
                </style>
                </head>
                ';

        $html .= ' <span style="font-weight: bold; float: left;">'. $institution[0]->name .'</span>
                <br>
                <span style="float: left;">Utilizator: '. $user->name .'</span>
                <h2 style="font-weight:bold; text-align: center;">NOTA DE INTRARE RECEPTIE</h2>
                <br>
                <span style="float: right;">Numar document: '. $aviz_id . ' / ' . $new_date .'</span>
                <br>
                <span style="float: right;">Furnizor: '. $provider->first()->name .'</span>
                <br>
                <span style="float: right;">Gestiune: DEPOZIT FARMACIE</span>
                <br>
                <span style="float: right;">Document intrare: Aviz intrare - '. $request->input('type') .' - '. $aviz_number .'</span>
                <br>
                <span style="float: right;">Data scadenta: '. $new_due_date .'</span>
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
            $aviz_item = new \App\Models\AvizEntryItem();
            
            //dd($productPost);
            //$invoice_item->invoice_id = $productPost['lot']; // lot or productCode
            //$invoice_item->invoice_id = $request->input('nir-number'); // lot or productCode
            $aviz_item->aviz_entry_id = AvizEntry::all()->last()->id;
            $aviz_item->item_id = $productPost['productId'];
            $aviz_item->cim_code = $productPost['productCim'];
            $aviz_item->product_code = $productPost['productCode'];
            $aviz_item->quantity = $productPost['productQty'];
            $aviz_item->exp_date = $productPost['productExp'];
            $aviz_item->lot = $productPost['productLot'];
            $aviz_item->measure_unit_id = $productPost['productUm'];
            $aviz_item->price = $productPost['productPrice'];
            $aviz_item->tva = $productPost['productTva'];
            $aviz_item->tva_price = $productPost['productTvaPrice'];
            $aviz_item->value = $productPost['productValue'];
            $aviz_item->save();

            $total_value = $total_value + $productPost['productValue'];

            $html.= '<tr>
                <td style="text-align: center;">'. $productPost['productCim'] .'</td>
                <td style="text-align: center;">'. $productPost['productCode'] .'</td>
                <td style="text-align: center;">'. $productPost['productName'] .'</td>
                <td style="text-align: center;">'. $productPost['productLot'] .'</td>
                <td style="text-align: center;">'. date("d-m-Y", strtotime($productPost['productExp'])) .'</td>
                <td style="text-align: center;">'. $productPost['productUmText'] .'</td>
                <td style="text-align: center;">'. $productPost['productQty'] .'</td>
                <td style="text-align: center;">'. $productPost['productPrice'] .'</td>
                <td style="text-align: center;">'. $productPost['productTvaPrice'] .'</td>
                <td style="text-align: center;">'. $productPost['productValue'] .'</td>
            </tr>';

            $item_stock = new \App\Models\ItemStock();
            $item_stock->item_id = $productPost['productId'];
            //$item_stock->inventory_id = Inventory::where('name', $request->input('type'))->first()->id;
            $item_stock->inventory_id = 1;
            $item_stock->invoice_item_id = AvizEntryItem::all()->last()->id;
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

        return redirect('/operatiuni/aviz-intrare')
            ->with('success', 'Aviz Intrare inregistrat cu succes!')->with('download');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AvizEntry  $avizEntry
     * @return \Illuminate\Http\Response
     */
    public function show(AvizEntry $avizEntry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AvizEntry  $avizEntry
     * @return \Illuminate\Http\Response
     */
    public function edit(AvizEntry $avizEntry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AvizEntry  $avizEntry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AvizEntry $avizEntry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AvizEntry  $avizEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy(AvizEntry $avizEntry)
    {
        //
    }
}
