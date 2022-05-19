<x-layout>
    <x-container>
        <x-form id="modificare-cant-min">
          <p class="text-dark bg-gradient-success">{{Session::get('success');}}</p>
          <p class="text-dark bg-gradient-danger">{{Session::get('error');}}</p>
        <div class="form-group">
                    <label>Medicament/Material Sanitar:</label>
                    <select class="meds-single-select w-100" id="select-meds" name="select-meds">
                      @if($items->count())
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                      @endif
                    </select>
                  </div>
                  <div class="form-group">
                        <label for="stoc-min-farm">Stoc Minim Farmacie</label>
                        <x-input type="number" id="stoc-min-farm" placeholder="Stoc Minim Farmacie" name="stoc-min-farm"/>
                    </div>
                    <div class="form-group">
                        <label for="stoc-min-stoc3">Stoc Minim Stoc 3</label>
                        <x-input type="number" id="stoc-min-stoc3" placeholder="Stoc Minim Stoc 3" name="stoc-min-stoc3"/>
                    </div>
                    <div class="form-group">
                        <x-button>Modifica</x-button>
                </div>
        </x-form>
    </x-container>
</x-layout>