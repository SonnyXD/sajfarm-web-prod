<x-layout>
    <x-container :title="$title">
        <x-form id="return-items" method="POST" action="{{ route('returningchecklist.store') }}">
          <!-- <p class="text-dark bg-gradient-success">{{Session::get('success');}}</p>
          <p class="text-dark bg-gradient-danger">{{Session::get('error');}}</p> -->
          <input type="hidden" id="from-location-id" name="from-location-id" value=""/>
        <div class="form-group row">
                <div class="col">
                <label>Din:</label>
                <select class="form-control" id="from-location" name="from-location">
                    @if( $inventories->count() )
                      @foreach ($inventories as $inventory)
                          <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                      @endforeach
                    @endif
                </select>
            </div>
            <!-- <div class="col">
                     <label>Ambulanta:</label>
                    <div class="the-basics">
                    <select class="meds-single-select w-100" id="ambulance-select" name="ambulance-select">
                      <option value="">Fara ambulanta</option>
                    @if( $ambulances->count() )
                      @foreach ($ambulances as $ambulance)
                          <option value="{{ $ambulance->id }}">{{ $ambulance->license_plate }}</option>
                      @endforeach
                    @endif
                    </select>
                    </div> -->
                <!-- </div>  -->
            </div>
            <div class="form-group row">
                    <div class="col">
                      <label>Data:</label>
                      <div id="the-basics">
                      <x-input type="date" name="document-date" id="document-date"/>
                      </div>
                    </div>
                    <!-- <div class="col">
                      <label>Numar</label>
                      <div id="bloodhound">
                      @if( !$returnings->count() )
                          <x-input type="number" name="nir-number" id="nir-number" disabled="disabled" value="1"/>
                        @else 
                          <x-input type="number" name="nir-number" id="nir-number" disabled="disabled" value="{{$returnings->last()->id + 1}}"/>
                        @endif
                      </div>
                    </div> -->
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
                    <x-button disabled="disabled">Finalizeaza checklist-ul</x-button>
                </div>
                <div id="test" style="display:none"></div>
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
                            Motiv
                          </th>
                          <th>
                            Ambulanta
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

            <x-modal>
              <x-form id="factura-modal">
              <div style="display: none;" class="alert alert-danger" role="alert" id="modal-alert">Cantitate invalida!</div>
              <div style="display: none;" class="alert alert-danger" role="alert" id="modal-alert-404">Alege un produs!</div>
              <div style="display: none;" class="alert alert-danger" role="alert" id="modal-alert-fields">Completeaza campurile!</div>
              <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Nume Produs:</label>
                  <x-input class="form-control" id="product-name" name="product-name" disabled="disabled"/>
                </div>
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">UM:</label>
                  <x-input class="form-control" id="um" name="um" disabled="disabled"/>
                </div>
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Cantitate:</label>
                  <x-input type="number" class="form-control" id="product-quantity" name="product-quantity"/>
                </div>
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Motiv:</label>
                  <x-input class="form-control" id="reason" name="reason"/>
                </div>
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Ambulanta:</label><br>
                  <select class="w-100" id="ambulance-select" name="ambulance-select">
                        <option value="0">Fara ambulanta</option>
                      @if( $ambulances->count() )
                        @foreach ($ambulances as $ambulance)
                            <option value="{{ $ambulance->id }}">{{ $ambulance->license_plate }}</option>
                        @endforeach
                      @endif
                      </select>
                </div>
                <div class="modal-footer">
                    <x-button type="button" class="btn btn-success" id="add-product-return">Adauga</x-button>
                    <x-modal-trigger type="button" class="btn btn-light" data-bs-dismiss="modal">Inchide</x-modal-trigger>
                </div>
              </x-form>
          </x-modal>
</x-layout>
<script src="/js/returning-checklist.js"></script>

@if(Session::has('success'))
<script>
swal("Succes", "Checklist retur generat cu succes!", "success");
</script>
@endif

@if ($errors->any())
@foreach($errors->all() as $error)
<script>
swal("Eroare", "Generare checklist retur esuata! Incearca din nou!", "error");
</script>
@endforeach
@endif

@if(Session::has('error'))
<script>
swal("Eroare", "Generare NIR esuata! Incearca din nou!", "error");
</script>
@endif