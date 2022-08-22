<x-layout>
    <x-container :title="$title">
        <!-- <p class="text-dark bg-gradient-success">{{Session::get('success');}}</p>
        <p class="text-dark bg-gradient-danger">{{Session::get('error');}}</p> -->
    <div class="form-group">
        <label for="select-id">Alege</label>
        <select class="form-control" id="form-select">
            <option disabled selected>Alege o optiune..</option>
            <option value="furnizor">Furnizor</option>
        </select>
    </div>
    <x-form id="add-furnizor" method="POST" action="{{ route('provider.store') }}">
        <div class="form-group">
            <label for="provider-name">Nume Furnizor</label>
            <x-input id="provider-name" placeholder="SC EXEMPLU SRL" name="provider-name"/>
        </div>
        <div class="form-group">
            <label for="provider-office">Sediu</label>
            <x-input id="provider-office" name="provider-office"/>
        </div>
        <div class="form-group">
            <label for="provider-address">Adresa</label>
            <x-input id="provider-address" name="provider-address"/>
        </div>
        <div class="form-group">
            <label for="provider-regc">Registrul Comertului</label>
            <x-input id="provider-regc" name="provider-regc"/>
        </div>
        <div class="form-group">
            <label for="provider-cui">CUI</label>
            <x-input id="provider-cui" name="provider-cui"/>
        </div>
        <x-button>Adauga</x-buttin>
    </x-form>
    
    </x-container>
    
</x-layout>
<script src="/js/properties.js"></script>

@if(Session::has('success'))
<script>
swal("Succes", "Inserare efectuata cu succes!", "success");
</script>
@endif

@if ($errors->any())
@foreach($errors->all() as $error)
<script>
swal("Eroare", "Inserare esuata! Te rog incearca din nou!", "error");
</script>
@endforeach
@endif