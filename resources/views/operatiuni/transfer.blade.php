<x-layout>
    <x-container>
        <x-form id="bon-transfer" method="POST" action="{{ route('transfers.store') }}">
            <div class="form-group">
                <label>Din:</label>
                <select class="form-control" id="from-location" name="from-location">
                    @if( $inventories->count() )
                      @foreach ($inventories as $inventory)
                          <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                      @endforeach
                    @endif
                </select>
            </div>
            <div class="form-group">
                <label>In:</label>
                <select class="form-control" id="to-location" name="to-location">
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
                      <x-input type="date" id="document-date" name="document-date"/>
                      </div>
                    </div>
                    <div class="col">
                      <label>Numar</label>
                      <div id="bloodhound">
                      @if( !$transfers->count() )
                          <x-input type="number" name="transfer-number" id="transfer-number" disabled="disabled" value="1"/>
                        @else 
                          <x-input type="number" name="transfer-number" id="transfer-number" disabled="disabled" value="{{$transfers->last()->id + 1}}"/>
                        @endif
                      </div>
                    </div>
                      <div class="col" id="sub-picker" style="display:none;">
                      <label>Substatie</label>
                      <div id="the-basics">
                      <select class="form-control" id="pick-substation" name="pick-substation">
                      
                    </select>
                      </div>
                    </div>
                    </div>
                    <div class="form-group row">
                    <div class="col">
                    <label>Medicamente/Materiale Sanitare</label>
                    <select class="meds-single-select w-100" name="meds">
                      <option value="1">Algocalmin</option>
                      <option value="2">Nurofen</option>
                      <option value="3">Fentanyl</option>
                    </select>
                  </div>
                  <div class="col">
                  <label>Adauga Pozitie</label><br>
                  <x-modal-trigger type="button" data-bs-toggle="modal" data-bs-target="#meds-modal">Adauga Pozitie</x-modal-trigger>
                  </div>
                  </div>
                  <div class="form-group">
                    <x-button>Transfera si genereaza Bonul de Transfer</x-button>
                </div>
                  </div>
        </x-form>
    </x-container>
    <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title" id="section-title">Previzualizare</h4>
                  <div class="table-responsive">
                    <table class="table table-striped" id="medstable">
                      <thead>
                        <tr class="header">
                          <th>
                            Nume
                          </th>
                          <th>
                            Cantitate
                          </th>
                          <th>
                            UM
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                  
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
        </div>

            <x-modal>
              <x-form id="factura-modal">
              <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Nume Produs (sa apara numele medicamentului ales):</label>
                  <x-input class="form-control" id="product-name" name="product-name" disabled="disabled"/>
                </div>
                <div class="form-group" id="cim-input">
                  <label for="recipient-name" class="col-form-label">UM:</label>
                  <x-input class="form-control" id="um" name="um" disabled="disabled"/>
                </div>
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Cantitate:</label>
                  <x-input type="number" class="form-control" id="product-quantity" name="product-quantity"/>
                </div>
              </x-form>
          </x-modal>
</x-layout>