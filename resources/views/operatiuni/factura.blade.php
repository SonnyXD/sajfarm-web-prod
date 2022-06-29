

<x-layout>
    <script language="javascript">
      //inline js reminder (or create .js files for every blade page)
    </script>
    <x-container :title="$title">
    <x-form id="intrare-factura" method="GET" action="{{ route('invoices.store') }}">
      <p class="text-dark bg-gradient-success">{{Session::get('success');}}</p>
      <p class="text-dark bg-gradient-danger">{{Session::get('error');}}</p>
        <div class="form-group">
        <label for="furnizor-select">Furnizor</label>
        <select class="form-control" id="furnizor-select" name="furnizor-select">
            @if( $providers->count() )
                @foreach ($providers as $provider)
                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                @endforeach
            @endif
        </select>
        </div>
        <div class="form-group row">
                    <div class="col">
                      <label>Numar</label>
                      <div id="the-basics">
                      <x-input type="number" name="document-number" id="document-number"/>
                      </div>
                    </div>
                    <div class="col">
                      <label>NIR</label>
                      <div id="bloodhound">
                        
                        @if( !$invoices->count() )
                          <x-input type="number" name="nir-number" id="nir-number" disabled="disabled" value="1"/>
                        @else 
                          <x-input type="number" name="nir-number" id="nir-number" disabled="disabled" value="{{$invoices->last()->id + 1}}"/>
                        @endif
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col">
                      <label>Data Document</label>
                      <div id="the-basics">
                      <x-input type="date" id="document-date" name="document-date"/>
                      </div>
                    </div>
                    <div class="col">
                      <label>Data Scadenta</label>
                      <div id="bloodhound">
                      <x-input type="date" id="due-date" name="due-date"/>
                      </div>
                    </div>
                    <div class="col">
                      <label>Data Introducerii</label>
                      <div id="bloodhound">
                      <x-input type="date" id="insertion-date" name="insertion-date"/>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col">
                      <label>Procent Discount</label>
                      <div id="the-basics">
                      <x-input type="number" id="discount-procent" name="discount-procent" step="any"/>
                      </div>
                    </div>
                    <div class="col">
                      <label>Valoare Discount</label>
                      <div id="bloodhound">
                      <x-input type="number" id="discount-value" name="discount-value" step="any"/>
                      </div>
                    </div>
                    <div class="col">
                      <label>Valoare Totala</label>
                      <div id="bloodhound">
                      <x-input type="number" id="total-value" name="total-value" step="any"/>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col">
                    <label>Medicamente/Materiale Sanitare</label>
                    <select class="meds-single-select w-100" name="meds" id="meds">
                    @if( $items->count() )
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    @endif
                    </select>
                  </div>
                  <div class="col">
                  <label>Adauga Pozitie</label><br>
                  <x-modal-trigger type="button" data-bs-toggle="modal" data-bs-target="#meds-modal" id="add-in-preview">Adauga Pozitie</x-modal-trigger>
                  </div>
                  </div>
                  <div class="form-group">
                    <x-button disabled="disabled">Adauga in Farmacie si genereaza NIR-ul</x-button>
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
                            Cod CIM 
                          </th>
                          <th>
                            Cod Produs
                          </th>
                          <th>
                            Cantitate
                          </th>
                          <th>
                            Termen Valabilitate
                          </th>
                          <th>
                            Lot
                          </th>
                          <th>
                            UM
                          </th>
                          <th>
                            Pret Unitar
                          </th>
                          <th>
                            TVA
                          </th>
                          <th>
                            Pret TVA
                          </th>
                          <th>
                            Valoare
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
              <x-form id="factura-modal">
              <div style="display: none;" class="alert alert-danger" role="alert" id="modal-alert">Completeaza toate campurile!</div>
              <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Nume Produs:</label>
                  <x-input class="form-control" id="product-name" name="product-name" disabled="disabled"/>
                </div>
                <div class="form-group">
                  <label>Cod CIM</label><br>
                <x-input type="checkbox" class="form-check-input" id="cim-checkbox" name="cim-checkbox"/>
                </div>
        
                <div class="form-group" style="display:none;" id="cim-input">
                  <label for="recipient-name" class="col-form-label" id="cim-label">Cod CIM:</label>
                  <x-input class="form-control" id="cim-code" name="cim-code"/>
                </div>
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Cod Produs:</label>
                  <x-input class="form-control" id="product-code" name="product-code"/>
                </div>
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Cantitate:</label>
                  <x-input type="number" class="form-control" id="product-quantity" name="product-quantity"/>
                </div>
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Termen Valabilitate:</label>
                  <x-input type="date" class="form-control" id="product-availability" name="product-availability"/>
                </div>
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Lot:</label>
                  <x-input class="form-control" id="product-lot" name="product-lot"/>
                </div>
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">UM:</label>
                  <select class="form-control" id="um" name="um">
                  @if( $units->count() )
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                  @endif
                  </select>
                </div>
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Pret Unitar:</label>
                  <x-input type="number" class="form-control" id="product-price" name="product-price"/>
                </div>
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">% TVA:</label>
                  <x-input type="number" class="form-control" id="product-tva" name="product-tva"/>
                </div>
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Pret cu TVA:</label>
                  <x-input type="number" class="form-control" id="product-tva-price" name="product-tva-price" disabled="disabled"/>
                <div class="form-group">
                  <label for="recipient-name" class="col-form-label">Valoare:</label>
                  <x-input type="number" class="form-control" id="product-value" name="product-value" disabled="disabled"/>
                </div>

                <div class="modal-footer">
                    <x-button type="button" class="btn btn-success" id="add-product">Adauga</x-button>
                    <x-modal-trigger type="button" class="btn btn-light" data-bs-dismiss="modal">Inchide</x-modal-trigger>
                </div>

              </x-form>
          </x-modal>
</x-layout>
<script src="/js/invoice.js"></script>