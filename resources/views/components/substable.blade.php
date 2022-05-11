<div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Substatii</h4>
                  <div class="table-responsive pt-3">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>
                            #
                          </th>
                          <th>
                            Substatie
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        @if( $substations->count() )

                            @foreach($substations as $sub)
                                <tr>
                            <td>
                                {{ $sub->id }}
                            </td>
                            <td>
                                <a href="substatie/{{lcfirst($sub->name)}}/medicamente"> {{ $sub->name }}</a>
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