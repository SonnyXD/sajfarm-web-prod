@props(['category_name' => '', 'items' => '', 'item_stock' => '', 'inventory_name' => '', 'inventory_id' => '', 'minimum_quantities_farm' => '', 'minimum_quantities_stoc3' => ''])

<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Cauta..">
<div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title" id="section-title">
                  {{$inventory_name}} - {{ $category_name }}
                  </h4>
                  <p></p>
                  <div class="table-responsive">
                    <table class="table table-striped" id="medstable">
                      <thead>
                        <tr class="header">
                          <th>
                            Nume
                          </th>
                            <th>Cantitate Totala</th>
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
                              @php
                                $item_sum = \App\Models\ItemStock::with('inventory', 'item')->where('inventory_id', '=', 1)->where('item_id', '=', $item->id)->sum('quantity');
                                $min_cant = \App\Models\MinimumQuantity::with('item')
                                ->where('item_id', '=', $item->id)
                                ->where('inventory_id', '=', 1)
                                ->first()
                                ->quantity??0;
                              @endphp 
                                @if($item_sum < $min_cant || $item_sum == 0)
                                  <td style="color: red; font-weight: bold;">{{$item_sum}} / {{$min_cant}}</td>
                                @else
                                  <td>{{$item_sum}} / {{$min_cant}}</td>
                                @endif
                                @php
                                  $i++;
                                @endphp
                              @elseif ($inventory_id != 2)
                                @php
                                  $item_sum = \App\Models\ItemStock::with('inventory', 'item')->where('inventory_id', '=', $inventory_id)->where('item_id', '=', $item->id)->sum('quantity');
                                @endphp
                                  @if($item_sum == 0)
                                    <td style="color: red; font-weight: bold;">{{$item_sum}}</td>
                                  @else
                                    <td>{{$item_sum}}</td>
                                  @endif
                            @endif
                            @if ($inventory_id == 2)
                              @php
                                $item_sum = \App\Models\ItemStock::with('inventory', 'item')->where('inventory_id', '=', 2)->where('item_id', '=', $item->id)->sum('quantity');
                                $min_cant = \App\Models\MinimumQuantity::with('item')
                                ->where('item_id', '=', $item->id)
                                ->where('inventory_id', '=', 2)
                                ->first()
                                ->quantity??0;
                              @endphp 
                                @if($item_sum < $min_cant || $item_sum == 0)
                                  <td style="color: red; font-weight: bold;">{{$item_sum}} / {{$min_cant}}</td>
                                @else
                                  <td>{{$item_sum}} / {{$min_cant}}</td>
                                @endif
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
                                  <th>Cantitate</th>
                                  <th>Termen Valab</th>
                                  <th>Lot</th>
                                  <th>UM</th>
                                  <th>Pret Unitar</th>
                                  <th>TVA</th>
                                  <th>Pret TVA</th>
                                  <th>Valoare</th>
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
                                      <td>{{number_format($inItem['tva_price'] * $inItem['quantity'],4)}}</td>
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
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            