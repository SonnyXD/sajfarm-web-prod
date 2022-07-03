<x-layout>
    <x-container :title="$title">
        <x-form id="inventar" method="GET" action="{{ route('inventory.store') }}" target="_blank">
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
            <x-button>Vizualizeaza Inventarul</x-button>
        </div>
        </x-form>
    </x-container>
</x-layout>