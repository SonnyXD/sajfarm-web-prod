<x-layout>
<div class="form-group">
        <label for="select-id">Alege</label>
        <select class="form-control" id="form-select">
            <option disabled selected>Alege o optiune..</option>
            <option value="transfers">Transferuri</option>
            <option value="consumptions">Consumuri</option>
        </select>
    </div>

<table id="transfers" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="th-sm">Nr.
      </th>
      <th class="th-sm">Nume
      </th>
      <th class="th-sm">Din
      </th>
      <th class="th-sm">In
      </th>
      <th class="th-sm">Data
      </th> 
    </tr>
  </thead>
  <tbody>
      @if($transfers->count())
        @foreach($transfers as $index => $value)
        <tr>
            <td>{{$transfers[$index]->id}}</td>
        <td><a target="_blank" href="/pdfs/transferuri/{{$transfers[$index]->uid}}">Transfer {{$transfers[$index]->id}}</a></td>
            <td>{{$transfers[$index]->inventory_from->name}}</td>
            <td>{{$transfers[$index]->inventory_to->name}}</td>
            <td>{{date("d-m-Y", strtotime($transfers[$index]->document_date))}}</td>
        </tr>
        @endforeach
      @endif
</table>

<table id="consumptions" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="th-sm">Nr.
      </th>
      <th class="th-sm">Nume
      </th>
      <th class="th-sm">Din
      </th>
      <th class="th-sm">Autosanitara/Medic
      </th>
      <th class="th-sm">Data
      </th>
    </tr>
  </thead>
  <tbody>
      @if($consumptions->count())
        @foreach($consumptions as $consumption)
        <tr>
            <td>{{$consumption->id}}</td>
        <td><a target="_blank" href="/pdfs/consumuri/{{$consumption->uid}}">Consum {{$consumption->id}}</a></td>
            <td>{{$consumption->inventory->name}}</td>
            <td>{{$consumption->ambulance->license_plate}} / {{$consumption->medic->name ?? ''}}</td>
            <td>{{date("d-m-Y", strtotime($consumption->document_date))}}</td>
        </tr>
        @endforeach
      @endif
</table>

</x-layout>
<script src="/js/different-documents.js"></script>