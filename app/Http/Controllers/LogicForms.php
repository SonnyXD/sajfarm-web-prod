<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\ItemStock;
use \App\Models\Checklist;

class LogicForms extends Controller
{
    public function inventory_products(Request $request) 
    {

        $items = ItemStock::with('item', 'invoice_item', 'invoice_item.measure_unit')->where('inventory_id', $request->inventory)->get();

        //dd($items);

        $html = "";

        foreach( $items as $item )
        {
            // if($item->invoice_item->measure_unit === null) {
            //     dd($item);
            // }
            if ($item->quantity > 0) {
                //dd($item->invoice_item->measure_unit->name);
                $html .= sprintf(
                    '<option value="%s">%s [/] %s [/] %s [/] %s</option>',
                    $item->id,
                    $item->item->name,
                    $item->invoice_item->measure_unit->name,
                    $item->quantity,
                    date("d-m-Y", strtotime($item->invoice_item->exp_date))
                );
            }
        }

        return response($html, 200);
    }

    public function ambulance_checklists(Request $request) 
    {
        $checklists = Checklist::with('ambulance')->where('ambulance_id', $request->ambulance)->get();

        $html = "";

        foreach($checklists as $checklist) {
            $html .= "<tr>";
            if($checklist->used == 0 && $checklist->medic_id === null) {
                $html .= sprintf(
                    '<td>%s</td>
                    <td>%s</td>
                    <td>%s</td>',
                    $checklist->ambulance->license_plate,
                    date("d-m-Y", strtotime($checklist->checklist_date)),
                    $checklist->tour
                );
            }

            $html .= "</tr>";
        }

        return response($html, 200);
    }

    public function medic_checklists(Request $request) 
    {
        $checklists = Checklist::with('medic', 'ambulance')->where('medic_id', $request->medic)->get();

        $html = "";

        foreach($checklists as $checklist) {
            $html .= "<tr>";
            if($checklist->used == 0) {
                $html .= sprintf(
                    '<td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>',
                    $checklist->medic->name,
                    $checklist->ambulance->license_plate,
                    date("d-m-Y", strtotime($checklist->checklist_date)),
                    $checklist->tour
                );
            }

            $html .= "</tr>";
        }

        return response($html, 200);
    }
}
