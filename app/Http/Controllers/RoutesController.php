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
use \App\Models\Institution;
use \App\Models\InvoiceItem;
use \App\Models\Ambulance;
use \App\Models\Medic;
use \App\Models\Transfer;
use \App\Models\AvizEntry;
use \App\Models\Returning;
use \App\Models\AmbulanceType;
use \App\Models\TransferItem;
use \App\Models\ConsumptionItem;
use \App\Models\ChecklistItem;
use \App\Models\ReturningItem;
use \App\Models\ItemStock;
use \App\Models\Assistent;
use \App\Models\Ambulancier;
use \App\Models\Consumption;
use \App\Models\Checklist;
use \App\Models\ReturningChecklistItem;
use \App\Models\MinimumQuantity;
use \App\Models\Task;
use DB;
use PDF;
use Session;
use Auth;

class RoutesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home()
    {
        $consumptions = Consumption::all();
        $consumptions = count($consumptions);
        $transfers = Transfer::all();
        $transfers = count($transfers);
        $nirs = Invoice::all();
        $nirs = count($nirs);
        $returnings = Returning::all();
        $returnings = count($returnings);
        $tasks = Task::where('done', 0)
        ->get();

        return view('home', ['consumptions' => $consumptions, 'transfers' => $transfers, 'nirs' => $nirs, 'returnings' => $returnings, 'tasks' => $tasks]);
    }

    public function inventory($inventory_slug, $category)
    {
        // $inventory_slug = Inventory::select('slug')->get();
        // $category = Category::select('slug')->get();

        $inventory = Inventory::where('slug', $inventory_slug)->first();

        $inventory_name = $inventory->name;

        $inventory_id = $inventory->id;

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

        // //$minimum_quantities_farm = MinimumQuantity::with('item')->where('inventory_id', '=', 1)->get();
        // $minimum_quantities_farm = MinimumQuantity::leftjoin('items', 'items.id', '=', 'minimum_quantities.item_id')
        // ->leftjoin('inventories', 'inventories.id', '=', 'minimum_quantities.inventory_id')
        // ->where('minimum_quantities.inventory_id', $inventory->id)
        // ->select('minimum_quantities.item_id', 'minimum_quantities.inventory_id', 'minimum_quantities.quantity',
        // 'minimum_quantities.id')
        // ->get();
        // $minimum_quantities_stoc3 = MinimumQuantity::where('inventory_id', '=', 2)->get();

        //$item_sum = ItemStock::where()->sum('quantity'); //join cu inventory_id si item_id -> dupa astea te iei ca sa faci suma

        //dd($item_sum);
        

        return view('gestiune.gestiune-view', ['inventory_slug' => $inventory, 'category' => $category, 'categories' => $all_categories, 'current_category' => $current_category, 'all_items' => $all_items, 'items' => $grouped, 'inventory_name' => $inventory_name, 'inventory_id' => $inventory_id]);
    }

    public function invoice() 
    {
        $providers = Provider::all();
        $items = Item::all();
        $units = MeasureUnit::all();
        $invoices = Invoice::all();

        $title = 'Intrare Factura';

        return view('operatiuni.factura', ['providers' => $providers, 'items' => $items, 'units' => $units, 'invoices' => $invoices, 'title' => $title]);
    }

    public function station_checklist() 
    {
        $inventories = Inventory::all();
        $ambulances = Ambulance::all();
        $item_stocks = ItemStock::all();
        $assistents = Assistent::all();
        $ambulanciers = Ambulancier::all();

        $title = 'Checklist Statii';

        return view('operatiuni.checklist-statii', ['inventories' => $inventories, 'ambulances' => $ambulances, 'item_stocks' => $item_stocks, 'assistents' => $assistents, 'ambulanciers' => $ambulanciers, 'title' => $title]);
    }

    public function medic_checklist() 
    {
        $inventories = Inventory::all();
        $ambulances = Ambulance::all();
        $medics = Medic::all();
        $assistents = Assistent::all();
        $ambulanciers = Ambulancier::all();

        $title = 'Checklist Medici';

        return view('operatiuni.checklist-medici', ['inventories' => $inventories, 'ambulances' => $ambulances, 'medics' => $medics, 'assistents' => $assistents, 'ambulanciers' => $ambulanciers, 'title' => $title]);
    }

    public function bon_transfer()
    {
        $inventories = Inventory::all();
        $transfers = Transfer::all();
        $assistents = Assistent::all();

        $title = 'Bon de Transfer';

        return view('operatiuni.transfer', ['inventories' => $inventories, 'transfers' => $transfers, 'title' => $title, 'assistents' => $assistents]);
    }

    public function bon_consum_ambulante()
    {
        $ambulances = Ambulance::all();
        $inventories = Inventory::all();
        $consumptions = Consumption::all();

        $title = 'Bon de Consum Ambulante';

        return view('operatiuni.consum-ambulante', ['ambulances' => $ambulances, 'inventories' => $inventories, 'consumptions' => $consumptions, 'title' => $title]);
    }

    public function bon_consum_medici()
    {
        $medics = Medic::all();
        $inventories = Inventory::all();
        $consumptions = Consumption::all();

        $title = 'Bon de Consum Medici';

        return view('operatiuni.consum-medici', ['medics' => $medics, 'inventories' => $inventories, 'consumptions' => $consumptions, 'title' => $title]);
    }

    public function aviz_intrare()
    {
        $providers = Provider::all();
        $items = Item::all();
        $units = MeasureUnit::all();
        $invoices = Invoice::all();
        $aviz = AvizEntry::all();

        $donation = Category::where('name', 'Donatii')->first()->id;
        $sponsor = Category::where('name', 'Sponsorizari')->first()->id;

        $title = 'Aviz Intrare';

        return view('operatiuni.aviz', ['providers' => $providers, 'items' => $items, 'units' => $units, 'invoices' => $invoices, 'aviz' => $aviz, 'title' => $title, 'donation_category' => $donation, 'sponsor_category' => $sponsor]);
    }

    public function returning_checklist()
    {
        $inventories = Inventory::all();
        $returnings = Returning::all();
        $ambulances = Ambulance::all();

        $title = 'Checklist Retur';

        return view('operatiuni.retur-checklist', ['inventories' => $inventories, 'returnings' => $returnings, 'title' => $title, 'ambulances' => $ambulances]);
    }
    
    public function returning()
    {
        $inventories = Inventory::all();
        $returnings = Returning::all();
        $ambulances = Ambulance::all();

        $title = 'Retur';

        return view('operatiuni.retur', ['inventories' => $inventories, 'returnings' => $returnings, 'title' => $title, 'ambulances' => $ambulances]);
    }

    public function min_cant()
    {
        $items = Item::all();

        $title = 'Modificare Cantitati Minime';

        return view('operatiuni.modificare', ['items' => $items, 'title' => $title]);
    }

    public function rapoarte()
    {
        $inventories = Inventory::all();

        $title = 'Rapoarte';

        $ambulances = Ambulance::all();

        return view('documente.raport', ['inventories' => $inventories, 'title' => $title, 'ambulances' => $ambulances]);
    }

    public function proprietati()
    {
        $categories = Category::all();
        $amb_types = AmbulanceType::all();
        $inventories = Inventory::all();

        $title = 'Inserare Proprietati';

        return view('operatiuni.proprietati', ['categories' => $categories, 'ambulanceTypes' => $amb_types, 'inventories' => $inventories, 'title' => $title]);
    }

    public function expirare()
    {
        $inventories = Inventory::all();

        $title = 'Expira In 6 Luni';

        return view('documente.expirare', ['inventories' => $inventories, 'title' => $title]);
    }

    public function fisa_produs()
    {
        $items = Item::all();

        $title = 'Fisa Produs';

        return view('documente.fisaprodus', ['items' => $items, 'title' => $title]);
    }

    public function inventar()
    {
        $inventories = Inventory::all();

        $title = 'Inventar';

        return view('documente.inventar', ['inventories' => $inventories, 'title' => $title]);
    }

    public function balanta()
    {
        $inventories = Inventory::all();

        $categories = Category::all();

        $title = 'Balanta';

        return view('documente.balanta', ['inventories' => $inventories, 'title' => $title, 'categories' => $categories]);
    }

    public function baza_date()
    {
        $title = 'Baza de Date';

        $items = Item::with('category')->get();

        $substations = Substation::all();

        $amb_types = AmbulanceType::all();

        $ambulances = Ambulance::leftjoin('inventories', 'ambulances.inventory_id', '=', 'inventories.id')
        ->leftjoin('ambulance_types', 'ambulances.ambulance_type_id', '=', 'ambulance_types.id')
        ->select('ambulances.license_plate', 'inventories.name as sub_name', 'ambulance_types.name as ambulance_type',
        'ambulances.id')
        ->get();

        $providers = Provider::all();

        $medics = Medic::all();

        $assistents = Assistent::all();

        $m_units = MeasureUnit::all();

        $ambulanciers = Ambulancier::all();

        return view('documente.database', ['title' => $title, 'items' => $items, 'substations' => $substations, 'amb_types' => $amb_types, 'ambulances' => $ambulances, 'providers' => $providers, 'medics' => $medics, 'assistents' => $assistents, 'm_units' => $m_units, 'ambulanciers' => $ambulanciers]);
    }

    public function documente_generate() 
    {
        $title = 'Documente Generate';
        //$transfers = Transfer::with('inventory')->join('inventories', 'transfers.from_inventory_id', '=', 'inventories.id')->get();
        $transfers = Transfer::with('inventory_from')->get();
        $transfers_to = Transfer::with('inventory_to')->get();

        $nirs = Invoice::all();

        $consumptions = Consumption::with('inventory', 'ambulance', 'medic')->get();

        $returnings = Returning::with('inventory')->get();

        $entries = AvizEntry::all();

        return view('documente.documente-generate', ['title' => $title, 'transfers' => $transfers, 'transfers_to' => $transfers_to, 'nirs' => $nirs, 'consumptions' => $consumptions, 'returnings' => $returnings, 'entries' => $entries]);
    }

    public function centralizator()
    {
        $title = 'Centralizator';

        $inventories = Inventory::all();

        return view('documente.centralizator', ['title' => $title, 'inventories' => $inventories]);
    }

    public function documentatie()
    {
        return view('diverse.documentatie');
    }

    public function cancel_invoice()
    {

        //dd($invoices->last());

        $invoices_ids = [];

        $invoices = Invoice::where('aviz', 0)
        ->where('canceled', 0)
        ->get();

        foreach($invoices as $invoice) {
            $invoices_ids[] = $invoice->id;
        }

        array_unshift($invoices_ids, "");

        unset($invoices_ids[0]);

        //dd($invoices_ids);

        $items = ItemStock::with('invoice_item', 'invoice_item.invoice', 'checklist_item', 'transfer_item', 'consumption_item',
        'returning_checklist_item')
        ->chunk(200, function($items) use(&$invoices_ids) {
            foreach($items as $item) {
                if($item->transfer_item || $item->checklist_item || $item->consumption_item || $item->returning_checklist_item) {
                    //unset($invoices_ids[$item->invoice_item->invoice->id]);
                    foreach (array_keys($invoices_ids, $item->invoice_item->invoice->id, true) as $key) {
                        unset($invoices_ids[$key]);
                    }
                }
            }
        });

        
        //dd($invoices_ids);
        

        // foreach($items as $item) {
        //     $checklist = ChecklistItem::where('item_stock_id', $item->id)->first();
        //     if($checklist == null) {
        //         $r_checklist = ReturningChecklistItem::where('item_stock_id', $item->id)->first();
        //         if($r_checklist == null) {
        //             $transfer = TransferItem::where('item_stock_id', $item->id)->first();    
        //             if($transfer == null) {
        //                 $consumption = ConsumptionItem::where('item_stock_id', $item->id)->first();
        //                 if($consumption == null) {
        //                     if(!in_array($item->invoice_item->invoice->id, $invoices_ids))
        //                     {
        //                         $invoices_ids[] = $item->invoice_item->invoice->id;
        //                     }
        //                 }     
        //             }
        //         }
        //     } 
        // }

        //dd($invoices_ids);

        $invoices = Invoice::whereHas('invoice_item')
        ->with(['invoice_item', 'invoice_item.itemstock'])
        ->whereIn('id', $invoices_ids)
        ->get();

        // foreach($invoices as $invoice) {
        //     foreach($invoice->invoice_item as $invoice_item) {
        //         foreach($invoice_item->itemstock as $item) {
        //             //dd($item);
        //            $transfer = TransferItem::find($item->id);
        //            $consumption = ConsumptionItem::find($item->id);
        //            $returning = ReturningItem::find($item->id);
        //            $checklist = ChecklistItem::find($item->id);
        //            $r_checklist = ReturningChecklist::find($item->id);
        //         }
        //     }
        // }

        return view('operatiuni.cancel-invoice', ['nirs' => $invoices]);
    }

    public function pdfs_facturi($uid = null) 
    {
        $invoice = Invoice::where('uid', $uid)->first();

        $data = [
            'invoice' => $invoice,
            'user' => Auth::user(),
            'institution' => Institution::first(),
            'provider' => Provider::where('id', $invoice->provider_id)->first(),
            'items' => InvoiceItem::where('invoice_id', $invoice->id)->get()
        ];

        $html = \View::make('documente.facturi', $data);
        $html_content = $html->render();
        
        PDF::SetTitle('Hello World');
        PDF::AddPage();
        PDF::writeHTML($html_content, true, false, true, false, '');

        PDF::Output('nir.pdf');
    }

    public function pdfs_retururi($uid = null) 
    {
    $returning = Returning::with('returning_item', 'returning_item.item_stock', 'returning_item.item_stock.invoice_item',
    'returning_item.item_stock.invoice_item.invoice', 'returning_item.item_stock.invoice_item.invoice.provider')
        ->where('uid', $uid)
        ->first();

        $exploded = explode('-', $returning->document_date);

        $first_date = '01-'.$exploded[1].'-'.$exploded[0];

        $items = ReturningItem::with('item_stock', 'item_stock.invoice_item', 'item_stock.invoice_item.item', 'item_stock.invoice_item.measure_unit')
        ->where('returning_id', $returning->id)    
        ->get();

        $categories = Category::all();

        $total_values = [];

        foreach($categories as $category) {
            $total = 0;
            foreach($items as $item) {
                if($category->id == $item->item_stock->invoice_item->item->category_id) {
                    $total += ($item->item_stock->invoice_item->tva_price * $item->quantity);
                    
                }
            }
            $total_values[] = $total;
        }

        $data = [
            'returning' => $returning,
            'user' => Auth::user(),
            'institution' => Institution::first(),
            //'provider' => Provider::where('id', $returning->returning_item->first()->item_stock->invoice_item->invoice->provider_id)->first(),
            'items' => ReturningItem::with('item_stock', 'item_stock.invoice_item', 'item_stock.invoice_item.item', 'item_stock.invoice_item.measure_unit')
            ->where('returning_id', $returning->id)->get(),
            'new_date' => date("d-m-Y", strtotime($returning->document_date)),
            'first_date' => date("d-m-Y", strtotime($first_date)),
            'inventory' => Inventory::where('id', $returning->inventory_id)->first()->name,
            'categories' => Category::all(),
            'total_values' => $total_values
        ];



        //dd($data);

        //$html = \View::make('documente.facturi', $data);
        $html = \View::make('pdf-generation.returning', $data);
        $html_content = $html->render();

        PDF::setFooterCallback(function($pdf) {

            // Position at 15 mm from bottom
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 10);
            // Page number
            $pdf->Cell(0, 10, 'Pagina '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    
    });
        
        PDF::SetTitle('Retur');
        PDF::AddPage();
        PDF::writeHTML($html_content, true, false, true, false, '');

        

        PDF::Output('retur.pdf');
    }

}
