<?php

use Illuminate\Support\Facades\Route;
use App\Models\Category;
use App\Models\Substation;
use App\Models\AmbulanceType;
use App\Models\Provider;
use App\Models\Medic;
use App\Models\Ambulance;
use App\Models\Item;
use App\Models\Unit;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ItemStock;

use \App\Http\Controllers\AmbulanceController;
use \App\Http\Controllers\AmbulanceTypeController;
use \App\Http\Controllers\AmbulancierController;
use \App\Http\Controllers\AssistentController;
use \App\Http\Controllers\AvizEntryController;
use \App\Http\Controllers\AvizentryItemController;
use \App\Http\Controllers\CategoryController;
use \App\Http\Controllers\ChecklistController;
use \App\Http\Controllers\ChecklistItemController;
use \App\Http\Controllers\ConsumptionController;
use \App\Http\Controllers\InstitutionController;
use \App\Http\Controllers\InventoryController;
use \App\Http\Controllers\InvoiceController;
use \App\Http\Controllers\InvoiceItemController;
use \App\Http\Controllers\ItemController;
use \App\Http\Controllers\ItemStockController;
use \App\Http\Controllers\MeasureUnitController;
use \App\Http\Controllers\MedicController;
use \App\Http\Controllers\MinimumQuantityController;
use \App\Http\Controllers\ProviderController;
use \App\Http\Controllers\ReturningController;
use \App\Http\Controllers\ReturningItemController;
use \App\Http\Controllers\SubstationController;
use \App\Http\Controllers\RoutesController;
use \App\Http\Controllers\AuthController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// Route::get('/', function () {
//     return view('home');
// });

Route::get('login', [AuthController::class, 'index'])->name('login');
//Route::get('register', [AuthController::class, 'registration'])->name('register');
Route::post('register', [AuthController::class, 'postRegistration'])->name('register.post');
Route::post('/', [AuthController::class, 'postLogin'])->name('login.post'); 

Route::post('logout', [AuthController::class, 'logout'])->name('logout');
//Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'index'])->name('login');

Route::get('/', array('uses' => 'App\Http\Controllers\RoutesController@home'));

Route::get('/gestiune/{inventory_slug}/{category}', array('uses' => 'App\Http\Controllers\RoutesController@inventory'));

// Route::get('gestiune/{gestiune}', function ($gestiune) {
//     //$path = __DIR__ . "/../resources/gestiune/{gestiune}";
//     // asta iti face match inainte sa ajuga aici
//     return view('home');
// });

// Route::get('/gestiune/farmacie/medicamente', function () {
//     // $path = __DIR__ . "/../resources/gestiune/{$dest}.php";

//     // if(!file_exists($path)) {
//     //     return redirect(404);
//     // }

//     //$data = LaravelMagicToGetData();
//     return view(
//         'gestiune.farmacie.medicamente',
//         array(
//             'categories' => Category::all()
//         )
//     );
//     //return 'test';
// });

// Route::get('/gestiune/farmacie/materiale-sanitare', function () {

//       return view('gestiune.farmacie.materiale');
// });

// Route::get('/gestiune/farmacie/{slug}', function ($slug) {

//     $current_cat = Category::where('slug', $slug)->first();
//     $items = $current_cat->items;

//     return view(
//         'gestiune.farmacie',
//         array(
//             'categories' => Category::all(),
//             'current_category' => $current_cat,
//             'meds' => $current_cat->meds,
//             'items' => $items
//         )
//     );
// });

// Route::get('/gestiune/stoctrei/{slug}', function ($slug) {

//     $current_cat = Category::where('slug', $slug)->first();
//     $items = $current_cat->items;

//     return view(
//         'gestiune.stoctrei',
//         array(
//             'categories' => Category::all(),
//             'current_category' => $current_cat,
//             'meds' => $current_cat->meds,
//             'items' => $items
//         )
//     );
// });

// Route::get('/gestiune/substatie', function () {

//     //$current_cat = Category::where('slug', $slug)->first();

//     return view(
//         'gestiune.substatie',
//         array(
//             'substations' => Substation::all(),
//             'items' => Item::all()
//         )
//     );
// });

// Route::get('/gestiune/substatie/{sub}/{slug}', function ($sub, $slug) {

//     $current_sub = Substation::where('name', $sub)->first();
//     $current_cat = Category::where('slug', $slug)->first();
//     $items = $current_cat->items;

//     if( $current_sub == null || ( $current_cat == null && $slug != "" ) ) {
//         abort(404);
//     }

//     return view(
//         'gestiune.generalsub',
//         array(
//             'categories' => Category::all(),
//             'current_sub' => $current_sub,
//             'current_category' => $current_cat,
//             'meds' => $current_cat->meds,
//             'items' => $items
//         )
//     );
// });

// Route::get('/gestiune/farmacie/sponsorizari', function () {

//     return view('gestiune.farmacie.sponsorizari');
// });

// Route::get('/gestiune/farmacie/donatii', function () {

//     return view('gestiune.farmacie.donatii');
// });

// Route::get('/gestiune/stoctrei/medicamente', function () {

//     return view('gestiune.stoctrei.medicamente');
// });

// Route::get('/gestiune/stoctrei/materiale-sanitare', function () {

//     return view('gestiune.stoctrei.materiale');
// });

// Route::get('/gestiune/stoctrei/dezinfectanti', function () {

//     return view('gestiune.stoctrei.dezinfectanti');
// });

// Route::get('/gestiune/stoctrei/sponsorizari', function () {

//     return view('gestiune.stoctrei.sponsorizari');
// });

// Route::get('/gestiune/stoctrei/donatii', function () {

//     return view('gestiune.stoctrei.donatii');
// });

// Route::get('/gestiune/substatie/medicamente', function () {
//     return view('gestiune');
// });

// Route::get('/gestiune/substatie/materiale-sanitare', function () {

//     return view('gestiune');
// });

// Route::get('/gestiune/substatie/dezinfectanti', function () {

//     return view('gestiune');
// });

// Route::get('/gestiune/substatie/sponsorizari', function () {

//     return view('gestiune');
// });

// Route::get('/gestiune/substatie/donatii', function () {

//     return view('gestiune');
// });

// Route::get('/gestiune/{gestiune}', function ($dest) {
//     $check = array(
//         'farmacie',
//         'stoctrei',
//         'substatie'
//     );

//     if(!in_array($dest,$check)) {
//         return redirect(404);
//     }

//     return view('gestiune');
// })->name('subgestiune');

Route::get('/testenv', function () {
    
    dd(ItemStock::with('invoice_item')->where('inventory_id', 1)->where('item_id', 1)->get());

});

Route::get('/inventory-products', array('uses' => 'App\Http\Controllers\LogicForms@inventory_products'));

Route::get('/ambulance-checklist', array('uses' => 'App\Http\Controllers\LogicForms@ambulance_checklists'));

Route::get('/available-ambulances', array('uses' => 'App\Http\Controllers\LogicForms@available_ambulances'));

Route::get('/available-medics', array('uses' => 'App\Http\Controllers\LogicForms@available_medics'));

Route::get('/medic-checklist', array('uses' => 'App\Http\Controllers\LogicForms@medic_checklists'));

Route::get('/returning-checklist', array('uses' => 'App\Http\Controllers\LogicForms@returning_checklists'));

Route::get('/operatiuni/intrare-factura', array('uses' => 'App\Http\Controllers\RoutesController@invoice'));

Route::get('/operatiuni/checklist-statii', array('uses' => 'App\Http\Controllers\RoutesController@station_checklist'));

Route::get('/operatiuni/checklist-medici', array('uses' => 'App\Http\Controllers\RoutesController@medic_checklist'));

Route::get('/operatiuni/bon-transfer', array('uses' => 'App\Http\Controllers\RoutesController@bon_transfer'));

Route::get('/operatiuni/bon-consum-ambulante', array('uses' => 'App\Http\Controllers\RoutesController@bon_consum_ambulante'));

Route::get('/operatiuni/bon-consum-medici', array('uses' => 'App\Http\Controllers\RoutesController@bon_consum_medici'));

Route::get('/operatiuni/aviz-intrare', array('uses' => 'App\Http\Controllers\RoutesController@aviz_intrare'));

Route::get('/operatiuni/checklist-retur', array('uses' => 'App\Http\Controllers\RoutesController@returning_checklist'));

Route::get('/operatiuni/retur', array('uses' => 'App\Http\Controllers\RoutesController@returning'));

Route::get('/operatiuni/modificare-cant-min', array('uses' => 'App\Http\Controllers\RoutesController@min_cant'));

Route::get('/operatiuni/inserare-proprietati', array('uses' => 'App\Http\Controllers\RoutesController@proprietati'));

Route::get('/documente/rapoarte', array('uses' => 'App\Http\Controllers\RoutesController@rapoarte'));

Route::get('/documente/expira-in-6-luni', array('uses' => 'App\Http\Controllers\RoutesController@expirare'));

Route::get('/documente/fisa-produs', array('uses' => 'App\Http\Controllers\RoutesController@fisa_produs'));

Route::get('/documente/inventar', array('uses' => 'App\Http\Controllers\RoutesController@inventar'));

Route::get('/documente/balanta', array('uses' => 'App\Http\Controllers\RoutesController@balanta'));

Route::get('/documente/baza-de-date', array('uses' => 'App\Http\Controllers\RoutesController@baza_date'));

Route::get('/documente/documente-generate', array('uses' => 'App\Http\Controllers\RoutesController@documente_generate'));

Route::get('/documente/centralizator', array('uses' => 'App\Http\Controllers\RoutesController@centralizator'));

//Route::get('/fun', [App\Http\Controllers\GeneratePDFController::class, 'invoice']);

Route::get('intrare-factura', 'App\Http\Controllers\InvoiceController@store')->name('invoices.store');

//Route::post('intrare-factura', 'App\Http\Controllers\GeneratePDFController@invoice')->name('invoicepdf.store');

Route::post('checklist-statii', 'App\Http\Controllers\ChecklistController@store')->name('checklists.store');

Route::post('checklist-medici', 'App\Http\Controllers\ChecklistController@store')->name('checklistsmedic.store');

Route::post('bon-transfer', 'App\Http\Controllers\TransferController@store')->name('transfers.store');

Route::post('bon-consum-ambulante', 'App\Http\Controllers\ConsumptionController@store')->name('consumptionsamb.store');

Route::post('retur', 'App\Http\Controllers\ReturningController@store')->name('returning.store');

Route::post('bon-consum-medici', 'App\Http\Controllers\ConsumptionController@store')->name('consumptionsmedic.store');

Route::post('aviz-intrare', 'App\Http\Controllers\AvizEntryController@store')->name('avizentries.store');

Route::post('checklist-retur', 'App\Http\Controllers\ReturningChecklistController@store')->name('returningchecklist.store');

Route::post('inserare-proprietati', 'App\Http\Controllers\ProviderController@store')->name('provider.store');

Route::post('modificare-cant-min', 'App\Http\Controllers\MinimumQuantityController@store')->name('modify.store');

Route::get('fisa-produs', 'App\Http\Controllers\ProductFileController@store')->name('productfile.store');

Route::get('balanta', 'App\Http\Controllers\BalanceController@store')->name('balance.store');

Route::get('centralizator', 'App\Http\Controllers\CentralizatorController@store')->name('centralizator.store');

Route::get('rapoarte', 'App\Http\Controllers\ReportController@store')->name('report.store');

Route::get('expira-in-6-luni', 'App\Http\Controllers\ExpirareController@store')->name('expirare.store');

Route::get('inventar', 'App\Http\Controllers\InventoryController@store')->name('inventory.store');


//Route::post('intrare-factura', 'App\Http\Controllers\InvoiceItemController@store')->name('invoiceitems.store');

// Route::get('/operatiuni/intrare-factura', function () {

//     //$providers = Provider::where('name', $prov)->all();

//     return view(
//         'operatiuni.factura',
//         array(
//             'categories' => Category::all(),
//             'providers' => Provider::all(),
//             'items' => Item::all(),
//             'units' => Unit::all(),
//             'invoices' => Invoice::all()
//         )
//     );
// });

// Route::get('/operatiuni/checklist-statii', function () {

//     return view(
//         'operatiuni.checklist-statii',
//         array(
//             'categories' => Category::all(),
//             'providers' => Provider::all(),
//             'substations' => Substation::all(),
//             'medics' => Medic::all(),
//             'ambulances' => Ambulance::all()
//         )
//     );
// });

// Route::get('/operatiuni/checklist-medici', function () {

//     return view(
//         'operatiuni.checklist-medici',
//         array(
//             'categories' => Category::all(),
//             'providers' => Provider::all(),
//             'substations' => Substation::all(),
//             'medics' => Medic::all(),
//             'ambulances' => Ambulance::all()
//         )
//     );
// });

// Route::get('/operatiuni/bon-transfer', function () {

//     return view(
//         'operatiuni.transfer',
//         array(
//             'categories' => Category::all(),
//             'providers' => Provider::all(),
//             'substations' => Substation::all()
//         )
//     );
// });

// Route::get('/operatiuni/bon-consum-ambulante', function () {

//     return view(
//         'operatiuni.consum-ambulante',
//         array(
//             'categories' => Category::all(),
//             'providers' => Provider::all(),
//             'substations' => Substation::all(),
//             'ambulances' => Ambulance::all()
//         )
//     );
// });

// Route::get('/operatiuni/bon-consum-medici', function () {

//     return view(
//         'operatiuni.consum-medici',
//         array(
//             'categories' => Category::all(),
//             'providers' => Provider::all(),
//             'substations' => Substation::all(),
//             'medics' => Medic::all(),
//             'ambulances' => Ambulance::all()
//         )
//     );
// });

// Route::get('/operatiuni/aviz-intrare', function () {

//     return view(
//         'operatiuni.aviz',
//         array(
//             'categories' => Category::all(),
//             'providers' => Provider::all(),
//             'substations' => Substation::all(),
//             'units' => Unit::all()
//         )
//     );
// });

// Route::get('/operatiuni/retur', function () {

//     return view(
//         'operatiuni.retur',
//         array(
//             'categories' => Category::all(),
//             'providers' => Provider::all(),
//             'substations' => Substation::all()
//         )
//     );
// });

// Route::get('/operatiuni/modificare-cant-min', function () {

//     return view('operatiuni.modificare');
// });

// Route::get('/operatiuni/inserare-proprietati', function () {



//     return view('operatiuni.proprietati', array(
//         'substations' => Substation::all(),
//         'ambulanceTypes' => AmbulanceType::all(),
//         'categories' => Category::all()
//     ));
// });

// Route::get('/documente/rapoarte', function () {

//     return view('documente.raport', array(
//         'substations' => Substation::all(),
//         'ambulanceTypes' => AmbulanceType::all(),
//         'categories' => Category::all()
//     ));
// });

// Route::get('/documente/expira-in-6-luni', function () {

//     return view('documente.expirare');
// });

// Route::get('/documente/fisa-produs', function () {

//     return view('documente.fisaprodus');
// });

// Route::get('/documente/inventar', function () {

//     return view('documente.inventar', array(
//         'substations' => Substation::all(),
//         'ambulanceTypes' => AmbulanceType::all(),
//         'categories' => Category::all()
//     ));
// });

// Route::get('/documente/balanta', function () {

//     return view('documente.balanta', array(
//         'substations' => Substation::all(),
//         'ambulanceTypes' => AmbulanceType::all(),
//         'categories' => Category::all()
//     ));
// });

// Route::get('/documente/baza-de-date', function () {

//     return view('documente.database');
// });

// Route::get('/documente/{document}', function ($dest) {
//     $path = __DIR__ . "/../resources/documente/{$dest}.php";

//     if(!file_exists($path)) {
//         return redirect(404);
//     }

//     return view('document');
// });

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified'
// ])->group(function () {
//     Route::get('/', function () {
//         return view('home');
//     })->name('home');
// });


//IMPORTAAAAANT
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

// require __DIR__.'/auth.php';