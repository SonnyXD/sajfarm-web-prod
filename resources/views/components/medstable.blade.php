@props(['category_name' => '', 'items' => ''])

<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Cauta..">
<div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title" id="section-title">
                  {{ $category_name }}
                  </h4>
                  <div class="table-responsive">
                    <table class="table table-striped" id="medstable">
                      <thead>
                        <tr class="header">
                          <th>
                            Nume
                          </th>
                          <th>
                            Cantitate Totala
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                      @if( $items->count() )
                        @foreach ($items as $item)
                          <tr data-count="{{ $loop->index }}">
                            <td> {{$item->name}}</td>
                            <td> 150 / 0 </td>
                          </tr>
                          <tr class="treeview tr-{{ $loop->index }}"> <!-- Cod CIM	Cod Produs	Nume	Cantitate	Termen Valabilitate	Lot	UM	Pret Unitar	TVA	Pret TVA -->
                            <td colspan="100%">
                              <table>
                                <thead>
                                <tr>
                                  @if ( $item->category_id == 1)
                                    <th>Cod CIM</th>
                                  @endif
                                  <th>Cod Produs</th>
                                  <th>Cantitate</th>
                                  <th>Termen Valab</th>
                                  <th>Lot</th>
                                  <th>UM</th>
                                  <th>Pret Unitar</th>
                                  <th>TVA</th>
                                  <th>Pret TVA</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                </tr>
                                <tr>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                </tr>
                                <tr>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                </tr>
                                <tr>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                        @endforeach
                      @endif
                        <!-- <tr>
                          <td> Algocalmin pentru copii</td>
                          <td> 150 </td>
                        </tr>
                        <tr>
                          <td> Nurofen </td>
                          <td> 500 </td>
                        </tr>
                        <tr>
                          <td> Algocalmin pentru copii</td>
                          <td> 150 </td>
                        </tr>
                        <tr>
                          <td> Nurofen </td>
                          <td> 500 </td>
                        </tr>
                        <tr>
                          <td> Algocalmin pentru copii</td>
                          <td> 150 </td>
                        </tr>
                        <tr>
                          <td> Nurofen </td>
                          <td> 500 </td>
                        </tr>
                        <tr>
                          <td> Algocalmin pentru copii</td>
                          <td> 150 </td>
                        </tr>
                        <tr>
                          <td> Nurofen </td>
                          <td> 500 </td>
                        </tr>
                        <tr>
                          <td> Algocalmin pentru copii</td>
                          <td> 150 </td>
                        </tr>
                        <tr>
                          <td> Nurofen </td>
                          <td> 500 </td>
                        </tr> -->
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            