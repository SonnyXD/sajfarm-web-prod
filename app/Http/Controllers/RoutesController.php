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

        $inv_slug = Inventory::where('slug', $inventory_slug)->first();
        $current_category = Category::where('slug', $category)->first();

        $all_categories = Category::select('name')->get();
        //$all_items = Item::select('name')->get();
        $all_items = Item::where('category_id', $current_category->id)->with('category')->get();



        //dd($all_items);

        abort_unless($inv_slug, 404);
        abort_unless($current_category, 404);

        return view('gestiune.gestiune-view', ['inventory_slug' => $inventory_slug, 'category' => $category, 'categories' => $all_categories, 'items' => $all_items , 'current_category' => $current_category]);
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

        return view('operatiuni.checklist-statii', ['inventories' => $inventories, 'ambulances' => $ambulances]);
    }

    public function medic_checklist() 
    {
        $inventories = Inventory::all();
        $ambulances = Ambulance::all();
        $medics = Medic::all();

        return view('operatiuni.checklist-medici', ['inventories' => $inventories, 'ambulances' => $ambulances, 'medics' => $medics]);
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

        return view('operatiuni.consum-ambulante', ['ambulances' => $ambulances, 'inventories' => $inventories]);
    }

    public function bon_consum_medici()
    {
        $medics = Medic::all();

        return view('operatiuni.consum-medici', ['medics' => $medics]);
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
        return view('documente.expirare');
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
