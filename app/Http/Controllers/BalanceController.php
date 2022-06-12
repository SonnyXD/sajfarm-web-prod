<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Inventory;
use \App\Models\Category;
use \App\Models\Institution;
use Session;
use PDF;
use Auth;


class BalanceController extends Controller
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
            'inventory-select' => 'required',
            'category-select' => 'required',
            'from-date' => 'required',
            'until-date' => 'required'
        ));

        $user = Auth::user();

        $six_months = date('Y-m-d', strtotime('+6 month'));

        $inventory_id = $request->input('inventory-select');
        $inventory_name = Inventory::where('id', '=', $inventory_id)->first()->name;

        $category_id = $request->input('category-select');
        $category_name = Category::where('id', '=', $category_id)->first()->name;

        $old_from_date = $request->input('from-date');
        $new_from_date = date("d-m-Y", strtotime($old_from_date)); 
        $old_until_date = $request->input('until-date');
        $new_until_date = date("d-m-Y", strtotime($old_until_date));

        $institution = Institution::all();

        $filename = 'balanta '. $inventory_name .' '. $new_from_date .' '. $new_until_date .'.pdf';

        $html = "<html>
                <head>
                <style>
                td, th {border: 1px solid black;}
                </style>
                </head>";
        
        $html .= ' <span style="font-weight: bold; float: left;">'. $institution[0]->name .'</span>
        <br>
        <span style="float: left;">Utilizator: '. $user->name .'</span>
        <h2 style="font-weight:bold; text-align: center;">BALANTA ANALITICA '. strtoupper($category_name) .'</h2>
        <br>
        <span style="float: right;">Gestiune: '. $inventory_name .'</span>
        <br>
        <span style="float: right;">Subgestiune: '. $category_name .'</span>
        <br>
        <span style="float: right;">Perioada: '. $new_from_date .' - '. $new_until_date .'</span>
        <br>';

        PDF::SetTitle('Balanta Analitica');
        PDF::AddPage('L', 'A4');
        PDF::writeHTML($html, true, false, true, false, '');

        //PDF::Output(public_path($filename), 'D');

        PDF::Output('name.pdf', 'I');
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
