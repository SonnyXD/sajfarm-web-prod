<?php

namespace App\Http\Controllers;

use App\Models\Consumption;
use Illuminate\Http\Request;
use App\Models\Transfer;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\ItemStock;
use App\Models\Institution;
use App\Models\Ambulance;
use App\Models\Medic;
use Session;
use PDF;
use Auth;

class ConsumptionController extends Controller
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
            'ambulance-select' => 'nullable',
            'medic-select' => 'nullable',
            'document-date' => 'required',
            'from-date' => 'required',
            'until-date' => 'required'
        ));

        $from = date($request->input('from-date'));
        $to = date($request->input('until-date'));

        $amb_id = $request->input('ambulance-select');
        $med_id = $request->input('medic-select');

        $from_name = "";

        //dd($med_id);

        $span = "";

        if( !empty( $amb_id ) ) {
            $checklists = \App\Models\Checklist::with('checklistitems', 'inventory', 'medic', 'ambulance', 'assistent', 'ambulancier')
            ->whereBetween('checklist_date', [$from, $to])->where('ambulance_id', '=', $amb_id)->where('used', '=', 0)->get();
            $from_name = Ambulance::where('id', $request->input('ambulance-select'))->get()->first()->license_plate;

            //$checklist_sub = \App\Models\Checklist::with('inventory')->where('ambulance_id', '=', $amb_id)->first()?->inventory->name;

            $sub = $request->input('substation-select');

            $checklist_sub = \App\Models\Inventory::where('id', $sub)->first()->name;

            if($checklist_sub == "Stoc 3") {
                $checklist_sub = "Statie centrala";
            }
            $span = '<span style="float: right;">Substatie: '. $checklist_sub .'</span><br>
            <span style="float: right;">Ambulanta: '. $from_name .'</span>';
        } else {
            $checklists = \App\Models\Checklist::with('checklistitems', 'inventory', 'medic', 'ambulance', 'assistent', 'ambulancier')
            ->whereBetween('checklist_date', [$from, $to])->where('medic_id', '=', $med_id)->where('used', '=', 0)->get();
            $from_name = Medic::where('id', $request->input('medic-select'))->get()->first()->name;
            $checklist_amb = \App\Models\Checklist::with('ambulance')->where('medic_id', '=', $med_id)->where('used', '=', 0)->get();
            $checklist_patients = \App\Models\Checklist::where('medic_id', '=', $med_id)->where('used', '=', 0)->get();

            $sub = $request->input('substation-select');

            $checklist_sub = \App\Models\Inventory::where('id', $sub)->first()->name;

            if($checklist_sub == "Stoc 3") {
                $checklist_sub = "Statie centrala";
            }

            $span = '<span style="float: right;">Substatie: '. $checklist_sub .'</span><br>
            <span style="float: right;">Medic: '. $from_name .'</span><br>';

            //$span .= '<span style="font-weight: bold; float: right;">Ambulante: ';

            //$counter = 0;
            //dd($checklist_amb);

            // foreach($checklist_amb as $ambulance) {
            //     if( ($counter == count( $checklist_amb ) - 1)) {
            //         $span .= $ambulance->ambulance->license_plate;
            //     } else {
            //         $span .= $ambulance->ambulance->license_plate.' / ';
            //     }
                
            //     $counter++;
                
            // }

            //$span .= '</span><br>';

            // $span .= '<span style="font-weight: bold; float: right;">Nr. fise pacienti: ';

            // $counter = 0;

            // foreach($checklist_patients as $patient) {
            //     if( $counter == count( $checklist_patients ) - 1) {
            //         $span .= $patient->patient_number;
            //     } else {
            //         $span .= $patient->patient_number.' / ';
            //     }
                
            //     $counter++;
                
            // }

            //$span .= '</span><br>';

        }
        if($checklists->isEmpty())
        {
            if(!empty( $amb_id )) {
                return redirect('/operatiuni/bon-consum-ambulante')
            ->with('error', 'Generare bon de consum esuat! Cauze posibile: nu exista checklist pentru ambulanta respectiva');
            } else {
                return redirect('/operatiuni/bon-consum-medici')
            ->with('error', 'Generare bon de consum esuat! Cauze posibile: nu exista checklist pentru medicul respectiv');
            }
            
        }

        $old_date = $request->input('document-date');
        $new_date = date("d-m-Y", strtotime($old_date));  

        $user = Auth::user();
        
       
        $institution = Institution::all();

        $consumption = Consumption::all();

        $consumption_id = $consumption->last()?->id;

        if($consumption_id === null) {
            $consumption_id = 1;
        } else {
            $consumption_id++;
        }

        $filename = 'pdfs/consum'.$consumption_id .'.pdf';

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
        <h2 style="font-weight:bold; text-align: center;">BON DE CONSUM</h2>
        <br>
        <span style="float: right;">Perioada: '. date("d-m-Y", strtotime($from)) .' - '. date("d-m-Y", strtotime($to)) .'</span>
        <br>
        <span style="float: right;">Numar document: '. $consumption_id . ' / ' . $new_date .'</span>
        <br>
        '. $span .'
        <br>
        <br>
';

        if(empty($amb_id)) {
            $html .= '
            <table>
            <tr>
            <th style="font-weight: bold; text-align: center;">Cod Produs</th>
            <th style="font-weight: bold; text-align: center;">Denumire Produs</th>
            <th style="font-weight: bold; text-align: center;">UM</th>
            <th style="font-weight: bold; text-align: center;">Cantitate</th>
            <th style="font-weight: bold; text-align: center;">Pret</th>
            <th style="font-weight: bold; text-align: center;">Valoare</th>
            <th style="font-weight: bold; text-align: center;">Lot</th>
            <th style="font-weight: bold; text-align: center;">Data expirare</th>
            <th style="font-weight: bold; text-align: center;">Ambulanta</th>
            <th style="font-weight: bold; text-align: center;">Nr fisa pacient</th>
            </tr>
            ';
        } else {
            $html .= '
            <table>
            <tr>
            <th style="font-weight: bold; text-align: center;">Cod Produs</th>
            <th style="font-weight: bold; text-align: center;">Nume</th>
            <th style="font-weight: bold; text-align: center;">UM</th>
            <th style="font-weight: bold; text-align: center;">Cantitate</th>
            <th style="font-weight: bold; text-align: center;">Pret</th>
            <th style="font-weight: bold; text-align: center;">Valoare</th>
            <th style="font-weight: bold; text-align: center;">Lot</th>
            <th style="font-weight: bold; text-align: center;">Data expirare</th>
            </tr>
            ';
        }

        
        //dd($checklists);
        $total_value = 0;
        $i = 1;
        $skippingId = array();

        $subset = $checklists->map(function ($checklist) {
            return collect($checklist->toArray())
                ->only(['id'])
                ->all();
        });

        $substation_id = $request->input('substation-select');

        foreach($checklists as $checklist)
        {

            // if($checklist->used == 1) {
            //     // return redirect('/operatiuni/bon-consum-ambulante')
            //     //     ->with('error', 'Generare bon de consum esuat! Cauze posibile: nu exista checklist pentru ambulanta/medicul respectiv');
            //     continue;
            // }
            
            if( $checklist->checklistitems->isEmpty() ) {
                continue;
            }

            if($i == 1) {
                $consumption = new \App\Models\Consumption();
                $consumption->inventory_id = $substation_id;
                $consumption->medic_id = $checklist->medic->id ?? null;
                $consumption->ambulance_id = $checklist->ambulance->id;
                $consumption->patient_number = $checklist->patient_number;
                $consumption->tour = $checklist->tour;
                $consumption->document_date = $request->input('document-date');
                $consumption->save();
            }

            $i++;

            // $consumption = new \App\Models\Consumption();
            // $consumption->inventory_id = $checklist->inventory->id;
            // $consumption->medic_id = $checklist->medic->id ?? null;
            // $consumption->ambulance_id = $checklist->ambulance->id;
            // $consumption->patient_number = $checklist->patient_number;
            // $consumption->tour = $checklist->tour;
            // $consumption->document_date = $request->input('document-date');
            // $consumption->save();

            
            $total_quantity = 0;

            

            foreach($checklist->checklistitems as $item)
            {
                $total_quantity = 0;
                // if($item->used == 1) {
                //     continue;
                // }
                if(!empty( $amb_id )) {
                    if (in_array($item->item_stock_id, $skippingId)) {
                        $item->used = 1;
                        $item->save();
                        continue;
                    }
                }
                
                $checklist_items = "";

                $detailedItem = \App\Models\ItemStock::with('item', 'invoice_item', 'invoice_item.measure_unit')->find($item->item_stock_id);

                if(!empty($amb_id)) {
                    $checklist_items = \App\Models\ChecklistItem::leftjoin('checklists', 'checklist_items.checklist_id', '=', 'checklists.id')
                    ->where('checklists.ambulance_id', '=', $amb_id)
                    ->where('checklist_items.item_stock_id', '=', $item->item_stock_id)
                    ->where('checklist_items.used', '=', 0)
                    ->where('checklists.used', '=', 0)
                    ->whereIn('checklists.id', $subset)
                    ->select('checklist_items.used', 'checklist_items.quantity', 'checklists.id', 'checklist_items.id as cid',
                    'checklist_items.item_id', 'checklist_items.item_stock_id')
                    ->get();
                    //dd($checklist_items);
                } else {
                    $checklist_items = \App\Models\ChecklistItem::join('checklists', 'checklist_items.checklist_id', '=', 'checklists.id')
                    ->where('checklists.medic_id', '=', $med_id)
                    ->where('checklist_items.item_stock_id', '=', $item->item_stock_id)
                    ->where('checklist_items.used', '=', 0)
                    ->where('checklists.used', '=', 0)
                    ->where('checklists.id', '=', $checklist->id)
                    ->select('checklist_items.used', 'checklist_items.quantity', 'checklists.id', 'checklist_items.id as cid',
                    'checklist_items.item_id', 'checklist_items.item_stock_id')
                    ->get();
                }

                $skippingId[] = $item->item_stock_id;

                //$checklist_items = \App\Models\Checklist::with('checklistitems')->where()->get();

                //dd($checklist_items);

                foreach($checklist_items as $checklist_item) {
                    //dd($checklist_item);
                    $total_quantity += $checklist_item->quantity;
                    $checklist_item->used = 1;
                    $checklist_item->save();

                    $consumItem = new \App\Models\ConsumptionItem();
                    $consumItem->consumption_id = $consumption->id;
                    $consumItem->item_id = $checklist_item->item_id;
                    $consumItem->item_stock_id = $checklist_item->item_stock_id;
                    $consumItem->quantity = $checklist_item->quantity;
                    $consumItem->save();
                    //$skippingId[] = $checklist_item->cid;
                    //dd($checklist_item);
                }

                //dd($total_quantity);
                
                // $consumItem = new \App\Models\ConsumptionItem();
                // $consumItem->consumption_id = $consumption->id;
                // $consumItem->item_id = $item->item_id;
                // $consumItem->item_stock_id = $item->item_stock_id;
                // $consumItem->quantity = $item->quantity;
                // $consumItem->save();
                //generez document

                $item->used = 1;
                $item->save();

                if(empty( $amb_id )) {
                    $html.= '<tr nobr="true">
                    <td style="text-align: center;">'. $detailedItem->invoice_item->product_code .'</td>
                    <td style="text-align: center;">'. $detailedItem->item->name .'</td>
                    <td style="text-align: center;">'. $detailedItem->invoice_item->measure_unit->name .'</td>
                    <td style="text-align: center;">'. $item->quantity .'</td>
                    <td style="text-align: center;">'. $detailedItem->invoice_item->price .'</td>
                    <td style="text-align: center;">'. $detailedItem->invoice_item->price * $item->quantity .'</td>
                    <td style="text-align: center;">'. $detailedItem->invoice_item->lot .'</td>
                    <td style="text-align: center;">'. date("d-m-Y", strtotime($detailedItem->invoice_item->exp_date)) .'</td>
                    <td style="text-align: center;">'. $checklist->ambulance->license_plate .'</td>
                    <td style="text-align: center;">'. $checklist->patient_number .'</td>
                </tr>';
                $total_value += $detailedItem->invoice_item->price * $item->quantity;
                } else {
                    $html.= '<tr nobr="true">
                    <td style="text-align: center;">'. $detailedItem->invoice_item->product_code .'</td>
                    <td style="text-align: center;">'. $detailedItem->item->name .'</td>
                    <td style="text-align: center;">'. $detailedItem->invoice_item->measure_unit->name .'</td>
                    <td style="text-align: center;">'. $total_quantity .'</td>
                    <td style="text-align: center;">'. $detailedItem->invoice_item->price .'</td>
                    <td style="text-align: center;">'. $detailedItem->invoice_item->price * $total_quantity .'</td>
                    <td style="text-align: center;">'. $detailedItem->invoice_item->lot .'</td>
                    <td style="text-align: center;">'. date("d-m-Y", strtotime($detailedItem->invoice_item->exp_date)) .'</td>
                </tr>';
                $total_value += $detailedItem->invoice_item->price * $total_quantity;
                }


            
            }
            
            //delete checklist here and checklist items
            //aa, cred ca am inteles. deci asta inseamna ca pot vedea bonul de consum mereu ca practic preia informatiile din consum, nu din checklist, asa-i?
            // da. tu estrgi din checklist pt ca daca le-ai lasa acolo as putea sa fac acelasi checklist de N ori. Odata ce consumul a fost efectuat, stergi din checklist si toate informatiile se nuta in tabelele de consum
            ///continua ce voiai sa zici
            //aici de ce trebuie sterse astea? ca practic dupa nu se mai poate face bonul de consum, nu?
            //tu adaugi din checklist in consum. ticketul de consum il faci dupa astea
            //asta stiu. dar spre ex, daca le sterg si vreau sa vad iar acelasi bon de consum, nu-l mai pot vedea inca o data pt ca sunt sterse alea si nu stie de unde sa ia datele, la ast
            // man. tu stergi din checklist si adaugi in consum. o sa le ai pe toate in consum.
        }

        $html .= '</table><br><br>';

        $html .= 'Total valoare: '. $total_value .'';

        $html .= '<br><br>Asistenti:<br>';

        $assistents = array();

        foreach($checklists as $checklist)
        {
            //$detailedChecklist = \App\Models\Checklist::with('assistent')->find($checklist->assistent_id);

            if($checklist->used == 1) {
                continue;
            }

            $detailedChecklist = \App\Models\Checklist::with('assistent', 'ambulancier')->find($checklist->id);
            $assistent = $detailedChecklist->assistent->name ?? '';
            //dd($detailedChecklist);
            
            if($assistent != '') {
                if (in_array($assistent, $assistents) == false) {
                    array_push($assistents, $assistent);
                    $html .= '
                    <span style="font-weight: bold;">'. $assistent .'</span>
                    <br>';
                }
                
            }
            
        }

        $html .= '<br><br>Ambulantieri:<br>';

        $ambulanciers = array();

        foreach($checklists as $checklist)
        {

            if($checklist->used == 1) {
                continue;
            }
            //$detailedChecklist = \App\Models\Checklist::with('assistent')->find($checklist->assistent_id);

            $detailedChecklist = \App\Models\Checklist::with('assistent', 'ambulancier')->find($checklist->id);
            $ambulancier = $detailedChecklist->ambulancier->name ?? '';
            //dd($detailedChecklist);

            if($ambulancier != '') {
                if (in_array($ambulancier, $ambulanciers) == false) {
                    array_push($ambulanciers, $ambulancier);
                    $html .= '
                    <span style="font-weight: bold;">'. $ambulancier .'</span>
                    <br>';
                }
                
            }

            $checklist->used = 1;
            $checklist->save();
            
        }

        $html .= '<p style="text-align: right;">Intocmit Farm. Sef<br>
        '. $institution[0]->pharmacy_manager .'</p>';

        //dd($assistents);

        $html .= '<br>';

        $html .= '</html>';

        PDF::setFooterCallback(function($pdf) {

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 10);
            // Page number
            $pdf->Cell(0, 10, 'Pagina '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    
    });

        PDF::SetTitle('Consum');
        PDF::AddPage('L', 'A4');
        PDF::writeHTML($html, true, false, true, false, '');

        PDF::Output(public_path($filename), 'F');

        Session::flash('fileToDownload', url($filename));

        if(!empty( $amb_id )) {
            return redirect('/operatiuni/bon-consum-ambulante')
        ->with('success', 'Consum generat cu succes!')->with('download',);
        } else {
            return redirect('/operatiuni/bon-consum-medici')
        ->with('success', 'Consum generat cu succes!')->with('download',);
        }

        // return redirect('/operatiuni/bon-transfer')
        //     ->with('success', 'Consum generat cu succes!')->with('download',);
        
    
        // return redirect('/operatiuni/bon-consum-ambulante')->with('success', 'Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Consumption  $consumption
     * @return \Illuminate\Http\Response
     */
    public function show(Consumption $consumption)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Consumption  $consumption
     * @return \Illuminate\Http\Response
     */
    public function edit(Consumption $consumption)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consumption  $consumption
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Consumption $consumption)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Consumption  $consumption
     * @return \Illuminate\Http\Response
     */
    public function destroy(Consumption $consumption)
    {
        //
    }
}
