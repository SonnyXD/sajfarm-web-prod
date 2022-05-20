<x-layout>
    <x-container :title="$title">
        <x-form id="fisa-produs">
        <div class="form-group">
                    <label>Medicament/Material Sanitar:</label>
                    <select class="meds-single-select w-100">
                      @if($items->count())
                        @foreach($items as $item)
                          <option value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                      @endif
                    </select>
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
                  </div>
                  <div class="form-group">
                      <x-button>Genereaza Fisa Produsului</x-button>
                </div>
        </x-form>
    </x-container>
</x-layout>