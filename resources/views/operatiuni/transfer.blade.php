<x-layout>
    <x-container :title="$title">
        <x-form id="bon-transfer" method="POST" action="{{ route('transfers.store') }}">
        <!-- <p class="text-dark bg-gradient-success">{{Session::get('success');}}</p>
        <p class="text-dark bg-gradient-danger">{{Session::get('error');}}</p> -->
        <input type="hidden" id="from-location-id" name="from-location-id" value=""/>
        <input type="hidden" id="to-location-id" name="to-location-id" value=""/>
        <input type="hidden" id="final-document-date" name="final-document-date" value=""/>
            <div class="form-group">
                <label>Din:</label>
                <select class="form-control" id="from-location" name="from-location">
                    @if( $inventories->count() )
                      @foreach ($inventories as $inventory)
                        @if($inventory->id < 2)
                          <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                        @endif
                      @endforeach
                    @endif
                </select>
            </div>
            <div class="form-group">
                <label>In:</label>
                <select class="form-control" id="to-location" name="to-location">
                    @if( $inventories->count() )
                      @foreach ($inventories as $inventory)
                        @if($inventory->name != 'Farmacie')
                          <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                        @endif
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
                    <!-- <div class="col">
                    <label>Asistent:</label>
                    <div class="the-basics">
                    <select class="meds-single-select w-100" id="assistent-select" name="assistent-select">
                    @if( $assistents->count() )
                      @foreach ($assistents as $assistent)
                          <option value="{{ $assistent->id }}">{{ $assistent->name }}</option>
                      @endforeach
                    @endif
                    </select>
                    </div>
                  </div> -->
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
                    <select class="meds-single-select w-100" name="meds" id="meds">
                      
                    </select>
                  </div>
                  <div class="col">
                  <label>Adauga Pozitie</label><br>
                  <x-modal-trigger type="button" data-bs-toggle="modal" data-bs-target="#meds-modal" id="add-in-preview">Adauga Pozitie</x-modal-trigger>
                  </div>
                  </div>
                  <div class="form-group">
                    <x-button disabled="disabled">Transfera si genereaza Bonul de Transfer</x-button>
                </div>
                  <div id="test" style="display:none"></div>
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
                            UM
                          </th>
                          <th>
                            Cantitate
                          </th>
                          <th>
                            
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
            <h5 id="total-value-preview"></h5>
        </div>

            <x-modal>
              <x-form id="transfer-modal">
                <div style="display: none;" class="alert alert-danger" role="alert" id="modal-alert">Cantitate invalida!</div>
                <div style="display: none;" class="alert alert-danger" role="alert" id="modal-alert-404">Alege un produs!</div>
              <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Nume Produs:</label>
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
                <div class="modal-footer">
                    <x-button type="button" class="btn btn-success" id="add-product-transfer">Adauga</x-button>
                    <x-modal-trigger type="button" class="btn btn-light" data-bs-dismiss="modal">Inchide</x-modal-trigger>
                </div>
              </x-form>
          </x-modal>
</x-layout>
<script src="/js/transfer.js"></script>

@if(Session::has('success'))
<script>
swal("Succes", "Bon transfer generat cu succes!", "success");
</script>
@endif

@if ($errors->any())
@foreach($errors->all() as $error)
<script>
swal("Eroare", "Generare bon transfer esuata! Incearca din nou!", "error");
</script>
@endforeach
@endif

@if(Session::has('error'))
<script>
swal("Eroare", "Generare bon transfer esuata! Incearca din nou!", "error");
</script>
@endif