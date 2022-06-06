<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
        <a class="nav-link" href="/">
            <i class="mdi mdi-grid-large menu-icon"></i>
            <span class="menu-title">Home</span>
        </a>
        </li>
        <li class="nav-item nav-category">Gestiune</li>
        <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
            <i class="menu-icon mdi mdi-table"></i>
            <span class="menu-title">Gestiune</span>
            <i class="menu-arrow"></i> 
        </a>
        <div class="collapse" id="ui-basic">
            <ul class="nav flex-column sub-menu">
            <!-- <li class="nav-item"> <a class="nav-link" href="/gestiune/farmacie/medicamente">Farmacie</a></li>
            <li class="nav-item"> <a class="nav-link" href="/gestiune/stoctrei/medicamente">Stoc 3</a></li>
            <li class="nav-item"> <a class="nav-link" href="/gestiune/substatie">Substatii</a></li> -->

            @if($inventories->count())
                @foreach($inventories as $inventory)
                        <li class="nav-item"> <a class="nav-link" href="/gestiune/{{$inventory->slug}}/{{$categories}}">{{$inventory->name}}</a></li>
                @endforeach
            @endif
            </ul>
        </div>
        </li>
        <li class="nav-item nav-category">Operatiuni</li>
        <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">
            <i class="menu-icon mdi mdi-file-document-edit-outline"></i>
            <span class="menu-title">Operatiuni</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="form-elements">
            <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="/operatiuni/intrare-factura">Intrare Factura</a></li>
            <li class="nav-item"><a class="nav-link" href="/operatiuni/checklist-statii">Checklist Statii</a></li>
            <li class="nav-item"><a class="nav-link" href="/operatiuni/checklist-medici">Checklist Medici</a></li>
            <li class="nav-item"><a class="nav-link" href="/operatiuni/bon-transfer">Bon Transfer</a></li>
            <li class="nav-item"><a class="nav-link" href="/operatiuni/bon-consum-ambulante">Bon Consum Ambulante</a></li>
            <li class="nav-item"><a class="nav-link" href="/operatiuni/bon-consum-medici">Bon Consum Medici</a></li>
            <li class="nav-item"><a class="nav-link" href="/operatiuni/aviz-intrare">Aviz Intrare</a></li>
            <li class="nav-item"><a class="nav-link" href="/operatiuni/retur">Retur</a></li>
            <li class="nav-item"><a class="nav-link" href="/operatiuni/modificare-cant-min">Modificare Cantitati Minime</a></li>
            </ul>
        </div>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="/operatiuni/inserare-proprietati">
            <i class="menu-icon mdi mdi-database-plus"></i>
            <span class="menu-title">Inserare Proprietati</span>
        </a>
        </li>
        <li class="nav-item nav-category">Documente</li>
        <li class="nav-item">
        <a class="nav-link" href="/documente/rapoarte">
            <i class="menu-icon mdi mdi-folder-outline"></i>
            <span class="menu-title">Rapoarte</span>
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="/documente/expira-in-6-luni">
            <i class="menu-icon mdi mdi-timer-sand"></i>
            <span class="menu-title">Expira In 6 Luni</span>
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="/documente/fisa-produs">
            <i class="menu-icon mdi mdi-note-outline"></i>
            <span class="menu-title">Fisa Produs</span>
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="/documente/inventar">
            <i class="menu-icon mdi mdi-package-variant-closed"></i>
            <span class="menu-title">Inventar</span>
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="/documente/balanta">
            <i class="menu-icon mdi mdi-scale-balance"></i>
            <span class="menu-title">Balanta</span>
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="/documente/baza-de-date">
            <i class="menu-icon mdi mdi-table-large"></i>
            <span class="menu-title">Baza de Date</span>
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="/documente/documente-generate">
            <i class="menu-icon mdi mdi-file-document-box-search-outline"></i>
            <span class="menu-title">Documente Generate</span>
        </a>
        </li>
    </ul>
</nav> 
<!-- partial -->
