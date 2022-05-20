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

        if( !empty( $amb_id ) ) {
            $checklists = \App\Models\Checklist::with('checklistitems', 'inventory', 'medic', 'ambulance', 'assistent', 'ambulancier')
            ->whereBetween('checklist_date', [$from, $to])->where('ambulance_id', '=', $amb_id)->get();
            $from_name = Ambulance::where('id', $request->input('ambulance-select'))->get()->first()->license_plate;
        } else {
            $checklists = \App\Models\Checklist::with('checklistitems', 'inventory', 'medic', 'ambulance', 'assistent', 'ambulancier')
            ->whereBetween('checklist_date', [$from, $to])->where('medic_id', '=', $med_id)->get();
            $from_name = Medic::where('id', $request->input('medic-select'))->get()->first()->name;
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
        $consumption = Consumption::all();
        $consumption_id = $consumption->last()->id;
        $institution = Institution::all();

        if($consumption_id === null) {
            $consumption_id = 1;
        }

        $filename = 'pdfs/consum'.$consumption_id.'.pdf';

        $html = '<html>
                <head>
                <style>
                td, th {border: 2px solid black;}
                </style>
                </head>
                ';
        
        $html .= ' <span style="font-weight: bold; float: left;">'. $institution[0]->name .'</span>
        <br>
        <span style="float: left;">Utilizator: '. $user->name .'</span>
        <h2 style="font-weight:bold; text-align: center;">BON DE CONSUM</h2>
        <br>
        <span style="font-weight: bold; float: right;">Numar document: '. $consumption_id . ' / ' . $new_date .'</span>
        <br>
        <span style="font-weight: bold; float: right;">Din: '. $from_name .'</span>
        <br>
        <br>
        <br>
        <br>
';

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
        //dd($checklists);
        $total_value = 0;
        foreach($checklists as $checklist)
        {
            if( $checklist->checklistitems->isEmpty() ) {
                continue;
            }

            $consumption = new \App\Models\Consumption();
            $consumption->inventory_id = $checklist->inventory->id;
            $consumption->medic_id = $checklist->medic->id ?? null;
            $consumption->ambulance_id = $checklist->ambulance->id;
            $consumption->patient_number = $checklist->patient_number;
            $consumption->tour = $checklist->tour;
            $consumption->document_date = $request->input('document-date');
            $consumption->save();
            $i++;

            foreach($checklist->checklistitems as $item)
            {
                $detailedItem = \App\Models\ItemStock::with('item', 'invoice_item', 'invoice_item.measure_unit')->find($item->item_stock_id);
                
                $consumItem = new \App\Models\ConsumptionItem();
                $consumItem->consumption_id = $consumption->id;
                $consumItem->item_id = $item->item_id;
                $consumItem->item_stock_id = $item->item_stock_id;
                $consumItem->quantity = $item->quantity;;
                $consumItem->save();
                //generez document

                $html.= '<tr>
                <td style="font-weight: bold; text-align: center;">'. $detailedItem->invoice_item->product_code .'</td>
                <td style="font-weight: bold; text-align: center;">'. $detailedItem->item->name .'</td>
                <td style="font-weight: bold; text-align: center;">'. $detailedItem->invoice_item->measure_unit->name .'</td>
                <td style="font-weight: bold; text-align: center;">'. $item->quantity .'</td>
                <td style="font-weight: bold; text-align: center;">'. $detailedItem->invoice_item->price .'</td>
                <td style="font-weight: bold; text-align: center;">'. $detailedItem->invoice_item->price * $item->quantity .'</td>
                <td style="font-weight: bold; text-align: center;">'. $detailedItem->invoice_item->lot .'</td>
                <td style="font-weight: bold; text-align: center;">'. date("d-m-Y", strtotime($detailedItem->invoice_item->exp_date)) .'</td>
            </tr>';

            $total_value += $detailedItem->invoice_item->price * $item->quantity;
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

        // foreach($checklists as $checklist)
        // {
        //     $detailedChecklist = \App\Models\Checklist::with('assistent')->find($checklist->assistent_id);
        //     dd($detailedChecklist);
        //     $html .= '<br>
        //         <p style="font-weight: bold;">'. $detailedChecklist->assistent->name .'</p>
        //     ';
        // }

        $html .= '<br>';

        $html .= 'Total valoare: '. $total_value .'';

        $html .= '</table>';

        $html .= '</html>';

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
