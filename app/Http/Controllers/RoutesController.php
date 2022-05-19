<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Inventory;
use \App\Models\Category;
use \App\Models\Item;
use \App\Models\Provider;
use \App\Models\MeasureUnit;
use \App\Models\Invoice;
use \App\Models\Substation;
use \App\Models\Ambulance;
use \App\Models\Medic;
use \App\Models\Transfer;
use \App\Models\AvizEntry;
use \App\Models\Returning;
use \App\Models\AmbulanceType;
use \App\Models\ItemStock;
use \App\Models\Assistent;
use \App\Models\Ambulancier;
use \App\Models\Consumption;
use DB;

class RoutesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home()
    {
        return view('home');
    }

    public function inventory($inventory_slug, $category)
    {
        // $inventory_slug = Inventory::select('slug')->get();
        // $category = Category::select('slug')->get();

        $inventory = Inventory::where('slug', $inventory_slug)->first();

        $current_category = Category::where('slug', $category)->first();

        $all_categories = Category::select('name')->get();
        //$all_items = Item::select('name')->get();
        

        // $item_detailed = ItemStock::all();

        abort_unless($inventory, 404);
        abort_unless($current_category, 404);


        //dd($inventory->id . ' - ' . $current_category->id );

        // $items = ItemStock::leftJoin('items', 'items.id', '=', 'item_stocks.item_id')
        // ->where('item_stocks.inventory_id', '=', $inventory->id)
        // ->where('items.category_id', $current_category->id)->get();
        // 
        // $items = ItemStock::with(['item.item' => function($q) use ($current_category) {
        //     $q->select('name', 'id', 'category_id');
        //     $q->where('category_id', '=', $current_category->id)
        // }])->where('inventory_id', $inventory->id)->get();
        // $quries = DB::getQueryLog();
        // dd($items);
        // $items = DB::table('item_stocks')
        // ->join('invoice_items', 'invoice_items.id', '=', 'item_stocks.item_id')
        // ->join('items', 'items.id', '=', 'invoice_items.item_id')
        // ->where('item_stocks.inventory_id', $inventory->id)
        // ->where('items.category_id', $current_category->id)->get();

        $all_items = Item::where('category_id', $current_category->id)->with('category')->get();

        $item_stock = Item::leftJoin('invoice_items', 'invoice_items.item_id', '=', 'items.id')
        ->leftJoin('measure_units as munit', 'munit.id', '=', 'invoice_items.measure_unit_id')
        ->leftJoin('item_stocks', 'item_stocks.invoice_item_id', '=', 'invoice_items.id')
        ->select('munit.name as m_name', 'invoice_items.*', 'items.*', 'item_stocks.*')
        ->where('item_stocks.inventory_id', '=', $inventory->id)->get();

        $grouped = array();

        foreach($item_stock as $item)
        {
            $grouped[$item->name][] = $item;
        }

        //dd($all_items);

        

        return view('gestiune.gestiune-view', ['inventory_slug' => $inventory, 'category' => $category, 'categories' => $all_categories, 'current_category' => $current_category, 'all_items' => $all_items, 'items' => $grouped]);
    }

    public function invoice() 
    {
        $providers = Provider::all();
        $items = Item::all();
        $units = MeasureUnit::all();
        $invoices = Invoice::all();

        return view('operatiuni.factura', ['providers' => $providers, 'items' => $items, 'units' => $units, 'invoices' => $invoices]);
    }

    public function station_checklist() 
    {
        $inventories = Inventory::all();
        $ambulances = Ambulance::all();
        $item_stocks = ItemStock::all();
        $assistents = Assistent::all();
        $ambulanciers = Ambulancier::all();

        return view('operatiuni.checklist-statii', ['inventories' => $inventories, 'ambulances' => $ambulances, 'item_stocks' => $item_stocks, 'assistents' => $assistents, 'ambulanciers' => $ambulanciers]);
    }

    public function medic_checklist() 
    {
        $inventories = Inventory::all();
        $ambulances = Ambulance::all();
        $medics = Medic::all();
        $assistents = Assistent::all();
        $ambulanciers = Ambulancier::all();

        return view('operatiuni.checklist-medici', ['inventories' => $inventories, 'ambulances' => $ambulances, 'medics' => $medics, 'assistents' => $assistents, 'ambulanciers' => $ambulanciers]);
    }

    public function bon_transfer()
    {
        $inventories = Inventory::all();
        $transfers = Transfer::all();

        return view('operatiuni.transfer', ['inventories' => $inventories, 'transfers' => $transfers]);
    }

    public function bon_consum_ambulante()
    {
        $ambulances = Ambulance::all();
        $inventories = Inventory::all();
        $consumptions = Consumption::all();

        return view('operatiuni.consum-ambulante', ['ambulances' => $ambulances, 'inventories' => $inventories, 'consumptions' => $consumptions]);
    }

    public function bon_consum_medici()
    {
        $medics = Medic::all();
        $inventories = Inventory::all();
        $consumptions = Consumption::all();

        return view('operatiuni.consum-medici', ['medics' => $medics, 'inventories' => $inventories, 'consumptions' => $consumptions]);
    }

    public function aviz_intrare()
    {
        $providers = Provider::all();
        $items = Item::all();
        $units = MeasureUnit::all();
        $invoices = Invoice::all();
        $aviz = AvizEntry::all();

        return view('operatiuni.aviz', ['providers' => $providers, 'items' => $items, 'units' => $units, 'invoices' => $invoices, 'aviz' => $aviz]);
    }

    public function retur()
    {
        $inventories = Inventory::all();
        $returnings = Returning::all();

        return view('operatiuni.retur', ['inventories' => $inventories, 'returnings' => $returnings]);
    }

    public function min_cant()
    {
        $items = Item::all();

        return view('operatiuni.modificare', ['items' => $items]);
    }

    public function rapoarte()
    {
        $inventories = Inventory::all();

        return view('documente.raport', ['inventories' => $inventories]);
    }

    public function proprietati()
    {
        $categories = Category::all();
        $amb_types = AmbulanceType::all();
        $inventories = Inventory::all();

        return view('operatiuni.proprietati', ['categories' => $categories, 'ambulanceTypes' => $amb_types, 'inventories' => $inventories]);
    }

    public function expirare()
    {
        $inventories = Inventory::all();

        return view('documente.expirare', ['inventories' => $inventories]);
    }

    public function fisa_produs()
    {
        $items = Item::all();

        return view('documente.fisaprodus', ['items' => $items]);
    }

    public function inventar()
    {
        $inventories = Inventory::all();

        return view('documente.inventar', ['inventories' => $inventories]);
    }

    public function balanta()
    {
        $inventories = Inventory::all();

        return view('documente.balanta', ['inventories' => $inventories]);
    }

    public function baza_date()
    {
        return view('documente.database');
    }

}
