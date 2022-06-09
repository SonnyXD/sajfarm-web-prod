<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\ItemStock;
use App\Models\Institution;
use Illuminate\Http\Request;
use Session;
use PDF;
use Auth;

class TransferController extends Controller
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
            'from-location-id' => 'required',
            'to-location-id' => 'required',
            'document-date' => 'required'
        ));
    
        $transfer = new \App\Models\Transfer();
        $transfer->from_inventory_id = $request->input('from-location-id');
        $transfer->to_inventory_id = $request->input('to-location-id');
        $transfer->document_date = $request->input('document-date');
        $transfer->save();

        $old_date = $request->input('document-date');
        $new_date = date("d-m-Y", strtotime($old_date));

        $from_location = Inventory::where('id', $request->input('from-location-id'))->get();
        $to_location = Inventory::where('id', $request->input('to-location-id'))->get();

        $user = Auth::user();
        $transfers = Transfer::all();
        $transfer_id = $transfers->last()->id;
        $institution = Institution::all();

        $filename = 'pdfs/transfer'.$transfer_id.'.pdf';

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
        <h2 style="font-weight:bold; text-align: center;">BON DE TRANSFER</h2>
        <br>
        <span style="float: right;">Numar document: '. $transfer_id . ' / ' . $new_date .'</span>
        <br>
        <span style="float: right;">Gestiune de iesire: '. $from_location->first()->name .'</span>
        <br>
        <span style="float: right;">Gestiune de intrare: '. $to_location->first()->name .'</span>
        <br>
        <br>
        <br>
        <br>
';

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
        </tr>
        ';

        // $products = collect($request->input('product'));
        // $ids = $products->pluck('item_stock_id');
        // $detailedItems = \App\Models\ItemStock::with('item', 'invoice_item', 'invoice_item.measure_unit')
        //     ->whereIn('id', $ids)
        //     ->get();
        // foreach ($products as $product) {
        //     //dd($product);
        //     $stockInfo = $detailedItems->find('id', $product['productId']);
        //     dd($stockInfo);
        // }

        $total_value = 0;

        
        
        foreach($request->input('product') as $productPost) {
            //dd($productPost['productId']);
            //$detailedItem = \App\Models\ItemStock::with('item', 'invoice_item', 'invoice_item.measure_unit')->find($productPost->item_stock_id);
            $detailedItem = \App\Models\ItemStock::with('item', 'invoice_item', 'invoice_item.measure_unit')->find($productPost['productId']);
            //dd($itemStock);
            $html.= '<tr>
                <td style="text-align: center;">'. $detailedItem->invoice_item->product_code .'</td>
                <td style="text-align: center;">'. $productPost['productName'] .'</td>
                <td style="text-align: center;">'. $productPost['productUmText'] .'</td>
                <td style="text-align: center;">'. $productPost['productQty'] .'</td>
                <td style="text-align: center;">'. $detailedItem->invoice_item->price .'</td>
                <td style="text-align: center;">'. $detailedItem->invoice_item->price * $productPost['productQty'] .'</td>
                <td style="text-align: center;">'. $detailedItem->invoice_item->lot .'</td>
                <td style="text-align: center;">'. date("d-m-Y", strtotime($detailedItem->invoice_item->exp_date)) .'</td>
            </tr>';

            $total_value += $detailedItem->invoice_item->price * $productPost['productQty'];

        $item = ItemStock::with('invoice_item')->where('id', $productPost['productId'])->get()->first();
        $item->quantity -= $productPost['productQty'];
        $item->save();
        //$new_item = ItemStock::with('invoiceitem')->where('lot', $item->lot)->get()->first();

        $new_item = ItemStock::whereHas('invoice_item', function ($query) use ($item) {
            return $query->where('lot', '=', $item->invoice_item->lot);
        })->where('inventory_id', $request->input('to-location'))->get()->first();

        // nu ai inventory id cu 1 aici, da, dar daca nu am, tocmai ca inserez o linie noua in item_ ok. testam.
        // Tre sa fie in inventory, ca altfel il ia tot pe primul :))). aaa ok :)) dai sa testam sa vad ce returneazaok
        // in ce inventory id tre sa se duca  ? primul 1
        // stai asa putin.ok
        // Tu transferi dintr-o gestiune in alta, corect ?da

        // mai fa acelasi transfer odata, sa vedem daca adauga. ok

        // Vezi acu.ok        dd($new_item);
        if($new_item === null) {
            $newItem = new \App\Models\ItemStock();
            $newItem->quantity = $productPost['productQty'];
            $newItem->inventory_id = $to_location->first()->id;
            $newItem->item_id = $item->item_id;
            $newItem->invoice_item_id = $item->invoice_item_id;
            $newItem->save();
        } else {
            // dam doar debug daca exista deja sa vedem. ok
            $new_item->quantity += $productPost['productQty']; // gresit aici. gata
            $new_item->save();
            //nice. si mai e o chestie.
        }
        // $newItem = new \App\Models\ItemStock();
        // $newItem->quantity = $productPost['productQty'];
        // $newItem->inventory_id = $to_location->first()->id;
        // $newItem->item_id = $item->item_id;
        // $newItem->invoice_item_id = $item->invoice_item_id;
        // $newItem->save();

        $transfer_item = new \App\Models\TransferItem();

        $transfer_item->item_stock_id = $productPost['productId'];
        $transfer_item->transfer_id = $transfer->id;
        $transfer_item->item_id = $item->item_id;
        $transfer_item->quantity = $productPost['productQty'];

        $transfer_item->save();

        }
        $html .= '<br>';

        $html .= '</table><br><br>';

        $html .= 'Total valoare: '. $total_value .'';

        $html .= '<br><br>';

        $html .= 'GESTIONAR<br>
        '. $institution[0]->pharmacy_manager .'<br><br>';

        $html .= '<span style="float: left;">Farm. Sef<br>'.$institution[0]->pharmacy_manager.'</span><br><br>
                  <span style="float: right;">As. Farm. <br>'.$institution[0]->assistent.'<br><br></span>
                  <p style="text-align: right;">Primitor:</p>';

        $html .= '</html>';

        PDF::SetTitle('Transfer');
        PDF::AddPage('L', 'A4');
        PDF::writeHTML($html, true, false, true, false, '');

        PDF::Output(public_path($filename), 'F');

        Session::flash('fileToDownload', url($filename));

        return redirect('/operatiuni/bon-transfer')
            ->with('success', 'Transfer efectuat cu succes!')->with('download',);

        // $medicament = new \App\Models\ItemStock();
        // $medicament->id = 1;
        // $medicament->inventory_id = 1;
        // $medicament->save();

        // Invoice($products, $factura);

        //$item = Item::find(id-ul_tau)->get();
        //$item->quantity -= cantitatea_ta;
        //$item->save();
        //$newItem = new Item();
        //$newItem->quantity = cantitatea_ta;
        //$newItem->inventory_id = id-ul_tau;
        //$newItem->item_id = $item->item_id;

    
        // return redirect('/operatiuni/bon-transfer')
        //     ->with('success', 'Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function show(Transfer $transfer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function edit(Transfer $transfer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transfer $transfer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transfer $transfer)
    {
        //
    }
}
