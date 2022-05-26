<x-layout>
    <x-container :title="$title">
    <x-form id="checklist" method="POST" action="{{ route('consumptionsamb.store') }}">
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
                      <label>Data Document:</label>
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
                      <x-button disabled="disabled">Genereaza Bonul de Consum</x-button>
                </div>
        </x-form>
    </x-container>

    <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title" id="section-title">Checklisturi</h4>
                  <div class="table-responsive">
                    <table class="table table-striped" id="amb-checklists">
                      <thead>
                        <tr class="header">
                          <th>
                            Ambulanta
                          </th>
                          <th>
                            Data 
                          </th>
                          <th>
                            Tura
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                  
                      </tbody>
                    </table>
                  </div>
                </div>
</x-layout>
<script src="/js/amb-consume.js"></script>