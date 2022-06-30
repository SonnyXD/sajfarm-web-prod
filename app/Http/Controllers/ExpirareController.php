<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Institution;
use \App\Models\ItemStock;
use \App\Models\Item;
use \App\Models\Inventory;
use \App\Models\Category;
use Session;
use PDF;
use Auth;


class ExpirareController extends Controller
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
            'inventory-select' => 'required'
        ));

        $user = Auth::user();
        $institution = Institution::all();

        //dd(now()->addMonths(6)->endOfMonth());

        $now = date('d-m-Y');

        $six_months = date('Y-m-d', strtotime(now()->addMonthsNoOverflow(6)->endOfMonth()));

        $inventory_id = $request->input('inventory-select');

        $inventory = Inventory::where('id', $inventory_id)->first();

        $categories = Category::all();

        // $items = ItemStock::leftjoin('invoice_items', 'invoice_items.id', '=', 'item_stocks.invoice_item_id')
        // ->leftjoin('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
        // ->leftjoin('measure_units', 'measure_units.id', '=', 'invoice_items.measure_unit_id')
        // ->leftjoin('items', 'items.id', '=', 'invoice_items.item_id')
        // ->where('item_stocks.inventory_id', $inventory->id)
        // ->whereBetween('invoice_items.exp_date', [$now, $six_months])
        // ->select('item_stocks.quantity as current_quantity', 'items.name as item_name', 'measure_units.name as um',
        // 'invoice_items.lot as lot', 'invoice_items.price as price', 'invoice_items.tva as tva',
        // 'invoice_items.tva_price as tva_price', 'invoice_items.exp_date as exp_date', 'items.category_id as category_id')
        // ->get();

        $items = ItemStock::leftjoin('invoice_items', 'invoice_items.id', '=', 'item_stocks.invoice_item_id')
        ->leftjoin('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
        ->leftjoin('measure_units', 'measure_units.id', '=', 'invoice_items.measure_unit_id')
        ->leftjoin('items', 'items.id', '=', 'invoice_items.item_id')
        ->where('item_stocks.inventory_id', $inventory->id)
        ->whereBetween('invoice_items.exp_date', [$now, $six_months])
        ->where('item_stocks.quantity', '!=', 0)
        ->select(ItemStock::raw('SUM(item_stocks.quantity) as current_quantity'), 'items.name as item_name', 'measure_units.name as um',
        'invoice_items.lot as lot', 'invoice_items.price as price', 'invoice_items.tva as tva',
        'invoice_items.tva_price as tva_price', 'invoice_items.exp_date as exp_date', 'items.category_id as category_id')
        ->groupBy('item_stocks.invoice_item_id')
        ->get();

        // if($items == null) {
        //     return redirect('/documente/expira-in-6-luni')
        //     ->with('error', 'Nu exista produse care expira in perioada respectiva!');
        // }

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
                <h2 style="font-weight:bold; text-align: center;">PRODUSE CARE EXPIRA IN URMATOARELE 6 LUNI</h2>
                <br>
                <span style="float: right;">Data de azi: '. $now .'</span>
                <br>
                <span style="float: right;">Produse care vor expira pana la: '. date('d-m-Y', strtotime($six_months)) .'</span>
                <br>
                <span style="float: right;">Gestiune: '. $inventory->name .'</span>
                <br>
                <br>
                <br>
        ';

        $table = '<table>
            <tr nobr="true">
            <th style="font-weight: bold; text-align: center;">Denumire Produs</th>
            <th style="font-weight: bold; text-align: center;">UM</th>
            <th style="font-weight: bold; text-align: center;">Cantitate</th>
            <th style="font-weight: bold; text-align: center;">Pret</th>
            <th style="font-weight: bold; text-align: center;">TVA</th>
            <th style="font-weight: bold; text-align: center;">Pret TVA</th>
            <th style="font-weight: bold; text-align: center;">Valoare</th>
            <th style="font-weight: bold; text-align: center;">Lot</th>
            <th style="font-weight: bold; text-align: center;">Data expirare</th>
            </tr>';

        foreach($categories as $category) {
            $html .= '<span style="font-weight: bold;">'. $category->name .'</span><br><br>';
            $html .= $table;
            foreach($items as $item) {
                if($item['category_id'] == $category->id) {
                    $html .= '<tr nobr="true">
                                    <td style="text-align: center;">'. $item['item_name'] .'</td>
                                    <td style="text-align: center;">'. $item['um'] .'</td>
                                    <td style="text-align: center;">'. $item['current_quantity'] .'</td>
                                    <td style="text-align: center;">'. $item['price'] .'</td>
                                    <td style="text-align: center;">'. $item['tva'] .'</td>
                                    <td style="text-align: center;">'. $item['tva_price'] .'</td>
                                    <td style="text-align: center;">'. $item['current_quantity'] * $item['tva_price'] .'</td>
                                    <td style="text-align: center;">'. $item['lot'] .'</td>
                                    <td style="text-align: center;">'. date("d-m-Y", strtotime($item['exp_date'])) .'</td>
                                </tr>';
                }
            }
            $html .= '</table><br><br>';
        }

        PDF::setFooterCallback(function($pdf) {

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 10);
            // Page number
            $pdf->Cell(0, 10, 'Pagina '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    
    });
            
            PDF::SetTitle('Expirare in 6 luni');
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
