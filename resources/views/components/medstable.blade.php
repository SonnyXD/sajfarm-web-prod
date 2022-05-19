@props(['category_name' => '', 'items' => '', 'item_stock' => ''])

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
                      @if( !empty($items) )
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
                                  @if ( !empty( $item_stock[$item->name] ) && $item_stock[$item->name][0]['category_id'] == 1)
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
                                @if( !empty($item_stock[$item->name]))
                                    @foreach($item_stock[$item->name] as $inItem)
                                      @if($inItem['quantity'] > 0)
                                    <tr>
                                      @if ( $inItem['category_id'] == 1)
                                        <td>{{$inItem['cim_code']}}</td>
                                      @endif
                                      <td>{{$inItem['product_code']}}</td>
                                      <td>{{$inItem['quantity']}}</td>
                                      @if ((new \DateTime($inItem['exp_date']))->format('Y-m-d') > (new \DateTime())->format('Y-m-d'))
                                        
                                        <td>{{$newDate = date("d-m-Y", strtotime($inItem['exp_date']));  }}</td>
                                      @else
                                        <td style="color:red; font-weight: bold;">EXPIRAT</td>
                                      @endif
                                      <td>{{$inItem['lot']}}</td>
                                      <td>{{$inItem['m_name']}}</td>
                                      <td>{{$inItem['price']}}</td>
                                      <td>{{$inItem['tva']}}</td>
                                      <td>{{$inItem['tva_price']}}</td>
                                    </tr>
                                      @endif
                                    @endforeach
                                @endif
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

            