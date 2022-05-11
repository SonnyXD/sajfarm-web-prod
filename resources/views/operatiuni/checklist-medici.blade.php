<x-layout>
    <x-container>
        <x-form id="checklist" method="POST" action="{{ route('checklists.store') }}">
        <div class="form-group row">
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
                    <select class="meds-single-select w-100" name="meds">
                      <option value="1">De adaugat stocul din stoc3</option>
                    </select>
                  </div>
                  <div class="col">
                  <label>Adauga Pozitie</label><br>
                  <x-modal-trigger type="button" data-bs-toggle="modal" data-bs-target="#meds-modal">Adauga Pozitie</x-modal-trigger>
                  </div>
                  </div>
                  <div class="form-group">
                      <x-button>Finalizeaza checklist-ul</x-button>
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
                        <tr class="header"> <!-- checkbox-uri cu medicamente, materiale sanitare etc ca sa stie aplicatia in ce sub-gestiune sa intre -->
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
              </x-form>
          </x-modal>
</x-layout>