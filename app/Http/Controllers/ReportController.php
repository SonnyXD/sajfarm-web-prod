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
use \App\Models\Ambulance;
use \App\Models\Returning;
use Session;
use PDF;
use Auth;
use DB;

class ReportController extends Controller
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
            'report-type' => 'required',
            'substation-select' => 'required',
            'ambulance-select' => 'required',
            'from-date' => 'required',
            'until-date' => 'required'
        ));

        ini_set('memory_limit', '-1');

        $user = Auth::user();

        $report_type = $request->input('report-type');

        $inventory_id = $request->input('substation-select');
        $inventory_name = Inventory::where('id', '=', $inventory_id)->first()->name;

        $ambulance_id = $request->input('ambulance-select');

        $ambulance = Ambulance::where('id', $ambulance_id)->first()->license_plate??'Toate ambulantele';

        $subset = 0;
        $consumption_items = 0;

        $old_from_date = $request->input('from-date');
        $new_from_date = date("d-m-Y", strtotime($old_from_date)); 
        $old_until_date = $request->input('until-date');
        $new_until_date = date("d-m-Y", strtotime($old_until_date));

        if($ambulance == 'Toate ambulantele') {
            $substation_ambulances = Ambulance::where('inventory_id', $inventory_id)->get();
            $subset = $substation_ambulances->map(function ($amb) {
                return collect($amb->toArray())
                    ->only(['id'])
                    ->all();
            });
            // $consumptions = Consumption::leftjoin('consumption_items', 'consumptions.id', '=', 'consumption_items.consumption_id')
            // ->groupBy('consumptions.id')
            // ->leftjoin('ambulances', 'ambulances.id', '=', 'consumptions.ambulance_id')
            // ->leftjoin('items', 'consumption_items.item_id', '=', 'items.id')
            // ->leftjoin('item_stocks', 'item_stocks.id', '=', 'consumption_items.item_stock_id')
            // ->leftjoin('invoice_items', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
            // ->leftjoin('measure_units', 'measure_units.id', '=', 'invoice_items.measure_unit_id')
            // ->whereIn('ambulances.id', $subset)
            // ->whereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
            // ->select('invoice_items.*', 'items.name as item_name', 'measure_units.name as um', ConsumptionItem::raw('SUM(consumption_items.quantity) as consumption_quantity'),
            // 'ambulances.license_plate as license_plate', 'items.category_id as item_cat_id')
            // ->groupBy('consumption_items.consumption_id')
            // ->groupBy('consumption_items.item_stock_id')
            // ->get();

            // $consumptions = Consumption::with(['consumption_item'  => function($query){
            //     $query->sum('quantity');
            //     $query->groupBy('consumption_items.consumption_id');
            //     $query->groupBy('consumption_items.item_stock_id');
            //  }], 'consumption_item.item_stock', 'consumption_item.item_stock.invoice_item',
            // 'consumption_item.item_stock.invoice_item.measure_unit')
            // ->whereIn('consumptions.ambulance_id', $subset)
            // ->whereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
            // ->get();

            // $consumptions = Consumption::with('consumption_items_grouped', 'consumption_items_grouped.item_stock', 'consumption_items_grouped.item_stock.invoice_item',
            // 'consumption_items_grouped.item_stock.invoice_item.measure_unit', 'ambulance')
            // ->whereIn('consumptions.ambulance_id', $subset)
            // ->whereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
            // //->groupBy('ambulance_id')
            // ->get();

            $ambulances = Ambulance::whereHas('consumptions', function ($query) use($old_from_date, $old_until_date, $subset) {
                $query->whereBetween('document_date', [$old_from_date, $old_until_date]);
                $query->whereIn('ambulance_id', $subset);
            })
            ->with('consumptions', 'consumptions.consumption_items_grouped', 'consumptions.consumption_items_grouped.item')
            ->get();

            //dd($ambulances);
            //dd($consumptions);
            
        } else {
            // $consumptions = Consumption::with('consumption_item', 'consumption_item.item_stock', 'consumption_item.item_stock.invoice_item',
            // 'consumption_item.item_stock.invoice_item.measure_unit', 'consumption_items_grouped', 'ambulance')
            // ->where('consumptions.ambulance_id', $ambulance_id)
            // ->whereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
            // //->sum('consumption_items.quantity')
            // ->get();
            $consumptions = Consumption::with('consumption_items_grouped', 'consumption_items_grouped.item_stock', 'consumption_items_grouped.item_stock.invoice_item',
            'consumption_items_grouped.item_stock.invoice_item.measure_unit', 'ambulance', 'consumption_items_grouped.item_stock.invoice_item.item')
            ->where('consumptions.ambulance_id', $ambulance_id)
            ->whereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
            //->sum('consumption_items.quantity')
            ->get();

            //dd($consumptions);

            $consumption_items = [];
            foreach($consumptions as $consumption) {
                foreach($consumption->consumption_items_grouped as $consumption_item) {
                    if(!isset($consumption_items[$consumption_item->item_stock_id])) {
                        $consumption_items[$consumption_item->item_stock_id] = array('id' => $consumption_item->id,
                         'quantity' => $consumption_item->quantity,
                         'item_stock_id' => $consumption_item->item_stock_id,
                         'item_name' => $consumption_item->item_stock->invoice_item->item->name,
                         'price' => $consumption_item->item_stock->invoice_item->price,
                         'tva' => $consumption_item->item_stock->invoice_item->tva,
                         'tva_price' => $consumption_item->item_stock->invoice_item->tva_price,
                         'um' => $consumption_item->item_stock->invoice_item->measure_unit->name,
                         'lot' => $consumption_item->item_stock->invoice_item->lot,
                         'exp_date' => $consumption_item->item_stock->invoice_item->exp_date,
                         'category_id' => $consumption_item->item_stock->invoice_item->item->category_id);
                    } else {
                        $consumption_items[$consumption_item->item_stock_id]['quantity'] += $consumption_item->quantity;
                    }
                }
                
            }

           // dd($consumption_items);
        }

        $returnings = "";
        
        
        // $grouped = $consumptions->groupBy('consumption_items.item_stock_id')->map(function ($row) {
        //     return $row->sum('consumption_items.quantity');
        //    });

           //dd($consumptions);

        //dd($consumptions[0]->consumption_items_grouped);

        //dd($consumptions[3]->consumption_item->sum('quantity'));

        $institution = Institution::all();

        // $products = Item::where('category_id', $category_id)->get();

        // $subset = $products->map(function ($product) {
        //     return collect($product->toArray())
        //         ->only(['id'])
        //         ->all();
        // });

        $type = "";

        if($report_type == 1) {
            $type = "CONSUMURI";
        } else if($report_type == 2) {
            // $returnings = Returning::with('returning_items_grouped', 'returning_items_grouped.item_stock_belongs',
            // 'returning_items_grouped.item_stock_belongs.invoice_item', 'returning_items_grouped.item_stock_belongs.invoice_item.measure_unit',
            // 'returning_items_grouped.item_stock_belongs.invoice_item.item', 'returning_items_grouped.ambulance')
            // ->where('returnings.inventory_id', $inventory_id)
            // ->whereBetween('returnings.document_date', [$old_from_date, $old_until_date])
            // ->get();
            $returnings = Returning::with('returning_item', 'returning_item.item_stock',
            'returning_item.item_stock.invoice_item', 'returning_item.item_stock.invoice_item.measure_unit',
            'returning_item.item_stock.invoice_item.item', 'returning_item.ambulance')
            ->where('returnings.inventory_id', $inventory_id)
            ->whereBetween('returnings.document_date', [$old_from_date, $old_until_date])
            ->get();

            $returning_items = [];
            foreach($returnings as $returning) {
                foreach($returning->returning_items_grouped as $returning_item) {
                    if(!isset($returning_items[$returning_item->item_stock_id])) {
                        $returning_items[$returning_item->item_stock_id] = array('id' => $returning_item->id,
                         'quantity' => $returning_item->quantity,
                         'item_stock_id' => $returning_item->item_stock_id,
                         'item_name' => $returning_item->item_stock->invoice_item->item->name,
                         'price' => $returning_item->item_stock->invoice_item->price,
                         'tva' => $returning_item->item_stock->invoice_item->tva,
                         'tva_price' => $returning_item->item_stock->invoice_item->tva_price,
                         'um' => $returning_item->item_stock->invoice_item->measure_unit->name,
                         'lot' => $returning_item->item_stock->invoice_item->lot,
                         'exp_date' => $returning_item->item_stock->invoice_item->exp_date,
                         'category_id' => $returning_item->item_stock->invoice_item->item->category_id);
                    } else {
                        $returning_items[$returning_item->item_stock_id]['quantity'] += $returning_item->quantity;
                    }
                }
                
            }

            //dd($returning_items);

            //dd($returnings);
            
            $type = "RETURURI";
        }

        $html = "<html>
                <head>
                <style>
                td, th {border: 1px solid black;}
                </style>
                </head>";
        
        $html .= ' <span style="font-weight: bold; float: left;">'. $institution[0]->name .'</span>
        <br>
        <span style="float: left;">Utilizator: '. $user->name .'</span>
        <h2 style="font-weight:bold; text-align: center;">RAPORT '. $type .'</h2>
        <br>
        <span style="float: right;">Substatie: '. $inventory_name .'</span>
        <br>';
        
        if($report_type == 1) {
            $html .= '<span style="float: right;">Ambulanta: '. $ambulance .'</span>
            <br>';
        }
        
        $html .= '<span style="float: right;">Perioada: '. $new_from_date .' - '. $new_until_date .'</span>
        <br>
        <br>
        <br>';

        $categories = Category::all();

        $table_cons = '<table>
            <tr nobr="true">
            <th style="font-weight: bold; text-align: center;">Denumire Produs</th>
            <th style="font-weight: bold; text-align: center;">UM</th>
            <th style="font-weight: bold; text-align: center;">Cantitate</th>
            <th style="font-weight: bold; text-align: center;">Pret</th>
            <th style="font-weight: bold; text-align: center;">TVA</th>
            <th style="font-weight: bold; text-align: center;">Pret TVA</th>
            <th style="font-weight: bold; text-align: center;">Valoare</th>
            <th style="font-weight: bold; text-align: center;">Lot</th>
            <th style="font-weight: bold; text-align: center;">Data expirare</th>
            </tr>';

            $table_ret = '<table>
            <tr nobr="true">
            <th style="font-weight: bold; text-align: center;">Denumire Produs</th>
            <th style="font-weight: bold; text-align: center;">UM</th>
            <th style="font-weight: bold; text-align: center;">Cantitate</th>
            <th style="font-weight: bold; text-align: center;">Pret</th>
            <th style="font-weight: bold; text-align: center;">TVA</th>
            <th style="font-weight: bold; text-align: center;">Pret TVA</th>
            <th style="font-weight: bold; text-align: center;">Valoare</th>
            <th style="font-weight: bold; text-align: center;">Lot</th>
            <th style="font-weight: bold; text-align: center;">Data expirare</th>
            <th style="font-weight: bold; text-align: center;">Motiv</th>
            <th style="font-weight: bold; text-align: center;">Gestiune de iesire</th>
            </tr>';

            $consumption_items_array = [];

            //dd($consumption_items_array);

            if($report_type == 1) {
                if($ambulance == 'Toate ambulantele') {
                    $total_values = [];
                    // foreach($consumptions as $consumption) {
                    //     $html .= '<span style="font-weight: bold;">'. $consumption->ambulance->license_plate .'</span><br><br>';
                    //     foreach($categories as $category) {
                    //         $html .= '<span style="font-weight: bold;">'. $category->name .'</span><br><br>';
                    //         $html .= $table_cons;
                    //         foreach($consumption->consumption_items_grouped as $item) {
                    //             if($category->id == $item->item_stock->invoice_item->item->category_id) {
                    //                 $html .= '<tr nobr="true">
                    //                 <td style="text-align: center;">'. $item->item_stock->invoice_item->item->name .'</td>
                    //                 <td style="text-align: center;">'. $item->item_stock->invoice_item->measure_unit->name .'</td>
                    //                 <td style="text-align: center;">'. $item->quantity .'</td>
                    //                 <td style="text-align: center;">'. $item->item_stock->invoice_item->price .'</td>
                    //                 <td style="text-align: center;">'. $item->item_stock->invoice_item->tva .'</td>
                    //                 <td style="text-align: center;">'. $item->item_stock->invoice_item->tva_price .'</td>
                    //                 <td style="text-align: center;">'. $item->quantity * $item->item_stock->invoice_item->tva_price .'</td>
                    //                 <td style="text-align: center;">'. $item->item_stock->invoice_item->lot .'</td>
                    //                 <td style="text-align: center;">'. date("d-m-Y", strtotime($item->item_stock->invoice_item->exp_date)) .'</td>
                    //             </tr>';
                    //             }
                    //         }
                    //         $html .= '</table><br><br>';
                    //     }
                    // }
                    foreach($ambulances as $ambulance) {
                         $html .= '<span style="font-weight: bold;">'. $ambulance->license_plate .'</span><br><br>';
                         foreach($categories as $category) {
                            $html .= '<span style="font-weight: bold;">'. $category->name .'</span><br><br>';
                            $html .= $table_cons;
                            foreach($ambulance->consumptions as $consumption) {
                                foreach($consumption->consumption_items_grouped as $item) {
                                    if($category->id == $item->item->category_id) {
                                        $html .= '<tr nobr="true">
                                        <td style="text-align: center;">'. $item->item_stock->invoice_item->item->name .'</td>
                                        <td style="text-align: center;">'. $item->item_stock->invoice_item->measure_unit->name .'</td>
                                        <td style="text-align: center;">'. $item->quantity .'</td>
                                        <td style="text-align: center;">'. $item->item_stock->invoice_item->price .'</td>
                                        <td style="text-align: center;">'. $item->item_stock->invoice_item->tva .'</td>
                                        <td style="text-align: center;">'. $item->item_stock->invoice_item->tva_price .'</td>
                                        <td style="text-align: center;">'. $item->quantity * $item->item_stock->invoice_item->tva_price .'</td>
                                        <td style="text-align: center;">'. $item->item_stock->invoice_item->lot .'</td>
                                        <td style="text-align: center;">'. date("d-m-Y", strtotime($item->item_stock->invoice_item->exp_date)) .'</td>
                                    </tr>';
                                    }
                                }
                                
                            }
                            $html .= '</table><br><br>';
                         }
                    }
                    //dd($total_values);
                } else {
                    $total_values = [];
                    foreach($categories as $category) {
                        $html .= '<span style="font-weight: bold;">'. $category->name .'</span><br><br>';
                        $html .= $table_cons;
                        $total = 0;
                        foreach($consumptions as $consumption) {
                            // $consumption_items_array[$consumption->id] = array(
                            // 'is_id' => 0,
                            // 'category_id' => 0
                            // );
                            foreach($consumption_items as $item) {
                               // dd($consumption_items);
                                //dd($item);
                                if($category->id == $item['category_id']) {
                                    $total += ($item['quantity'] * $item['tva_price']);
                                    $html .= '<tr nobr="true">
                                    <td style="text-align: center;">'. $item['item_name'] .'</td>
                                    <td style="text-align: center;">'. $item['um'] .'</td>
                                    <td style="text-align: center;">'. $item['quantity'] .'</td>
                                    <td style="text-align: center;">'. $item['price'] .'</td>
                                    <td style="text-align: center;">'. $item['tva'] .'</td>
                                    <td style="text-align: center;">'. $item['tva_price'] .'</td>
                                    <td style="text-align: center;">'. $item['quantity'] * $item['tva_price'] .'</td>
                                    <td style="text-align: center;">'. $item['lot'] .'</td>
                                    <td style="text-align: center;">'. date("d-m-Y", strtotime($item['exp_date'])) .'</td>
                                </tr>';
                                }
                                
                                //$consumption_items_array[$consumption->id] = $item->item_stock_id;
                                //$consumption_items_array[$consumption->id][]['category_id'] = $category->id;
                                
                            }
                            
                        }
                        $total_values[] = $total;
                        $html .= '</table><br><br>';
                        //dd($consumption_items_array);
                    }
                    foreach($categories as $category) {
                        $html .= '<span>Total Valoare '. $category->name .': '. $total_values[$category->id-1] .'</span>';
                        $html .= '<br>';
                    }
                }
            } else if($report_type == 2) {
                $total_values = [];
                $station = Inventory::where('id', $inventory_id)->first()->name;
                foreach($categories as $category) {
                    $html .= '<span style="font-weight: bold;">'. $category->name .'</span><br><br>';
                    $html .= $table_ret;
                    $total = 0;
                    foreach($returnings as $returning) {
                        foreach($returning->returning_item as $item) {
                            //dd($returnings);
                            // if($item->item_stock_belongs == null) {
                            //     continue;
                            // }
                            //dd($item);
                            $item_ambulance = Ambulance::where('id', $item->ambulance_id)->first()->license_plate??'';
                            if($category->id == $item->item_stock->invoice_item->item->category_id) {
                                $total += ($item->quantity * $item->item_stock->invoice_item->tva_price);
                                $html .= '<tr nobr="true">
                                <td style="text-align: center;">'. $item->item_stock->invoice_item->item->name .'</td>
                                <td style="text-align: center;">'. $item->item_stock->invoice_item->measure_unit->name .'</td>
                                <td style="text-align: center;">'. $item->quantity .'</td>
                                <td style="text-align: center;">'. $item->item_stock->invoice_item->price .'</td>
                                <td style="text-align: center;">'. $item->item_stock->invoice_item->tva .'</td>
                                <td style="text-align: center;">'. $item->item_stock->invoice_item->tva_price .'</td>
                                <td style="text-align: center;">'. $item->quantity * $item->item_stock->invoice_item->tva_price .'</td>
                                <td style="text-align: center;">'. $item->item_stock->invoice_item->lot .'</td>
                                <td style="text-align: center;">'. date("d-m-Y", strtotime($item->item_stock->invoice_item->exp_date)) .'</td>
                                <td style="text-align: center;">'. $item->reason .'</td>
                                <td style="text-align: center;">'. $station .' - '. $item_ambulance .'</td>
                            </tr>';
                            }
                            
                            //$consumption_items_array[$consumption->id][]['is_id'] = $item->item_stock_id;
                            //$consumption_items_array[$consumption->id][]['category_id'] = $category->id;
                            
                        }
                        
                    }
                    $total_values[] = $total;
                    $html .= '</table><br><br>';
                    //dd($consumption_items_array);
                }
                foreach($categories as $category) {
                    $html .= '<span>Total Valoare '. $category->name .': '. $total_values[$category->id-1] .'</span>';
                    $html .= '<br>';
                }
            }

            

        // foreach($categories as $category) {
        //     $html .= '<span style="font-weight: bold;">'. $category->name .'</span>';
        //     $html .= '<br>';
        //     $i = 0;
        //     foreach($consumptions as $consumption) {
        //         $html .= '<span style="font-weight: bold;">'. $consumption->license_plate .'</span>';
        //         $html .= '<br>';
        //         if($i == 0) {
        //             $html .= $table;
        //         }
        //         if($category->id == $consumption->item_cat_id) {
        //             $i++;
        //             $html .= '<tr>';
        //             $html .= '<th style="text-align: center;">'. $consumption->product_code .'</th>';
        //             $html .= '<th style="text-align: center;">'. $consumption->item_name .'</th>';
        //             $html .= '<th style="text-align: center;">'. $consumption->um .'</th>';
        //             $html .= '<th style="text-align: center;">'. $consumption->consumption_quantity .'</th>';
        //             $html .= '<th style="text-align: center;">'. $consumption->price .'</th>';
        //             $html .= '<th style="text-align: center;">'. $consumption->tva .'</th>';
        //             $html .= '<th style="text-align: center;">'. $consumption->tva_price .'</th>';
        //             $html .= '<th style="text-align: center;">'. $consumption->tva_price * $consumption->consumption_quantity .'</th>';
        //             $html .= '<th style="text-align: center;">'. $consumption->lot .'</th>';
        //             $html .= '<th style="text-align: center;">'. $consumption->exp_date .'</th>';
        //             $html .= '</tr>';
        //         }
                
                
                
        //     }
        //     $html .= '</table>';
        // }

        // $skippingAmb = array();

        // foreach($consumptions as $consumption) {
        //     if (in_array($consumption->license_plate, $skippingAmb)) {
        //             continue;
        //         }
        //     $html .= '<span style="font-weight: bold;">'. $consumption->license_plate .'</span>';
        //     $skippingAmb[] = $consumption->license_plate;
        //     $html .= '<br>';
        //     foreach($categories as $category) {
        //         $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight: bold;">'. $category->name .'</span>';
        //         $html .= '<br>';
        //     }
        // }

        // $html .= '
        // <table>
        //     <tr>
        //     <th style="font-weight: bold; text-align: center;">Cod Produs</th>
        //     <th style="font-weight: bold; text-align: center;">Denumire Produs</th>
        //     <th style="font-weight: bold; text-align: center;">UM</th>
        //     <th style="font-weight: bold; text-align: center;">Cantitate</th>
        //     <th style="font-weight: bold; text-align: center;">Pret</th>
        //     <th style="font-weight: bold; text-align: center;">TVA</th>
        //     <th style="font-weight: bold; text-align: center;">Pret TVA</th>
        //     <th style="font-weight: bold; text-align: center;">Valoare</th>
        //     <th style="font-weight: bold; text-align: center;">Lot</th>
        //     <th style="font-weight: bold; text-align: center;">Data expirare</th>
        //     </tr>';

        //$skippingId = array();

        // foreach($consumptions as $consumption) {
        //     foreach($consumption->consumption_item as $item) {
        //         if (in_array($item->itme_stock->id, $skippingId)) {
        //             continue;
        //         }
        //         $skippingId[] = $item->item_stock_id;
        //     }

        //     $html .= '<tr>';
        //     $html .= '<th style="text-align: center;">'. $item->consumption_item->consumption_id .'</th>';
        //     $html .= '<th style="text-align: center;">Nr. Crt</th>';
        //     $html .= '<th style="text-align: center;">Nr. Crt</th>';
        //     $html .= '<th style="text-align: center;">Nr. Crt</th>';
        //     $html .= '<th style="text-align: center;">Nr. Crt</th>';
        //     $html .= '<th style="text-align: center;">Nr. Crt</th>';
        //     $html .= '<th style="text-align: center;">Nr. Crt</th>';
        //     $html .= '<th style="text-align: center;">Nr. Crt</th>';
        //     $html .= '</tr>';
           
        // }

        // foreach($consumptions as $consumption) {
        //     $html .= '<tr>';
        //     $html .= '<th style="text-align: center;">'. $consumption->product_code .'</th>';
        //     $html .= '<th style="text-align: center;">'. $consumption->item_name .'</th>';
        //     $html .= '<th style="text-align: center;">'. $consumption->um .'</th>';
        //     $html .= '<th style="text-align: center;">'. $consumption->consumption_quantity .'</th>';
        //     $html .= '<th style="text-align: center;">'. $consumption->price .'</th>';
        //     $html .= '<th style="text-align: center;">'. $consumption->tva .'</th>';
        //     $html .= '<th style="text-align: center;">'. $consumption->tva_price .'</th>';
        //     $html .= '<th style="text-align: center;">'. $consumption->tva_price * $consumption->consumption_quantity .'</th>';
        //     $html .= '<th style="text-align: center;">'. $consumption->lot .'</th>';
        //     $html .= '<th style="text-align: center;">'. $consumption->exp_date .'</th>';
        //     $html .= '</tr>';
        // }

        //$html .= '</table>';

        $html .= '</html>';

        PDF::setFooterCallback(function($pdf) {

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 10);
            // Page number
            $pdf->Cell(0, 10, 'Pagina '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    
    });

        PDF::SetTitle('Raport');
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
