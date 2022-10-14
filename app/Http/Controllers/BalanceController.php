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
use \App\Models\ChecklistItem;
use \App\Models\ConsumptionItem;
use \App\Models\ReturningItem;
use Session;
use PDF;
use Auth;
use DB;


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

        $total_initial = 0;
        $total_ins = 0;
        $total_outs = 0;
        $total_sold = 0;

        $inventory_id = $request->input('inventory-select');
        $inventory_name = Inventory::where('id', '=', $inventory_id)->first()->name;

        $category_id = $request->input('category-select');
        $category_name = Category::where('id', '=', $category_id)->first()->name;

        $old_from_date = $request->input('from-date');
        $new_from_date = date("d-m-Y", strtotime($old_from_date)); 
        $old_until_date = $request->input('until-date');
        $new_until_date = date("d-m-Y", strtotime($old_until_date));

        $old_from_date_interval = date("Y-m-d", strtotime('-1 day', strtotime($old_from_date)));
        
        $after_from = date("Y-m-d", strtotime('+1 day', strtotime($old_from_date))); 

        $institution = Institution::all();

        $products = Item::where('category_id', $category_id)->get();

        $subset = $products->map(function ($product) {
            return collect($product->toArray())
                ->only(['id'])
                ->all();
        });

        $entries = "";

        $today = date('Y-m-d');

        if($inventory_id == 1) {

            $in_items = ItemStock::whereHas('invoice_item.invoice', function ($query) use($old_from_date, $old_until_date, $inventory_id, $old_from_date_interval, $subset) {
                $query->where('document_date', '>=', $old_from_date);
                $query->where('document_date', '<=', $old_until_date);
               // $query->where('to_inventory_id', $inventory_id);
            })
            ->with(['invoice_item.invoice' => function($query) use($old_from_date, $old_until_date, $inventory_id, $old_from_date_interval, $subset) {
                $query->where('document_date', '>=', $old_from_date);
                $query->where('document_date', '<=', $old_until_date);
                //$query->where('to_inventory_id', $inventory_id);
            }
            ,'invoice_item' => function($query) use($old_until_date, $inventory_id, $old_from_date_interval, $subset) {
                // $query->whereIn('item_id', $subset);
                //$query->where('to_inventory_id', $inventory_id);
            }])
            ->whereIn('item_id', $subset)
            ->where('inventory_id', $inventory_id)
            // ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            // ->join('item_stocks', 'item_stocks.id', '=', 'invoice_items.item_stock_id')
            // ->select('item_stock_id', InvoiceItem::raw('sum(invoice_items.quantity) as total_quantity'), 'invoices.document_date'
            // , 'item_stocks.invoice_item_id')->groupBy('item_stock_id')
            ->get();

            $no_action_items = ItemStock::whereDoesntHave('transfer_item.transfer', function ($query) use($old_from_date, $old_until_date, $inventory_id, $old_from_date_interval, $subset) {
                $query->where('document_date', '>=', $old_from_date);
                $query->where('document_date', '<=', $old_until_date);
               // $query->where('to_inventory_id', $inventory_id);
            })
            ->whereDoesntHave('returning_item.returning', function ($query) use($old_from_date, $old_until_date, $inventory_id, $old_from_date_interval, $subset) {
                $query->where('document_date', '>=', $old_from_date);
                $query->where('document_date', '<=', $old_until_date);
               // $query->where('to_inventory_id', $inventory_id);
            })
            ->whereIn('item_id', $subset)
            ->where('inventory_id', $inventory_id)
            ->where('quantity', '!=', 0)
            ->get();

            //dd($no_action_items);

            //dd($in_items);

            $out_items = TransferItem::whereHas('transfer', function ($query) use($old_from_date, $old_until_date, $inventory_id, $old_from_date_interval, $subset) {
                $query->where('document_date', '>=', $old_from_date);
                $query->where('document_date', '<=', $old_until_date);
                $query->where('from_inventory_id', $inventory_id);
            })
            ->with(['transfer' => function($query) use($old_from_date, $old_until_date, $inventory_id, $old_from_date_interval) {
                $query->where('document_date', '>=', $old_from_date);
                $query->where('document_date', '<=', $old_until_date);
                $query->where('from_inventory_id', $inventory_id);
            }
            ])
            ->whereIn('transfer_items.item_id', $subset)
            ->join('transfers', 'transfers.id', '=', 'transfer_items.transfer_id')
            ->join('item_stocks', 'item_stocks.id', '=', 'transfer_items.item_stock_id')
            ->join('invoice_items', 'invoice_items.id', '=', 'item_stocks.invoice_item_id')
            ->select('item_stock_id', TransferItem::raw('sum(transfer_items.quantity) as total_quantity'), 'transfers.document_date',
            'invoice_items.id as invoice_item_id')
            ->groupBy('item_stock_id')
            ->get();

            //dd($out_items);

            //dd($old_from_date_interval + 1);

            //dd($out_items);

            // $in_items = TransferItem::whereHas('transfer', function ($query) use($old_until_date, $inventory_id, $old_from_date_interval, $subset) {
            //     $query->where('document_date', '>=', $old_from_date_interval);
            //     $query->where('document_date', '<=', $old_until_date);
            //     $query->where('from_inventory_id', $inventory_id);
            // })
            // ->whereIn('item_id', $subset)
            // ->get();

            //dd($in_items);

            $details = [];

            // $details[$item->item_stock_id] = array(
            // 'name' => $item_stock->item->name,
            // 'um' => $item_stock->invoice_item->measure_unit->name,
            // 'date' => $item->document_date,
            // 'price' => $item_stock->invoice_item->price,
            // 'initial' => $current->quantity + $quantity,
            // 'ins' => $item->total_quantity,
            // 'outs' => 0,
            // 'final' => 0,
            // 'sold' => 0);

            foreach($in_items as $item) {
                //store name, document_date, measure_unit, price, ins
                $detailed_item = ItemStock::with('invoice_item', 'invoice_item.invoice', 'item', 'invoice_item.measure_unit')
                ->where('id', $item->id)
                ->first();

                $returned_quantity = ReturningItem::whereHas('returning', function ($query) use($old_until_date, $old_from_date, $inventory_id, $subset) {
                    $query->where('document_date', '<=', $old_until_date);
                    $query->where('document_date', '>=', $old_from_date);
                    $query->where('inventory_id', $inventory_id);
                })
                ->with(['returning' => function($query) use($old_until_date, $inventory_id, $old_from_date) {
                    $query->where('document_date', '<=', $old_until_date);
                    $query->where('document_date', '>=', $old_from_date);
                    $query->where('inventory_id', $inventory_id);
                }
                ])
                ->where('item_stock_id', $item->id)
                ->sum('quantity');

                //store outs for the ins (calculating them)
                $transfered_to = TransferItem::whereHas('transfer', function ($query) use($old_until_date, $inventory_id, $old_from_date_interval, $subset) {
                    $query->where('document_date', '>=', $old_from_date_interval);
                    $query->where('document_date', '<=', $old_until_date);
                    $query->where('from_inventory_id', $inventory_id);
                })
                ->with(['transfer' => function($query) use($old_until_date, $inventory_id, $old_from_date_interval) {
                    $query->where('document_date', '>=', $old_from_date_interval);
                    $query->where('document_date', '<=', $old_until_date);
                    $query->where('from_inventory_id', $inventory_id);
                }
                ])
                ->where('item_stock_id', $item->id)
                ->sum('quantity');

                //dd($transfered_to);

                //initial is 0 because they just entered in pharmacy (if substations: calculating it -> invoice_item_quantity - transfered_quantity of the item_stock_id)

                //final is (initial + ins) - outs

                //sold is price * final
                //dd($item);

                if(!isset($details[$item->id])) {
                    $details[$item->id] = array(
                        'name' => $detailed_item->item->name,
                        'um' => $detailed_item->invoice_item->measure_unit->name,
                        'date' => $item->invoice_item->invoice->document_date,
                        'price' => $item->invoice_item->price,
                        'initial' => 0,
                        'ins' => $item->invoice_item->quantity,
                        'outs' => $transfered_to + $returned_quantity,
                        'final' => (0 + $item->invoice_item->quantity) - $transfered_to - $returned_quantity,
                        'sold' => $item->invoice_item->price * ((0 + $item->invoice_item->quantity) - $transfered_to - $returned_quantity));
                }

                //dd($detailed_item->invoice_item);

                $total_initial += 0 * $detailed_item->invoice_item->tva_price;
                $total_ins += ($item->invoice_item->quantity * $detailed_item->invoice_item->tva_price);
                $total_outs += (($transfered_to + $returned_quantity) * $detailed_item->invoice_item->tva_price);
                $total_sold += ($item->invoice_item->tva_price * ((0 + $item->invoice_item->quantity) - $transfered_to - $returned_quantity));
                //dd($details);
            }

            //dd($out_items);

            foreach($out_items as $item) {
                // if($item->item_stock_id = 2351) {
                //     dd($item);
                // }
                //store name, document_date, measure_unit, price, outs, (ins = 0)
                $detailed_item = ItemStock::with('invoice_item', 'invoice_item.invoice', 'item', 'invoice_item.measure_unit')
                ->where('id', $item->item_stock_id)
                ->first();

                //dd($item);

                $returned_quantity = ReturningItem::whereHas('returning', function ($query) use($old_until_date, $old_from_date, $inventory_id, $subset) {
                    $query->where('document_date', '<=', $old_until_date);
                    $query->where('document_date', '>=', $old_from_date);
                    $query->where('inventory_id', $inventory_id);
                })
                ->with(['returning' => function($query) use($old_until_date, $inventory_id, $old_from_date) {
                    $query->where('document_date', '<=', $old_until_date);
                    $query->where('document_date', '>=', $old_from_date);
                    $query->where('inventory_id', $inventory_id);
                }
                ])
                ->where('item_stock_id', $item->item_stock_id)
                ->sum('quantity');

                $returned_initial = ReturningItem::whereHas('returning', function ($query) use($old_until_date, $old_from_date_interval, $inventory_id, $subset, $old_from_date) {
                    $query->where('document_date', '<=', $old_from_date);
                    $query->where('inventory_id', $inventory_id);
                })
                ->with(['returning' => function($query) use($old_until_date, $inventory_id, $old_from_date_interval, $old_from_date) {
                    $query->where('document_date', '<=', $old_from_date);
                    $query->where('inventory_id', $inventory_id);
                }
                ])
                ->where('item_stock_id', $item->item_stock_id)
                ->sum('quantity');

                //dd($returned_initial);

                //initial (calculating it -> invoice_item_quantity - transfered_quantity of the item_stock_id)
                //invoice_item_quantity
                $invoice_item_quantity = InvoiceItem::with(['invoice' => function($query) use($old_until_date, $inventory_id, $old_from_date_interval) {
     
                    }
                ])
                ->where('id', $item->invoice_item_id)
                ->first();

                // if($item->item_stock_id == 2341) {
                //     dd($invoice_item_quantity);
                // }

                //dd($invoice_item_quantity);

                

                //transfered_quantity
                $transfered_quantity = TransferItem::whereHas('transfer', function($query) use($inventory_id, $old_from_date_interval, $invoice_item_quantity, $old_from_date) {
                    $query->where('from_inventory_id', $inventory_id);
                    $query->whereBetween('document_date', [$invoice_item_quantity->invoice->document_date, $old_from_date_interval]);
                }
            )
                ->with(['transfer' => function($query) use($inventory_id, $old_from_date_interval, $invoice_item_quantity, $old_from_date) {
                    $query->where('from_inventory_id', $inventory_id);
                    $query->whereBetween('document_date', [$invoice_item_quantity->invoice->document_date, $old_from_date_interval]);
                }
            ])
            ->where('item_stock_id', $item->item_stock_id)
            ->sum('quantity');

            // if($item->item_stock_id == 2341) {
            //     dd($transfered_quantity);
            // }

            // if($item->item_stock_id == 3112) {
            //     dd($transfered_quantity);
            // }

            //dd($item);

            // if($item->item_stock_id == 2340) {
            //     dd($transfered_quantity);
            // }

                //final is (initial + ins) - outs

                //sold is price * final

                // if($item->item_stock_id == 2351) {
                //     dd($transfered_quantity);
                // }

                if(!isset($details[$item->item_stock_id])) {
                    $details[$item->item_stock_id] = array(
                        'name' => $detailed_item->item->name,
                        'um' => $detailed_item->invoice_item->measure_unit->name,
                        'date' => $detailed_item->invoice_item->invoice->document_date,
                        'price' => $detailed_item->invoice_item->price,
                        'initial' => $invoice_item_quantity->quantity - $transfered_quantity - $returned_initial,
                        'ins' => 0,
                        'outs' => $item->total_quantity + $returned_quantity,
                        'final' => ($invoice_item_quantity->quantity - $transfered_quantity + 0) - $item->total_quantity - $returned_quantity,
                        'sold' => $detailed_item->invoice_item->price * (($invoice_item_quantity->quantity - $transfered_quantity - $returned_quantity + 0) - $item->total_quantity));
                    
                }

                $total_initial += (($invoice_item_quantity->quantity - $transfered_quantity - $returned_initial) * $detailed_item->invoice_item->tva_price);
                $total_ins += 0 * $detailed_item->invoice_item->tva_price;
                $total_outs += (($item->total_quantity + $returned_quantity) * $detailed_item->invoice_item->tva_price);
                $total_sold += ($detailed_item->invoice_item->tva_price * (($invoice_item_quantity->quantity - $transfered_quantity - $returned_quantity + 0) - $item->total_quantity));
                //dd($total_sold);
                // if($item->item_stock_id == 2339) {
                //     dd($detailed_item->invoice_item->price * (($invoice_item_quantity->quantity - $transfered_quantity + 0) - $item->total_quantity));
                // }
                
                
            }

            foreach($no_action_items as $item) {
                //store name, document_date, measure_unit, price, outs, (ins = 0)
                $detailed_item = ItemStock::with('invoice_item', 'invoice_item.invoice', 'item', 'invoice_item.measure_unit')
                ->where('id', $item->id)
                ->first();

                //dd($item);

                //dd($detailed_item);

                //dd($details);

                if(!isset($details[$item->id])) {
                    $details[$item->id] = array(
                        'name' => $detailed_item->item->name,
                        'um' => $detailed_item->invoice_item->measure_unit->name,
                        'date' => $detailed_item->invoice_item->invoice->document_date,
                        'price' => $detailed_item->invoice_item->price,
                        'initial' => $item->quantity,
                        'ins' => 0,
                        'outs' => 0,
                        'final' => $item->quantity,
                        'sold' => $detailed_item->invoice_item->price * $item->quantity);
                    
                }

                $total_initial += $item->quantity * $detailed_item->invoice_item->tva_price;
                $total_ins += 0;
                $total_outs += 0;
                $total_sold += $detailed_item->invoice_item->tva_price * $item->quantity;
                //dd($total_sold);
                // if($item->item_stock_id == 2339) {
                //     dd($detailed_item->invoice_item->price * (($invoice_item_quantity->quantity - $transfered_quantity + 0) - $item->total_quantity));
                // }
                
                
            }
            //dd($details);

            
    

            //     $t_items_between = TransferItem::whereHas('transfer', function ($query) use($old_until_date, $inventory_id, $old_from_date_interval) {
            //     $query->where('document_date', '>=', $old_from_date_interval);
            //     $query->where('document_date', '<=', $old_until_date);
            //     $query->where('to_inventory_id', $inventory_id);
            // })
            // ->with(['transfer' => function($query) use($old_until_date, $inventory_id, $old_from_date_interval) {
            //     $query->where('document_date', '>=', $old_from_date_interval);
            //     $query->where('document_date', '<=', $old_until_date);
            //     $query->where('to_inventory_id', $inventory_id);
            // }
            // ])
            // ->whereIn('transfer_items.item_id', $subset)
            // ->join('transfers', 'transfers.id', '=', 'transfer_items.transfer_id')
            // ->join('item_stocks', 'item_stocks.id', '=', 'transfer_items.item_stock_id')
            // ->select('item_stock_id', TransferItem::raw('sum(transfer_items.quantity) as total_quantity'), 'transfers.document_date'
            // , 'item_stocks.invoice_item_id')->groupBy('item_stock_id')
            // ->get();



            // dd($transfer_items);

            // foreach($items as $item) {
            //     dd($item->invoice_item->quantity);
            // }
            
        } else {

            $in_items = TransferItem::whereHas('transfer', function ($query) use($old_until_date, $inventory_id, $old_from_date_interval, $subset) {
                $query->where('document_date', '>=', $old_from_date_interval);
                $query->where('document_date', '<=', $old_until_date);
                $query->where('to_inventory_id', $inventory_id);
            })
            ->with(['transfer' => function($query) use($old_until_date, $inventory_id, $old_from_date_interval, $subset) {
                $query->where('document_date', '>=', $old_from_date_interval);
                $query->where('document_date', '<=', $old_until_date);
                $query->where('to_inventory_id', $inventory_id);
            }
            , 'item', 'item_stock', 'item_stock.invoice_item', 'item_stock.invoice_item.measure_unit'])
            ->whereIn('item_id', $subset)
            ->get();

            //dd($in_items);

            // $out_items = ChecklistItem::whereHas('checklist_pf', function ($query) use($old_until_date, $inventory_id, $old_from_date_interval, $subset) {
            //     $query->where('checklist_date', '>', $old_from_date_interval);
            //     $query->where('checklist_date', '<=', $old_until_date);
            //     $query->where('inventory_id', $inventory_id);
            // })
            // ->with(['checklist_pf' => function($query) use($old_until_date, $inventory_id, $old_from_date_interval) {
            //     $query->where('checklist_date', '>', $old_from_date_interval);
            //     $query->where('checklist_date', '<=', $old_until_date);
            //     $query->where('inventory_id', $inventory_id);
            // }
            // ])
            // ->whereIn('checklist_items.item_id', $subset)
            // ->join('checklists', 'checklists.id', '=', 'checklist_items.checklist_id')
            // ->join('item_stocks', 'item_stocks.id', '=', 'checklist_items.item_stock_id')
            // ->join('invoice_items', 'invoice_items.id', '=', 'item_stocks.invoice_item_id')
            // ->select('item_stock_id', ChecklistItem::raw('sum(checklist_items.quantity) as total_quantity'), 'checklists.checklist_date',
            // 'invoice_items.id as invoice_item_id')
            // ->groupBy('item_stock_id')
            // ->get(); ------------> GOOD. BEFORE.

            $out_items = ConsumptionItem::whereHas('consumption', function ($query) use($old_until_date, $inventory_id, $old_from_date, $subset) {
                $query->where('document_date', '>', $old_from_date);
                $query->where('document_date', '<=', $old_until_date);
                $query->where('inventory_id', $inventory_id);
            })
            ->with(['consumption' => function($query) use($old_until_date, $inventory_id, $old_from_date) {
                $query->where('document_date', '>', $old_from_date);
                $query->where('document_date', '<=', $old_until_date);
                $query->where('inventory_id', $inventory_id);
            }
            ])
            ->whereIn('consumption_items.item_id', $subset)
            ->join('consumptions', 'consumptions.id', '=', 'consumption_items.consumption_id')
            ->join('item_stocks', 'item_stocks.id', '=', 'consumption_items.item_stock_id')
            ->join('invoice_items', 'invoice_items.id', '=', 'item_stocks.invoice_item_id')
            ->select('item_stock_id', ConsumptionItem::raw('sum(consumption_items.quantity) as total_quantity'), 'consumptions.document_date',
            'invoice_items.id as invoice_item_id')
            ->groupBy('item_stock_id')
            ->get();

            //dd($subset);
            //dd($out_items);

            $details = [];

            foreach($in_items as $item) {
                //dd($item);
                //dd($item);
                //store name, document_date, measure_unit, price, ins
                $detailed_item = ItemStock::with('invoice_item', 'invoice_item.invoice', 'item', 'invoice_item.measure_unit')
                ->where('invoice_item_id', $item->item_stock->invoice_item_id)
                ->where('inventory_id', $inventory_id)
                ->first();
                //dd($out_items);

                //dd($item->item_stock->id);

                //ins value
                //dd($item->item_stock->id);
                $in = TransferItem::whereHas('transfer', function ($query) use($old_until_date, $inventory_id, $old_from_date_interval, $subset) {
                    $query->where('document_date', '>=', $old_from_date_interval);
                    $query->where('document_date', '<=', $old_until_date);
                    $query->where('to_inventory_id', $inventory_id);
                })
                ->with(['transfer' => function($query) use($old_until_date, $inventory_id, $old_from_date_interval, $subset) {
                    $query->where('document_date', '>=', $old_from_date_interval);
                    $query->where('document_date', '<=', $old_until_date);
                    $query->where('to_inventory_id', $inventory_id);
                }
                , 'item', 'item_stock', 'item_stock.invoice_item', 'item_stock.invoice_item.measure_unit'])
                ->where('item_stock_id', $item->item_stock->id)
                ->sum('quantity');

                //dd($in);

                // if($detailed_item->id == 3410) {
                //     dd($item->item_stock->id);
                // }

                //dd($in);

                //dd($detailed_item);

                // if($detailed_item->inventory_id != 2) {
                //     dd($detailed_item);
                // }

                //store outs for the ins (calculating them)
                // $checklisted = ChecklistItem::whereHas('checklist_pf', function ($query) use($old_until_date, $inventory_id, $old_from_date_interval, $subset) {
                //     $query->where('checklist_date', '>=', $old_from_date_interval);
                //     $query->where('checklist_date', '<=', $old_until_date);
                //     $query->where('inventory_id', $inventory_id);
                // })
                // ->with(['checklist_pf' => function($query) use($old_until_date, $inventory_id, $old_from_date_interval) {
                //     $query->where('checklist_date', '>=', $old_from_date_interval);
                //     $query->where('checklist_date', '<=', $old_until_date);
                //     $query->where('inventory_id', $inventory_id);
                // }
                // ])
                // ->where('item_stock_id', $detailed_item->id)
                // ->sum('quantity'); ------> GOOD. BEFORE

                $consumpted = ConsumptionItem::whereHas('consumption', function ($query) use($old_until_date, $inventory_id, $old_from_date, $subset) {
                    $query->where('document_date', '>=', $old_from_date);
                    $query->where('document_date', '<=', $old_until_date);
                    $query->where('inventory_id', $inventory_id);
                })
                ->with(['consumption' => function($query) use($old_until_date, $inventory_id, $old_from_date) {
                    $query->where('document_date', '>=', $old_from_date);
                    $query->where('document_date', '<=', $old_until_date);
                    $query->where('inventory_id', $inventory_id);
                }
                ])
                ->where('item_stock_id', $detailed_item->id)
                ->sum('quantity');

                //dd($detailed_item->id);

                $initial_transfered = TransferItem::whereHas('transfer', function ($query) use($old_until_date, $inventory_id, $old_from_date_interval, $item) {
                    if($item->transfer->document_date < $old_from_date_interval) {
                        $query->where('document_date', '>=', $item->transfer->document_date);
                        $query->where('document_date', '<=', $old_from_date_interval);
                        $query->where('to_inventory_id', $inventory_id);
                    } else {
                        $query->where('document_date', '>=', $old_from_date_interval);
                        $query->where('document_date', '<=', $item->transfer->document_date);
                        $query->where('to_inventory_id', $inventory_id);
                    }
                    
                })
                ->with(['transfer' => function($query) use($old_until_date, $inventory_id, $old_from_date_interval, $item) {
                    if($item->transfer->document_date < $old_from_date_interval) {
                        $query->where('document_date', '>=', $item->transfer->document_date);
                        $query->where('document_date', '<=', $old_from_date_interval);
                        $query->where('to_inventory_id', $inventory_id);
                    } else {
                        $query->where('document_date', '>=', $old_from_date_interval);
                        $query->where('document_date', '<=', $item->transfer->document_date);
                        $query->where('to_inventory_id', $inventory_id);
                    }
                }
                , 'item', 'item_stock', 'item_stock.invoice_item', 'item_stock.invoice_item.measure_unit'])
                ->where('item_stock_id', $item->item_stock->id)
                ->join('transfers', 'transfers.id', '=', 'transfer_items.transfer_id')
                ->join('item_stocks', 'item_stocks.id', '=', 'transfer_items.item_stock_id')
                ->join('invoice_items', 'invoice_items.id', '=', 'item_stocks.invoice_item_id')
                ->select('item_stock_id', TransferItem::raw('sum(transfer_items.quantity) as total_quantity'), 'transfers.document_date',
            'invoice_items.id as invoice_item_id')
                ->groupBy('item_stock_id')
                ->first();

                //dd($initial_transfered);

                // $checklist_initial = ChecklistItem::whereHas('checklist_pf', function ($query) use($old_until_date, $inventory_id, $old_from_date_interval, $item) {
                //     if($item->transfer->document_date < $old_from_date_interval) {
                //         $query->where('checklist_date', '>=', $item->transfer->document_date);
                //         $query->where('checklist_date', '<=', $old_from_date_interval);
                //         $query->where('inventory_id', $inventory_id);
                //     } else {
                //         $query->where('checklist_date', '>=', $old_from_date_interval);
                //         $query->where('checklist_date', '<=', $item->transfer->document_date);
                //         $query->where('inventory_id', $inventory_id);
                //     }
                // })
                // ->with(['checklist_pf' => function($query) use($old_until_date, $inventory_id, $old_from_date_interval, $item) {
                //     if($item->transfer->document_date < $old_from_date_interval) {
                //         $query->where('checklist_date', '>=', $item->transfer->document_date);
                //         $query->where('checklist_date', '<=', $old_from_date_interval);
                //         $query->where('inventory_id', $inventory_id);
                //     } else {
                //         $query->where('checklist_date', '>=', $old_from_date_interval);
                //         $query->where('checklist_date', '<=', $item->transfer->document_date);
                //         $query->where('inventory_id', $inventory_id);
                //     }
                // }
                // ])
                // ->where('item_stock_id', $detailed_item->id)
                // ->sum('quantity'); --------> GOOD. BEFORE

                $consumpted_initial = ConsumptionItem::whereHas('consumption', function ($query) use($old_until_date, $inventory_id, $old_from_date, $item) {
                    if($item->transfer->document_date < $old_from_date) {
                        $query->where('document_date', '>=', $item->transfer->document_date);
                        $query->where('document_date', '<=', $old_from_date);
                        $query->where('inventory_id', $inventory_id);
                    } else {
                        $query->where('document_date', '>=', $old_from_date);
                        $query->where('document_date', '<=', $item->transfer->document_date);
                        $query->where('inventory_id', $inventory_id);
                    }
                })
                ->with(['consumption' => function($query) use($old_until_date, $inventory_id, $old_from_date, $item) {
                    if($item->transfer->document_date < $old_from_date) {
                        $query->where('document_date', '>=', $item->transfer->document_date);
                        $query->where('document_date', '<=', $old_from_date);
                        $query->where('inventory_id', $inventory_id);
                    } else {
                        $query->where('document_date', '>=', $old_from_date);
                        $query->where('document_date', '<=', $item->transfer->document_date);
                        $query->where('inventory_id', $inventory_id);
                    }
                }
                ])
                ->where('item_stock_id', $detailed_item->id)
                ->sum('quantity');

               // dd($checklist_initial);

                //dd($item);

                //dd($initial_transfered??0);

                //initial is 0 because they just entered in pharmacy (if substations: calculating it -> invoice_item_quantity - transfered_quantity of the item_stock_id)

                //final is (initial + ins) - outs

                //sold is price * final
                //dd($item);

                if(!isset($details[$detailed_item->id])) {
                    $details[$detailed_item->id] = array(
                        'name' => $detailed_item->item->name,
                        'um' => $detailed_item->invoice_item->measure_unit->name,
                        'date' => $item->transfer->document_date,
                        'price' => $item->item_stock->invoice_item->price,
                        'initial' => $initial_transfered->total_quantity - $consumpted_initial,
                        'ins' => $in,
                        'outs' => $consumpted,
                        'final' => ($in) - $consumpted,
                        'sold' => $item->item_stock->invoice_item->price * (($initial_transfered->total_quantity - $consumpted_initial + $in) - $consumpted));
                    
                }
                //dd($details);
            }

            foreach($out_items as $item) {
                //store name, document_date, measure_unit, price, outs, (ins = 0)
                $detailed_item = ItemStock::with('invoice_item', 'invoice_item.invoice', 'item', 'invoice_item.measure_unit')
                ->where('invoice_item_id', $item->item_stock->invoice_item_id)
                ->where('inventory_id', $inventory_id)
                ->first();

                //dd($item);

                //initial (calculating it -> invoice_item_quantity - transfered_quantity of the item_stock_id)
                //invoice_item_quantity
                $invoice_item_quantity = InvoiceItem::with(['invoice' => function($query) use($old_until_date, $inventory_id, $old_from_date_interval) {
     
                    }
                ])
                ->where('id', $item->invoice_item_id)
                ->first();

                

                //transfered_quantity
                $transfered_quantity = TransferItem::whereHas('transfer', function($query) use($inventory_id, $old_from_date_interval, $invoice_item_quantity) {
                    $query->where('from_inventory_id', $inventory_id);
                    $query->whereBetween('document_date', [$invoice_item_quantity->invoice->document_date, $old_from_date_interval]);
                }
            )
                ->with(['transfer' => function($query) use($inventory_id, $old_from_date_interval, $invoice_item_quantity) {
                    $query->where('from_inventory_id', $inventory_id);
                    $query->whereBetween('document_date', [$invoice_item_quantity->invoice->document_date, $old_from_date_interval]);
                }
            ])
            ->where('item_stock_id', $item->item_stock_id)
            ->sum('quantity');

            // if($item->item_stock_id == 3112) {
            //     dd($transfered_quantity);
            // }

            //dd($item);

            // if($item->item_stock_id == 2340) {
            //     dd($transfered_quantity);
            // }

                //final is (initial + ins) - outs

                //sold is price * final

                if(!isset($details[$item->item_stock_id])) {
                    $details[$item->item_stock_id] = array(
                        'name' => $detailed_item->item->name,
                        'um' => $detailed_item->invoice_item->measure_unit->name,
                        'date' => $detailed_item->invoice_item->invoice->document_date,
                        'price' => $detailed_item->invoice_item->price,
                        'initial' => $invoice_item_quantity->quantity - $transfered_quantity,
                        'ins' => 0,
                        'outs' => $item->total_quantity,
                        'final' => ($invoice_item_quantity->quantity - $transfered_quantity + 0) - $item->total_quantity,
                        'sold' => $detailed_item->invoice_item->price * (($invoice_item_quantity->quantity - $transfered_quantity + 0) - $item->total_quantity));
                    
                }

                // if($item->item_stock_id == 2339) {
                //     dd($detailed_item->invoice_item->price * (($invoice_item_quantity->quantity - $transfered_quantity + 0) - $item->total_quantity));
                // }
                
                
            }
            
        }

        //dd($details);

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

        $html .= <<<EOD
        <table style="width: 100%;">
        <thead>
        <tr nobr="true">
        <th style="height: 25px;" colspan="9">Subgestiunea: $category_name  -  $inventory_name </th>
        </tr>
        <tr nobr="true">
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
        </thead>
        EOD;

        // $total_sold = 0;
        // $initial_value = 0;
        // $ins = 0;
        // $outs = 0;

        //dd($details);

        if($inventory_id == 1) {
            foreach($details as $item) {
                $html .= '<tr nobr="true">';
                $html .= '<td style="text-align: center;">'. $item['name'] .'</td>';
                $html .= '<td style="text-align: center;">'. $item['um'] .'</td>';
                $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($item['date'])) .'</td>';
                $html .= '<td style="text-align: center;">'. $item['price'] .'</td>';
                $html .= '<td style="text-align: center;">'. $item['initial'] .'</td>';
                $html .= '<td style="text-align: center;">'. $item['ins'] .'</td>';
                $html .= '<td style="text-align: center;">'. $item['outs'] .'</td>';
                $html .= '<td style="text-align: center;">'. $item['final'] .'</td>';
                $html .= '<td style="text-align: center;">'. $item['sold'] .'</td>';
                $html .= '</tr>';
            }
           
        } else {
            foreach($details as $item) {
                $html .= '<tr nobr="true">';
                $html .= '<td style="text-align: center;">'. $item['name'] .'</td>';
                $html .= '<td style="text-align: center;">'. $item['um'] .'</td>';
                $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($item['date'])) .'</td>';
                $html .= '<td style="text-align: center;">'. $item['price'] .'</td>';
                $html .= '<td style="text-align: center;">'. $item['initial'] .'</td>';
                $html .= '<td style="text-align: center;">'. $item['ins'] .'</td>';
                $html .= '<td style="text-align: center;">'. $item['outs'] .'</td>';
                $html .= '<td style="text-align: center;">'. $item['final'] .'</td>';
                $html .= '<td style="text-align: center;">'. $item['sold'] .'</td>';
                $html .= '</tr>';
            }
            
        }


        $html .= '</table>';

        $html .= '<br>';

        $html .= '<br>';

        $html .= '<span>Total stoc initial: '. $total_initial .'</span>';

        $html .= '<br>';

        $html .= '<span>Total intrari: '. $total_ins .'</span>';

        $html .= '<br>';

        $html .= '<span>Total iesiri: '. $total_outs .'</span>';

        $html .= '<br>';

        $html .= '<span>Total sold: '. $total_sold .'</span>';

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
