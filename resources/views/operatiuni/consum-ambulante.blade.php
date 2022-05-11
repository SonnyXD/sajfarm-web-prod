<x-layout>
    <x-container>
    <x-form id="checklist" method="POST" action="{{ route('consumptionsamb.store') }}">
        <div class="form-group row">
        <div class="col">
                    <label>Substatie:</label>
                    <div class="the-basics">
                    <select class="meds-single-select w-100" id="substation-select" name="substation-select">
                    @if( $inventories->count() )
                      @foreach ($inventories as $inventory)
                        @if($inventory->parent == 1)
                          <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                        @endif
                      @endforeach
                    @endif
                    </select>
                    </div>
                  </div>
                      <div class="col">
                    <label>Ambulanta:</label>
                    <div class="the-basics">
                    <select class="meds-single-select w-100" id="ambulance-select" name="ambulance-select">
                    @if( $ambulances->count() )
                      @foreach ($ambulances as $ambulance)
                          <option value="{{ $ambulance->id }}">{{ $ambulance->license_plate }}</option>
                      @endforeach
                    @endif
                    </select>
                    </div>
                  </div>
                  </div>
                  <div class="form-group row">
                    <!-- <div class="col">
                      <label>De la:</label>
                      <div id="the-basics">
                      <x-input type="date" name="since-date" id="since-date"/>
                      </div>
                    </div>
                    <div class="col">
                      <label>Pana la:</label>
                      <div id="the-basics">
                      <x-input type="date" name="since-date" id="since-date"/>
                      </div>
                    </div> -->
                    <div class="col">
                      <label>Data Document:</label>
                      <div id="the-basics">
                      <x-input type="date" name="document-date" id="document-date"/>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                      <x-button>Genereaza Bonul de Consum</x-button>
                </div>
        </x-form>
    </x-container>
</x-layout>