<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\ChecklistItem;
use Illuminate\Http\Request;

class ChecklistController extends Controller
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
            'substation-select' => 'nullable',
            'medic-select' => 'nullable',
            'ambulance-select' => 'nullable',
            'document-date' => 'required',
            'patient-number' => 'nullable',
            'tura' => 'required',
            'assistent-select' => 'nullable',
            'ambulancier-select' => 'nullable'
        ));
    
        $checklist = new \App\Models\Checklist();
        $checklist->inventory_id = $request->input('substation-select');
        $checklist->medic_id = $request->input('medic-select');
        $checklist->ambulance_id = $request->input('ambulance-select');
        $checklist->checklist_date = $request->input('document-date');
        $checklist->patient_number = $request->input('patient-number');
        $checklist->assistent_id = $request->input('assistent-select') ?? null;
        $checklist->ambulancier_id = $request->input('ambulancier-select') ?? null;
        $checklist->tour = $request->input('tura');
        $checklist->used = 0;
        $checklist->save();


        $products = $request->input('product');
        
        foreach($products as $product)
        {
            $item = \App\Models\ItemStock::with('item')->find($product['productId']);
            $item->quantity -= $product['productQty'];
            $item->save();

            // $new_item = ItemStock::whereHas('invoice_item', function ($query) use ($item) {
            //     return $query->where('lot', '=', $item->invoice_item->lot);
            // })->with('checklist_item')->where('lot', $product['productLot'])->get()->first();

            $new_item = ChecklistItem::where('item_stock_id', '=', $product['productId'])->get()->first();

            if($new_item === null) {
                $newItem = new \App\Models\ChecklistItem();
                $newItem->checklist_id = $checklist->id;
                $newItem->item_id = $item->item->id;
                $newItem->item_stock_id = $product['productId'];
                $newItem->quantity = $product['productQty'];
                $newItem->used = 0;
                $newItem->save();
            } else {
                // dam doar debug daca exista deja sa vedem. ok
                $new_item->quantity += $product['productQty']; // gresit aici. gata
                $new_item->save();
                //nice. si mai e o chestie.
            }

            // $checklist_product = new \App\Models\ChecklistItem();
            // $checklist_product->checklist_id = $checklist->id;
            // $checklist_product->item_stock_id = $product['productId'];
            // $checklist_product->item_id = $item->item->id;
            // $checklist_product->quantity = $product['productQty'];
            // $checklist_product->used = 0;
            // $checklist_product->save();
        }
    
        return redirect('/operatiuni/checklist-statii')
            ->with('success', 'Checklist generat cu succes!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Checklist  $checklist
     * @return \Illuminate\Http\Response
     */
    public function show(Checklist $checklist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Checklist  $checklist
     * @return \Illuminate\Http\Response
     */
    public function edit(Checklist $checklist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Checklist  $checklist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Checklist $checklist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Checklist  $checklist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Checklist $checklist)
    {
        //
    }
}
