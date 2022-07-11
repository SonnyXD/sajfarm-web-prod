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

        $inventory_id = $request->input('inventory-select');
        $inventory_name = Inventory::where('id', '=', $inventory_id)->first()->name;

        $category_id = $request->input('category-select');
        $category_name = Category::where('id', '=', $category_id)->first()->name;

        $old_from_date = $request->input('from-date');
        $new_from_date = date("d-m-Y", strtotime($old_from_date)); 
        $old_until_date = $request->input('until-date');
        $new_until_date = date("d-m-Y", strtotime($old_until_date));

        $old_from_date_interval = date("Y-m-d", strtotime('-1 day', strtotime($old_from_date))); 

        $institution = Institution::all();

        $products = Item::where('category_id', $category_id)->get();

        $subset = $products->map(function ($product) {
            return collect($product->toArray())
                ->only(['id'])
                ->all();
        });

        // $entries = InvoiceItem::leftjoin('item_stocks', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
        // ->leftjoin('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
        // ->leftjoin('items', 'invoice_items.item_id', '=', 'items.id')
        // ->leftjoin('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
        // ->leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
        // ->leftjoin('transfer_items', 'transfer_items.item_stock_id', '=', 'item_stocks.id')
        // ->leftjoin('transfers', 'transfers.id', '=', 'transfer_items.transfer_id')
        // ->leftjoin('consumption_items', 'consumption_items.item_stock_id', '=', 'item_stocks.id')
        // ->leftjoin('consumptions', 'consumptions.id', '=', 'consumption_items.consumption_id')
        // ->where('inventories.id', '=', $inventory_id)
        // ->whereIn('invoice_items.item_id', $subset)
        // ->whereBetween('invoices.document_date', [$old_from_date, $old_until_date])
        // ->select('invoices.id as invoice_id', 'invoice_items.*',
        // 'item_stocks.quantity as remaining_quantity', 'item_stocks.id as item_stock_id',
        // ItemStock::raw('SUM(item_stocks.quantity) as used_quantity'), 'items.name as item_name',
        // 'measure_units.name as um', 'invoices.document_date as document_date',
        // 'transfers.id as transfer_id', 'transfer_items.quantity as transfer_quantity',
        // 'consumptions.id as consumption_id', 'consumption_items.quantity as consumption_quantity')
        // ->groupBy('item_stocks.inventory_id')
        // ->groupBy('invoice_items.id')
        // ->get();

        // $entries = ItemStock::leftjoin('invoice_items', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
        // ->leftjoin('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
        // ->leftjoin('items', 'invoice_items.item_id', '=', 'items.id')
        // ->leftjoin('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
        // ->leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
        // ->leftjoin('transfer_items', 'transfer_items.item_stock_id', '=', 'item_stocks.id')
        // ->leftjoin('transfers', 'transfers.id', '=', 'transfer_items.transfer_id')
        // ->leftjoin('consumption_items', 'consumption_items.item_stock_id', '=', 'item_stocks.id')
        // ->leftjoin('consumptions', 'consumptions.id', '=', 'consumption_items.consumption_id')
        // ->where('inventories.id', '=', $inventory_id)
        // ->whereIn('invoice_items.item_id', $subset)
        // ->whereBetween('transfers.document_date', [$old_from_date, $old_until_date])
        // ->whereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
        // ->whereBetween('invoices.document_date', [$old_from_date, $old_until_date])
        // ->select('invoices.id as invoice_id', 'invoice_items.*',
        // 'item_stocks.quantity as remaining_quantity', 'item_stocks.id as item_stock_id',
        // 'items.name as item_name', 'item_stocks.quantity as item_stocks_quantity',
        // 'measure_units.name as um', 'invoices.document_date as document_date',
        // 'transfers.id as transfer_id', 'transfer_items.quantity as transfer_quantity',
        // 'consumptions.id as consumption_id', 'consumption_items.quantity as consumption_quantity',
        // TransferItem::raw('SUM(transfer_items.quantity) as used_quantity_transfer'),
        // ConsumptionItem::raw('SUM(consumption_items.quantity) as used_quantity_consumption'))
        // ->groupby('item_stocks.id')
        // ->get();

        $entries = "";

        if($inventory_id == 1) {
            // $entries = ItemStock::leftjoin('invoice_items', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
            // ->leftjoin('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            // // ->leftjoin('invoices', function($join) use($old_from_date_interval, $old_until_date)
            // //              {
            // //                  $join->on('invoice_items.invoice_id', '=', 'invoices.id');
            // //                  $join->on('invoices.insertion_date', '<=', DB::raw('"' . $old_until_date . '"'));
            // //                  $join->on('invoices.insertion_date', '>=', DB::raw('"' . $old_from_date_interval . '"'));
            // //              })
            // ->groupBy('item_stocks.invoice_item_id')
            // //->groupBy('transfer_items.item_stock_id')
            // ->leftjoin('items', 'invoice_items.item_id', '=', 'items.id')
            // ->leftjoin('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
            // ->leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
            // ->leftjoin('transfer_items', 'transfer_items.item_stock_id', '=', 'item_stocks.id')
            // //->leftjoin('transfers', 'transfers.id', '=', 'transfer_items.transfer_id')
            // ->leftJoin('transfers', function($join) use($old_from_date_interval, $old_until_date, $inventory_id)
            //              {
            //                  $join->on('transfer_items.transfer_id', '=', 'transfers.id');
            //                  $join->on('transfers.document_date', '<=', DB::raw('"' . $old_until_date . '"'));
            //                  $join->on('transfers.document_date', '>=', DB::raw('"' . $old_from_date_interval . '"'));
            //                  $join->on('transfers.from_inventory_id', '=', DB::raw('"' . $inventory_id . '"'));
            //              })
            // ->leftjoin('consumption_items', 'consumption_items.item_stock_id', '=', 'item_stocks.id')
            //  //->leftjoin('consumptions', 'consumptions.id', '=', 'consumption_items.consumption_id')
            //  ->leftJoin('consumptions', function($join) use($old_from_date_interval, $old_until_date, $inventory_id)
            //              {
            //                  $join->on('consumption_items.consumption_id', '=', 'consumptions.id');
            //                  $join->on('consumptions.document_date', '<=', DB::raw('"' . $old_until_date . '"'));
            //                  $join->on('consumptions.document_date', '>=', DB::raw('"' . $old_from_date_interval . '"'));
            //              })
            // ->where('inventories.id', '=', $inventory_id)
            // ->whereIn('invoice_items.item_id', $subset)
            // // ->where(fn ($q) => $q
            // //     ->whereBetween('invoices.document_date', [$old_from_date, $old_until_date])
            // //     ->orWhereBetween('transfers.document_date', [$old_from_date, $old_until_date])
            // //     ->orWhereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
            // // )
            // //->whereBetween('transfers.document_date', [$old_from_date, $old_until_date])
            // // ->whereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
            // //->whereBetween('invoices.document_date', [$old_from_date, $old_until_date])
            // ->select('invoices.id as invoice_id', 'invoice_items.*',
            // 'item_stocks.quantity as remaining_quantity', 'item_stocks.id as item_stock_id',
            // 'items.name as item_name', 'item_stocks.quantity as item_stocks_quantity',
            // 'measure_units.name as um', 'invoices.document_date as document_date',
            // 'transfers.id as transfer_id', 
            // 'consumptions.id as consumption_id', 'consumption_items.quantity as consumption_quantity',
            // 'transfers.document_date as transfer_date',
            // TransferItem::raw('SUM(transfer_items.quantity) as used_quantity_transfer'),
            // ConsumptionItem::raw('SUM(consumption_items.quantity) as used_quantity_consumption'),
            // 'consumptions.document_date as consumption_date', 'invoices.insertion_date as invoice_date')
            //  //->groupby('transfer_items.transfer_id')
            // //->groupby('transfer_items.item_stock_id') //-> BUN INCOMPLET
            
            // //->groupby('consumption_items.item_stock_id')
            // ->get(); //QUERY BUN INCOMPLET!!!!!!!!!!!!!!!!

            $details = Invoice::leftjoin('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->leftjoin('items', 'invoice_items.item_id', '=', 'items.id')
            ->leftjoin('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
            ->leftjoin('item_stocks', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
            ->leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
            ->where('inventories.id', '=', $inventory_id)
            ->whereIn('invoice_items.item_id', $subset)
            ->where('invoices.document_date', '<=', $old_until_date)
            ->groupby('item_stocks.id')
            ->select('items.name as item_name', 'measure_units.name as um', 'document_date as acquisition_date',
            'invoice_items.price as price', 'item_stocks.id as is_id', 'invoice_items.tva_price as tva_price')
            ->get(); //QUERY BUN INCOMPLET!!!!!!!!!!!!!!!!

            //dd($details);

            $entries = ItemStock::join('invoice_items', 'invoice_items.id', '=', 'item_stocks.invoice_item_id')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->where('invoices.document_date', '<=', $old_until_date)
            ->where('item_stocks.inventory_id', $inventory_id)
            ->whereIn('invoice_items.item_id', $subset)
            ->select('invoice_items.quantity as ii_quantity', 'item_stocks.id as is_id')
            ->get();

           //dd($entries);

            $entries_array = [];

            

            foreach($entries as $entry) {
                $entries_array[$entry['is_id']] = array('initial' => $entry['ii_quantity'],
                'starting_quantity' => 0,
                'ending_quantity' => 0,
                'item_name' => '',
                'um' => '',
                'price' => 0,
                'tva_price' => 0,
                'acquisition_date' => '',
                'transfered_quantity' => 0);
            }

            foreach($details as $detail) {
                $entries_array[$detail['is_id']]['item_name'] = $detail['item_name'];
                $entries_array[$detail['is_id']]['um'] = $detail['um'];
                $entries_array[$detail['is_id']]['price'] = $detail['price'];
                $entries_array[$detail['is_id']]['acquisition_date'] = $detail['acquisition_date'];
                $entries_array[$detail['is_id']]['tva_price'] = $detail['tva_price'];
            }
            //dd($entries_array);

            $entries = Transfer::join('transfer_items', 'transfer_items.transfer_id', '=', 'transfers.id')
            ->where('transfers.from_inventory_id', $inventory_id)
            ->where('transfers.document_date', '<=', $old_from_date_interval)
            ->whereIn('transfer_items.item_id', $subset)
            ->select('item_stock_id as is_id', TransferItem::raw('SUM(quantity) as transfered_quantity'))
            ->groupby('item_stock_id')
            ->get();

            foreach($entries as $entry) {
                $entries_array[$entry['is_id']]['starting_quantity'] = $entry['transfered_quantity'];
            }

            $entries = Transfer::join('transfer_items', 'transfer_items.transfer_id', '=', 'transfers.id')
            ->where('transfers.from_inventory_id', $inventory_id)
            ->where('transfers.document_date', '<=', $old_until_date)
            ->whereIn('transfer_items.item_id', $subset)
            ->select('item_stock_id as is_id', TransferItem::raw('SUM(quantity) as ending_quantity'))
            ->groupby('item_stock_id')
            ->get();

            foreach($entries as $entry) {
                $entries_array[$entry['is_id']]['ending_quantity'] = $entry['ending_quantity'];
            }
            
            //dd($entries_array);

            $entries = Transfer::join('transfer_items', 'transfer_items.transfer_id', '=', 'transfers.id')
            ->where('transfers.from_inventory_id', $inventory_id)
            ->whereBetween('transfers.document_date', [$old_from_date_interval, $old_until_date])
            ->whereIn('transfer_items.item_id', $subset)
            ->select('item_stock_id as is_id', TransferItem::raw('SUM(quantity) as transfered_quantity'))
            ->groupby('item_stock_id')
            ->get();

            foreach($entries as $entry) {
                $entries_array[$entry['is_id']]['transfered_quantity'] = $entry['transfered_quantity'];
            }

            // $entries = Invoice::leftjoin('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            // ->leftjoin('item_stocks', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
            // ->where('item_stocks.inventory_id', $inventory_id)
            // ->whereIn('invoices.document_date',  [$old_from_date, $old_until_date])
            // ->whereIn('invoice_items.item_id', $subset)
            // ->select('item_stocks.id as is_id', 'invoice_items.quantity as invoice_quantity')
            // ->get();

            // foreach($entries as $entry) {
            //     $entries_array[$entry['is_id']]['invoice_quantity'] = $entry['invoice_quantity'];
            // }

           // dd($entries);

            //dd($entries_array);

            /*
                        select invoice_items.quantity, invoice_items.item_id from item_stocks 
                inner join invoice_items on invoice_item.id = item_stocks.invoice_item_id
                where inventory_id = 1


                select sum(quantity), item_stock_id 
                from transfer 
                inner join transfer_item on transfer_item.transfer_id = transfer.id
                where transfer.from_inventory_id = 1
                and transfer.document_data < '2020-01-01'
                group by item_stock_id
            */


            // $entries = ItemStock::whereHas('invoice_item')
            // ->with(['invoice_item', 'invoice_item.item', 'invoice_item.invoice'   => fn ($query) => $query->whereBetween('insertion_date', [$old_from_date_interval, $old_until_date]),
            // 'transfer_item'  => fn ($query) => $query->whereHas('transfer', fn ($query) => $query->whereBetween('document_date', [$old_from_date, $old_until_date])), 'consumption_item',
            // 'transfer_item.transfer'  => fn ($query) => $query->whereBetween('document_date', [$old_from_date_interval, $old_until_date])->where('from_inventory_id', $inventory_id)])
            // //->where('item_stocks.id', '=', 2069)
            // ->get();

            // $entries = ItemStock::whereHas('invoice_item')
            // ->with(['invoice_item', 'invoice_item.item', 'invoice_item.invoice'   => fn ($query) => $query->whereBetween('insertion_date', [$old_from_date_interval, $old_until_date]),
            // 'transfer_item', 'consumption_item',
            // 'transfer_item.transfer'])
            // //->where('item_stocks.id', '=', 2069)
            // ->toSql();

            // $entries_array = collect();
            // //dd($entries_array);
            // //dd($entries);

            // foreach($entries as $entry) {
            //     // foreach($entry->transfer_item as $transfer_item) {
            //     //     if(($transfer_item->transfer->document_date < $old_from_date || $transfer_item->transfer->document_date > $old_until_date) && ($transfer_item->transfer->document_date < $old_from_date || $transfer_item->transfer->document_date > $old_until_date)) {
                    
            //     //     } else {
            //             $entries_array[] = $entry;
            //         }
            // //     }
            // //  }

            //->with(['consumption_item' => fn ($query) => $query->where('id', 1), 'consumption_item.item'])
            // dd(array_slice($entries_array, 0, 10));
            // dd($entries_array);

            // $documents = Transfer::whereHas('transfer_item')
            // ->with(['transfer_item', 'transfer_item.item_detail', 'transfer_item.item_stock_detail', 'transfer_item.item_stock_detail.invoice_item'])
            // ->where('from_inventory_id', '=', $inventory_id)
            // ->whereBetween('document_date', [$old_from_date, $old_until_date])
            // ->get();
        } else {
            // $entries = ItemStock::leftjoin('invoice_items', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
            // ->leftjoin('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            // ->leftjoin('items', 'invoice_items.item_id', '=', 'items.id')
            // ->leftjoin('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
            // ->leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
            // ->leftjoin('transfer_items', 'transfer_items.item_stock_id', '=', 'item_stocks.id')
            // ->leftjoin('transfers', 'transfers.id', '=', 'transfer_items.transfer_id')
            // ->leftjoin('consumption_items', 'consumption_items.item_stock_id', '=', 'item_stocks.id')
            // ->leftjoin('consumptions', 'consumptions.id', '=', 'consumption_items.consumption_id')
            // ->where('inventories.id', '=', $inventory_id)
            // ->whereIn('transfer_items.item_id', $subset)
            // ->where(fn ($q) => $q
            //     ->whereBetween('transfers.document_date', [$old_from_date, $old_until_date])
            //     ->orWhereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
            // )
            // // ->whereBetween('transfers.document_date', [$old_from_date, $old_until_date])
            // // ->whereBetween('consumptions.document_date', [$old_from_date, $old_until_date])
            // //->whereBetween('transfers.document_date', [$old_from_date, $old_until_date])
            // ->select('invoices.id as invoice_id', 'invoice_items.*',
            // 'item_stocks.quantity as remaining_quantity', 'item_stocks.id as item_stock_id',
            // 'items.name as item_name', 'item_stocks.quantity as item_stocks_quantity',
            // 'measure_units.name as um', 'invoices.document_date as document_date',
            // 'transfers.id as transfer_id', 'transfer_items.quantity as transfer_quantity',
            // 'consumptions.id as consumption_id', 'consumption_items.quantity as consumption_quantity',
            // ConsumptionItem::raw('SUM(consumption_items.quantity) as used_quantity_consumption'))
            // ->groupby('consumption_items.item_stock_id')
            // ->get();

            // $details = Invoice::leftjoin('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            // ->leftjoin('items', 'invoice_items.item_id', '=', 'items.id')
            // ->leftjoin('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
            // ->leftjoin('item_stocks', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
            // ->leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
            // ->where('inventories.id', '=', $inventory_id)
            // ->whereIn('invoice_items.item_id', $subset)
            // ->where('invoices.document_date', '<=', $old_until_date)
            // ->groupby('item_stocks.id')
            // ->select('items.name as item_name', 'measure_units.name as um', 'document_date as acquisition_date',
            // 'invoice_items.price as price', 'item_stocks.id as is_id')
            // ->get(); //QUERY BUN INCOMPLET!!!!!!!!!!!!!!!!

            // $details = Invoice::leftjoin('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            // ->leftjoin('items', 'invoice_items.item_id', '=', 'items.id')
            // ->leftjoin('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
            // ->leftjoin('item_stocks', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
            // ->leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
            // ->leftjoin('transfer_items', 'transfer_items.item_stock_id', '=', 'item_stocks.id')
            // ->leftjoin('transfers', 'transfer_items.transfer_id', '=', 'transfers.id')
            // ->where('transfers.to_inventory_id', '=', $inventory_id)
            // ->whereIn('transfer_items.item_id', $subset)
            // ->where('transfers.document_date', '<=', $old_until_date)
            // ->groupby('item_stocks.id')
            // ->select('items.name as item_name', 'measure_units.name as um', 'transfers.document_date as acquisition_date',
            // 'invoice_items.price as price', 'item_stocks.id as is_id')
            // ->get(); //QUERY BUN INCOMPLET!!!!!!!!!!!!!!!!


            // Base information
            // $details = Transfer::leftjoin('transfer_items', 'transfer_items.transfer_id', '=', 'transfers.id')
            // ->leftjoin('items', 'transfer_items.item_id', '=', 'items.id')
            // ->leftjoin('item_stocks', 'transfer_items.item_stock_id', '=', 'item_stocks.id')
            // ->leftjoin('invoice_items', 'invoice_items.id', '=', 'item_stocks.invoice_item_id')
            // ->leftjoin('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            // ->leftjoin('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
            // ->leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
            // ->where('transfers.to_inventory_id', '=', $inventory_id)
            // ->whereIn('transfer_items.item_id', $subset)
            // ->where('transfers.document_date', '<=', $old_until_date)
            // //->groupby('transfer_items.item_stock_id')
            // ->select('items.name as item_name', 'measure_units.name as um', 'transfers.document_date as acquisition_date',
            // 'invoice_items.price as price', 'item_stocks.id as is_id')
            // ->get(); //QUERY BUN INCOMPLET!!!!!!!!!!!!!!!!

            // transfer quantities
            $entries = ItemStock::join('transfer_items', 'item_stocks.id', '=', 'transfer_items.item_stock_id')
            ->join('transfers', 'transfers.id', '=', 'transfer_items.transfer_id')
            ->where('transfers.document_date', '<=', $old_until_date)
            //->where('item_stocks.inventory_id', $inventory_id)
            ->where('transfers.to_inventory_id', $inventory_id)
            ->whereIn('transfer_items.item_id', $subset)
            ->select(TransferItem::raw('SUM(transfer_items.quantity) as ii_quantity'), 'item_stocks.id as is_id')
            ->groupby('transfer_items.item_stock_id')
            //->groupby('transfer_items.transfer_id')
            ->get();
           
           //dd($entries);

            $entries_array = [];

            

            foreach($entries as $entry) {

                if (!isset($entries_array[$entry['is_id']])) {
                    $entries_array[$entry['is_id']] = array('initial' => 0,
                    'starting_quantity' => 0,
                    'ending_quantity' => 0,
                    'item_name' => '',
                    'um' => '',
                    'price' => 0,
                    'tva_price' => 0,
                    'acquisition_date' => '',
                    'consumption_quantity' => 0);
                }
                $entries_array[$entry['is_id']]['initial'] = $entry['ii_quantity'];


             
            }


            // base value
            // foreach($details as $detail) {
            //     $entries_array[$detail['is_id']]['item_name'] = $detail['item_name'];
            //     $entries_array[$detail['is_id']]['um'] = $detail['um'];
            //     $entries_array[$detail['is_id']]['price'] = $detail['price'];
            //     $entries_array[$detail['is_id']]['acquisition_date'] = $detail['acquisition_date'];
            // }

//            dd($entries_array);

            // starting quantities

            $entries = Consumption::join('consumption_items', 'consumption_items.consumption_id', '=', 'consumptions.id')
            ->where('consumptions.inventory_id', $inventory_id)
            ->where('consumptions.document_date', '<=', $old_from_date_interval)
            ->whereIn('consumption_items.item_id', $subset)
            ->leftjoin('item_stocks', 'consumption_items.item_stock_id', '=', 'item_stocks.id')
            ->leftjoin('invoice_items', 'invoice_items.id', '=', 'item_stocks.invoice_item_id')
            ->leftjoin('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
            ->select('item_stock_id as is_id', ConsumptionItem::raw('SUM(consumption_items.quantity) as starting_quantity'))
            ->groupby('item_stock_id')
            ->get();

            foreach($entries as $entry) {

                if (!isset($entries_array[$entry['is_id']])) {
                    $entries_array[$entry['is_id']] = array('initial' => 0,
                    'starting_quantity' => 0,
                    'ending_quantity' => 0,
                    'item_name' => '',
                    'um' => '',
                    'price' => 0,
                    'tva_price' => 0,
                    'acquisition_date' => '',
                    'consumption_quantity' => 0);
                }

                $entries_array[$entry['is_id']]['starting_quantity'] = $entry['starting_quantity'];
            }

            // Ending quantities
            $entries = Consumption::join('consumption_items', 'consumption_items.consumption_id', '=', 'consumptions.id')
            ->where('consumptions.inventory_id', $inventory_id)
            ->where('consumptions.document_date', '<=', $old_until_date)
            ->whereIn('consumption_items.item_id', $subset)
            ->select('item_stock_id as is_id', ConsumptionItem::raw('SUM(quantity) as ending_quantity'))
            ->groupby('item_stock_id')
            //->groupby('consumption_items.consumption_id')
            ->get();

            foreach($entries as $entry) {

                if (!isset($entries_array[$entry['is_id']])) {
                    $entries_array[$entry['is_id']] = array('initial' => 0,
                    'starting_quantity' => 0,
                    'ending_quantity' => 0,
                    'item_name' => '',
                    'um' => '',
                    'price' => 0,
                    'tva_price' => 0,
                    'acquisition_date' => '',
                    'consumption_quantity' => 0);
                }

                $entries_array[$entry['is_id']]['ending_quantity'] = $entry['ending_quantity'];
            }

            //dd($entries_array);
            
            // consumed quantities
            $entries = Consumption::join('consumption_items', 'consumption_items.consumption_id', '=', 'consumptions.id')
            ->where('consumptions.inventory_id', $inventory_id)
            ->whereBetween('consumptions.document_date', [$old_from_date_interval, $old_until_date])
            ->whereIn('consumption_items.item_id', $subset)
            ->select('item_stock_id as is_id', ConsumptionItem::raw('SUM(quantity) as transfered_quantity'))
            ->groupby('item_stock_id')
            ->get();
            

            foreach($entries as $entry) {

                if (!isset($entries_array[$entry['is_id']])) {
                    $entries_array[$entry['is_id']] = array('initial' => 0,
                    'starting_quantity' => 0,
                    'ending_quantity' => 0,
                    'item_name' => '',
                    'um' => '',
                    'price' => 0,
                    'tva_price' => 0,
                    'acquisition_date' => '',
                    'consumption_quantity' => 0);
                }
                $entries_array[$entry['is_id']]['consumption_quantity'] = $entry['transfered_quantity'];
            }
            //dd($entries_array);

            $details = Transfer::leftjoin('transfer_items', 'transfer_items.transfer_id', '=', 'transfers.id')
            ->leftjoin('item_stocks', 'transfer_items.item_stock_id', '=', 'item_stocks.id')
            ->leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
            ->where('transfers.to_inventory_id', '=', $inventory_id)
            ->whereIn('transfer_items.item_id', $subset)
            ->where('transfers.document_date', '<=', $old_until_date)
            //->groupby('transfer_items.item_stock_id')
            ->select('transfers.document_date as acquisition_date', 'item_stocks.id as is_id')
            ->get(); //QUERY BUN INCOMPLET!!!!!!!!!!!!!!!!

            foreach($details as $detail) {

                if (!isset($entries_array[$detail['is_id']])) {
                    $entries_array[$detail['is_id']] = array('initial' => 0,
                    'starting_quantity' => 0,
                    'ending_quantity' => 0,
                    'item_name' => '',
                    'um' => '',
                    'price' => 0,
                    'tva_price' => 0,
                    'acquisition_date' => '',
                    'consumption_quantity' => 0);
                }
                $entries_array[$detail['is_id']]['acquisition_date'] = $detail['acquisition_date'];
            }

            //dd($entries_array);
            //dd($subset);

            $details = Item::join('item_stocks', 'item_stocks.item_id', '=', 'items.id')
            ->join('invoice_items', 'invoice_items.item_id', '=', 'items.id')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('measure_units', 'measure_units.id', '=', 'invoice_items.measure_unit_id')
            ->whereIn('item_stocks.id', array_keys($entries_array))
            ->select('items.name as item_name', 'measure_units.name as um',
            'invoice_items.price as price', 'item_stocks.id as is_id', 'invoice_items.tva_price as tva_price')
            ->get();

            foreach($details as $detail) {

                if (!isset($entries_array[$detail['is_id']])) {
                    $entries_array[$detail['is_id']] = array('initial' => 0,
                    'starting_quantity' => 0,
                    'ending_quantity' => 0,
                    'item_name' => '',
                    'um' => '',
                    'price' => 0,
                    'tva_price' => 0,
                    'acquisition_date' => '',
                    'consumption_quantity' => 0);
                }
                $entries_array[$detail['is_id']]['item_name'] = $detail['item_name'];
                $entries_array[$detail['is_id']]['price'] = $detail['price'];
                $entries_array[$detail['is_id']]['um'] = $detail['um'];
                $entries_array[$detail['is_id']]['tva_price'] = $detail['tva_price'];
            }

            $items_without_transfer = [];

            foreach($entries_array as $key => $value) {
                if($value['acquisition_date'] == "") {
                    $items_without_transfer[] = $key;
                }
            }

            $details = Invoice::leftjoin('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->leftjoin('item_stocks', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
            ->leftjoin('inventories', 'item_stocks.inventory_id', '=', 'inventories.id')
            ->where('item_stocks.inventory_id', '=', $inventory_id)
            ->whereIn('item_stocks.id', $items_without_transfer)
            ->where('invoices.document_date', '<=', $old_until_date)
            //->groupby('transfer_items.item_stock_id')
            ->select('invoices.document_date as acquisition_date', 'item_stocks.id as is_id', 'invoice_items.quantity as initial')
            ->get(); //QUERY BUN INCOMPLET!!!!!!!!!!!!!!!!

           //dd($details);

            foreach($details as $detail) {

                if (!isset($entries_array[$detail['is_id']])) {
                    $entries_array[$detail['is_id']] = array('initial' => 0,
                    'starting_quantity' => 0,
                    'ending_quantity' => 0,
                    'item_name' => '',
                    'um' => '',
                    'price' => 0,
                    'tva_price' => 0,
                    'acquisition_date' => '',
                    'consumption_quantity' => 0);
                }
                $entries_array[$detail['is_id']]['acquisition_date'] = $detail['acquisition_date'];
                $entries_array[$detail['is_id']]['initial'] = $detail['initial'];
            }

            //dd($entries_array);

        }
        //dd($entries_array);

        

        //dd($entries[0]->transfer_date);

        // $entries_array = [];

        // foreach($entries as $entry) {
        //     if(($entry->transfer_date < $old_from_date || $entry->transfer_date > $old_until_date) && ($entry->consumption_date < $old_from_date || $entry->consumption_date > $old_until_date)) {
                
        //     } else {
        //         $entries_array[] = $entry;
        //     }
        // }

        //dd($entries_array);

        //$filename = 'balanta '. $inventory_name .' '. $new_from_date .' '. $new_until_date .'.pdf';

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

        $html .= '
        <table>
        <tr>
        <th style="height: 25px;" colspan="9">Subgestiunea: '. $category_name .' - '. $inventory_name .'</th>
        </tr>
        <tr>
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
        ';

        // foreach($entries_array as $entry) {
        //     $html .= '<tr nobr="true">';
        //     $html .= '<td style="text-align: center;">'. $entry['item_name'] .'</td>';
        //     $html .= '<td style="text-align: center;">'. $entry['um'] .'</td>';
        //     $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($entry['document_date'])) .'</td>';
        //     $html .= '<td style="text-align: center;">'. $entry['price'] .'</td>';
        //     $html .= '<td style="text-align: center;">'. $entry['item_stocks_quantity'] + $entry['used_quantity_transfer'] + $entry['used_quantity_consumption'].'</td>';
        //     $html .= '<td style="text-align: center;">0</td>';
        //     $html .= '<td style="text-align: center;">0</td>';
        //     $html .= '<td style="text-align: center;">0</td>';
        //     $html .= '<td style="text-align: center;">0</td>';
        //     $html .= '</tr>';
        // }

        // foreach($entries as $entry) {
        //     if($entry['transfer_date'] == null && $entry['used_quantity_transfer'] != null) {
        //         $entry['used_quantity_transfer'] = 0;
        //         dd($entries);
        //     } else if($entry['transfer_date'] == null && $entry['used_quantity_transfer'] == null) {
        //         $entry['used_quantity_transfer'] = 0;
        //     }
        //     $html .= '<tr nobr="true">';
        //     $html .= '<td style="text-align: center;">'. $entry['item_name'] .'</td>';
        //     $html .= '<td style="text-align: center;">'. $entry['um'] .'</td>';
        //     $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($entry['document_date'])) .'</td>';
        //     $html .= '<td style="text-align: center;">'. $entry['price'] .'</td>';
        //     $html .= '<td style="text-align: center;">'. $entry['item_stocks_quantity'] + $entry['used_quantity_transfer'].'</td>';
        //     $html .= '<td style="text-align: center;">'. $entry['quantity'] .'</td>';
        //     $html .= '<td style="text-align: center;">'. $entry['used_quantity_transfer'] .'</td>';
        //     $html .= '<td style="text-align: center;">0</td>';
        //     $html .= '<td style="text-align: center;">0</td>';
        //     $html .= '</tr>';
        // }

        // foreach($details as $key => $value) {
        //          //dd($value);
        //         $html .= '<tr nobr="true">';
        //         $html .= '<td style="text-align: center;">'. $value['item_name'] .'</td>';
        //         $html .= '<td style="text-align: center;">'. $value['um'] .'</td>';
        //         $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($value['acquisition_date'])) .'</td>';
        //         $html .= '<td style="text-align: center;">'. $value['price'] .'</td>';
        //         $html .= '<td style="text-align: center;">'. $entries_array[2338]['initial'].'</td>';
        //         $html .= '<td style="text-align: center;">'. $value['quantity'] .'</td>';
        //         $html .= '<td style="text-align: center;">'. $value['used_quantity_transfer'] .'</td>';
        //         $html .= '<td style="text-align: center;">0</td>';
        //         $html .= '<td style="text-align: center;">0</td>';
        //         $html .= '</tr>';
        //     }

        //dd($entries_array);

        $total_sold = 0;
        $initial_value = 0;
        $ins = 0;
        $outs = 0;

        if($inventory_id == 1) {
            foreach($entries_array as $entry) {
                //dd($entry);
               $total_sold += $entry['tva_price'] * ($entry['initial'] - $entry['ending_quantity']);
               $initial_value += $entry['tva_price'] * ($entry['initial'] - $entry['starting_quantity']);
               $outs += $entry['tva_price'] * $entry['transfered_quantity'];
    
               $startDate = date('Y-m-d', strtotime($old_from_date));
               $endDate = date('Y-m-d', strtotime($old_until_date));
               $checkDate = date('Y-m-d', strtotime($entry['acquisition_date']));
               //dd($entries_array);
    
               $html .= '<tr nobr="true">';
               $html .= '<td style="text-align: center;">'. $entry['item_name'] .'</td>';
               $html .= '<td style="text-align: center;">'. $entry['um'] .'</td>';
               $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($entry['acquisition_date'])) .'</td>';
               $html .= '<td style="text-align: center;">'. $entry['price'] .'</td>';
               $html .= '<td style="text-align: center;">'. $entry['initial'] - $entry['starting_quantity'].'</td>';
               if(($checkDate >= $startDate) && ($checkDate <= $endDate)) {
                    $html .= '<td style="text-align: center;">'. $entry['initial'] .'</td>';
                    $ins += $entry['tva_price'] * $entry['initial'];
                } else {
                    $html .= '<td style="text-align: center;">0</td>';
                    $ins += $entry['tva_price'] * 0;
                }
               
               $html .= '<td style="text-align: center;">'. $entry['transfered_quantity'] .'</td>';
               $html .= '<td style="text-align: center;">'. $entry['initial'] - $entry['ending_quantity'] .'</td>';
               $html .= '<td style="text-align: center;">'. $entry['tva_price'] * ($entry['initial'] - $entry['ending_quantity']) .'</td>';
               $html .= '</tr>';
           }
        } else {
            //dd($entries_array);
            foreach($entries_array as $entry) {
                //dd($entry);
               $total_sold += $entry['tva_price'] * ($entry['initial'] - $entry['ending_quantity']);
               $initial_value += $entry['tva_price'] * ($entry['initial'] - $entry['starting_quantity']);
               $outs += $entry['tva_price'] * $entry['consumption_quantity'];
    
               $startDate = date('Y-m-d', strtotime($old_from_date));
               $endDate = date('Y-m-d', strtotime($old_until_date));
               $checkDate = date('Y-m-d', strtotime($entry['acquisition_date']));
               //dd($entries_array);
    
               $html .= '<tr nobr="true">';
               $html .= '<td style="text-align: center;">'. $entry['item_name'] .'</td>';
               $html .= '<td style="text-align: center;">'. $entry['um'] .'</td>';
               $html .= '<td style="text-align: center;">'. date("d-m-Y", strtotime($entry['acquisition_date'])) .'</td>';
               $html .= '<td style="text-align: center;">'. $entry['price'] .'</td>';
               $html .= '<td style="text-align: center;">'. $entry['initial'] - $entry['starting_quantity'].'</td>';
               //$html .= '<td style="text-align: center;">'. $entry['initial'] .'</td>';
               $ins += $entry['tva_price'] * $entry['initial'];
               if(($checkDate >= $startDate) && ($checkDate <= $endDate)) {
                    $html .= '<td style="text-align: center;">'. $entry['initial'] .'</td>';
                    $ins += $entry['tva_price'] * $entry['initial'];
                } else {
                    $html .= '<td style="text-align: center;">0</td>';
                    $ins += $entry['tva_price'] * 0;
                }
               
               $html .= '<td style="text-align: center;">'. $entry['consumption_quantity'] .'</td>';
               $html .= '<td style="text-align: center;">'. $entry['initial'] - $entry['ending_quantity'] .'</td>';
               $html .= '<td style="text-align: center;">'. $entry['tva_price'] * ($entry['initial'] - $entry['ending_quantity']) .'</td>';
               $html .= '</tr>';
           }
        }

        

        // $html .= '<tr>
        //     <td style="height: 25px; border: none;"></td>
        //     <td style="height: 25px; border: none;"></td>
        //     <td style="height: 25px; border: none;"></td>
        //     <td style="height: 25px; border: none;"></td>
        //     <td style="height: 25px; border: none;"></td>
        //     <td style="height: 25px; border: none;"></td>
        //     <td style="height: 25px; border: none;"></td>
        //     <td style="height: 25px; border: none;"></td>
        //     <td style="height: 25px; border: none;">Total '. $total_sold .'</td>
        // </tr>';

        $html .= '</table>';

        $html .= '<br>';

        $html .= '<br>';

        $html .= '<span>Total stoc initial: '. $initial_value .'</span>';

        $html .= '<br>';

        $html .= '<span>Total intrari: '. $ins .'</span>';

        $html .= '<br>';

        $html .= '<span>Total iesiri: '. $outs .'</span>';

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
