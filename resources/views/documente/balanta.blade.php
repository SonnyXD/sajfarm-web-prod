<x-layout>
    <x-container>
        <x-form id="balanta">
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
                  <div class="form-group row">
                    <div class="col">
                      <label>Data Document</label>
                      <div id="the-basics">
                      <x-input type="date" name="document-date" id="document-date"/>
                      </div>
                    </div>
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
                      <x-button>Vizualizeaza raportul</x-button>
                </div>
        </x-form>
    </x-container>
</x-layout>