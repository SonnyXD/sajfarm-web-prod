<?php

namespace App\Http\Controllers;

use App\Models\Returning;
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
            'from-location' => 'required',
            'document-date' => 'required',
            'ambulance-select' => 'nullable'
        ));
    
        $returning = new \App\Models\Returning();
        $returning->inventory_id = $request->input('from-location');
        $returning->document_date = $request->input('document-date');
        $returning->ambulance_id = $request->input('ambulance-select') ?? null;

        $ambulance_id = $request->input('ambulance-select');
   
        $returning->save();

        $products = $request->input('product');

        $old_date = $request->input('document-date');
        $new_date = date("d-m-Y", strtotime($old_date));  
        $from_location = Inventory::where('id', $request->input('from-location'))->get();

        $span_amb = '';

        if($ambulance_id === null) {
            $span_amb = '<span style="font-weight: bold; float: right;">Gestiune de iesire: '. $from_location->first()->name .'</span>';
        } else {
            $amb_name = Ambulance::where('id', $ambulance_id)->get()->first()->license_plate;
            $span_amb = '<span style="font-weight: bold; float: right;">Gestiune de iesire: '. $from_location->first()->name .' - '. $amb_name .'</span>';
        }

        $user = Auth::user();
        $returning = Returning::all();
        $returning_id = $returning->last()->id;
        $institution = Institution::all();

        $filename = 'pdfs/retur'.$returning_id.'.pdf';

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
        <h2 style="font-weight:bold; text-align: center;">PROCES VERBAL RETUR</h2>
        <br>
        <span style="font-weight: bold; float: right;">Numar document: '. $returning_id . ' / ' . $new_date .'</span>
        <br>
        '. $span_amb .'
        <br>
        <br>
        <br>
        <br>
';

        $html .= '
        <table>
        <tr>
        <th style="font-weight: bold; text-align: center;">Cod Produs</th>
        <th style="font-weight: bold; text-align: center;">Nume</th>
        <th style="font-weight: bold; text-align: center;">UM</th>
        <th style="font-weight: bold; text-align: center;">Cantitate</th>
        <th style="font-weight: bold; text-align: center;">Pret</th>
        <th style="font-weight: bold; text-align: center;">Valoare</th>
        <th style="font-weight: bold; text-align: center;">Lot</th>
        <th style="font-weight: bold; text-align: center;">Data expirare</th>
        <th style="font-weight: bold; text-align: center;">Motiv</th>
        </tr>
        ';

        $total_value = 0;

        foreach($products as $product) {
            $item = ItemStock::with('invoice_item')->where('id', $product['productId'])->get()->first();
            $item->quantity -= $product['productQty'];
            $item->save();

            $html.= '<tr>
            <td style="font-weight: bold; text-align: center;">'. $item->invoice_item->product_code .'</td>
            <td style="font-weight: bold; text-align: center;">'. $product['productName'] .'</td>
            <td style="font-weight: bold; text-align: center;">'. $product['productUmText'] .'</td>
            <td style="font-weight: bold; text-align: center;">'. $product['productQty'] .'</td>
            <td style="font-weight: bold; text-align: center;">'. $item->invoice_item->price .'</td>
            <td style="font-weight: bold; text-align: center;">'. $item->invoice_item->price * $product['productQty'] .'</td>
            <td style="font-weight: bold; text-align: center;">'. $item->invoice_item->lot .'</td>
            <td style="font-weight: bold; text-align: center;">'. $item->invoice_item->exp_date .'</td>
            <td style="font-weight: bold; text-align: center;">'. $product['productReason'] .'</td>
        </tr>';

        $total_value += $item->invoice_item->price * $product['productQty'];

        $returning_item = new \App\Models\ReturningItem();

        $returning_item->item_stock_id = $product['productId'];
        $returning_item->returning_id = $returning_id;
        $returning_item->item_id = $item->item_id;
        $returning_item->quantity = $product['productQty'];
        $returning_item->reason = $product['productReason'];

        $returning_item->save();
        }

        $html .= '<br>';

        $html .= 'Total valoare: '. $total_value .'';

        $html .= '</table>';

        $html .= '</html>';

        PDF::SetTitle('Proces Verbal Retur');
        PDF::AddPage('L', 'A4');
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
