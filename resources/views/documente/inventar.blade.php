<x-layout>
    <x-container :title="$title">
        <x-form id="inventar">
        <div class="form-group">
                    <label>Gestiune:</label>
                    <select class="form-control">
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