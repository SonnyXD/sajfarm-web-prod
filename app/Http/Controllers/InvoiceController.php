<?php

namespace App\Http\Controllers;

use \App\Models\Invoice;
use \App\Models\Institution;
use \App\Models\Provider;
use \App\Models\InvoiceItem;
use \App\Models\ItemStock;
use Illuminate\Http\Request;
//use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
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
            'nir-number' => 'nullable',
            'insertion-date' => 'required',
            'product' => 'required'
        ));

        $user = Auth::user();

        if($user == null) {
            return redirect('/login');
        }

        foreach($request->input('product') as $productPost) {
            if($productPost['productId'] == null || $productPost['productQty'] == null || $productPost['productExp'] == null
            || $productPost['productUm'] == null || $productPost['productPrice'] == null || $productPost['productTva'] == null
            || $productPost['productTvaPrice'] == null || $productPost['productValue'] == null || $productPost['productUmText'] == null
            || $productPost['productName'] == null) {
                Session::flash('error');
                return redirect('/operatiuni/intrare-factura');
            }
        }

        $uid = Str::random(30);
    
        $invoice = new \App\Models\Invoice();
        $invoice->provider_id = $request->input('furnizor-select');
        $invoice->number = $request->input('document-number');
        $invoice->document_date = $request->input('document-date');
        $invoice->due_date = $request->input('due-date');
        $invoice->discount_procent = $request->input('discount-procent');
        $invoice->discount_value = $request->input('discount-value');
        $invoice->total = $request->input('total-value');
        $invoice->insertion_date = $request->input('insertion-date');
        $invoice->aviz = 0;
        $invoice->uid = $uid;
        //$invoice->canceled = 0;
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

        $old_date = $request->input('document-date');
        $new_date = date("d-m-Y", strtotime($old_date));  
        $provider_id = $request->input('furnizor-select');
        $provider = Provider::where('id', $provider_id)->get();
        $invoice_number = $request->input('document-number');
        $old_due_date = $request->input('due-date');
        $new_due_date = date("d-m-Y", strtotime($old_due_date));
        $old_insertion_date = $request->input('insertion-date');
        $new_insertion_date = date("d-m-Y", strtotime($old_insertion_date));

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
                <span style="float: right;">Numar document: '. $invoice_id . ' / ' . $new_date .'</span>
                <br>
                <span style="float: right;">Furnizor: '. $provider->first()->name .'</span>
                <br>
                <span style="float: right;">Gestiune: DEPOZIT FARMACIE</span>
                <br>
                <span style="float: right;">Document intrare: Factura fiscala - '. $invoice_number .'</span>
                <br>
                <span style="float: right;">Data scadenta: '. $new_due_date .'</span>
                <br>
                <span style="float: right;">Data introducerii facturii: '. $new_insertion_date .'</span>
                <br>
                <br>
                <br>
        ';

        $html .= '
        <table>
        <tr>
          <th style="font-weight: bold; text-align: center;">Cod CIM</th>
          <th style="font-weight: bold; text-align: center;">Denumire Produs</th>
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
        
            $invoice_item->invoice_id = Invoice::all()->last()->id;
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

            $html.= '<tr nobr="true">
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
            $item_stock->inventory_id = 1;
            $item_stock->invoice_item_id = InvoiceItem::all()->last()->id;
            $item_stock->quantity = $productPost['productQty'];
            $item_stock->save();
            
        }

        $html .= '</table>';

        $html .= '<br><br>
                    Total valoare: '. $total_value .'<br><br><br>';     

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
        
        PDF::SetTitle('NIR');
        PDF::AddPage('P', 'A4');
        PDF::writeHTML($html, true, false, true, false, '');

        //PDF::Output(public_path($filename), 'D');

        //return response()->download(public_path($filename));

        //$item_stock->save();

        // Session::flash('success', 'Here is your success message');
        
        PDF::Output(public_path($filename), 'F');

        Session::flash('fileToDownload', url($filename));
        Session::flash('success', 'Factura inregistrata cu succes!');

        // return redirect('/operatiuni/intrare-factura')
        //     ->with('success', 'Factura inregistrata cu succes!')->with('download',);
        return redirect('/operatiuni/intrare-factura')
           ->with('download',);
    
        // return redirect('/operatiuni/intrare-factura')
        //    ->with('success', 'Factura inregistrata cu succes!');
        // $invoice = $this->invoice_form($request);

        // $filename = 'pdfs/nir'.$invoice->invoice_id.'.pdf';

        // //Session::flash('fileToDownload', url($filename));

        // $pdf = PDF::loadView('pdf-generation.invoice', compact('invoice'));

        // $pdf->setPaper('A4', 'landscape');

        // Session::flash('fileToDownload', url($filename));

        // //Storage::put('/public/pdfs/'.$filename.'', $pdf->output()) ;
        // // return $pdf->setPaper('a4', 'landscape')->download('invoice.pdf');
        // file_put_contents(''.$filename, $pdf->output());
        // //return $pdf->stream($filename, array("Attachment" => false));
        // return redirect('/operatiuni/intrare-factura')
        //     ->with('success', 'Factura inregistrata cu succes!')->with('download',);
    }

    public function institution() {
        $institution = Institution::where('id', 1)->get()->first();

        return $institution;
    }

    public function user() {
        $user = Auth::user();

        return $user;
    }

    public function invoice_form(Request $request) {

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

        $invoices = Invoice::all();
        $invoice_id = $invoices->last()->id;

        $old_date = $request->input('document-date');
        $new_date = date("d-m-Y", strtotime($old_date));  
        $provider_id = $request->input('furnizor-select');
        $provider = Provider::where('id', $provider_id)->get();
        $invoice_number = $request->input('document-number');
        $old_due_date = $request->input('due-date');
        $new_due_date = date("d-m-Y", strtotime($old_due_date));  

        $filename = 'pdfs/nir'.$invoice_id.'.pdf';

        $institution = $this->institution();
        $user = $this->user();

        $total_value = 0;

        $products = $request->input('product');

        foreach($products as $productPost) {
            $invoice_item = new \App\Models\InvoiceItem();
        
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

            // $html.= '<tr>
            //     <td style="font-weight: bold; text-align: center;">'. $productPost['productCim'] .'</td>
            //     <td style="font-weight: bold; text-align: center;">'. $productPost['productCode'] .'</td>
            //     <td style="font-weight: bold; text-align: center;">'. $productPost['productName'] .'</td>
            //     <td style="font-weight: bold; text-align: center;">'. $productPost['productLot'] .'</td>
            //     <td style="font-weight: bold; text-align: center;">'. date("d-m-Y", strtotime($productPost['productExp'])) .'</td>
            //     <td style="font-weight: bold; text-align: center;">'. $productPost['productUmText'] .'</td>
            //     <td style="font-weight: bold; text-align: center;">'. $productPost['productQty'] .'</td>
            //     <td style="font-weight: bold; text-align: center;">'. $productPost['productPrice'] .'</td>
            //     <td style="font-weight: bold; text-align: center;">'. $productPost['productTvaPrice'] .'</td>
            //     <td style="font-weight: bold; text-align: center;">'. $productPost['productValue'] .'</td>
            // </tr>';

            $item_stock = new \App\Models\ItemStock();
            $item_stock->item_id = $productPost['productId'];
            $item_stock->inventory_id = 1;
            $item_stock->invoice_item_id = InvoiceItem::all()->last()->id;
            $item_stock->quantity = $productPost['productQty'];
            $item_stock->save();
            
        }

        $array = array(
            'invoice_id' => $invoice_id,
            'new_date' => $new_date,
            'provider' => $provider,
            'invoice_number' => $invoice_number,
            'due_date' => $new_due_date,
            'provider' => $provider,
            'institution' => $institution,
            'user' => $user,
            'products' => $products,
            'total_value' => $total_value
        );

        return (object) $array;
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
    public function destroy(Invoice $nir)
    {
        $nir->canceled = 1;
        $nir->save();

        // $items = ItemStock::with(['invoice_item' => function($query) use($nir){
        //     $query->where('invoice_id', $nir->id);
        // }])
        // ->get();

        $items = InvoiceItem::with('itemstock')
        ->where('invoice_id', $nir->id)
        ->get();

        foreach($items as $item) {
            foreach($item->itemstock as $i_stock) {
                $i_stock->delete();
            }
            $item->delete();
        }

        Session::flash('success', '');

        return redirect('/operatiuni/anulare-factura');
    }
}
