<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsumptionItemRequest;
use App\Http\Requests\UpdateConsumptionItemRequest;
use App\Models\ConsumptionItem;

class ConsumptionItemController extends Controller
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
     * @param  \App\Http\Requests\StoreConsumptionItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreConsumptionItemRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ConsumptionItem  $consumptionItem
     * @return \Illuminate\Http\Response
     */
    public function show(ConsumptionItem $consumptionItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ConsumptionItem  $consumptionItem
     * @return \Illuminate\Http\Response
     */
    public function edit(ConsumptionItem $consumptionItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateConsumptionItemRequest  $request
     * @param  \App\Models\ConsumptionItem  $consumptionItem
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateConsumptionItemRequest $request, ConsumptionItem $consumptionItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ConsumptionItem  $consumptionItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConsumptionItem $consumptionItem)
    {
        //
    }
}
