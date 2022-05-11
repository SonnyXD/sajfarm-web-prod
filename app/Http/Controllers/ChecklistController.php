<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
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
            'tura' => 'required'
        ));
    
        $checklist = new \App\Models\Checklist();
        $checklist->inventory_id = $request->input('substation-select') ?? 2;
        $checklist->medic_id = $request->input('medic-select');
        $checklist->ambulance_id = $request->input('ambulance-select');
        $checklist->checklist_date = $request->input('document-date');
        $checklist->patient_number = $request->input('patient-number');
        $checklist->tour = $request->input('tura');
        $checklist->save();

       
    
        return redirect('/operatiuni/checklist-statii')
            ->with('success', 'Created Successfully');
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
