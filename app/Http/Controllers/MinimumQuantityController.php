<?php

namespace App\Http\Controllers;

use App\Models\MinimumQuantity;
use Illuminate\Http\Request;
use \App\Models\Inventory;
use Session;

class MinimumQuantityController extends Controller
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
            'select-meds' => 'required',
            'stoc-min-farm' => 'required',
            'stoc-min-stoc3' => 'required'
        ));

        $farm_id = Inventory::where('name', 'Farmacie')->get()->first()->id;
        $stoc3_id = Inventory::where('name', 'Stoc 3')->get()->first()->id;

        $existing_farm = MinimumQuantity::where('item_id', '=', $request->input('select-meds'))
        ->where('inventory_id', '=', $farm_id)->first();
        if ($existing_farm === null) {
            $modify = new \App\Models\MinimumQuantity();
            $modify->item_id = $request->input('select-meds');
            $modify->inventory_id = $farm_id;
            $modify->quantity = $request->input('stoc-min-farm');
            $modify->save();
        } else {
            $existing_farm->quantity = $request->input('stoc-min-farm');
            $existing_farm->save();
        }

        $existing_stoc3 = MinimumQuantity::where('item_id', '=', $request->input('select-meds'))
        ->where('inventory_id', '=', $stoc3_id)->first();
        if ($existing_stoc3 === null) {
            $modify = new \App\Models\MinimumQuantity();
            $modify->item_id = $request->input('select-meds');
            $modify->inventory_id = $stoc3_id;
            $modify->quantity = $request->input('stoc-min-stoc3');
            $modify->save();
        } else {
            $existing_stoc3->quantity = $request->input('stoc-min-stoc3');
            $existing_stoc3->save();
        }

        // $med_id = $request->input('select-meds');

        // $modify = MinimumQuantity::whereHas('item', function ($query) use ($med_id) {
        //     return $query->where('item_id', '=', $med_id)->get()->first();
        // });

        // dd($modify);

        // if($new_item === null) {
        //     $newItem = new \App\Models\ItemStock();
        //     $newItem->quantity = $productPost['productQty'];
        //     $newItem->inventory_id = $to_location->first()->id;
        //     $newItem->item_id = $item->item_id;
        //     $newItem->invoice_item_id = $item->invoice_item_id;
        //     $newItem->save();
        // } else {
        //     $new_item->quantity += $productPost['productQty'];
        //     $new_item->save();
        // }

        // $modify = new \App\Models\MinimumQuantity();
        // $modify->item_id = $request->input('select-meds');
        // $modify->inventory_id = Inventory::where('name', 'Farmacie')->get()->first()->id;
        // $modify->quantity = $request->input('stoc-min-farm');

        // $modify->save();

        // $modify2 = new \App\Models\MinimumQuantity();
        // $modify2->item_id = $request->input('select-meds');
        // $modify2->inventory_id = Inventory::where('name', 'Stoc 3')->get()->first()->id;
        // $modify2->quantity = $request->input('stoc-min-farm');

        // $modify2->save();

        Session::flash('success', '');

        return redirect('/operatiuni/modificare-cant-min');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MinimumQuantity  $minimumQuantity
     * @return \Illuminate\Http\Response
     */
    public function show(MinimumQuantity $minimumQuantity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MinimumQuantity  $minimumQuantity
     * @return \Illuminate\Http\Response
     */
    public function edit(MinimumQuantity $minimumQuantity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MinimumQuantity  $minimumQuantity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MinimumQuantity $minimumQuantity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MinimumQuantity  $minimumQuantity
     * @return \Illuminate\Http\Response
     */
    public function destroy(MinimumQuantity $minimumQuantity)
    {
        //
    }
}
