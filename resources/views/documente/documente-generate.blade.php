<x-layout>
<div class="form-group">
        <label for="select-id">Alege</label>
        <select class="form-control" id="form-select">
            <option disabled selected>Alege o optiune..</option>
            <option value="nirs">NIR-uri</option>
            <option value="transfers">Transferuri</option>
            <option value="consumptions">Consumuri</option>
            <option value="returnings">Retururi</option>
            <option value="entries">Avize</option>
        </select>
    </div>
    <table id="nirs" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="th-sm">Nr.
      </th>
      <th class="th-sm">Nume
      </th>
      <th class="th-sm">Data
      </th>
    </tr>
  </thead>
  <tbody>
      @if($nirs->count())
        @foreach($nirs as $nir)
        <tr>
            <td>{{$nir->id}}</td>
        <td><a target="_blank" href="/pdfs/nir{{$nir->id}}.pdf">NIR {{$nir->id}}</a></td>
            <td>{{date("d-m-Y", strtotime($nir->document_date))}}</td>
        </tr>
        @endforeach
      @endif
</table>

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
        <td><a target="_blank" href="/pdfs/transfer{{$transfers[$index]->id}}.pdf">Transfer {{$transfers[$index]->id}}</a></td>
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
      <th class="th-sm">Data
      </th>
    </tr>
  </thead>
  <tbody>
      @if($consumptions->count())
        @foreach($consumptions as $consumption)
        <tr>
            <td>{{$consumption->id}}</td>
        <td><a target="_blank" href="/pdfs/consum{{$consumption->id}}.pdf">Consum {{$consumption->id}}</a></td>
            <td>{{$consumption->inventory->name}}</td>
            <td>{{date("d-m-Y", strtotime($consumption->document_date))}}</td>
        </tr>
        @endforeach
      @endif
</table>

<table id="returnings" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="th-sm">Nr.
      </th>
      <th class="th-sm">Nume
      </th>
      <th class="th-sm">Din
      </th>
      <th class="th-sm">Data
      </th>
    </tr>
  </thead>
  <tbody>
      @if($returnings->count())
        @foreach($returnings as $returning)
        <tr>
            <td>{{$returning->id}}</td>
        <td><a target="_blank" href="/pdfs/retur{{$returning->id}}.pdf">Retur {{$returning->id}}</a></td>
            <td>{{$returning->inventory->name}}</td>
            <td>{{date("d-m-Y", strtotime($returning->document_date))}}</td>
        </tr>
        @endforeach
      @endif
</table>

<table id="entries" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="th-sm">Nr.
      </th>
      <th class="th-sm">Nume
      </th>
      <th class="th-sm">Data
      </th>
    </tr>
  </thead>
  <tbody>
      @if($entries->count())
        @foreach($entries as $entry)
        <tr>
            <td>{{$entry->id}}</td>
        <td><a target="_blank" href="/pdfs/aviz{{$entry->id}}.pdf">Aviz {{$entry->id}}</a></td>
            <td>{{date("d-m-Y", strtotime($entry->document_date))}}</td>
        </tr>
        @endforeach
      @endif
</table>
</x-layout>
<script src="/js/generated-documents.js"></script>