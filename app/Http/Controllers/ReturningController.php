<?php

namespace App\Http\Controllers;

use App\Models\Returning;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\ItemStock;
use App\Models\Institution; 
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
            'document-date' => 'required'
        ));
    
        $returning = new \App\Models\Returning();
        $returning->inventory_id = $request->input('from-location');
        $returning->document_date = $request->input('document-date');
   
        $returning->save();

        $products = $request->input('product');

        $old_date = $request->input('document-date');
        $new_date = date("d-m-Y", strtotime($old_date));  
        $from_location = Inventory::where('id', $request->input('from-location'))->get();

        $user = Auth::user();
        $returning = Returning::all();
        $returning_id = $returning->last()->id;
        $institution = Institution::all();

        $filename = 'pdfs/transfer'.$returning_id.'.pdf';

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
        <span style="font-weight: bold; float: right;">Gestiune de iesire: '. $from_location->first()->name .'</span>
        <br>
        <br>
        <br>
        <br>
';

        $html .= '
        <table>
        <tr>
          <th style="font-weight: bold; text-align: center;">Nume</th>
          <th style="font-weight: bold; text-align: center;">UM</th>
          <th style="font-weight: bold; text-align: center;">Cantitate</th>
          <th style="font-weight: bold; text-align: center;">Motiv</th>
        </tr>
        ';

        foreach($products as $product) {
            $item = ItemStock::with('invoice_item')->where('id', $product['productId'])->get()->first();
            $item->quantity -= $product['productQty'];
            $item->save();

            $html.= '<tr>
            <td style="font-weight: bold; text-align: center;">'. $product['productName'] .'</td>
            <td style="font-weight: bold; text-align: center;">'. $product['productUmText'] .'</td>
            <td style="font-weight: bold; text-align: center;">'. $product['productQty'] .'</td>
            <td style="font-weight: bold; text-align: center;">'. $product['productReason'] .'</td>
        </tr>';
        }

        $html .= '<br>';

        $html .= '';

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
