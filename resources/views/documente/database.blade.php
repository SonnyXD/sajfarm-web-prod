<x-layout>
        <div class="form-group">
        <label for="select-id">Alege</label>
        <select class="form-control" id="form-select">
            <option disabled selected>Alege o optiune..</option>
            <option value="nomenclator">Nomenclator</option>
            <option value="substatie">Substatie</option>
            <option value="tip-ambulanta">Tip Ambulanta</option>
            <option value="ambulanta">Ambulanta</option>
            <option value="furnizor">Furnizor</option>
            <option value="medic">Medic</option>
            <option value="asistent">Asistent</option>
            <option value="unitate">Unitate Masura</option>
        </select>
    </div>

    <table id="nomenclator" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="th-sm">Nr.
      </th>
      <th class="th-sm">Nume
      </th>
      <th class="th-sm">Categorie
      </th>
    </tr>
  </thead>
  <tbody>
      @if($items->count())
        @foreach($items as $item)
        <tr>
            <td>{{$item->id}}</td>
            <td>{{$item->name}}</td>
            <td>{{$item->category->name}}</td>
        </tr>
        @endforeach
      @endif
</table>

<table id="substatie" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="th-sm">Nr.
      </th>
      <th class="th-sm">Nume
      </th>
    </tr>
  </thead>
  <tbody>
      @if($substations->count())
        @foreach($substations as $substation)
        <tr>
            <td>{{$substation->id}}</td>
            <td>{{$substation->name}}</td>
        </tr>
        @endforeach
      @endif
</table>

<table id="tip-ambulanta" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="th-sm">Nr.
      </th>
      <th class="th-sm">Nume
      </th>
    </tr>
  </thead>
  <tbody>
      @if($amb_types->count())
        @foreach($amb_types as $amb_type)
        <tr>
            <td>{{$amb_type->id}}</td>
            <td>{{$amb_type->name}}</td>
        </tr>
        @endforeach
      @endif
</table>

<table id="ambulanta" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="th-sm">Nr.
      </th>
      <th class="th-sm">Nr. Inmatriculare
      </th>
      <th class="th-sm">Tip Ambulanta
      </th>
      <th class="th-sm">Substatie
      </th>
    </tr>
  </thead>
  <tbody>
      @if($ambulances->count())
        @foreach($ambulances as $ambulance)
        <tr>
            <td>{{$ambulance->id}}</td>
            <td>{{$ambulance->license_plate}}</td>
            <td>{{$ambulance->ambulance_type}}</td>
            <td>{{$ambulance->sub_name}}</td>
        </tr>
        @endforeach
      @endif
</table>

<table id="furnizor" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="th-sm">Nr.
      </th>
      <th class="th-sm">Nume
      </th>
      <th class="th-sm">Sediu
      </th>
      <th class="th-sm">Adresa
      </th>
      <th class="th-sm">Reg. Com.
      </th>
      <th class="th-sm">CUI
      </th>
    </tr>
  </thead>
  <tbody>
      @if($providers->count())
        @foreach($providers as $provider)
        <tr>
            <td>{{$provider->id}}</td>
            <td>{{$provider->name}}</td>
            <td>{{$provider->office}}</td>
            <td>{{$provider->address}}</td>
            <td>{{$provider->regc}}</td>
            <td>{{$provider->cui}}</td>
        </tr>
        @endforeach
      @endif
</table>

<table id="medic" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="th-sm">Nr.
      </th>
      <th class="th-sm">Nume
      </th>
    </tr>
  </thead>
  <tbody>
      @if($medics->count())
        @foreach($medics as $medic)
        <tr>
            <td>{{$medic->id}}</td>
            <td>{{$medic->name}}</td>
        </tr>
        @endforeach
      @endif
</table>

<table id="asistent" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="th-sm">Nr.
      </th>
      <th class="th-sm">Nume
      </th>
    </tr>
  </thead>
  <tbody>
      @if($assistents->count())
        @foreach($assistents as $assistent)
        <tr>
            <td>{{$assistent->id}}</td>
            <td>{{$assistent->name}}</td>
        </tr>
        @endforeach
      @endif
</table>

<table id="unitate" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="th-sm">Nr.
      </th>
      <th class="th-sm">Nume
      </th>
    </tr>
  </thead>
  <tbody>
      @if($m_units->count())
        @foreach($m_units as $m_unit)
        <tr>
            <td>{{$m_unit->id}}</td>
            <td>{{$m_unit->name}}</td>
        </tr>
        @endforeach
      @endif
</table>
    
</x-layout>
<script src="/js/database-info.js"></script>