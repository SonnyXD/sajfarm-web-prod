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
use \App\Models\ItemStock;
use \App\Models\Inventory;
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
        ->join('item_stocks', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
        ->leftjoin('providers', 'invoices.provider_id', '=', 'providers.id')
        ->where('invoice_items.item_id', '=', $med_id)
        ->whereBetween('invoices.insertion_date', [$old_from_date, $old_until_date])
        ->select('invoices.id as invoice_id',  'invoice_items.*', 'providers.name as provider_name',
        'invoices.insertion_date as insertion_date',
        ItemStock::raw('SUM(item_stocks.quantity) as remaining_quantity'))
        //->groupBy('item_stocks.id')
        ->groupBy('invoice_items.invoice_id')
        ->groupBy('item_stocks.invoice_item_id')
        ->get();

       //dd($invoice_items);

        $transfer_items = TransferItem::join('item_stocks', 'transfer_items.item_stock_id', '=', 'item_stocks.id')
        ->join('invoice_items', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
        ->join('transfers', 'transfer_items.transfer_id', '=', 'transfers.id')
        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
        ->leftjoin('providers', 'invoices.provider_id', '=', 'providers.id')
        ->join('inventories', 'transfers.from_inventory_id', '=', 'inventories.id')
        ->join('inventories as inv', 'transfers.to_inventory_id', '=', 'inv.id')
        ->where('transfer_items.item_id', '=', $med_id)
        ->whereBetween('transfers.document_date', [$old_from_date, $old_until_date])
        ->select('transfers.id as transfer_id', 'invoice_items.*',
        'item_stocks.quantity as remaining_quantity', 'item_stocks.id as item_stock_id',
        'transfer_items.quantity as used_quantity', 'inventories.name as from_inventory',
        'inv.name as to_inventory', TransferItem::raw('SUM(transfer_items.quantity) as used_quantity'),
        'transfers.document_date', 'providers.name as provider_name')
        ->groupBy('transfer_items.transfer_id')
        //->groupBy('transfer_items.item_stock_id')
        //->groupBy('transfer_items.id')
        ->get();

        $returning_items = ReturningItem::join('item_stocks', 'returning_items.item_stock_id', '=', 'item_stocks.id')
        ->join('invoice_items', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
        ->join('returnings', 'returning_items.returning_id', '=', 'returnings.id')
        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
        ->leftjoin('providers', 'invoices.provider_id', '=', 'providers.id')
        ->join('inventories', 'returnings.inventory_id', '=', 'inventories.id')
        ->leftjoin('ambulances', 'returning_items.ambulance_id', '=', 'ambulances.id')
        ->where('returning_items.item_id', '=', $med_id)
        ->whereBetween('returnings.document_date', [$old_from_date, $old_until_date])
        ->select('returnings.id as returning_id', 'invoice_items.*',
        'item_stocks.quantity as remaining_quantity', 'item_stocks.id as item_stock_id',
        'returning_items.quantity as used_quantity', 'ambulances.license_plate as ambulance_license_plate',
        'inventories.name as from_inventory', 'returnings.document_date as document_date', 'providers.name as provider_name')
        ->get();

        //dd($transfer_items);

        $consumption_items = ConsumptionItem::join('item_stocks', 'consumption_items.item_stock_id', '=', 'item_stocks.id')
        ->join('invoice_items', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
        ->join('consumptions', 'consumption_items.consumption_id', '=', 'consumptions.id')
        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
        ->leftjoin('providers', 'invoices.provider_id', '=', 'providers.id')
        ->join('items', 'consumption_items.item_id', '=', 'items.id')
        ->join('inventories', 'consumptions.inventory_id', '=', 'inventories.id')
        ->leftjoin('ambulances', 'consumptions.ambulance_id', '=', 'ambulances.id')
        ->leftjoin('medics', 'consumptions.medic_id', '=', 'medics.id')
        ->where('consumption_items.item_id', '=', $med_id)
        ->whereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
        ->select('consumptions.id as consumption_id', 'invoice_items.*',
        'item_stocks.quantity as remaining_quantity', 'item_stocks.id as item_stock_id',
        ConsumptionItem::raw('SUM(consumption_items.quantity) as used_quantity'),
        'inventories.name as from_inventory', 'ambulances.license_plate as license_plate',
        'medics.name as medic_name', 'consumptions.document_date as document_date',
        'providers.name as provider_name')
        ->groupBy('consumption_items.consumption_id')
        //->groupBy('consumption_items.item_stock_id')
        ->get();

       

        if($invoice_items->isEmpty() && $returning_items->isEmpty() && $consumption_items->isEmpty() && $transfer_items->isEmpty()) {
            return redirect('/documente/fisa-produs')
            ->with('error', 'Nu exista istoric pentru perioada selectata!');
        }

        //dd($invoice_items);
        
        $institution = Institution::all();

        $filename = 'fisa produs '. $med_name .' '. $new_from_date .' '. $new_until_date .'.pdf';

        $html = "";

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
        <br>';

        $inventories = Inventory::get();

        foreach($inventories as $inventory) {
            $remaining_quantity = ItemStock::leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
            ->leftjoin('invoice_items', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
            ->where('item_stocks.item_id', '=', $med_id)
            ->where('item_stocks.inventory_id', '=', $inventory->id)
            ->select(ItemStock::raw('SUM(item_stocks.quantity) as remaining_quantity'),
            'inventories.name as inventory_name', 'invoice_items.tva_price as tva_price')
            ->groupBy('item_stocks.inventory_id')
            ->first();

            if($remaining_quantity == null) {
                $html .= '<span style="float: right;">Stoc Curent / Valoare '. $inventory->name .': 0 / 0</span>
                <br>';
                continue;
            }
            
            $html .= '<span style="float: right;">Stoc Curent / Valoare '. $remaining_quantity->inventory_name .': '. $remaining_quantity->remaining_quantity .' / '. $remaining_quantity->remaining_quantity * $remaining_quantity->tva_price .'</span>
            <br>';
        }

        $html .= '<br>
        <br>';

        $html .= '
        <table>
        <tr>
          <th style="font-weight: bold; text-align: center;">Data</th>
          <th style="font-weight: bold; text-align: center;">Tip Document</th>
          <th style="font-weight: bold; text-align: center;">Detalii</th>
          <th style="font-weight: bold; text-align: center;">Intrare</th>
          <th style="font-weight: bold; text-align: center;">Iesire</th>
          <th style="font-weight: bold; text-align: center;">Stoc Curent</th>
          <th style="font-weight: bold; text-align: center;">Lot</th>
          <th style="font-weight: bold; text-align: center;">Furnizor</th>
          <th style="font-weight: bold; text-align: center;">Data exp.</th>
          <th style="font-weight: bold; text-align: center;">Pret</th>
          <th style="font-weight: bold; text-align: center;">Pret TVA</th>
          <th style="font-weight: bold; text-align: center;">Valoare</th>
        </tr>
        ';

        //dd($invoice_items);

        $invoice_value = 0;
        $transfer_value = 0;
        $consumption_value = 0;
        $returning_value = 0;

        foreach($invoice_items as $item) {
            $html .= '<tr nobr="true">';
            $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($item['insertion_date'])) .'</td>';
            $html .= '<td style="text-align: center;">Intrare factura</td>';
            $html .= '<td style="text-align: center;">NIR '. $item['invoice_id'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['quantity'] .'</td>';
            $html .= '<td style="text-align: center;">0</td>';
            $html .= '<td style="text-align: center;">'. $item['remaining_quantity'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['lot'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['provider_name'] .'</td>';
            $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($item['exp_date'])) .'</td>';
            $html .= '<td style="text-align: center;">'. $item['price'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['tva_price'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['tva_price'] * $item['quantity'] .'</td>';
            $html .= '</tr>';
            $invoice_value += ($item['tva_price'] * $item['quantity']);
        }

        foreach($transfer_items as $item) {
            $html .= '<tr nobr="true">';
            $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($item['document_date'])) .'</td>';
            $html .= '<td style="text-align: center;">Transfer '. $item['transfer_id'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['from_inventory'] .' -> '. $item['to_inventory'] .'</td>';
            $html .= '<td style="text-align: center;">0</td>';
            $html .= '<td style="text-align: center;">'. $item['used_quantity'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['remaining_quantity'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['lot'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['provider_name'] .'</td>';
            $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($item['exp_date'])) .'</td>';
            $html .= '<td style="text-align: center;">'. $item['price'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['tva_price'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['tva_price'] * $item['used_quantity'] .'</td>';
            $html .= '</tr>';
            $transfer_value += ($item['tva_price'] * $item['used_quantity']);
        }

        foreach($consumption_items as $item) {
            $details = "";
            if($item['medic_name'] != null) {
                $details = $item['from_inventory'] .' - '. $item['license_plate'] . ' - ' . $item['medic_name'];
            }
            else {
                $details = $item['from_inventory'] .' - '. $item['license_plate'];
            }
            $html .= '<tr nobr="true">';
            $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($item['document_date'])) .'</td>';
            $html .= '<td style="text-align: center;">Consum '. $item['consumption_id'] .'</td>';
            $html .= '<td style="text-align: center;">'. $details .'</td>';
            $html .= '<td style="text-align: center;">0</td>';
            $html .= '<td style="text-align: center;">'. $item['used_quantity'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['remaining_quantity'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['lot'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['provider_name'] .'</td>';
            $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($item['exp_date'])) .'</td>';
            $html .= '<td style="text-align: center;">'. $item['price'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['tva_price'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['tva_price'] * $item['used_quantity'] .'</td>';
            $html .= '</tr>';
            $consumption_value += ($item['tva_price'] * $item['used_quantity']);
        }

        foreach($returning_items as $item) {
            $details = "";
            if($item['ambulance_license_plate'] != null) {
                $details = $item['from_inventory'] .' - '. $item['ambulance_license_plate'];
            }
            else {
                $details = $item['from_inventory'];
            }
            $html .= '<tr nobr="true">';
            $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($item['document_date'])) .'</td>';
            $html .= '<td style="text-align: center;">Retur '. $item['returning_id'] .'</td>';
            $html .= '<td style="text-align: center;">'. $details .'</td>';
            $html .= '<td style="text-align: center;">0</td>';
            $html .= '<td style="text-align: center;">'. $item['used_quantity'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['remaining_quantity'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['lot'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['provider_name'] .'</td>';
            $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($item['exp_date'])) .'</td>';
            $html .= '<td style="text-align: center;">'. $item['price'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['tva_price'] .'</td>';
            $html .= '<td style="text-align: center;">'. $item['tva_price'] * $item['used_quantity'] .'</td>';
            $html .= '</tr>';
            $returning_value += ($item['tva_price'] * $item['used_quantity']);
        }

        $html .= '</table>';

        $html .= '<br>';

        $html .= '<br>';

        $html .= '<span>Total valoare intrata: '. $invoice_value .'</span>';

        $html .= '<br>';

        $html .= '<span>Total valoare transferata: '. $transfer_value .'</span>';

        $html .= '<br>';

        $html .= '<span>Total valoare consumata: '. $consumption_value .'</span>';

        $html .= '<br>';

        $html .= '<span>Total valoare returnata: '. $returning_value .'</span>';

        $html .= '</html>';

        PDF::setFooterCallback(function($pdf) {

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 10);
            // Page number
            $pdf->Cell(0, 10, 'Pagina '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    
    });

        PDF::SetTitle('Fisa Produs');
        PDF::AddPage('L', 'A4');
        PDF::writeHTML($html, true, false, true, false, '');
        //PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //PDF::Output(public_path($filename), 'D');

        PDF::Output('name.pdf', 'I');

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
