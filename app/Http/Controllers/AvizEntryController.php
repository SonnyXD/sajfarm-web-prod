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
use Illuminate\Support\Str;
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
            'insertion-date' => 'required',
            'discount-procent' => 'required',
            'discount-value' => 'required',
            'total-value' => 'required',
            'nir-number' => 'nullable'
        ));

        $uid = Str::random(30);
    
        $aviz = new \App\Models\AvizEntry();
        $aviz->type = $request->input('type');
        $aviz->provider_id = $request->input('furnizor-select');
        $aviz->number = $request->input('document-number');
        $aviz->document_date = $request->input('document-date');
        $aviz->due_date = $request->input('due-date');
        $aviz->discount_procent = $request->input('discount-procent');
        $aviz->discount_value = $request->input('discount-value');
        $aviz->total = $request->input('total-value');
        $aviz->uid = $uid;
        $aviz->save();

        $invoice = new \App\Models\Invoice();
        $invoice->provider_id = $request->input('furnizor-select');
        $invoice->number = $request->input('document-number');
        $invoice->document_date = $request->input('document-date');
        $invoice->due_date = $request->input('due-date');
        $invoice->insertion_date = $request->input('insertion-date');
        $invoice->discount_procent = $request->input('discount-procent');
        $invoice->discount_value = $request->input('discount-value');
        $invoice->total = $request->input('total-value');
        $invoice->aviz = 1;
        $invoice->uid = $uid;
        $invoice->save();

        $last_invoice_id = Invoice::orderBy('id', 'desc')->first()->id;
    
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

        $filename = 'pdfs/'.$uid.'.pdf';

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
                <span style="float: right;">Numar document: '. $last_invoice_id . ' / ' . $new_date .'</span>
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
            $aviz_item->product_code = null;
            $aviz_item->quantity = $productPost['productQty'];
            $aviz_item->exp_date = $productPost['productExp'];
            $aviz_item->lot = $productPost['productLot'];
            $aviz_item->measure_unit_id = $productPost['productUm'];
            $aviz_item->price = $productPost['productPrice'];
            $aviz_item->tva = $productPost['productTva'];
            $aviz_item->tva_price = $productPost['productTvaPrice'];
            $aviz_item->value = $productPost['productValue'];
            $aviz_item->save();

            $invoice_item = new \App\Models\InvoiceItem();
            $invoice_item->invoice_id = $last_invoice_id;
            $invoice_item->item_id = $productPost['productId'];
            $invoice_item->cim_code = $productPost['productCim'];
            $invoice_item->product_code = null;
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
                <td style="text-align: center;">'. $productPost['productCim'] .'</td>
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
            $item_stock->invoice_item_id = InvoiceItem::orderBy('id', 'desc')->first()->id;
            $item_stock->quantity = $productPost['productQty'];
            $item_stock->save();
            
        }

        $html .= '</table>';

        $html .= '<br>
                    Total valoare: '. $total_value .'<br><br><br>';

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
        
        //$html .= '';

        $html .= '<table class="footer-table">
        <tr nobr="true">
        <td colspan="2" style="text-align: center;">Comisia de receptie</td>
        </tr>
        <tr nobr="true">
        <td style="text-align: center;">Nume si prenume</td>
        <td style="text-align: center;">Semnatura</td>
        </tr>
        <tr nobr="true">
        <td style="text-align: center;" colspan="1">Director medical dr. '. $institution[0]->medical_director .'</td>
        <td colspan="1"></td>
        </tr>
        <tr nobr="true">
        <td style="text-align: center;" colspan="1">As. sef '. $institution[0]->assistent_manager .'</td>
        <td colspan="1"></td>
        </tr>
        <tr nobr="true">
        <td style="text-align: center;" colspan="1">Ec. '. $institution[0]->pharmacy_ec .'</td>
        <td colspan="1"></td>
        </tr>
    </table>';

    $html .= '<br>';

    $html .= 'Gestionari:';

    $html .= '<br>';

    $html .= '<span style="text-align: left;">Farm. Sef<br>'.$institution[0]->pharmacy_manager.'<br></span>';
    
    $html .= '<br>';

    $html .= '<span style="text-align: left;">As. Farm. <br>'.$institution[0]->assistent.'</span>';

        $html .= '</html>';

        PDF::setFooterCallback(function($pdf) {

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 10);
            // Page number
            $pdf->Cell(0, 10, 'Pagina '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    
    });
        
        PDF::SetTitle('NIR Aviz');
        PDF::AddPage('P', 'A4');
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
