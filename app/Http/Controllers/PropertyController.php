<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;

class PropertyController extends Controller
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
            'provider-name' => 'nullable',
            'provider-office' => 'nullable',
            'provider-address' => 'nullable',
            'provider-regc' => 'nullable',
            'provider-cui' => 'nullable'
        ));

        $choice = $request->input('form-select');

        if($choice == 'furnizor') {
            if($request->input('provider-name') == null || $request->input('provider-office') == null || $request->input('provider-address') == null
            || $request->input('provider-regc') == null || $request->input('provider-cui') == null) {
                Session::flash('error');
                return redirect('/operatiuni/inserare-proprietati');
            }

            $property = new \App\Models\Provider();
            $property->name = $request->input('provider-name');
            $property->office = $request->input('provider-office');
            $property->address = $request->input('provider-address');
            $property->regc = $request->input('provider-regc');
            $property->cui = $request->input('provider-cui');
            $property->save();
        } else if($choice == 'item') {
            if($request->input('item-name') == null || $request->input('item-category') == null) {
                Session::flash('error');
                return redirect('/operatiuni/inserare-proprietati');
            }

            $property = new \App\Models\Item();
            $property->name = $request->input('item-name');
            $property->category_id = $request->input('item-category');
            $property->special = 0;
            $property->save();
        } else if($choice == 'medic') {
            if($request->input('medic-name') == null) {
                Session::flash('error');
                return redirect('/operatiuni/inserare-proprietati');
            }

            $property = new \App\Models\Medic();
            $property->name = $request->input('medic-name');
            $property->save();
        } else if($choice == 'assistent') {
            if($request->input('assistent-name') == null) {
                Session::flash('error');
                return redirect('/operatiuni/inserare-proprietati');
            }

            $property = new \App\Models\Assistent();
            $property->name = $request->input('assistent-name');
            $property->save();
        } else if($choice == 'ambulancier') {
            if($request->input('ambulancier-name') == null) {
                Session::flash('error');
                return redirect('/operatiuni/inserare-proprietati');
            }

            $property = new \App\Models\Ambulancier();
            $property->name = $request->input('ambulancier-name');
            $property->save();
        }
        
        Session::flash('success', '');

        return redirect('/operatiuni/inserare-proprietati');
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
