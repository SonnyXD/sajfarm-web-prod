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

        $transfer_items = TransferItem::with('item_stock')
        ->get();

        dd($transfer_items->first());
        
        $institution = Institution::all();

        $filename = 'fisa produs '. $med_name .' '. $new_from_date .' '. $new_until_date .'.pdf';

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
        <h2 style="font-weight:bold; text-align: center;">FISA PRODUS</h2>
        <br>
        <span style="float: right;">Produs: '. $med_name .'</span>
        <br>
        <span style="float: right;">Perioada: '. $new_from_date .' - '. $new_until_date .'</span>
        <br>
        <br>
        <br>';

        $html .= '
        <table>
        <tr>
          <th style="font-weight: bold; text-align: center;">Cod CIM</th>
          <th style="font-weight: bold; text-align: center;">Cod Produs</th>
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
