@props(['category_name' => '', 'items' => '', 'item_stock' => '', 'inventory_name' => '', 'inventory_id' => '', 'minimum_quantities_farm' => '', 'minimum_quantities_stoc3' => ''])

<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Cauta..">
<div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title" id="section-title">
                  {{$inventory_name}} - {{ $category_name }}
                  </h4>
                  <div class="table-responsive">
                    <table class="table table-striped" id="medstable">
                      <thead>
                        <tr class="header">
                          <th>
                            Nume
                          </th>
                          @if ($inventory_id == 1 || $inventory_id == 2) 
                            <th>Cantitate Totala</th>
                          @endif
                        </tr>
                      </thead>
                      <tbody>
                      @if( !empty($items) )
                        @php
                          $i = 0;
                        @endphp
                        @foreach ($items as $item)
                          <tr data-count="{{ $loop->index }}">
                            <td> {{$item->name}}</td>
                            @if ($inventory_id == 1) 
                                <td>150 / {{$minimum_quantities_farm[$i]->quantity ?? 0}}</td>
                                @php
                                  $i++;
                                @endphp
                            @endif
                            @if ($inventory_id == 2) 
                                <td>150 / {{$minimum_quantities_stoc3[$i]->quantity ?? 0}}</td>
                                @php
                                  $i++;
                                @endphp
                            @endif
                          </tr>
                          <tr class="treeview tr-{{ $loop->index }}"> 
                            <td colspan="100%">
                              <table>
                                <thead>
                                <tr>
                                  @if ($item->category_id == 1) 
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

            