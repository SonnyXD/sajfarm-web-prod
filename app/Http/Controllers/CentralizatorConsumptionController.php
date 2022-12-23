<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Inventory;
use \App\Models\Checklist;
use \App\Models\ChecklistItem;
use \App\Models\Item;
use \App\Models\InvoiceItem;
use \App\Models\Institution;
use \App\Models\ItemStock;
use \App\Models\Category;
use Session;
use PDF;
use Auth;
use Carbon\Carbon;

class CentralizatorConsumptionController extends Controller
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
            'from-date' => 'required',
            'until-date' => 'required'
        ));

        $inventories = Inventory::all();
        $user = Auth::user();
        $institution = Institution::all();
        $categories = Category::all();

        $from = $request->input('from-date');
        $to = $request->input('until-date');

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
                <h2 style="font-weight:bold; text-align: center;">CENTRALIZATOR CONSUMURI</h2>
                <br>
                <br>
                <span style="float: right;">Perioada: '. date("d-m-Y", strtotime($from)) .' - '. date("d-m-Y", strtotime($to)) .'</span>
                <br>
                <br>
                <br>
        ';

        foreach($inventories as $inventory) {
            if($inventory->id > 1) {
                $html .= '<span style="float: right; font-weight: bold;">Gestiune: '. $inventory->name .'</span><br><br>';

                $html .= '<table>';

                $html .= '<thead>
                <tr nobr="true">
                <th style="text-align: center; font-weight: bold;">Subgestiune</th>
                <th style="text-align: center; font-weight: bold;">Total Valoare Fara TVA</th>
                <th style="text-align: center; font-weight: bold;">Total Valoare Cu TVA</th>
                </tr>
                </thead>';

                


                // $checklists = Checklist::with(['checklistitems.item.category' => function($query) use($old_from_date, $old_until_date, $inventory_id, $old_from_date_interval, $subset) {
                //     $query->where('category_id', $category->id);
                // }])
                // ->whereBetween('checklist_date', [$from, $to])
                // ->where('inventory_id', $inventory->id)
                // ->get();

                foreach($categories as $category) {

                    $checklists = Checklist::with(['checklistitems.item.category' => function($query) use($category) {
                        // $query->where('id', 9);
                    }])
                    ->whereHas('checklistitems.item.category', function ($query) use($category) {
                        // $query->where('id', 3);
                    })
                    ->whereBetween('checklist_date', [$from, $to])
                    ->where('inventory_id', $inventory->id)
                    ->get();

                    $no_tva = 0;

                    $tva = 0;

                    //dd($checklists);

                    foreach($checklists as $checklist) {
                        foreach($checklist->checklistitems as $item) {
                            if($item->item->category->id == $category->id) {
                                $item_stock = ItemStock::where('id', $item->item_stock_id)
                                ->first();
                                
                                $invoice_item = InvoiceItem::where('id', $item_stock->invoice_item_id)
                                ->first();

                                $no_tva += $invoice_item->price;
                                $tva += $invoice_item->tva_price;
                            }
                        }
                    }

                    $html .= '<tr nobr="true">
                            <td style="text-align: center;">'. $category->name .'</td>
                            <td style="text-align: center;">'. round($no_tva, 4) .'</td>
                            <td style="text-align: center;">'. round($tva, 4) .'</td>
                         </tr>';
                }

                $html .= '</table>';
            }
        }

        PDF::setFooterCallback(function($pdf) {
    
            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 10);
            // Page number
            $pdf->Cell(0, 10, 'Pagina '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    
    });
            
            PDF::SetTitle('Inventar');
            PDF::AddPage('L', 'A4');
            PDF::writeHTML($html, true, false, true, false, '');

            PDF::Output('name.pdf', 'I');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
