<?php

namespace App\Http\Controllers;

use App\Models\Returning;
use App\Models\ReturningItem;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\ItemStock;
use App\Models\Institution; 
use App\Models\Ambulance; 
use Illuminate\Http\Request;
use Session;
use PDF;
use Auth;

class ReturningController extends Controller
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
            'from-location-id' => 'required',
            'document-date' => 'required'
        ));
    
        $returning = new \App\Models\Returning();
        $returning->inventory_id = $request->input('from-location-id');
        $returning->document_date = $request->input('document-date');

        //$ambulance_id = $request->input('ambulance-select');
   
        $returning->save();

        $products = $request->input('product');

        $old_date = $request->input('document-date');
        $new_date = date("d-m-Y", strtotime($old_date));  
        $from_location = Inventory::where('id', $request->input('from-location-id'))->first();

        // $span_amb = '';

        // if($ambulance_id === null) {
        //     $span_amb = '<span style="font-weight: bold; float: right;">Gestiune de iesire: '. $from_location->first()->name .'</span>';
        // } else {
        //     $amb_name = Ambulance::where('id', $ambulance_id)->get()->first()->license_plate;
        //     $span_amb = '<span style="font-weight: bold; float: right;">Gestiune de iesire: '. $from_location->first()->name .' - '. $amb_name .'</span>';
        // }

        $user = Auth::user();
        $returning = Returning::all();
        $returning_id = $returning->last()->id;
        $institution = Institution::all();

        $filename = 'pdfs/retur'.$returning_id.'.pdf';

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
        <h2 style="font-weight:bold; text-align: center;">PROCES VERBAL RETUR</h2>
        <br>
        <span style="float: right;">Numar document: '. $returning_id . ' / ' . $new_date .'</span>
        <br>
        <br>
';

        $html .= '
        <table width: 100%>
        <tr>
        <th style="font-weight: bold; text-align: center; width: 18%;">Denumire Produs</th>
        <th style="font-weight: bold; text-align: center; width: 10%;">UM</th>
        <th style="font-weight: bold; text-align: center; width: 10%;">Cantitate</th>
        <th style="font-weight: bold; text-align: center; width: 10%;">Pret</th>
        <th style="font-weight: bold; text-align: center; width: 10%;">Valoare</th>
        <th style="font-weight: bold; text-align: center; width: 10%;">Lot</th>
        <th style="font-weight: bold; text-align: center; width: 12%;">Data expirare</th>
        <th style="font-weight: bold; text-align: center; width: 10%;">Motiv</th>
        <th style="font-weight: bold; text-align: center; width: 10%;">Gestiunea din care iese</th>
        </tr>
        ';

        $total_value = 0;

        foreach($products as $product) {
            $item = ItemStock::with('invoice_item')->where('id', $product['productId'])->get()->first();
            $item->quantity -= $product['productQty'];
            $item->save();

            $returning_item = new \App\Models\ReturningItem();

            $returning_item->item_stock_id = $product['productId'];
            $returning_item->returning_id = $returning_id;
            $returning_item->item_id = $item->item_id;
            $returning_item->quantity = $product['productQty'];
            $returning_item->reason = $product['productReason'];
            $returning_item->ambulance_id = $product['productAmb'];

            $returning_item->save();

            $from = "";

            if(!empty($returning_item->ambulance_id)) {
                $from = $from_location->name.' - '.ReturningItem::with('ambulance')->where('ambulance_id', '=', $product['productAmb'])->first()->ambulance->license_plate;
            }
            
            if(empty($from)) {
                $from = $from_location->name;
            }

            $html.= '<tr nobr="true">
            <td style="text-align: center;">'. $product['productName'] .'</td>
            <td style="text-align: center;">'. $product['productUmText'] .'</td>
            <td style="text-align: center;">'. $product['productQty'] .'</td>
            <td style="text-align: center;">'. $item->invoice_item->price .'</td>
            <td style="text-align: center;">'. $item->invoice_item->price * $product['productQty'] .'</td>
            <td style="text-align: center;">'. $item->invoice_item->lot .'</td>
            <td style="text-align: center;">'. date("d-m-Y", strtotime($item->invoice_item->exp_date )).'</td>
            <td style="text-align: center;">'. $product['productReason'] .'</td>
            <td style="text-align: center;">'. $from .'</td>
        </tr>';

        $total_value += $item->invoice_item->price * $product['productQty'];
        }

        $html .= '</table>';

        $html .= '<br>';

        $html .= '<br>';

        $html .= 'Total valoare: '. $total_value .'';

        $html .= '<br>';

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

        PDF::SetTitle('Proces Verbal Retur');
        PDF::AddPage('P', 'A4');
        PDF::writeHTML($html, true, false, true, false, '');

        PDF::Output(public_path($filename), 'F');

        Session::flash('fileToDownload', url($filename));

        return redirect('/operatiuni/retur')
            ->with('success', 'Proces verbal retur efectuat cu succes!')->with('download',);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Returning  $returning
     * @return \Illuminate\Http\Response
     */
    public function show(Returning $returning)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Returning  $returning
     * @return \Illuminate\Http\Response
     */
    public function edit(Returning $returning)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Returning  $returning
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Returning $returning)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Returning  $returning
     * @return \Illuminate\Http\Response
     */
    public function destroy(Returning $returning)
    {
        //
    }
}
