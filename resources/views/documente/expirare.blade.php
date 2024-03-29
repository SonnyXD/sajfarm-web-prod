<x-layout>
    <x-container :title="$title">
        <x-form id="expirare-6-luni" method="GET" action="{{ route('expirare.store') }}" target="_blank">
        <!-- <p class="text-dark bg-gradient-danger">{{Session::get('error');}}</p> -->
        <div class="form-group">
                    <label>Gestiune:</label>
                    <select class="form-control" id="inventory-select" name="inventory-select">
                    @if( $inventories->count() )
                      @foreach ($inventories as $inventory)
                          <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                      @endforeach
                    @endif
                    </select>
                    </div>
            <div class="form-group">
                <x-button>Genereaza documentul</x-button>
            </div>
        </x-form>
    </x-container>
</x-layout>

@if(Session::has('error'))
<script>
swal("Eroare", "Nu exista produse care expira in urmatoarele 6 luni pentru aceasta gestiune", "error");
</script>
@endif