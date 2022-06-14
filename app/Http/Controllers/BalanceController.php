<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Inventory;
use \App\Models\Category;
use \App\Models\Institution;
use \App\Models\Invoice;
use \App\Models\InvoiceItem;
use \App\Models\Transfer;
use \App\Models\TransferItem;
use \App\Models\Item;
use \App\Models\ItemStock;
use \App\Models\Consumption;
use \App\Models\ConsumptionItem;
use Session;
use PDF;
use Auth;


class BalanceController extends Controller
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
            'inventory-select' => 'required',
            'category-select' => 'required',
            'from-date' => 'required',
            'until-date' => 'required'
        ));

        $user = Auth::user();

        $six_months = date('Y-m-d', strtotime('+6 month'));

        $inventory_id = $request->input('inventory-select');
        $inventory_name = Inventory::where('id', '=', $inventory_id)->first()->name;

        $category_id = $request->input('category-select');
        $category_name = Category::where('id', '=', $category_id)->first()->name;

        $old_from_date = $request->input('from-date');
        $new_from_date = date("d-m-Y", strtotime($old_from_date)); 
        $old_until_date = $request->input('until-date');
        $new_until_date = date("d-m-Y", strtotime($old_until_date));

        $institution = Institution::all();

        $products = Item::where('category_id', $category_id)->get();

        $subset = $products->map(function ($product) {
            return collect($product->toArray())
                ->only(['id'])
                ->all();
        });

        // $entries = InvoiceItem::leftjoin('item_stocks', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
        // ->leftjoin('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
        // ->leftjoin('items', 'invoice_items.item_id', '=', 'items.id')
        // ->leftjoin('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
        // ->leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
        // ->leftjoin('transfer_items', 'transfer_items.item_stock_id', '=', 'item_stocks.id')
        // ->leftjoin('transfers', 'transfers.id', '=', 'transfer_items.transfer_id')
        // ->leftjoin('consumption_items', 'consumption_items.item_stock_id', '=', 'item_stocks.id')
        // ->leftjoin('consumptions', 'consumptions.id', '=', 'consumption_items.consumption_id')
        // ->where('inventories.id', '=', $inventory_id)
        // ->whereIn('invoice_items.item_id', $subset)
        // ->whereBetween('invoices.document_date', [$old_from_date, $old_until_date])
        // ->select('invoices.id as invoice_id', 'invoice_items.*',
        // 'item_stocks.quantity as remaining_quantity', 'item_stocks.id as item_stock_id',
        // ItemStock::raw('SUM(item_stocks.quantity) as used_quantity'), 'items.name as item_name',
        // 'measure_units.name as um', 'invoices.document_date as document_date',
        // 'transfers.id as transfer_id', 'transfer_items.quantity as transfer_quantity',
        // 'consumptions.id as consumption_id', 'consumption_items.quantity as consumption_quantity')
        // ->groupBy('item_stocks.inventory_id')
        // ->groupBy('invoice_items.id')
        // ->get();

        // $entries = ItemStock::leftjoin('invoice_items', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
        // ->leftjoin('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
        // ->leftjoin('items', 'invoice_items.item_id', '=', 'items.id')
        // ->leftjoin('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
        // ->leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
        // ->leftjoin('transfer_items', 'transfer_items.item_stock_id', '=', 'item_stocks.id')
        // ->leftjoin('transfers', 'transfers.id', '=', 'transfer_items.transfer_id')
        // ->leftjoin('consumption_items', 'consumption_items.item_stock_id', '=', 'item_stocks.id')
        // ->leftjoin('consumptions', 'consumptions.id', '=', 'consumption_items.consumption_id')
        // ->where('inventories.id', '=', $inventory_id)
        // ->whereIn('invoice_items.item_id', $subset)
        // ->whereBetween('transfers.document_date', [$old_from_date, $old_until_date])
        // ->whereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
        // ->whereBetween('invoices.document_date', [$old_from_date, $old_until_date])
        // ->select('invoices.id as invoice_id', 'invoice_items.*',
        // 'item_stocks.quantity as remaining_quantity', 'item_stocks.id as item_stock_id',
        // 'items.name as item_name', 'item_stocks.quantity as item_stocks_quantity',
        // 'measure_units.name as um', 'invoices.document_date as document_date',
        // 'transfers.id as transfer_id', 'transfer_items.quantity as transfer_quantity',
        // 'consumptions.id as consumption_id', 'consumption_items.quantity as consumption_quantity',
        // TransferItem::raw('SUM(transfer_items.quantity) as used_quantity_transfer'),
        // ConsumptionItem::raw('SUM(consumption_items.quantity) as used_quantity_consumption'))
        // ->groupby('item_stocks.id')
        // ->get();

        $entries = "";

        if($inventory_id == 1) {
            $entries = ItemStock::leftjoin('invoice_items', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
            ->leftjoin('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->leftjoin('items', 'invoice_items.item_id', '=', 'items.id')
            ->leftjoin('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
            ->leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
            ->leftjoin('transfer_items', 'transfer_items.item_stock_id', '=', 'item_stocks.id')
            ->leftjoin('transfers', 'transfers.id', '=', 'transfer_items.transfer_id')
            ->leftjoin('consumption_items', 'consumption_items.item_stock_id', '=', 'item_stocks.id')
            ->leftjoin('consumptions', 'consumptions.id', '=', 'consumption_items.consumption_id')
            ->where('inventories.id', '=', $inventory_id)
            ->whereIn('invoice_items.item_id', $subset)
            // ->where(fn ($q) => $q
            //     ->whereBetween('invoices.document_date', [$old_from_date, $old_until_date])
            //     ->orWhereBetween('transfers.document_date', [$old_from_date, $old_until_date])
            //     ->orWhereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
            // )
            // ->whereBetween('transfers.document_date', [$old_from_date, $old_until_date])
            // ->whereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
            ->whereBetween('invoices.document_date', [$old_from_date, $old_until_date])
            ->select('invoices.id as invoice_id', 'invoice_items.*',
            'item_stocks.quantity as remaining_quantity', 'item_stocks.id as item_stock_id',
            'items.name as item_name', 'item_stocks.quantity as item_stocks_quantity',
            'measure_units.name as um', 'invoices.document_date as document_date',
            'transfers.id as transfer_id', 'transfer_items.quantity as transfer_quantity',
            'consumptions.id as consumption_id', 'consumption_items.quantity as consumption_quantity',
            'transfers.document_date as transfer_date',
            TransferItem::raw('SUM(transfer_items.quantity) as used_quantity_transfer'),
            ConsumptionItem::raw('SUM(consumption_items.quantity) as used_quantity_consumption'),
            'consumptions.document_date as consumption_date')
            ->groupby('transfer_items.transfer_id')
            ->groupby('transfer_items.item_stock_id')
            ->get();
        } else {
            $entries = ItemStock::leftjoin('invoice_items', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
            ->leftjoin('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->leftjoin('items', 'invoice_items.item_id', '=', 'items.id')
            ->leftjoin('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
            ->leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
            ->leftjoin('transfer_items', 'transfer_items.item_stock_id', '=', 'item_stocks.id')
            ->leftjoin('transfers', 'transfers.id', '=', 'transfer_items.transfer_id')
            ->leftjoin('consumption_items', 'consumption_items.item_stock_id', '=', 'item_stocks.id')
            ->leftjoin('consumptions', 'consumptions.id', '=', 'consumption_items.consumption_id')
            ->where('inventories.id', '=', $inventory_id)
            ->whereIn('transfer_items.item_id', $subset)
            ->where(fn ($q) => $q
                ->whereBetween('transfers.document_date', [$old_from_date, $old_until_date])
                ->orWhereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
            )
            // ->whereBetween('transfers.document_date', [$old_from_date, $old_until_date])
            // ->whereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
            //->whereBetween('transfers.document_date', [$old_from_date, $old_until_date])
            ->select('invoices.id as invoice_id', 'invoice_items.*',
            'item_stocks.quantity as remaining_quantity', 'item_stocks.id as item_stock_id',
            'items.name as item_name', 'item_stocks.quantity as item_stocks_quantity',
            'measure_units.name as um', 'invoices.document_date as document_date',
            'transfers.id as transfer_id', 'transfer_items.quantity as transfer_quantity',
            'consumptions.id as consumption_id', 'consumption_items.quantity as consumption_quantity',
            ConsumptionItem::raw('SUM(consumption_items.quantity) as used_quantity_consumption'))
            ->groupby('consumption_items.item_stock_id')
            ->get();
        }

        //dd($entries[0]->transfer_date);

        $entries_array = [];

        foreach($entries as $entry) {
            if(($entry->transfer_date < $old_from_date || $entry->transfer_date > $old_until_date) && ($entry->consumption_date < $old_from_date || $entry->consumption_date > $old_until_date)) {
                
            } else {
                $entries_array[] = $entry;
            }
        }

        //dd($entries_array);

        //$filename = 'balanta '. $inventory_name .' '. $new_from_date .' '. $new_until_date .'.pdf';

        $html = "<html>
                <head>
                <style>
                td, th {border: 1px solid black;}
                </style>
                </head>";
        
        $html .= ' <span style="font-weight: bold; float: left;">'. $institution[0]->name .'</span>
        <br>
        <span style="float: left;">Utilizator: '. $user->name .'</span>
        <h2 style="font-weight:bold; text-align: center;">BALANTA ANALITICA '. strtoupper($category_name) .'</h2>
        <br>
        <span style="float: right;">Gestiune: '. $inventory_name .'</span>
        <br>
        <span style="float: right;">Subgestiune: '. $category_name .'</span>
        <br>
        <span style="float: right;">Perioada: '. $new_from_date .' - '. $new_until_date .'</span>
        <br>
        <br>
        <br>';

        $html .= '
        <table>
        <tr>
          <th style="font-weight: bold; text-align: center;">Denumire Produs</th>
          <th style="font-weight: bold; text-align: center;">UM</th>
          <th style="font-weight: bold; text-align: center;">Data Achizitiei</th>
          <th style="font-weight: bold; text-align: center;">Pret Unitar</th>
          <th style="font-weight: bold; text-align: center;">Stoc Initial</th>
          <th style="font-weight: bold; text-align: center;">Intrari</th>
          <th style="font-weight: bold; text-align: center;">Iesiri</th>
          <th style="font-weight: bold; text-align: center;">Stoc Final</th>
          <th style="font-weight: bold; text-align: center;">Sold</th>
        </tr>
        ';

        foreach($entries_array as $entry) {
            $html .= '<tr nobr="true">';
            $html .= '<td style="text-align: center;">'. $entry['item_name'] .'</td>';
            $html .= '<td style="text-align: center;">'. $entry['um'] .'</td>';
            $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($entry['document_date'])) .'</td>';
            $html .= '<td style="text-align: center;">'. $entry['price'] .'</td>';
            $html .= '<td style="text-align: center;">'. $entry['item_stocks_quantity'] + $entry['used_quantity_transfer'] + $entry['used_quantity_consumption'].'</td>';
            $html .= '<td style="text-align: center;">0</td>';
            $html .= '<td style="text-align: center;">0</td>';
            $html .= '<td style="text-align: center;">0</td>';
            $html .= '<td style="text-align: center;">0</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $html .= '</html>';

        PDF::setFooterCallback(function($pdf) {

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 10);
            // Page number
            $pdf->Cell(0, 10, 'Pagina '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    
    });

        PDF::SetTitle('Balanta Analitica');
        PDF::AddPage('L', 'A4');
        PDF::writeHTML($html, true, false, true, false, '');

        //PDF::Output(public_path($filename), 'D');

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
