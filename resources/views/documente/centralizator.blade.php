<x-layout>
    <x-container :title="$title">
    <x-form id="centralizator" method="GET" action="{{ route('centralizator.store') }}" target="_blank">
    <div class="form-group">
                    <label>Tip:</label>
                    <select class="form-control" id="type-select" name="type-select">
                        <option value="1">Transferuri</option>
                        <option value="2">Consumuri</option>
                    </select>
                  </div>
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
                      <x-button disabled="disabled">Vizualizeaza Centralizatorul</x-button>
                </div>
        </x-form>
    </x-container>
</x-layout>
<script src="/js/centralizator.js"></script>