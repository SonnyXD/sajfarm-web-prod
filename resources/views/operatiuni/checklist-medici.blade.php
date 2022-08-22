<x-layout>
    <x-container :title="$title">
        <x-form id="checklist" method="POST" action="{{ route('checklists.store') }}">
        <!-- <p class="text-dark bg-gradient-success">{{Session::get('success');}}</p>
        <p class="text-dark bg-gradient-danger">{{Session::get('error');}}</p> -->
        <input type="hidden" id="from-location-id" name="from-location-id" value=""/>
        <div class="form-group row">
        <div class="col">
        <label>De unde se face consumul:</label>
                    <div class="the-basics">
                    <select class="meds-single-select w-100" id="substation-select" name="substation-select">
                    @if( $inventories->count() )
                      @foreach ($inventories as $inventory)
                        @if($inventory->parent == 1 || $inventory->name == 'Stoc 3')
                          <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                        @endif
                      @endforeach
                    @endif
                    </select>
                    </div>
                  </div>
        <div class="col">
                    <label>Medic:</label>
                    <div class="the-basics">
                    <select class="meds-single-select w-100" id="medic-select" name="medic-select">
                    @if( $medics->count() )
                      @foreach ($medics as $medic)
                          <option value="{{ $medic->id }}">{{ $medic->name }}</option>
                      @endforeach
                    @endif
                    </select>
                    </div>
                  </div>
                      <div class="col">
                    <label>Ambulanta:</label>
                    <div class="the-basics">
                    <select class="meds-single-select w-100" id="ambulance-select" name="ambulance-select">
                    @if( $ambulances->count() )
                      @foreach ($ambulances as $ambulance)
                          <option value="{{ $ambulance->id }}">{{ $ambulance->license_plate }}</option>
                      @endforeach
                    @endif
                    </select>
                    </div>
                  </div>
                  <div class="col">
                    <label>Asistent:</label>
                    <div class="the-basics">
                    <select class="meds-single-select w-100" id="assistent-select" name="assistent-select">
                      <option value="">Fara asistent</option>
                    @if( $assistents->count() )
                      @foreach ($assistents as $assistent)
                          <option value="{{ $assistent->id }}">{{ $assistent->name }}</option>
                      @endforeach
                    @endif
                    </select>
                    </div>
                  </div>
                  <div class="col">
                    <label>Ambulantier:</label>
                    <div class="the-basics">
                    <select class="meds-single-select w-100" id="ambulancier-select" name="ambulancier-select">
                    <option value="">Fara ambulantier</option>
                    @if( $ambulanciers->count() )
                      @foreach ($ambulanciers as $ambulancier)
                          <option value="{{ $ambulancier->id }}">{{ $ambulancier->name }}</option>
                      @endforeach
                    @endif
                    </select>
                    </div>
                  </div>
                  </div>
                  <div class="form-group row">
                    <div class="col">
                      <label>Data:</label>
                      <div id="the-basics">
                      <x-input type="date" name="document-date" id="document-date"/>
                      </div>
                    </div>
                    <div class="col" id="div-patient-number">
                      <label>Numar fisa pacient:</label>
                      <x-input type="number" name="patient-number" id="patient-number"/>
                    </div>
                    <div class="col">
                      <label>Tura:</label>
                      <div id="bloodhound">
                        <select class="form-control" id="tura" name="tura">
                            <option value="07-19">07 - 19</option>
                            <option value="19-07">19 - 07</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <!-- <div class="form-group">
                          <div class="form-check">
                            <label class="form-check-label">
                              <x-input type="checkbox" class="form-check-input" id="special-checkbox" name="special-checkbox"/>
                              Medicament cu regim special (baga-l in modal)
                            <i class="input-helper"></i></label>
                          </div>
                        </div> -->
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
                        <tr class="header"> <!-- checkbox-uri cu medicamente, materiale sanitare etc ca sa stie aplicatia in ce sub-gestiune sa intre -->
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
          </div>

            <x-modal>
              <x-form id="factura-modal">
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
                    <x-button type="button" class="btn btn-success" id="add-product-checklist-medic">Adauga</x-button>
                    <x-modal-trigger type="button" class="btn btn-light" data-bs-dismiss="modal">Inchide</x-modal-trigger>
                </div>
              </x-form>
          </x-modal>
</x-layout>
<script src="/js/medic-checklist.js"></script>

@if(Session::has('success'))
<script>
swal("Succes", "Checklist generat cu succes!", "success");
</script>
@endif

@if ($errors->any())
@foreach($errors->all() as $error)
<script>
swal("Eroare", "Generare checklist esuata! Incearca din nou!", "error");
</script>
@endforeach
@endif

@if(Session::has('error'))
<script>
swal("Eroare", "Generare checklist esuata! Incearca din nou!", "error");
</script>
@endif