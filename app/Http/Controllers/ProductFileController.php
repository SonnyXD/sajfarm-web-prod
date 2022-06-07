<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Institution;
use \App\Models\Item;
use \App\Models\Invoice;
use \App\Models\InvoiceItem;
use \App\Models\Transfer;
use \App\Models\TransferItem;
use \App\Models\Consumption;
use \App\Models\ConsumptionItem;
use \App\Models\Returning;
use \App\Models\ReturningItem;
use Session;
use PDF;
use Auth;

class ProductFileController extends Controller
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
            'meds' => 'required',
            'from-date' => 'required',
            'until-date' => 'required'
        ));

        $user = Auth::user();

        $med_id = $request->input('meds');
        $med_name = Item::where('id', '=', $med_id)->first()->name;

        $old_from_date = $request->input('from-date');
        $new_from_date = date("d-m-Y", strtotime($old_from_date)); 
        $old_until_date = $request->input('until-date');
        $new_until_date = date("d-m-Y", strtotime($old_until_date));

        $invoice_items = InvoiceItem::with('invoice')
        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
        ->join('providers', 'invoices.provider_id', '=', 'providers.id')
        ->where('item_id', '=', $med_id)
        ->whereBetween('invoices.insertion_date', [$old_from_date, $old_until_date])
        ->select('invoices.id as invoice_id',  'invoice_items.*', 'providers.name as provider_name')
        ->get();

        $transfer_items = TransferItem::join('item_stocks', 'transfer_items.item_stock_id', '=', 'item_stocks.id')
        ->join('invoice_items', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
        ->join('transfers', 'transfer_items.transfer_id', '=', 'transfers.id')
        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
        ->join('inventories', 'transfers.from_inventory_id', '=', 'inventories.id')
        ->join('inventories as inv', 'transfers.to_inventory_id', '=', 'inv.id')
        ->where('transfer_items.item_id', '=', $med_id)
        ->whereBetween('transfers.document_date', [$old_from_date, $old_until_date])
        ->select('transfers.id as transfer_id', 'invoice_items.*',
        'item_stocks.quantity as remaining_quantity', 'item_stocks.id as item_stock_id',
        'transfer_items.quantity as used_quantity', 'inventories.name as from_inventory',
        'inv.name as to_inventory')
        ->get();

        $returning_items = ReturningItem::join('item_stocks', 'returning_items.item_stock_id', '=', 'item_stocks.id')
        ->join('invoice_items', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
        ->join('returnings', 'returning_items.returning_id', '=', 'returnings.id')
        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
        ->join('inventories', 'returnings.inventory_id', '=', 'inventories.id')
        ->leftjoin('ambulances', 'returning_items.ambulance_id', '=', 'ambulances.id')
        ->where('returning_items.item_id', '=', $med_id)
        ->whereBetween('returnings.document_date', [$old_from_date, $old_until_date])
        ->select('returnings.id as returning_id', 'invoice_items.*',
        'item_stocks.quantity as remaining_quantity', 'item_stocks.id as item_stock_id',
        'returning_items.quantity as used_quantity', 'ambulances.license_plate as ambulance_license_plate',
        'inventories.name as from_inventory')
        ->get();

        $consumption_items = ConsumptionItem::join('item_stocks', 'consumption_items.item_stock_id', '=', 'item_stocks.id')
        ->join('invoice_items', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
        ->join('consumptions', 'consumption_items.consumption_id', '=', 'consumptions.id')
        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
        ->join('items', 'consumption_items.item_id', '=', 'items.id')
        ->join('inventories', 'consumptions.inventory_id', '=', 'inventories.id')
        ->leftjoin('ambulances', 'consumptions.ambulance_id', '=', 'ambulances.id')
        ->leftjoin('medics', 'consumptions.medic_id', '=', 'medics.id')
        ->where('consumption_items.item_id', '=', $med_id)
        ->whereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
        ->select('consumptions.id as consumption_id', 'invoice_items.*',
        'item_stocks.quantity as remaining_quantity', 'item_stocks.id as item_stock_id',
        ConsumptionItem::raw('SUM(consumption_items.quantity) as used_quantity'),
        'inventories.name as from_inventory')
        ->groupBy('consumptions.id')
        ->groupBy('item_stocks.id')
        ->get();

        //dd($consumption_items);
        
        $institution = Institution::all();

        $filename = 'fisa produs '. $med_name .' '. $new_from_date .' '. $new_until_date .'.pdf';

        $html = "<html>
                <head>
                <style>
                td, th {border: 1px solid black;}
                </style>
                </head>";
        
        $html .= ' <span style="font-weight: bold; float: left;">'. $institution[0]->name .'</span>
        <br>
        <span style="float: left;">Utilizator: '. $user->name .'</span>
        <h2 style="font-weight:bold; text-align: center;">FISA PRODUS</h2>
        <br>
        <span style="float: right;">Produs: '. $med_name .'</span>
        <br>
        <span style="float: right;">Perioada: '. $new_from_date .' - '. $new_until_date .'</span>
        <br>
        <br>
        <br>';

        $html .= '
        <table style="border: 1px solid black;">
        <tr style="border: 1px solid black;">
          <th style="font-weight: bold; text-align: center; border: 1px solid black;">Data</th>
          <th style="font-weight: bold; text-align: center;">Tip Document</th>
          <th style="font-weight: bold; text-align: center;">Denumire Produs</th>
          <th style="font-weight: bold; text-align: center;">Detalii</th>
          <th style="font-weight: bold; text-align: center;">Intrare</th>
          <th style="font-weight: bold; text-align: center;">Iesire</th>
          <th style="font-weight: bold; text-align: center;">Stoc Curent</th>
          <th style="font-weight: bold; text-align: center;">Lot</th>
          <th style="font-weight: bold; text-align: center;">Furnizor</th>
          <th style="font-weight: bold; text-align: center;">Data exp.</th>
          <th style="font-weight: bold; text-align: center;">Pret</th>
        </tr>
        ';

        PDF::SetTitle('Fisa Produs');
        PDF::AddPage('L', 'A4');
        PDF::writeHTML($html, true, false, true, false, '');

        PDF::Output(public_path($filename), 'D');

        //return response()->download($filename);
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
