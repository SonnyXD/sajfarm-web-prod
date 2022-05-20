<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\ItemStock;

class LogicForms extends Controller
{
    public function inventory_products(Request $request) 
    {

        $items = ItemStock::with('item', 'invoice_item', 'invoice_item.measure_unit')->where('inventory_id', $request->inventory)->get();

        //dd($items);

        $html = "";

        foreach( $items as $item )
        {
            if ($item->quantity > 0) {
                //dd($item->item->item->name);
                $html .= sprintf(
                    '<option value="%s">%s [/] %s [/] %s [/] %s</option>',
                    $item->id,
                    $item->item->name,
                    $item->invoice_item->measure_unit->name,
                    $item->quantity,
                    $item->invoice_item->lot
                );
            }
        }

        return response($html, 200);
    }
}
