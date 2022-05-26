<!DOCTYPE html>
<html>
    <head>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <style>
    table, td, th {  
  border: 1px solid;
  text-align: left;
}

table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  padding: 5px;
}

.last { position: relative; }

    </style>
    </head>
    <span style="font-weight: bold;">{{$invoice->institution->name}}</span>
        <br>
        <span style="">Utilizator: {{$invoice->user->name}}</span>
        <h2 style="font-weight:bold; text-align: center;">NOTA DE INTRARE RECEPTIE</h2>
        <br>
        <span style="font-weight: bold;">Numar document: {{$invoice->invoice_id}} / {{$invoice->new_date}}</span>
        <br>
        <span style="font-weight: bold;">Furnizor: {{$invoice->provider->first()->name}}</span>
        <br>
        <span style="font-weight: bold;">Gestiune: DEPOZIT FARMACIE</span>
        <br>
        <span style="font-weight: bold;">Document intrare: Factura fiscala - {{$invoice->invoice_number}}</span>
        <br>
        <span style="font-weight: bold;">Data scadenta: {{$invoice->due_date}}</span>
        <br>
        <br>
        <br>
        <table>
        <tr>
          <th style="font-weight: bold; text-align: center;">Cod CIM</th>
          <th style="font-weight: bold; text-align: center;">Cod Produs</th>
          <th style="font-weight: bold; text-align: center;">Nume</th>
          <th style="font-weight: bold; text-align: center;">Lot</th>
          <th style="font-weight: bold; text-align: center; width:15%;">Data Exp.</th>
          <th style="font-weight: bold; text-align: center;">UM</th>
          <th style="font-weight: bold; text-align: center;">Cantitate</th>
          <th style="font-weight: bold; text-align: center;">Pret Unitar</th>
          <th style="font-weight: bold; text-align: center;">Pret cu TVA</th>
          <th style="font-weight: bold; text-align: center;">Valoare (RON)</th>
        </tr>
        @foreach($invoice->products as $product)
            <tr>
                <td style="font-weight: bold; text-align: center;">{{$product['productCim']}}</td>
                <td style="font-weight: bold; text-align: center;">{{$product['productCode']}}</td>
                <td style="font-weight: bold; text-align: center;">{{$product['productName']}}</td>
                <td style="font-weight: bold; text-align: center;">{{$product['productLot']}}</td>
                <td style="font-weight: bold; text-align: center; width:15%;">{{date("d-m-Y", strtotime($product['productExp']))}}</td>
                <td style="font-weight: bold; text-align: center;">{{$product['productUmText']}}</td>
                <td style="font-weight: bold; text-align: center;">{{$product['productQty']}}</td>
                <td style="font-weight: bold; text-align: center;">{{$product['productPrice']}}</td>
                <td style="font-weight: bold; text-align: center;">{{$product['productTvaPrice']}}</td>
                <td style="font-weight: bold; text-align: center;">{{$product['productValue']}}</td>
            </tr>
        @endforeach
    </table>

    <p>Valoare totala: {{$invoice->total_value}}</p>

    <br>
    <br>

    <div class="last">
    <table>
        <tr>
        <td colspan="2" style="text-align: center;">Comisia de receptie</td>
        </tr>
        <tr>
        <td style="text-align: center;">Nume si prenume</td>
        <td style="text-align: center;">Semnatura</td>
        </tr>
        <tr>
        <td colspan="1" style="height: 5%;"></td>
        <td colspan="1" style="height: 5%;"></td>
        </tr>
        <tr>
        <td colspan="1" style="height: 5%;"></td>
        <td colspan="1" style="height: 5%;"></td>
        </tr>
        <tr>
        <td colspan="1" style="height: 5%;"></td>
        <td colspan="1" style="height: 5%;"></td>
        </tr>
    </table>
  </div>
  <br>

  <span style="float: left;">Farm. Sef<br>{{$invoice->institution->pharmacy_manager}}</span>
  <span style="float: right;">As. Farm. <br>{{$invoice->institution->assistent}}</span>
  

</html>