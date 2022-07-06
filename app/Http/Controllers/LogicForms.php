<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\ItemStock;
use \App\Models\Checklist;
use \App\Models\ChecklistItem;

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
           // dd($item);
            if ($item->quantity > 0) {
                //dd($item->invoice_item->measure_unit->name);
                $html .= sprintf(
                    '<option value="%s">%s [/] %s [/] %s [/] %s [/] %s</option>',
                    $item->id,
                    $item->item->name,
                    $item->invoice_item->measure_unit->name,
                    $item->quantity,
                    date("d-m-Y", strtotime($item->invoice_item->exp_date)),
                    $item->invoice_item->tva_price
                );
            }
        }

        return response($html, 200);
    }

    public function ambulance_checklists(Request $request) 
    {
        $checklists = Checklist::with('ambulance')
        ->where('ambulance_id', $request->ambulance)
        ->where('inventory_id', $request->substation)
        ->get();

/*
select ci.checklist_id, i.name, mu.name, ci.quantity
from checklist_items ci 
inner join item_stocks its on ci.item_stock_id = its.id
inner join invoice_items ini on its.invoice_item_id = ini.id
inner join items i on ini.item_id = i.id
inner join measure_units mu on ini.measure_unit_id = mu.id
inner join checklists c on ci.checklist_id = c.id
inner join ambulances a on c.ambulance_id = a.id
where ci.used = 0 and a.id = 1

*/

        $checklist_items = ChecklistItem::join('item_stocks', 'item_stocks.id', '=', 'checklist_items.item_stock_id')
        ->join('invoice_items', 'invoice_items.id', '=', 'item_stocks.invoice_item_id')
        ->join('items', 'invoice_items.item_id', '=', 'items.id')
        ->join('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
        ->join('checklists', 'checklist_items.checklist_id', '=', 'checklists.id')
        ->join('ambulances', 'checklists.ambulance_id', '=', 'ambulances.id')
        ->where('checklist_items.used', '=', 0)->where('ambulances.id', '=', $request->ambulance)
        ->select('checklists.id as checklist_id', 'items.name', 'measure_units.name as measure_unit', 'checklist_items.quantity')->get();

       //dd($checklist_items);


        $checklistItemsArray = array();
        foreach($checklist_items as $item) {
            if (!isset($checklistItemsArray[$item->checklist_id])) {
                $checklistItemsArray[$item->checklist_id] = array();
            }

            $checklistItemsArray[$item->checklist_id][] = array(
                'name' => $item->name,
                'quantity' => $item->quantity,
                'measure_unit' => $item->measure_unit
            );
        }

        //dd($checklistItemsArray);

        $html = "";

        $i = 0;

        foreach($checklists as $checklist) {
            if($checklist->used == 0 && $checklist->medic_id === null) {
                $html .= "<tr data-count=". $i .">";
                $html .= sprintf(
                    '<td>%s</td>
                    <td>%s</td>
                    <td>%s</td>',
                    $checklist->ambulance->license_plate,
                    date("d-m-Y", strtotime($checklist->checklist_date)),
                    $checklist->tour
                );

                $html .= "</tr>";

                $html .= '<tr class="treeview tr-'. $i .'">';

                $html .= '<td colspan="100%">';
                
                $html .= '<table>';

                $html .= '<thead>';

                $html .= '<tr>';

                $html .= '<th>Nume</th>';

                $html .= '<th>Cantitate</th>';

                $html .= '<th>UM</th>';

                $html .= '</tr>';

                $html .= '</thead>';

                $html .= '<tbody>';

                foreach($checklistItemsArray[$checklist->id] as $item) {

                    $html .= '<tr>';
                    $html .= '<td>'. $item['name'] .'</td>';   
                    $html .= '<td>'. $item['quantity'] .'</td>';  
                    $html .= '<td>'. $item['measure_unit'] .'</td>';   
                    $html .= '</tr>';

                }

                $html .= '</tbody>';

                $html .= '</table>';

                $html .= '</td>';

                $html .= '</tr>';

                $i++;
            }     
        }

        return response($html, 200);
    }

    public function medic_checklists(Request $request) 
    {
        $checklists = Checklist::with('medic', 'ambulance')->where('medic_id', $request->medic)->get();

        //dd($checklists);

        $checklist_items = ChecklistItem::join('item_stocks', 'item_stocks.id', '=', 'checklist_items.item_stock_id')
        ->join('invoice_items', 'invoice_items.id', '=', 'item_stocks.invoice_item_id')
        ->join('items', 'invoice_items.item_id', '=', 'items.id')
        ->join('measure_units', 'invoice_items.measure_unit_id', '=', 'measure_units.id')
        ->join('checklists', 'checklist_items.checklist_id', '=', 'checklists.id')
        ->join('ambulances', 'checklists.ambulance_id', '=', 'ambulances.id')
        ->join('medics', 'checklists.medic_id', '=', 'medics.id')
        ->where('checklist_items.used', '=', 0)->where('medics.id', '=', $request->medic)
        ->select('checklists.id as checklist_id', 'items.name', 'measure_units.name as measure_unit', 'checklist_items.quantity')->get();

        //dd($checklist_items);

        $checklistItemsArray = array();
        foreach($checklist_items as $item) {
            if (!isset($checklistItemsArray[$item->checklist_id])) {
                $checklistItemsArray[$item->checklist_id] = array();
            }

            $checklistItemsArray[$item->checklist_id][] = array(
                'name' => $item->name,
                'quantity' => $item->quantity,
                'measure_unit' => $item->measure_unit
            );
        }

        //dd($checklistItemsArray);

        $html = "";

        $i = 0;

        foreach($checklists as $checklist) {
            if($checklist->used == 0) {
                $html .= "<tr data-count=". $i .">";
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

                $html .= "</tr>";

                $html .= "</tr>";

                $html .= '<tr class="treeview tr-'. $i .'">';

                $html .= '<td colspan="100%">';
                
                $html .= '<table>';

                $html .= '<thead>';

                $html .= '<tr>';

                $html .= '<th>Nume</th>';

                $html .= '<th>Cantitate</th>';

                $html .= '<th>UM</th>';

                $html .= '</tr>';

                $html .= '</thead>';

                $html .= '<tbody>';

                if(isset($checklistItemsArray[$checklist->id])) {
                    foreach($checklistItemsArray[$checklist->id] as $item) {

                        $html .= '<tr>';
                        $html .= '<td>'. $item['name'] .'</td>';   
                        $html .= '<td>'. $item['quantity'] .'</td>';  
                        $html .= '<td>'. $item['measure_unit'] .'</td>';   
                        $html .= '</tr>';
    
                    }
                }

                

                $html .= '</tbody>';

                $html .= '</table>';

                $html .= '</td>';

                $html .= '</tr>';

                $i++;
            }

        }

        return response($html, 200);
    }
}
