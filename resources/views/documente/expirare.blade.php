<x-layout>
    <x-container :title="$title">
        <x-form id="expirare-6-luni">
        <div class="form-group">
                    <label>Gestiune:</label>
                    <select class="meds-single-select w-100" id="inventory-select" name="inventory-select">
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