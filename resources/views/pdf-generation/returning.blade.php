<!DOCTYPE html>
<html>
    <head>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <style>
  


    </style>
    </head>
    <span style="font-weight: bold;">{{$institution->name}}</span>
        <br>
        <span style="">Utilizator: {{$user->name}}</span>
        <h2 style="font-weight:bold; text-align: center;">PROCES VERBAL RETUR</h2>
        <br>
        <span style="">Numar document: {{$returning->id}} / {{$new_date}}</span>
        <br>
        <span style="">Perioada: {{$first_date}} / {{$new_date}}</span>
        <br>
        <span style="">Gestiune: {{$inventory}}</span>
        <br>
        <br>
        <br>
        @foreach($categories as $category)
            <span style="font-weight: bold;">{{$category->name}}</span><br><br>
            <table border="1">
            <thead>
        <tr nobr="true">
          <th style="font-weight: bold; text-align: center;">Denumire Produs</th>
          <th style="font-weight: bold; text-align: center;">UM</th>
          <th style="font-weight: bold; text-align: center;">Cantitate</th>
          <th style="font-weight: bold; text-align: center;">Pret</th>
          <th style="font-weight: bold; text-align: center;">TVA</th>
          <th style="font-weight: bold; text-align: center;">Valoare</th>
          <th style="font-weight: bold; text-align: center;">Lot</th>
          <th style="font-weight: bold; text-align: center;">Motiv</th>
          <th style="font-weight: bold; text-align: center;">Gestiune de iesire</th>
        </tr>
    </thead>
        @foreach($items as $item)
        @if($item->item_stock->invoice_item->item->category_id == $category->id)
        <tr nobr="true">
            <td style="text-align: center;">{{\App\Models\Item::where('id', $item->item_stock->invoice_item->item->id)->first()->name}}</td>
            <td style="text-align: center;">{{\App\Models\MeasureUnit::where('id', $item->item_stock->invoice_item->measure_unit->id)->first()->name}}</td>
            <td style="text-align: center;">{{$item->quantity}}</td>
            <td style="text-align: center;">{{\App\Models\InvoiceItem::where('id', $item->item_stock->invoice_item->id)->first()->price}}</td>
            <td style="text-align: center;">{{\App\Models\InvoiceItem::where('id', $item->item_stock->invoice_item->id)->first()->tva}}</td>
            <td style="text-align: center;">{{\App\Models\InvoiceItem::where('id', $item->item_stock->invoice_item->id)->first()->tva_price * $item->quantity}}</td>
            <td style="text-align: center;">{{\App\Models\InvoiceItem::where('id', $item->item_stock->invoice_item->id)->first()->lot}}</td>
            <td style="text-align: center;">{{$item->reason}}</td>
            <td style="text-align: center;">{{$inventory}} - {{\App\Models\Ambulance::where('id', $item->ambulance_id)->first()?->license_plate}}</td>
        </tr>
        @endif
        @endforeach
        </table><br><br>
        @endforeach
        
    

    

    <br>
    <br>

    @foreach($categories as $category) 
    <span>Total Valoare {{$category->name}}: {{$total_values[$category->id-1]}}</span>
    <br>
    @endforeach
  <br>

  <span>Gestionari:</span><br>
  <span>Farm. sef</span><br>
  <span>{{$institution->pharmacy_manager}}</span><br><br>
  <span>As. Farm.</span><br>
  <span>{{$institution->assistent}}</span>

  

</html>