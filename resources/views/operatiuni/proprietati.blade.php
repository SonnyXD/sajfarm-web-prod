<x-layout>
    <x-container :title="$title">
        <!-- <p class="text-dark bg-gradient-success">{{Session::get('success');}}</p>
        <p class="text-dark bg-gradient-danger">{{Session::get('error');}}</p> -->
        <x-form id="add-property" method="POST" action="{{ route('property.store') }}">
    <div class="form-group">
        <label for="select-id">Alege</label>
        <select class="form-control" id="form-select" name="form-select">
            <!-- <option disabled selected>Alege o optiune..</option> -->
            <option value="furnizor">Furnizor</option>
            <option value="item">Produs</option>
            <option value="medic">Medic</option>
            <option value="ambulancier">Ambulantier</option>
            <option value="assistent">Asistent</option>
        </select>
    </div>
        <div id="provider">
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
    </div>

        <div id="item">
        <div class="form-group">
            <label for="item-name">Nume Produs</label>
            <x-input id="item-name" name="item-name"/>
        </div>
        <div class="form-group">
            <label for="item-category">Subgestiune</label>
            <select class="form-control" id="item-category" name="item-category">
            @if( $categories->count() )
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            @endif
            </select>
        </div>
    </div>

    <div id="medic">
        <div class="form-group">
            <label for="medic-name">Nume Medic</label>
            <x-input id="medic-name" name="medic-name"/>
        </div>
    </div>

    <div id="assistent">
        <div class="form-group">
            <label for="assistent-name">Nume Asistent</label>
            <x-input id="assistent-name" name="assistent-name"/>
        </div>
    </div>

    <div id="ambulancier">
        <div class="form-group">
            <label for="ambulancier-name">Nume Ambulantier</label>
            <x-input id="ambulancier-name" name="ambulancier-name"/>
        </div>
    </div>

        <x-button>Adauga</x-button>
    </x-form>
    </x-container>
    
</x-layout>
<script src="/js/properties.js"></script>

@if(Session::has('success'))
<script>
swal("Succes", "Inserare efectuata cu succes!", "success");
</script>
@endif

@if(Session::has('error'))
<script>
swal("Eroare", "Inserare esuata! Completeaza toate campurile!", "error");
</script>
@endif

@if ($errors->any())
@foreach($errors->all() as $error)
<script>
swal("Eroare", "Inserare esuata! Te rog incearca din nou!", "error");
</script>
@endforeach
@endif