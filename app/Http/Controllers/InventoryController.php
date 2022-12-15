<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use \App\Models\Institution;
use \App\Models\ItemStock;
use \App\Models\Item;
use \App\Models\Category;
use Session;
use PDF;
use Auth;
use Carbon\Carbon;

class InventoryController extends Controller
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

        $now = date('d-m-Y');

        $inventory_id = $request->input('inventory-select');

        $inventory = Inventory::where('id', $inventory_id)->first();

        $categories = Category::all();

        $items = ItemStock::leftjoin('invoice_items', 'invoice_items.id', '=', 'item_stocks.invoice_item_id')
        ->leftjoin('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
        ->leftjoin('measure_units', 'measure_units.id', '=', 'invoice_items.measure_unit_id')
        ->leftjoin('items', 'items.id', '=', 'invoice_items.item_id')
        ->where('item_stocks.inventory_id', $inventory->id)
        ->where('item_stocks.quantity', '!=', 0)
        ->select(ItemStock::raw('SUM(item_stocks.quantity) as current_quantity'), 'items.name as item_name', 'measure_units.name as um',
        'invoice_items.lot as lot', 'invoice_items.price as price', 'invoice_items.tva as tva',
        'invoice_items.tva_price as tva_price', 'invoice_items.exp_date as exp_date', 'items.category_id as category_id')
        // ->groupBy('item_stocks.item_id')
        ->groupBy('item_stocks.invoice_item_id')
        ->groupBy('invoice_items.measure_unit_id')
        ->get();

        // $items = collect($items)->sortBy('item_name');
        //$items = collect($items);

        // $test = [];

        // foreach($items as $item) {
        //     if($item->category_id == 4) {
        //         $test[] = $item;
        //     }
        // }

        // dd($test);

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
                <h2 style="font-weight:bold; text-align: center;">INVENTAR</h2>
                <br>
                <span style="float: right;">Data: '. $now .'</span>
                <br>
                <span style="float: right;">Gestiune: '. $inventory->name .'</span>
                <br>
                <br>
                <br>
        ';

        $table = '<table>
        <thead>
            <tr nobr="true">
            <th style="font-weight: bold; text-align: center;">Denumire Produs</th>
            <th style="font-weight: bold; text-align: center;">UM</th>
            <th style="font-weight: bold; text-align: center;" colspan="4">Cantitati</th>
            </tr>
            <tr nobr="true">
            <th style="font-weight: bold; text-align: center; border-top:none;"></th>
            <th style="font-weight: bold; text-align: center; border-top:none;"></th>
            <th style="font-weight: bold; text-align: center;" colspan="2">Stocuri</th>
            <th style="font-weight: bold; text-align: center;" colspan="2">Diferente</th>
            </tr>
            <tr nobr="true">
            <th style="font-weight: bold; text-align: center;"></th>
            <th style="font-weight: bold; text-align: center;"></th>
            <th style="font-weight: bold; text-align: center;">Scriptice</th>
            <th style="font-weight: bold; text-align: center;">Faptice</th>
            <th style="font-weight: bold; text-align: center;">Plus</th>
            <th style="font-weight: bold; text-align: center;">Minus</th>
            </tr>
            </thead>';

            $total_values = [];

            foreach($categories as $category) {
                $html .= '<span style="font-weight: bold;">'. $category->name .'</span><br><br>';
                $html .= $table;
                $total = 0;
                foreach($items as $item) {
                    if($item['category_id'] == $category->id) {
                        $total += ($item['tva_price'] * $item['current_quantity']);
                        $html .= '<tr nobr="true">
                                        <td style="text-align: center;">'. $item['item_name'] .'</td>
                                        <td style="text-align: center;">'. $item['um'] .'</td>
                                        <td style="text-align: center;">'. $item['current_quantity'] .'</td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                    </tr>';
                    }
                }
                $total_values[] = $total;
                $html .= '</table><br><br><br>';
            }

            $date = date("Y-m-d");

            $date = Carbon::createFromDate($date);

            $startOfYear = $date->copy()->startOfYear();
            $endOfYear = $date->copy()->endOfYear();

            $items = ItemStock::leftjoin('invoice_items', 'invoice_items.id', '=', 'item_stocks.invoice_item_id')
            ->leftjoin('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->leftjoin('measure_units', 'measure_units.id', '=', 'invoice_items.measure_unit_id')
            ->leftjoin('items', 'items.id', '=', 'invoice_items.item_id')
            ->where('item_stocks.inventory_id', $inventory->id)
            //->where('item_stocks.quantity', '=', 0)
            ->whereBetween('invoices.document_date', [$startOfYear, $endOfYear])
            ->select(ItemStock::raw('SUM(item_stocks.quantity) as current_quantity'), 'items.name as item_name', 'measure_units.name as um',
            'invoice_items.lot as lot', 'invoice_items.price as price', 'invoice_items.tva as tva',
            'invoice_items.tva_price as tva_price', 'invoice_items.exp_date as exp_date', 'items.category_id as category_id', 'item_stocks.id as is_id')
            ->groupBy('item_stocks.item_id')
            //->groupBy('invoice_items.measure_unit_id')
            ->get();

            $items = collect($items)->sortBy('item_name');

            $html .= '<h4>Produse cu cantitate 0:</h4><br><br>';

            foreach($categories as $category) {
                $html .= '<span style="font-weight: bold;">'. $category->name .'</span><br><br>';
                $html .= $table;
                foreach($items as $item) {
                    if($item['category_id'] == $category->id && $item['current_quantity'] == 0) {
                        $html .= '<tr nobr="true">
                                        <td style="text-align: center;">'. $item['item_name'] .'</td>
                                        <td style="text-align: center;">'. $item['um'] .'</td>
                                        <td style="text-align: center;">'. $item['current_quantity'] .'</td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                    </tr>';
                    }
                }
                $html .= '</table><br><br><br>';
            }

            foreach($categories as $category) {
                $html .= '<span>Total Valoare '. $category->name .': '. $total_values[$category->id-1] .'</span>';
                $html .= '<br>';
            }

            $html .= '<br>';

            $html .= '<table>
            <tr nobr="true">
            <td style="text-align: center; font-weight: bold;" colspan="2">Comisia de inventariere</td>
            <td style="text-align: center; font-weight: bold;" colspan="2">Gestionari</td>
            </tr>
            <tr nobr="true">
            <td style="text-align: center; font-weight: bold;">Nume</td>
            <td style="text-align: center; font-weight: bold;">Semnatura</td>
            <td style="text-align: center; font-weight: bold;">Nume</td>
            <td style="text-align: center; font-weight: bold;">Semnatura</td>
            </tr>
            <tr nobr="true">
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;">Farm. Sef '. $institution[0]->pharmacy_manager .'</td>
            <td style="text-align: center;"></td>
            </tr>
            <tr nobr="true">
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;">As. Farm '. $institution[0]->assistent .'</td>
            <td style="text-align: center;"></td>
            </tr>
            <tr nobr="true">
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            </tr>';

           $html .= '</table>';
    
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
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Inventory $inventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventory $inventory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        //
    }
}
