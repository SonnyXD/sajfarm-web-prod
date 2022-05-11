<x-layout>
    <x-container>
    <x-form id="checklist" method="POST" action="{{ route('consumptionsmedic.store') }}">
        <div class="form-group row">
        <div class="col">
                    <label>Medic:</label>
                    <div class="the-basics">
                    <select class="meds-single-select w-100" id="substation-select" name="substation-select">
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
                      <label>Data:</label>
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