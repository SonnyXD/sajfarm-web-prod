<?php

namespace App\Http\Controllers;

use App\Models\AvizEntry;
use Illuminate\Http\Request;

class AvizEntryController extends Controller
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
            'type' => 'required',
            'furnizor-select' => 'required',
            'document-number' => 'required',
            'document-date' => 'required',
            'due-date' => 'required',
            'discount-procent' => 'required',
            'discount-value' => 'required',
            'total-value' => 'required'
        ));
    
        $aviz = new \App\Models\AvizEntry();
        $aviz->type = $request->input('type');
        $aviz->provider_id = $request->input('furnizor-select');
        $aviz->number = $request->input('document-number');
        $aviz->document_date = $request->input('document-date');
        $aviz->due_date = $request->input('due-date');
        $aviz->discount_procent = $request->input('discount-procent');
        $aviz->discount_value = $request->input('discount-value');
        $aviz->total = $request->input('total-value');
        $aviz->save();
    
        return redirect('/operatiuni/aviz-intrare')
            ->with('success', 'Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AvizEntry  $avizEntry
     * @return \Illuminate\Http\Response
     */
    public function show(AvizEntry $avizEntry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AvizEntry  $avizEntry
     * @return \Illuminate\Http\Response
     */
    public function edit(AvizEntry $avizEntry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AvizEntry  $avizEntry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AvizEntry $avizEntry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AvizEntry  $avizEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy(AvizEntry $avizEntry)
    {
        //
    }
}
