<x-layout>
    <x-container :title="$title">
        <x-form id="balanta" method="GET" action="{{ route('balance.store') }}" target="_blank">
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
                    <label>Subgestiune:</label>
                    <select class="form-control" id="category-select" name="category-select">
                      @if( $categories->count() )
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                      <x-button disabled="disabled">Vizualizeaza balanta</x-button>
                </div>
        </x-form>
    </x-container>
</x-layout>
<script src="/js/balance.js"></script>