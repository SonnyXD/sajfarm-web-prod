<x-layout>
    <x-container :title="$title">
        <x-form id="modificare-cant-min" method="POST" action="{{ route('modify.store') }}">
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
                        <x-button disabled="disabled">Modifica</x-button>
                </div>
        </x-form>
    </x-container>
</x-layout>
<script src="/js/modify.js"></script>

@if(Session::has('success'))
<script>
swal("Succes", "Modificarile au fost efectuate cu succes!", "success");
</script>
@endif

@if ($errors->any())
@foreach($errors->all() as $error)
<script>
swal("Eroare", "Modificarile au esuat! Incearca din nou!", "error");
</script>
@endforeach
@endif