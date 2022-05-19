<x-layout>
    <x-container>
    <x-form id="checklist" method="POST" action="{{ route('consumptionsmedic.store') }}">
    <p class="text-dark bg-gradient-success">{{Session::get('success');}}</p>
    <p class="text-dark bg-gradient-danger">{{Session::get('error');}}</p>
        <div class="form-group row">
        <div class="col">
        <label>Substatie:</label>
                    <div class="the-basics">
                    <select class="meds-single-select w-100" id="substation-select" name="substation-select">
                    @if( $inventories->count() )
                      @foreach ($inventories as $inventory)
                        @if($inventory->parent == 1 || $inventory->name == 'Stoc 3')
                          <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                        @endif
                      @endforeach
                    @endif
                    </select>
                    </div>
                  </div>
        <div class="col">
                    <label>Medic:</label>
                    <div class="the-basics">
                    <select class="meds-single-select w-100" id="medic-select" name="medic-select">
                    @if( $medics->count() )
                      @foreach ($medics as $medic)
                          <option value="{{ $medic->id }}">{{ $medic->name }}</option>
                      @endforeach
                    @endif
                    </select>
                    </div>
                  </div>
                      
                  </div>
                  <div class="form-group row">
                  <div class="col">
                      <label>De la:</label>
                      <div id="the-basics">
                      <x-input type="date" name="from-date" id="from-date"/>
                      </div>
                    </div>
                  
                  <div class="col">
                      <label>Pana la:</label>
                      <div id="the-basics">
                      <x-input type="date" name="until-date" id="until-date"/>
                      </div>
                    </div>
                    <div class="col">
                      <label>Data document:</label>
                      <div id="the-basics">
                      <x-input type="date" name="document-date" id="document-date"/>
                      </div>
                    </div>
                    <div class="col">
                      <label>Numar:</label>
                      <div id="the-basics">
                      @if( !$consumptions->count() )
                          <x-input type="number" name="consumption-number" id="consumption-number" disabled="disabled" value="1"/>
                        @else 
                          <x-input type="number" name="consumption-number" id="consumption-number" disabled="disabled" value="{{$consumptions->last()->id + 1}}"/>
                        @endif
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                      <x-button>Genereaza Bonul de Consum</x-button>
                </div>
        </x-form>
    </x-container>
</x-layout>
<script src="/js/medic-consume.js"></script>