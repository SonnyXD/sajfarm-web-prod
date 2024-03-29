<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Returning;
use App\Models\ReturningItem;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\ItemStock;
use App\Models\Institution; 
use App\Models\Ambulance; 
use App\Models\ReturningChecklist; 
use Session;
use PDF;
use Auth;

class ReturningChecklistController extends Controller
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
            'from-location-id' => 'required',
            'final-document-date' => 'nullable',
            'product' => 'required'
        ));

        $user = Auth::user();

        if($request->input('final-document-date') == null) {
            Session::flash('error');
            return redirect('/operatiuni/checklist-retur');
        }

        if($user == null) {
            return redirect('/login');
        }

        foreach($request->input('product') as $product) {
            if($product['productId'] == null || $product['productName'] == null || $product['productUmText'] == null 
            || $product['productQty'] == null || $product['productReason'] == null || $product['productAmb'] == null) {
                Session::flash('error');
                return redirect('/operatiuni/checklist-retur');
            }

            $stock = ItemStock::where('id', $product['productId'])->first()->quantity;
            if($stock < $product['productQty']) {
                Session::flash('error');
                return redirect('/operatiuni/checklist-retur');
            }
        }

        $returning_checklist = new \App\Models\ReturningChecklist();
        $returning_checklist->inventory_id = $request->input('from-location-id');
        $returning_checklist->checklist_date = $request->input('final-document-date');
        $returning_checklist->used = 0;
        // $returning_checklist->user = $user->name;
        $returning_checklist->save();

        $products = $request->input('product');
        
        foreach($products as $product) {
            $item = \App\Models\ItemStock::with('item')->find($product['productId']);
            $item->quantity -= $product['productQty'];
            $item->save();
            
            if($product['productAmb'] == 0) {
                $product['productAmb'] = null;
            }

            $returning_checklist_item = new \App\Models\ReturningChecklistItem;
            $returning_checklist_item->checklist_id = $returning_checklist->id;
            $returning_checklist_item->item_id = $item->item->id;
            $returning_checklist_item->item_stock_id = $product['productId'];
            $returning_checklist_item->quantity = $product['productQty'];
            $returning_checklist_item->reason = $product['productReason'];
            $returning_checklist_item->ambulance_id = $product['productAmb'];
            $returning_checklist_item->save();
        }

        Session::flash('success', 'Checklist retur generat cu succes!');

        return redirect('/operatiuni/checklist-retur');
        
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
