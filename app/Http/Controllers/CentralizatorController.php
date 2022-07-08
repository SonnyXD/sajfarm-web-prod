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

class CentralizatorController extends Controller
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
            'type-select' => 'required',
            'from-date' => 'required',
            'until-date' => 'required'
        ));

        $user = Auth::user();

        $inventory_id = $request->input('inventory-select');
        $inventory_name = Inventory::where('id', '=', $inventory_id)->first()->name;

        $type_id = $request->input('type-select');
        $type_name = "";

        $categories = Category::all();

        $old_from_date = $request->input('from-date');
        $new_from_date = date("d-m-Y", strtotime($old_from_date)); 
        $old_until_date = $request->input('until-date');
        $new_until_date = date("d-m-Y", strtotime($old_until_date));

        $documents = "";
        $document_type = "";

        if($type_id == 1) {
            $type_name = "TRANSFERURI";
            $document_type = "Transfer";
            $documents = Transfer::whereHas('transfer_item')
            ->with(['transfer_item', 'transfer_item.item_detail', 'transfer_item.item_stock_detail', 'transfer_item.item_stock_detail.invoice_item', 'inventory_to'])
            ->where('from_inventory_id', '=', $inventory_id)
            ->whereBetween('document_date', [$old_from_date, $old_until_date])
            ->get();

        } else if($type_id == 2) {
            $type_name = "CONSUMURI";
            $document_type = "Consum";
            // $documents = Consumption::with('consumption_item', 'consumption_item.item')
            // ->whereBetween('document_date', [$old_from_date, $old_until_date])
            // ->where('consumption_items.id', 1)
            // ->where('inventory_id', '=', $inventory_id)
            // ->get();

            $documents = Consumption::whereHas('consumption_item')
            ->with(['consumption_item', 'consumption_item.item', 'consumption_item.item_stock', 'consumption_item.item_stock.invoice_item'])
            ->where('inventory_id', '=', $inventory_id)
            ->whereBetween('document_date', [$old_from_date, $old_until_date])
            ->get();

            // $documents = Consumption::leftjoin('consumption_items', 'consumption_items.consumption_id', '=', 'consumptions.id')
            // ->leftjoin('item_stocks', 'item_stocks.id', '=', 'consumption_items.item_stock_id')
            // ->leftjoin('invoice_items', 'invoice_items.id', '=', 'item_stocks.invoice_item_id')
            // ->where('consumptions.inventory_id', '=', $inventory_id)
            // ->whereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
            // ->get();
        }
        

        //dd($documents[0]);

         //dd($documents[0]->consumption_item[0]->item_stock->invoice_item->tva_price * $documents[0]->consumption_item[0]->quantity);

        $institution = Institution::all();

        //$products = Item::where('category_id', $category_id)->get();

        // $subset = $products->map(function ($product) {
        //     return collect($product->toArray())
        //         ->only(['id'])
        //         ->all();
        // });

        $now = date('d-m-Y');

        $html = "<html>
                <head>
                <style>
                td, th {border: 1px solid black;}
                </style>
                </head>";
        
        $html .= ' <span style="font-weight: bold; float: left;">'. $institution[0]->name .'</span>
        <br>
        <span style="float: left;">Utilizator: '. $user->name .'</span>
        <h2 style="font-weight:bold; text-align: center;">CENTRALIZATOR '. $type_name .'</h2>
        <br>
        <span style="float: right;">Gestiune: '. $inventory_name .'</span>
        <br>
        <span style="float: right;">Perioada: '. $new_from_date .' - '. $new_until_date .'</span>
        <br>
        <span style="float: right;">Data: '. $now .'</span>
        <br>
        <br>
        <br>';

        $total_values = [];

        foreach($categories as $category) {
            $i = 1;
            $total = 0;
            $html .= '<span style="font-weight: bold;">'. $category->name .':</span>';
            $html .= '<br>';

            if($type_id == 2) {
                $html .= '<table>
                <tr>
                <th style="font-weight: bold; text-align: center;">Nr. Crt</th>
                <th style="font-weight: bold; text-align: center;">Data</th>
                <th style="font-weight: bold; text-align: center;">Nr. Document</th>
                <th style="font-weight: bold; text-align: center;">Valoare '. $document_type .'</th>
                </tr>';
                foreach($documents as $document) {
                    //dd($document);
                    $value = 0;
                    // foreach($document->consumption_item as $consumption_item) {
                    //     $value = $value + $consumption_item->item_stock->invoice_item->tva_price * $consumption_item->quantity;
                    // }
    
                    //if($consumption_item->item->category_id == $category->id) {
                    foreach($document->consumption_item as $consumption_item) {
                        if($consumption_item->item->category_id == $category->id) {
                            $value = $value + $consumption_item->item_stock->invoice_item->tva_price * $consumption_item->quantity;
                        }
                    }
                        $total += $value;
                        $html .= '<tr nobr="true">';
                        $html .= '<td style="text-align: center;">'. $i .'</td>';
                        $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($document->document_date)) .'</td>';
                        $html .= '<td style="text-align: center;">'. $document_type .' '. $document->id .'</td>';
                        $html .= '<td style="text-align: center;">'. $value .'</td>';
                        $html .= '</tr>';
                    //}
                    $i++;
                }
            } else if($type_id == 1) {
                $html .= '<table>
                <tr>
                <th style="font-weight: bold; text-align: center;">Nr. Crt</th>
                <th style="font-weight: bold; text-align: center;">Data</th>
                <th style="font-weight: bold; text-align: center;">Nr. Document</th>
                <th style="font-weight: bold; text-align: center;">In</th>
                <th style="font-weight: bold; text-align: center;">Valoare '. $document_type .'</th>
                </tr>';
                foreach($documents as $document) {
                    //dd($document);
                    $value = 0;
                    // foreach($document->consumption_item as $consumption_item) {
                    //     $value = $value + $consumption_item->item_stock->invoice_item->tva_price * $consumption_item->quantity;
                    // }
    
                    //if($consumption_item->item->category_id == $category->id) {
                    foreach($document->transfer_item as $transfer_item) {
                        //dd($transfer_item);
                        if($transfer_item->item_detail->category_id == $category->id) {
                            $value = $value + $transfer_item->item_stock_detail->invoice_item->tva_price * $transfer_item->quantity;
                        }
                    }
                        $total += $value;
                        $html .= '<tr nobr="true">';
                        $html .= '<td style="text-align: center;">'. $i .'</td>';
                        $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($document->document_date)) .'</td>';
                        $html .= '<td style="text-align: center;">'. $document_type .' '. $document->id .'</td>';
                        $html .= '<td style="text-align: center;">'. $document->inventory_to->name .'</td>';
                        $html .= '<td style="text-align: center;">'. $value .'</td>';
                        $html .= '</tr>';
                    //}
                    $i++;
                }
            }
            
            $total_values[] = $total;
            $html .= '</table>';
            $html .= '<br>';
        }

        $html .= '<br>';

        foreach($categories as $category) {
            $html .= '<span>Total Valoare '. $category->name .': '. $total_values[$category->id-1] .'</span>';
            $html .= '<br>';
        }

        $html .= '<br>';

        $html .= 'Gestionari:';

        $html .= '<br>';

        $html .= '<span style="text-align: left;">Farm. Sef<br>'.$institution[0]->pharmacy_manager.'<br></span>';
        
        $html .= '<br>';

        $html .= '<span style="text-align: left;">As. Farm. <br>'.$institution[0]->assistent.'</span>';

        // foreach( $categories as $key => $value ) {
        //     // echo '<option value="' . $code . '">' . $names[$index] . '</option>';
        //     $html .= '<span>Total Valoare '. $value .': </span>';
        //  }

        //  foreach (array_combine($categories, $total_values) as $category => $value) {
        //     // echo '<option value="' . $code . '">' . $name . '</option>';
        //     $html .= '<span>Total Valoare '. $category->name .': '. $value .'</span>';
        // }

        PDF::setFooterCallback(function($pdf) {

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 10);
            // Page number
            $pdf->Cell(0, 10, 'Pagina '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    
    });

        PDF::SetTitle('Centralizator');
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
