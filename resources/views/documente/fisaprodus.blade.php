<x-layout>
    <x-container :title="$title">
        <x-form id="fisa-produs" method="GET" action="{{ route('productfile.store') }}" target="_blank">
        <!-- <p class="text-dark bg-gradient-danger">{{Session::get('error');}}</p> -->
        <div class="form-group">
                    <label>Medicament/Material Sanitar:</label>
                    <select class="meds-single-select w-100" id="meds" name="meds">
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
                      <x-button disabled="disabled">Genereaza Fisa Produsului</x-button>
                </div>
        </x-form>
    </x-container>
</x-layout>
<script src="/js/product-file.js"></script>

@if ($errors->any())
@foreach($errors->all() as $error)
<script>
swal("Eroare", "Generare document esuata! Incearca din nou!", "error");
</script>
@endforeach
@endif

@if(Session::has('error'))
<script>
swal("Eroare", "Nu exista istoric pentru produsul respectiv in perioada selectata", "error");
</script>
@endif