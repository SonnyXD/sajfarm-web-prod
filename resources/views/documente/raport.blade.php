<x-layout>
    <x-container :title="$title">
      <x-form id="raport" method="GET" action="{{ route('report.store') }}" target="_blank">
      <p class="text-dark bg-gradient-danger">{{Session::get('error');}}</p>
        <div class="form-group row">
                <div class="col">
            <label>Tip raport:</label>
            <select class="form-control" id="report-type" name="report-type">
                <option value="1">Consum</option>
                <option value="2">Retur</option>
            </select>
        </div>
        <div class="col" id="sub-picker">
            <label>Substatie:</label>
            <select class="form-control" id="substation-select" name="substation-select">
                @if( $inventories->count() )
                      @foreach ($inventories as $inventory)
                            @if($inventory->id == 2)
                              <option value="{{ $inventory->id }}">Statie centrala</option>
                            @else
                              <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                            @endif
                      @endforeach
                @endif
            </select>
        </div>
        </div>
        <div class="form-group row">
                    <div class="col">
                      <label>De la:</label>
                      <div id="bloodhound">
                        <x-input type="date" name="from-date" id="from-date"/>
                      </div>
                    </div>
                    <div class="col">
                      <label>Pana la:</label>
                      <div id="bloodhound">
                        <x-input type="date" name="until-date" id="until-date"/>
                      </div>
                    </div>
                    <div class="col">
                    <label id="amb-label">Ambulanta:</label>
                    <div class="the-basics">
                    <select class="meds-single-select w-100" id="ambulance-select" name="ambulance-select">
                      <option value="0" selected>Toate ambulantele</option>
                    @if( $ambulances->count() )
                      @foreach ($ambulances as $ambulance)
                          <option value="{{ $ambulance->id }}">{{ $ambulance->license_plate }}</option>
                      @endforeach
                    @endif
                    </select>
                    </div>
                  </div>
                  </div>
                  <div class="form-group">
                      <x-button>Vizualizeaza raportul</x-button>
                </div>
        </x-form>
    </x-container>
</x-layout>
<script src="/js/reports.js"></script>