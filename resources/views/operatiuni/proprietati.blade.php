<x-layout>
    <x-container>
    <div class="form-group">
        <label for="select-id">Alege</label>
        <select class="form-control" id="form-select">
            <option disabled selected>Alege o optiune..</option>
            <option value="nomenclator">Nomenclator</option>
            <option value="substatie">Substatie</option>
            <option value="tip-ambulanta">Tip Ambulanta</option>
            <option value="ambulanta">Ambulanta</option>
            <option value="furnizor">Furnizor</option>
            <option value="medic">Medic</option>
            <option value="asistent">Asistent</option>
            <option value="asistent">Ambulantier</option>
            <option value="unitate">Unitate Masura</option>
        </select>
    </div>
    <x-form id="add-nomenclator">
    <div class="form-group">
        <label for="denumire-produs">Denumire Produs</label>
        <x-input id="denumire-produs" placeholder="Denumire Produs" name="denumire-produs"/>
    </div>
    <div class="form-group">
        <label for="denumire-produs">Categorie</label>
        <x-select id="select-category" name="select-category">
            @if( $categories->count() )
                <option value="-1" selected="selected">Alege o categorie</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }} </option>
                @endforeach

            @else
                <option value="-1"> Nu exista categorii</option>
            @endif
        </x-select>
        <!-- <select class="form-control" id="select-category" name="select-category">
            
            <option>Medicament (Sterge dupa ce bagi db)</option>
        </select> -->
    </div>
    <div class="form-group">
        <div class="form-check">
        <label class="form-check-label">
            <x-input type="checkbox" class="form-check-input" id="special-med" name="special-med"/>
            Medicament cu regim special
        <i class="input-helper"></i></label>
        </div>
    </div>
    <x-button>Adauga</x-buttin>
    </x-form>

    <x-form id="add-substatie">
        <div class="form-group">
            <label for="nume-sub">Nume Substatie</label>
            <x-input id="nume-sub" placeholder="Nume Substatie" name="nume-sub"/>
        </div>
        <x-button>Adauga</x-buttin>
    </x-form>

    <x-form id="add-tip-ambulanta">
        <div class="form-group">
            <label for="tip-ambulanta">Tip Ambulanta</label>
            <x-input id="tip-ambulanta" placeholder="Tip Ambulanta" name="tip-ambulanta"/>
        </div>
        <x-button>Adauga</x-buttin>
    </x-form>

    <x-form id="add-ambulanta">
        <div class="form-group">
            <label for="nr-amb">Nr Inmatriculare Ambulanta</label>
            <x-input id="nr-amb" placeholder="EX 01 AMB" name="nr-amb"/>
        </div>
        <div class="form-group">
            <label for="tip-amb">Tipul Ambulantei</label>
            <select id="tip-amb" class="form-control">
            @if( $ambulanceTypes->count() )
                @foreach ($ambulanceTypes as $ambType)
                    <option value="{{ $ambType->id }}">{{ $ambType->name }}</option>
                @endforeach
            @endif
            </select>
        </div>
        <div class="form-group">
            <label for="sub-amb">Substatie Ambulanta</label>
            <select id="sub-amb" class="form-control">
            @if( $inventories->count() )
                @foreach ($inventories as $inventory)
                    @if($inventory->parent == 1)
                        <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                    @endif
                @endforeach
            @endif
            </select>
        </div>
        <x-button>Adauga</x-buttin>
    </x-form>

    <x-form id="add-furnizor">
        <div class="form-group">
            <label for="nume-furnizor">Nume Furnizor</label>
            <x-input id="nume-furnizor" placeholder="SC EXEMPLU SRL" name="nume-furnizor"/>
        </div>
        <div class="form-group">
            <label for="sediu-furnizor">Sediu</label>
            <x-input id="sediu-furnizor" name="sediu-furnizor"/>
        </div>
        <div class="form-group">
            <label for="adresa-furnizor">Adresa</label>
            <x-input id="adresa-furnizor" name="adresa-furnizor"/>
        </div>
        <div class="form-group">
            <label for="registru-furnizor">Registrul Comertului</label>
            <x-input id="registru-furnizor" name="registru-furnizor"/>
        </div>
        <div class="form-group">
            <label for="cui-furnizor">CUI</label>
            <x-input id="cui-furnizor" name="cui-furnizor"/>
        </div>
        <x-button>Adauga</x-buttin>
    </x-form>

    <x-form id="add-medic">
        <div class="form-group">
            <label for="nume-medic">Nume Medic</label>
            <x-input id="nume-medic"/>
        </div>
        <x-button>Adauga</x-buttin>
    </x-form>

    <x-form id="add-asistent">
        <div class="form-group">
            <label for="nume-asistent">Nume Asistent</label>
            <x-input id="nume-asistent"/>
        </div>
        <x-button>Adauga</x-buttin>
    </x-form>

    <x-form id="add-unitate">
        <div class="form-group">
            <label for="nume-unitate">Nume Unitate Masura</label>
            <x-input id="nume-unitate" placeholder="BUC"/>
        </div>
        <x-button>Adauga</x-buttin>
    </x-form>
    </x-container>
    
</x-layout>