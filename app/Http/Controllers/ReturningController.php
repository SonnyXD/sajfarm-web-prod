<?php

namespace App\Http\Controllers;

use App\Models\Returning;
use Illuminate\Http\Request;

class ReturningController extends Controller
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
            'from-location' => 'required',
            'document-date' => 'required'
        ));
    
        $returning = new \App\Models\Returning();
        $returning->inventory_id = $request->input('from-location');
        $returning->document_date = $request->input('document-date');
   
        $returning->save();
    
        return redirect('/operatiuni/retur')
            ->with('success', 'Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Returning  $returning
     * @return \Illuminate\Http\Response
     */
    public function show(Returning $returning)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Returning  $returning
     * @return \Illuminate\Http\Response
     */
    public function edit(Returning $returning)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Returning  $returning
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Returning $returning)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Returning  $returning
     * @return \Illuminate\Http\Response
     */
    public function destroy(Returning $returning)
    {
        //
    }
}
