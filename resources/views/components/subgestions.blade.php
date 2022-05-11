<div class="home-tab">
  <div class="d-sm-flex align-items-center justify-content-between border-bottom">
    <ul class="nav nav-tabs" role="tablist">

      @if( $categories->count() )

        @foreach ($categories as $category)
          <li class="nav-item">
            <a class="nav-link ps-0" id="{{ $category->slug }}-tab" href="{{ $category->slug }}" role="tab" aria-controls="overview" data-title="{{ $category->name }}" aria-selected="true">{{ $category->name }}</a>
          </li>
        @endforeach

      @endif

      <!-- <li class="nav-item">
        <a class="nav-link ps-0" id="med-tab" data-title="Medicamente" href="medicamente" role="tab" aria-controls="overview" aria-selected="true">Medicamente</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="mat-tab" data-title="Materiale Sanitare" href="materiale-sanitare" role="tab" aria-selected="false">Materiale Sanitare</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="dez-tab" data-title="Dezinfectanti" href="dezinfectanti" role="tab" aria-selected="false">Dezinfectanti</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="spon-tab" data-title="Sponsorizari" href="sponsorizari" role="tab" aria-selected="false">Sponsorizari</a>
      </li>
      <li class="nav-item">
        <a class="nav-link border-0" id="don-tab" data-title="Donatii" href="donatii" role="tab" aria-selected="false">Donatii</a>
      </li> -->
    </ul>
  </div>
  <div class="tab-content tab-content-basic">

  </div>
</div>