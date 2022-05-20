<x-layout>
    <x-container :title="$title">
        <x-form id="raport">
        <div class="form-group row">
                <div class="col">
            <label>Tip raport:</label>
            <select class="form-control" id="report-type" name="report-type">
                <option value="consum">Consum</option>
                <option value="retur">Retur</option>
            </select>
        </div>
        <div class="col" id="sub-picker">
            <label>Substatie:</label>
            <select class="form-control" id="substation-name" name="substation-name">
                <option selected disabled>Alege o substatie..</option>
                @if( $inventories->count() )
                      @foreach ($inventories as $inventory)
                          @if($inventory->parent == 1)
                            <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                          @endif
                      @endforeach
                @endif
            </select>
        </div>
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