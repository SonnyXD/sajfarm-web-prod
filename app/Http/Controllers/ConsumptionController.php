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
use App\Models\Staff;
use App\Models\Category;
use App\Models\Checklist;
use App\Models\ChecklistItem;
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

        $staff = Staff::all();

        $from = date($request->input('from-date'));
        $to = date($request->input('until-date'));

        $amb_id = $request->input('ambulance-select');
        $med_id = $request->input('medic-select');

        $from_name = "";

        //dd($med_id);

        $span = "";

        if( !empty( $amb_id ) ) {
            $sub = $request->input('substation-select');
            $ambulance_checklists = Ambulance::with(['checklist' => function ($query) use($to, $from, $sub) {
                $query->whereBetween('checklist_date', [$from, $to]);
                $query->where('used', 0);
                $query->where('medic_id', null);
                $query->where('inventory_id', $sub);
            }, 'checklist.checklistitems', 'checklist.inventory',
            'checklist.assistent', 'checklist.ambulancier',
            'checklist.ambulance', 'checklist.checklistitems.item'])
            ->where('ambulances.id', $amb_id)
            ->get();

            if($ambulance_checklists[0]->checklist->isEmpty())
            {   
                    return redirect('/operatiuni/bon-consum-ambulante')
                ->with('error', 'Generare bon de consum esuat! Cauze posibile: nu exista checklist pentru ambulanta respectiva');
                
            }

            //dd($ambulance_checklists[0]->checklist->first());
            //dd($ambulance_checklists->first());

            // $checklists = \App\Models\Checklist::with('checklistitems', 'inventory', 'medic', 'ambulance', 'assistent', 'ambulancier')
            // ->whereBetween('checklist_date', [$from, $to])->where('ambulance_id', '=', $amb_id)->where('used', '=', 0)->get();
             $from_name = Ambulance::where('id', $request->input('ambulance-select'))->get()->first()->license_plate;

            //$checklist_sub = \App\Models\Checklist::with('inventory')->where('ambulance_id', '=', $amb_id)->first()?->inventory->name;

            $checklist_sub = \App\Models\Inventory::where('id', $sub)->first()->name;

            if($checklist_sub == "Stoc 3") {
                $checklist_sub = "Statie centrala";
            }
            $span = '<span style="float: right;">Substatie: '. $checklist_sub .'</span><br>
            <span style="float: right;">Ambulanta: '. $from_name .'</span>';
        } else {
            // $checklists = \App\Models\Checklist::with('checklistitems', 'inventory', 'medic', 'ambulance', 'assistent', 'ambulancier')
            // ->whereBetween('checklist_date', [$from, $to])->where('medic_id', '=', $med_id)->where('used', '=', 0)->get();
            $from_name = Medic::where('id', $request->input('medic-select'))->get()->first()->name;
            // $checklist_amb = \App\Models\Checklist::with('ambulance')->where('medic_id', '=', $med_id)->where('used', '=', 0)->get();
            // $checklist_patients = \App\Models\Checklist::where('medic_id', '=', $med_id)->where('used', '=', 0)->get();

            $medic_checklists = Medic::with(['checklist' => function ($query) use($to, $from) {
                $query->whereBetween('checklist_date', [$from, $to]);
                $query->where('used', 0);
            }, 'checklist.checklistitems' => function ($query) {
                $query->where('used', 0);
            }, 'checklist.inventory',
                'checklist.assistent', 'checklist.ambulancier',
                'checklist.ambulance', 'checklist.checklistitems.item'])
            ->where('medics.id', $med_id)
            ->get();

            if($medic_checklists[0]->checklist->isEmpty())
            {   
                    return redirect('/operatiuni/bon-consum-medici')
                ->with('error', 'Generare bon de consum esuat! Cauze posibile: nu exista checklist pentru medicul respectiv');
                
            }

            //dd($medic_checklists->first());

            $sub = $request->input('substation-select');

            $checklist_sub = \App\Models\Inventory::where('id', $sub)->first()->name;

            if($checklist_sub == "Stoc 3") {
                $checklist_sub = "Statie centrala";
            }

            $span = '<span style="float: right;">Medic: '. $from_name .'</span><br>';

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
        // if($checklists->isEmpty())
        // {
        //     if(!empty( $amb_id )) {
        //         return redirect('/operatiuni/bon-consum-ambulante')
        //     ->with('error', 'Generare bon de consum esuat! Cauze posibile: nu exista checklist pentru ambulanta respectiva');
        //     } else {
        //         return redirect('/operatiuni/bon-consum-medici')
        //     ->with('error', 'Generare bon de consum esuat! Cauze posibile: nu exista checklist pentru medicul respectiv');
        //     }
            
        // }

        $old_date = $request->input('document-date');
        $new_date = date("d-m-Y", strtotime($old_date));  

        $user = Auth::user();

        //dd($ambulance_checklists->first());
       
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
            <th style="font-weight: bold; text-align: center;">Denumire Produs</th>
            <th style="font-weight: bold; text-align: center;">UM</th>
            <th style="font-weight: bold; text-align: center;">Cantitate</th>
            <th style="font-weight: bold; text-align: center;">Pret</th>
            <th style="font-weight: bold; text-align: center;">TVA</th>
            <th style="font-weight: bold; text-align: center;">Valoare</th>
            <th style="font-weight: bold; text-align: center;">Lot</th>
            <th style="font-weight: bold; text-align: center;">Data expirare</th>
            <th style="font-weight: bold; text-align: center;">Ambulanta</th>
            <th style="font-weight: bold; text-align: center;">Nr fisa pacient</th>
            <th style="font-weight: bold; text-align: center;">Substatie</th>
            </tr>
            ';
        } else {
            $amb_table = '
            <table>
            <tr>
            <th style="font-weight: bold; text-align: center;">Denumire Produs</th>
            <th style="font-weight: bold; text-align: center;">UM</th>
            <th style="font-weight: bold; text-align: center;">Cantitate</th>
            <th style="font-weight: bold; text-align: center;">Pret</th>
            <th style="font-weight: bold; text-align: center;">TVA</th>
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

        // $subset = $checklists->map(function ($checklist) {
        //     return collect($checklist->toArray())
        //         ->only(['id'])
        //         ->all();
        // });

        $consumption_items = [];
        // foreach($consumptions as $consumption) {
        //     foreach($consumption->consumption_items_grouped as $consumption_item) {
        //         if(!isset($consumption_items[$consumption_item->item_stock_id])) {
        //             $consumption_items[$consumption_item->item_stock_id] = array('id' => $consumption_item->id,
        //                 'quantity' => $consumption_item->quantity,
        //                 'item_stock_id' => $consumption_item->item_stock_id,
        //                 'item_name' => $consumption_item->item_stock->invoice_item->item->name,
        //                 'price' => $consumption_item->item_stock->invoice_item->price,
        //                 'tva' => $consumption_item->item_stock->invoice_item->tva,
        //                 'tva_price' => $consumption_item->item_stock->invoice_item->tva_price,
        //                 'um' => $consumption_item->item_stock->invoice_item->measure_unit->name,
        //                 'lot' => $consumption_item->item_stock->invoice_item->lot,
        //                 'exp_date' => $consumption_item->item_stock->invoice_item->exp_date,
        //                 'category_id' => $consumption_item->item_stock->invoice_item->item->category_id);
        //         } else {
        //             $consumption_items[$consumption_item->item_stock_id]['quantity'] += $consumption_item->quantity;
        //         }
        //     }
            
        // }

        $categories = Category::all();

        $substation_id = $request->input('substation-select');

        if(isset($ambulance_checklists)) {
            foreach($ambulance_checklists->first()->checklist as $checklist) {
                //dd($checklist);
                if($i == 1) {
                    $consumption = new \App\Models\Consumption();
                    $consumption->inventory_id = $substation_id;
                    $consumption->medic_id = $checklist->medic_id ?? null;
                    $consumption->ambulance_id = $checklist->ambulance_id;
                    $consumption->patient_number = $checklist->patient_number ?? null;
                    $consumption->tour = $checklist->tour;
                    $consumption->document_date = $request->input('document-date');
                    $consumption->save();
                }

                $i++;
                foreach($checklist->checklistitems as $consumption_item) {
                    if(!isset($consumption_items[$consumption_item->item_stock_id])) {
                        //dd($consumption_item);
                        $detailedItem = \App\Models\ItemStock::with('item', 'invoice_item', 'invoice_item.measure_unit')->find($consumption_item->item_stock_id);
                        $consumption_items[$consumption_item->item_stock_id] = array('id' => $consumption_item->id,
                            'quantity' => $consumption_item->quantity,
                            'item_stock_id' => $consumption_item->item_stock_id,
                            'item_name' => $detailedItem->item->name,
                            'price' => $detailedItem->invoice_item->price,
                            'tva' => $detailedItem->invoice_item->tva,
                            'tva_price' => $detailedItem->invoice_item->tva_price,
                            'um' => $detailedItem->invoice_item->measure_unit->name,
                            'lot' => $detailedItem->invoice_item->lot,
                            'exp_date' => $detailedItem->invoice_item->exp_date,
                            'category_id' => $detailedItem->item->category_id,
                            'checklist_item_id' => $consumption_item->id,
                            'item_id' => $consumption_item->item_id,
                            'item_stock_id' => $consumption_item->item_stock_id);
                    } else {
                        $consumption_items[$consumption_item->item_stock_id]['quantity'] += $consumption_item->quantity;
                    }
                }
                
            }

            //dd($consumption_items);
            $total_values = [];
            foreach($categories as $category) {
                $html .= '<span style="font-weight: bold;">'. $category->name .'</span><br><br>';
                $html .= $amb_table;
                $total = 0;
                // foreach($ambulance_checklists->first()->checklist as $checklist) {
                //     foreach($checklist->checklistitems as $item) {
                //         //dd($item);
                //         if($category->id == $item->item->category_id) {
                //             $detailedItem = \App\Models\ItemStock::with('item', 'invoice_item', 'invoice_item.measure_unit')->find($item->item_stock_id);
                //             $substation = Inventory::where('id', $checklist->inventory_id)->first()->name;
                //             $html.= '<tr nobr="true">
                //             <td style="text-align: center;">'. $detailedItem->item->name .'</td>
                //             <td style="text-align: center;">'. $detailedItem->invoice_item->measure_unit->name .'</td>
                //             <td style="text-align: center;">'. $item->quantity .'</td>
                //             <td style="text-align: center;">'. $detailedItem->invoice_item->price .'</td>
                //             <td style="text-align: center;">'. $detailedItem->invoice_item->price * $item->quantity .'</td>
                //             <td style="text-align: center;">'. $detailedItem->invoice_item->lot .'</td>
                //             <td style="text-align: center;">'. date("d-m-Y", strtotime($detailedItem->invoice_item->exp_date)) .'</td>
                //         </tr>';
                //     $total_value += $detailedItem->invoice_item->price * $item->quantity;
                //         }
                    
                //     }
                    
                // }
                foreach($consumption_items as $item) {
                        if($category->id == $item['category_id']) {
                            $html.= '<tr nobr="true">
                            <td style="text-align: center;">'. $item['item_name'] .'</td>
                            <td style="text-align: center;">'. $item['um'] .'</td>
                            <td style="text-align: center;">'. $item['quantity'] .'</td>
                            <td style="text-align: center;">'. $item['price'] .'</td>
                            <td style="text-align: center;">'. $item['tva'] .'</td>
                            <td style="text-align: center;">'. $item['tva_price'] * $item['quantity'] .'</td>
                            <td style="text-align: center;">'. $item['lot'] .'</td>
                            <td style="text-align: center;">'. date("d-m-Y", strtotime($item['exp_date'])) .'</td>
                        </tr>';
                    $total += $item['tva_price'] * $item['quantity'];
                        $consumItem = new \App\Models\ConsumptionItem();
                        $consumItem->consumption_id = $consumption->id;
                        $consumItem->item_id = $item['item_id'];
                        $consumItem->item_stock_id = $item['item_stock_id'];
                        $consumItem->quantity = $item['quantity'];
                        $consumItem->save();
                        }
                        ChecklistItem::where('id', $item['id'])
                        ->update(['used' => 1]);
                }
                $total_values[] = $total;
                $html .= '</table><br><br>';
            }
            
        } else if(isset($medic_checklists)) {
                foreach($medic_checklists->first()->checklist as $checklist) {
                    if($i == 1) {
                        $consumption = new \App\Models\Consumption();
                        $consumption->inventory_id = $substation_id;
                        $consumption->medic_id = $checklist->medic_id ?? null;
                        $consumption->ambulance_id = $checklist->ambulance_id;
                        $consumption->patient_number = $checklist->patient_number ?? null;
                        $consumption->tour = $checklist->tour;
                        $consumption->document_date = $request->input('document-date');
                        $consumption->save();
                    }
    
                    $i++;
                    foreach($checklist->checklistitems as $item) {
                        //dd($item);
                        $detailedItem = \App\Models\ItemStock::with('item', 'invoice_item', 'invoice_item.measure_unit')->find($item->item_stock_id);
                        $substation = Inventory::where('id', $checklist->inventory_id)->first()->name;
                        $html.= '<tr nobr="true">
                        <td style="text-align: center;">'. $detailedItem->item->name .'</td>
                        <td style="text-align: center;">'. $detailedItem->invoice_item->measure_unit->name .'</td>
                        <td style="text-align: center;">'. $item->quantity .'</td>
                        <td style="text-align: center;">'. $detailedItem->invoice_item->price .'</td>
                        <td style="text-align: center;">'. $detailedItem->invoice_item->tva .'</td>
                        <td style="text-align: center;">'. $detailedItem->invoice_item->tva_price * $item->quantity .'</td>
                        <td style="text-align: center;">'. $detailedItem->invoice_item->lot .'</td>
                        <td style="text-align: center;">'. date("d-m-Y", strtotime($detailedItem->invoice_item->exp_date)) .'</td>
                        <td style="text-align: center;">'. $checklist->ambulance->license_plate .'</td>
                        <td style="text-align: center;">'. $checklist->patient_number .'</td>
                        <td style="text-align: center;">'. $substation .'</td>
                    </tr>';
                    $total_value += $detailedItem->invoice_item->tva_price * $item->quantity;
                    ChecklistItem::where('id', $item['id'])
                        ->update(['used' => 1]);
                    
                        $consumItem = new \App\Models\ConsumptionItem();
                        $consumItem->consumption_id = $consumption->id;
                        $consumItem->item_id = $item['item_id'];
                        $consumItem->item_stock_id = $item['item_stock_id'];
                        $consumItem->quantity = $item['quantity'];
                        $consumItem->save();
                    }
                }

            // Checklist::where('id', $checklist->id)
            // ->update(['used' => 1]);
            
        }

        // foreach($checklists as $checklist)
        // {

        //     // if($checklist->used == 1) {
        //     //     // return redirect('/operatiuni/bon-consum-ambulante')
        //     //     //     ->with('error', 'Generare bon de consum esuat! Cauze posibile: nu exista checklist pentru ambulanta/medicul respectiv');
        //     //     continue;
        //     // }
            
        //     if( $checklist->checklistitems->isEmpty() ) {
        //         continue;
        //     }

        //     if($i == 1) {
        //         $consumption = new \App\Models\Consumption();
        //         $consumption->inventory_id = $substation_id;
        //         $consumption->medic_id = $checklist->medic->id ?? null;
        //         $consumption->ambulance_id = $checklist->ambulance->id;
        //         $consumption->patient_number = $checklist->patient_number;
        //         $consumption->tour = $checklist->tour;
        //         $consumption->document_date = $request->input('document-date');
        //         $consumption->save();
        //     }

        //     $i++;

        //     // $consumption = new \App\Models\Consumption();
        //     // $consumption->inventory_id = $checklist->inventory->id;
        //     // $consumption->medic_id = $checklist->medic->id ?? null;
        //     // $consumption->ambulance_id = $checklist->ambulance->id;
        //     // $consumption->patient_number = $checklist->patient_number;
        //     // $consumption->tour = $checklist->tour;
        //     // $consumption->document_date = $request->input('document-date');
        //     // $consumption->save();

            
        //     $total_quantity = 0;

            

        //     foreach($checklist->checklistitems as $item)
        //     {
        //         $total_quantity = 0;
        //         // if($item->used == 1) {
        //         //     continue;
        //         // }
        //         if(!empty( $amb_id )) {
        //             if (in_array($item->item_stock_id, $skippingId)) {
        //                 $item->used = 1;
        //                 $item->save();
        //                 continue;
        //             }
        //         }
                
        //         $checklist_items = "";

        //         $detailedItem = \App\Models\ItemStock::with('item', 'invoice_item', 'invoice_item.measure_unit')->find($item->item_stock_id);

        //         if(!empty($amb_id)) {
        //             $checklist_items = \App\Models\ChecklistItem::leftjoin('checklists', 'checklist_items.checklist_id', '=', 'checklists.id')
        //             ->where('checklists.ambulance_id', '=', $amb_id)
        //             ->where('checklist_items.item_stock_id', '=', $item->item_stock_id)
        //             ->where('checklist_items.used', '=', 0)
        //             ->where('checklists.used', '=', 0)
        //             ->whereIn('checklists.id', $subset)
        //             ->select('checklist_items.used', 'checklist_items.quantity', 'checklists.id', 'checklist_items.id as cid',
        //             'checklist_items.item_id', 'checklist_items.item_stock_id')
        //             ->get();
        //             //dd($checklist_items);
        //         } else {
        //             $checklist_items = \App\Models\ChecklistItem::join('checklists', 'checklist_items.checklist_id', '=', 'checklists.id')
        //             ->where('checklists.medic_id', '=', $med_id)
        //             ->where('checklist_items.item_stock_id', '=', $item->item_stock_id)
        //             ->where('checklist_items.used', '=', 0)
        //             ->where('checklists.used', '=', 0)
        //             ->where('checklists.id', '=', $checklist->id)
        //             ->select('checklist_items.used', 'checklist_items.quantity', 'checklists.id', 'checklist_items.id as cid',
        //             'checklist_items.item_id', 'checklist_items.item_stock_id')
        //             ->get();
        //         }

        //         $skippingId[] = $item->item_stock_id;

        //         //$checklist_items = \App\Models\Checklist::with('checklistitems')->where()->get();

        //         //dd($checklist_items);

        //         foreach($checklist_items as $checklist_item) {
        //             //dd($checklist_item);
        //             $total_quantity += $checklist_item->quantity;
        //             $checklist_item->used = 1;
        //             $checklist_item->save();

        //             $consumItem = new \App\Models\ConsumptionItem();
        //             $consumItem->consumption_id = $consumption->id;
        //             $consumItem->item_id = $checklist_item->item_id;
        //             $consumItem->item_stock_id = $checklist_item->item_stock_id;
        //             $consumItem->quantity = $checklist_item->quantity;
        //             $consumItem->save();
        //             //$skippingId[] = $checklist_item->cid;
        //             //dd($checklist_item);
        //         }

        //         //dd($total_quantity);
                
        //         // $consumItem = new \App\Models\ConsumptionItem();
        //         // $consumItem->consumption_id = $consumption->id;
        //         // $consumItem->item_id = $item->item_id;
        //         // $consumItem->item_stock_id = $item->item_stock_id;
        //         // $consumItem->quantity = $item->quantity;
        //         // $consumItem->save();
        //         //generez document

        //         $item->used = 1;
        //         $item->save();

        //         if(empty( $amb_id )) {
        //             $substation = Inventory::where('id', $checklist->inventory_id)->first()->name;
        //             $html.= '<tr nobr="true">
        //             <td style="text-align: center;">'. $detailedItem->item->name .'</td>
        //             <td style="text-align: center;">'. $detailedItem->invoice_item->measure_unit->name .'</td>
        //             <td style="text-align: center;">'. $item->quantity .'</td>
        //             <td style="text-align: center;">'. $detailedItem->invoice_item->price .'</td>
        //             <td style="text-align: center;">'. $detailedItem->invoice_item->price * $item->quantity .'</td>
        //             <td style="text-align: center;">'. $detailedItem->invoice_item->lot .'</td>
        //             <td style="text-align: center;">'. date("d-m-Y", strtotime($detailedItem->invoice_item->exp_date)) .'</td>
        //             <td style="text-align: center;">'. $checklist->ambulance->license_plate .'</td>
        //             <td style="text-align: center;">'. $checklist->patient_number .'</td>
        //             <td style="text-align: center;">'. $substation .'</td>
        //         </tr>';
        //         $total_value += $detailedItem->invoice_item->price * $item->quantity;
        //         } else {
        //             $html.= '<tr nobr="true">
        //             <td style="text-align: center;">'. $detailedItem->item->name .'</td>
        //             <td style="text-align: center;">'. $detailedItem->invoice_item->measure_unit->name .'</td>
        //             <td style="text-align: center;">'. $total_quantity .'</td>
        //             <td style="text-align: center;">'. $detailedItem->invoice_item->price .'</td>
        //             <td style="text-align: center;">'. $detailedItem->invoice_item->price * $total_quantity .'</td>
        //             <td style="text-align: center;">'. $detailedItem->invoice_item->lot .'</td>
        //             <td style="text-align: center;">'. date("d-m-Y", strtotime($detailedItem->invoice_item->exp_date)) .'</td>
        //         </tr>';
        //         $total_value += $detailedItem->invoice_item->price * $total_quantity;
        //         }


            
        //     }
            
        //     //delete checklist here and checklist items
        //     //aa, cred ca am inteles. deci asta inseamna ca pot vedea bonul de consum mereu ca practic preia informatiile din consum, nu din checklist, asa-i?
        //     // da. tu estrgi din checklist pt ca daca le-ai lasa acolo as putea sa fac acelasi checklist de N ori. Odata ce consumul a fost efectuat, stergi din checklist si toate informatiile se nuta in tabelele de consum
        //     ///continua ce voiai sa zici
        //     //aici de ce trebuie sterse astea? ca practic dupa nu se mai poate face bonul de consum, nu?
        //     //tu adaugi din checklist in consum. ticketul de consum il faci dupa astea
        //     //asta stiu. dar spre ex, daca le sterg si vreau sa vad iar acelasi bon de consum, nu-l mai pot vedea inca o data pt ca sunt sterse alea si nu stie de unde sa ia datele, la ast
        //     // man. tu stergi din checklist si adaugi in consum. o sa le ai pe toate in consum.
        // }

        $total = 0;

        if(isset($medic_checklists)) {
            $html .= '</table><br><br>';
            $html .= 'Total valoare: '. $total_value .'';
        } else if(isset($ambulance_checklists)) {
            foreach($categories as $category) {
                $html .= '<span>Total Valoare '. $category->name .': '. $total_values[$category->id-1] .'</span>';
                $html .= '<br>';
                $total += $total_values[$category->id-1];
            }
            $html .= '<span>Valoare totala: '. $total .'</span>';
            $html .= '<br>';
        }

        $html .= '<br><br>Asistenti:<br>';

        $assistents = array();

        if(isset($ambulance_checklists)) {
            foreach($ambulance_checklists->first()->checklist as $checklist) {
                $assistent = Checklist::leftjoin('assistents', 'assistents.id', '=', 'checklists.assistent_id')
                ->where('checklists.id', $checklist->id)
                ->select('assistents.name')
                ->first();

                if($checklist->used == 1) {
                    continue;
                }

                if($assistent != '') {
                    if (in_array($assistent->name, $assistents) == false) {
                        array_push($assistents, $assistent->name);
                        $html .= '
                        <span style="font-weight: bold;">'. $assistent->name .'</span>
                        <br>';
                    }
            }

                // Checklist::where('id', $checklist->id)
                // ->update(['used' => 1]);
                
            }
        } else if(isset($medic_checklists)) {
            foreach($medic_checklists->first()->checklist as $checklist) {
                foreach($medic_checklists->first()->checklist as $checklist) {
                    $assistent = Checklist::leftjoin('assistents', 'assistents.id', '=', 'checklists.assistent_id')
                    ->where('checklists.id', $checklist->id)
                    ->select('assistents.name')
                    ->first();
    
                    if($checklist->used == 1) {
                        continue;
                    }
    
                    if($assistent != '') {
                        if (in_array($assistent->name, $assistents) == false) {
                            array_push($assistents, $assistent->name);
                            $html .= '
                            <span style="font-weight: bold;">'. $assistent->name .'</span>
                            <br>';
                        }
                }
    
                    // Checklist::where('id', $checklist->id)
                    // ->update(['used' => 1]);
                    
                }
            }
        }

        // foreach($checklists as $checklist)
        // {
        //     //$detailedChecklist = \App\Models\Checklist::with('assistent')->find($checklist->assistent_id);

        //     if($checklist->used == 1) {
        //         continue;
        //     }

        //     $detailedChecklist = \App\Models\Checklist::with('assistent', 'ambulancier')->find($checklist->id);
        //     $assistent = $detailedChecklist->assistent->name ?? '';
        //     //dd($detailedChecklist);
            
        //     if($assistent != '') {
        //         if (in_array($assistent, $assistents) == false) {
        //             array_push($assistents, $assistent);
        //             $html .= '
        //             <span style="font-weight: bold;">'. $assistent .'</span>
        //             <br>';
        //         }
                
        //     }
            
        // }

        $html .= '<br><br>Ambulantieri:<br>';

        $ambulanciers = array();

        if(isset($ambulance_checklists)) {
            foreach($ambulance_checklists->first()->checklist as $checklist) {
                $ambulancier = Checklist::leftjoin('ambulanciers', 'ambulanciers.id', '=', 'checklists.ambulancier_id')
                ->where('checklists.id', $checklist->id)
                ->select('ambulanciers.name')
                ->first();

                if($checklist->used == 1) {
                    continue;
                }

                if($ambulancier != '') {
                    if (in_array($ambulancier->name, $ambulanciers) == false) {
                        array_push($ambulanciers, $ambulancier->name);
                        $html .= '
                        <span style="font-weight: bold;">'. $ambulancier->name .'</span>
                        <br>';
                    }
            }

                Checklist::where('id', $checklist->id)
                ->update(['used' => 1]);
                
            }
        } else if(isset($medic_checklists)) {
            foreach($medic_checklists->first()->checklist as $checklist) {
                foreach($medic_checklists->first()->checklist as $checklist) {
                    $ambulancier = Checklist::leftjoin('ambulanciers', 'ambulanciers.id', '=', 'checklists.ambulancier_id')
                    ->where('checklists.id', $checklist->id)
                    ->select('ambulanciers.name')
                    ->first();
    
                    if($checklist->used == 1) {
                        continue;
                    }
    
                    if($ambulancier != '') {
                        if (in_array($ambulancier->name, $ambulanciers) == false) {
                            array_push($ambulanciers, $ambulancier->name);
                            $html .= '
                            <span style="font-weight: bold;">'. $ambulancier->name .'</span>
                            <br>';
                        }
                }
    
                    Checklist::where('id', $checklist->id)
                    ->update(['used' => 1]);
                    
                }
            }
        }

        // foreach($checklists as $checklist)
        // {

        //     if($checklist->used == 1) {
        //         continue;
        //     }
        //     //$detailedChecklist = \App\Models\Checklist::with('assistent')->find($checklist->assistent_id);

        //     $detailedChecklist = \App\Models\Checklist::with('assistent', 'ambulancier')->find($checklist->id);
        //     $ambulancier = $detailedChecklist->ambulancier->name ?? '';
        //     //dd($detailedChecklist);

        //     if($ambulancier != '') {
        //         if (in_array($ambulancier, $ambulanciers) == false) {
        //             array_push($ambulanciers, $ambulancier);
        //             $html .= '
        //             <span style="font-weight: bold;">'. $ambulancier .'</span>
        //             <br>';
        //         }
                
        //     }

        //     $checklist->used = 1;
        //     $checklist->save();
            
        // }

        $html .= '<p style="text-align: right;">Intocmit Farm. Sef<br>
        '. $institution[0]->pharmacy_manager .'</p>';

        //dd($assistents);

        $html .= '<br>'; 

        $html .= '<br>'; 
        
        $html .= '<span>Gestionari</span><br>';

        $html .= '<table>';

        $html .= '<tr nobr="true">
        <th style="font-weight: bold; text-align: center;">Nume</th>
        <th style="font-weight: bold; text-align: center;">Semnatura</th>
        </tr>';

        $html .= '<tr nobr="true">
        <td style="text-align: center;">Farm. sef '. $institution[0]->pharmacy_manager .'</td>
        <td style="text-align: center;"></td>
        </tr>';

        $html .= '<tr nobr="true">
        <td style="text-align: center;">As. farm. '. $institution[0]->assistent .'</td>
        <td style="text-align: center;"></td>
        </tr>';
            
        $html .= '</table>';

        $html .= '<br>'; 

        $html .= '<br>'; 

        $html .= '<span>Responsabili Stoc 3</span><br>';

        $html .= '<table>';

        $html .= '<tr nobr="true">
            <th style="font-weight: bold; text-align: center;">Nume</th>
            <th style="font-weight: bold; text-align: center;">Semnatura</th>
        </tr>';

        foreach($staff as $person) {
            if($person['inventory_id'] == 2) {
                $html .= '<tr nobr="true">
                            <td style="text-align: center;">'. $person['name'] .'</td>
                            <td style="text-align: center;"></td>
                    </tr>';
            }
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

        PDF::SetTitle('Consum');
        PDF::AddPage('P', 'A4');
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
