<?php

namespace App\Http\Controllers;

use App\Models\Returning;
use App\Models\ReturningItem;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\ItemStock;
use App\Models\Institution; 
use App\Models\Ambulance; 
use App\Models\ReturningChecklist; 
use App\Models\ReturningChecklistItem; 
use App\Models\Category; 
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            'substation-select' => 'required',
            'document-date' => 'required',
            'from-date' => 'required',
            'until-date' => 'required'
            ));

        $uid = Str::random(30);
        
        $old_from_date = $request->input('from-date');
        $old_until_date = $request->input('until-date');

        $new_from_date = date("d-m-Y", strtotime($old_from_date));  
        $new_until_date = date("d-m-Y", strtotime($old_until_date)); 

        $checklists = ReturningChecklist::with('returning_checklist_item', 'returning_checklist_item.item_stock')
        ->where('inventory_id', $request->input('substation-select'))
        ->where('used', 0)
        ->whereBetween('checklist_date', [$old_from_date, $old_until_date])
        ->get();

        // dd($checklists);

        if($checklists->isEmpty()) {
            return redirect('/operatiuni/retur')
                ->with('error', 'Generare proces verbal retur esuat! Cauze posibile: nu exista checklist-uri in perioada selectata');
        }
    
        $returning = new \App\Models\Returning();
        $returning->inventory_id = $request->input('substation-select');
        $returning->document_date = $request->input('document-date');
        $returning->uid = $uid;

        //$ambulance_id = $request->input('ambulance-select');
   
        $returning->save(); 

        $from_location = Inventory::where('id', $request->input('substation-select'))->first();

        // $span_amb = '';

        // if($ambulance_id === null) {
        //     $span_amb = '<span style="font-weight: bold; float: right;">Gestiune de iesire: '. $from_location->first()->name .'</span>';
        // } else {
        //     $amb_name = Ambulance::where('id', $ambulance_id)->get()->first()->license_plate;
        //     $span_amb = '<span style="font-weight: bold; float: right;">Gestiune de iesire: '. $from_location->first()->name .' - '. $amb_name .'</span>';
        // }

        $user = Auth::user();
    
        $institution = Institution::all();

        $new_date = date("d-m-Y", strtotime($request->input('document-date')));  

        $filename = 'pdfs/'.$uid.'.pdf';

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
        <h2 style="font-weight:bold; text-align: center;">PROCES VERBAL RETUR</h2>
        <br>
        <span style="float: right;">Numar document: '. $returning->id . ' / ' . $new_date .'</span>
        <br>
        <span style="float: right;">Perioada: '. $new_from_date . ' / ' . $new_until_date .'</span>
        <br>
        <span style="float: right;">Gestiune: '. $from_location->name .'</span>
        <br>
        <br>
';

        $table = <<<EOD
        <table width: 100%>
        <thead>
        <tr nobr="true">
        <th style="font-weight: bold; text-align: center;">Denumire Produs</th>
        <th style="font-weight: bold; text-align: center;">UM</th>
        <th style="font-weight: bold; text-align: center;">Cantitate</th>
        <th style="font-weight: bold; text-align: center;">Pret</th>
        <th style="font-weight: bold; text-align: center;">TVA</th>
        <th style="font-weight: bold; text-align: center;">Valoare</th>
        <th style="font-weight: bold; text-align: center;">Lot</th>
        <th style="font-weight: bold; text-align: center;">Data expirare</th>
        <th style="font-weight: bold; text-align: center;">Motiv</th>
        <th style="font-weight: bold; text-align: center;">Gestiune de iesire</th>
        </tr>
        </thead>
        EOD;

        $categories = Category::all();

        $total_value = 0;

        $total_values = [];

        $returning_items = [];

        foreach($categories as $category) {
            $html .= '<span style="font-weight: bold;">'. $category->name .'</span><br><br>';
            $html .= $table;
            $total = 0;
            foreach($checklists as $checklist) {
                $checklist->used = 1;
                $checklist->save();
                foreach($checklist->returning_checklist_item as $item) {
                    $detailedItem = \App\Models\ItemStock::with('item', 'invoice_item', 'invoice_item.measure_unit')->find($item->item_stock->id);
                    if($category->id == $detailedItem->item->category_id) {
                        if($item->ambulance_id == null) {
                            $from = $from_location->name;
                        } else {
                            $ambulance = Ambulance::where('id', $item->ambulance_id)->first()->license_plate;
                            $from = $from_location->name.' - '.$ambulance;
                        }
                        $html.= '<tr nobr="true">
                        <td style="text-align: center;">'. $detailedItem->item->name .'</td>
                        <td style="text-align: center;">'. $detailedItem->invoice_item->measure_unit->name .'</td>
                        <td style="text-align: center;">'. $item->quantity .'</td>
                        <td style="text-align: center;">'. $detailedItem->invoice_item->price .'</td>
                        <td style="text-align: center;">'. $detailedItem->invoice_item->tva .'</td>
                        <td style="text-align: center;">'. $detailedItem->invoice_item->tva_price * $item->quantity .'</td>
                        <td style="text-align: center;">'. $detailedItem->invoice_item->lot .'</td>
                        <td style="text-align: center;">'. date("d-m-Y", strtotime($detailedItem->invoice_item->exp_date)).'</td>
                        <td style="text-align: center;">'. $item->reason .'</td>
                        <td style="text-align: center;">'. $from .'</td>
                    </tr>';
                    $total += ($detailedItem->invoice_item->tva_price * $item->quantity);

                    $returning_item = new \App\Models\ReturningItem();
                    $returning_item->returning_id = $returning->id;
                    $returning_item->item_id = $detailedItem->item->id;
                    $returning_item->item_stock_id = $detailedItem->id;
                    $returning_item->quantity = $item->quantity;
                    $returning_item->reason = $item->reason;
                    $returning_item->ambulance_id = $item->ambulance_id;
                    $returning_item->save();
                    }
                }
                
            }
            $total_values[] = $total;
            $html .= '</table><br><br>';
        }

        // foreach($products as $product) {
        //     $item = ItemStock::with('invoice_item')->where('id', $product['productId'])->get()->first();
        //     $item->quantity -= $product['productQty'];
        //     $item->save();

        //     $returning_item = new \App\Models\ReturningItem();

        //     $returning_item->item_stock_id = $product['productId'];
        //     $returning_item->returning_id = $returning_id;
        //     $returning_item->item_id = $item->item_id;
        //     $returning_item->quantity = $product['productQty'];
        //     $returning_item->reason = $product['productReason'];
        //     $returning_item->ambulance_id = $product['productAmb'];

        //     $returning_item->save();

        //     $from = "";

        //     if(!empty($returning_item->ambulance_id)) {
        //         $from = $from_location->name.' - '.ReturningItem::with('ambulance')->where('ambulance_id', '=', $product['productAmb'])->first()->ambulance->license_plate;
        //     }
            
        //     if(empty($from)) {
        //         $from = $from_location->name;
        //     }

        //     $html.= '<tr nobr="true">
        //     <td style="text-align: center;">'. $product['productName'] .'</td>
        //     <td style="text-align: center;">'. $product['productUmText'] .'</td>
        //     <td style="text-align: center;">'. $product['productQty'] .'</td>
        //     <td style="text-align: center;">'. $item->invoice_item->price .'</td>
        //     <td style="text-align: center;">'. $item->invoice_item->tva .'</td>
        //     <td style="text-align: center;">'. $item->invoice_item->tva_price * $product['productQty'] .'</td>
        //     <td style="text-align: center;">'. $item->invoice_item->lot .'</td>
        //     <td style="text-align: center;">'. date("d-m-Y", strtotime($item->invoice_item->exp_date )).'</td>
        //     <td style="text-align: center;">'. $product['productReason'] .'</td>
        //     <td style="text-align: center;">'. $from .'</td>
        // </tr>';

        // $total_value += $item->invoice_item->tva_price * $product['productQty'];
        // }

       // $html .= '</table>';

        $html .= '<br>';

        $html .= '<br>';

        //$html .= 'Total valoare: '. $total_value .'';

        foreach($categories as $category) {
            $html .= '<span>Total Valoare '. $category->name .': '. $total_values[$category->id-1] .'</span>';
            $html .= '<br>';
        }

        $html .= '<br>';

        $html .= '<br>';

        $html .= 'Gestionari:';

        $html .= '<br>';

        $html .= '<span style="text-align: left;">Farm. Sef<br>'.$institution[0]->pharmacy_manager.'<br></span>';
        
        $html .= '<br>';

        $html .= '<span style="text-align: left;">As. Farm. <br>'.$institution[0]->assistent.'</span>';

        $html .= '</html>';

        PDF::setFooterCallback(function($pdf) {

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 10);
            // Page number
            $pdf->Cell(0, 10, 'Pagina '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    
    });

        PDF::SetTitle('Proces Verbal Retur');
        PDF::AddPage('P', 'A4');
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
