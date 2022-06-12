function hideProprietatiForms() {
    $('#nomenclator').css('display', 'none');
    $('#substatie').css('display', 'none');
    $('#tip-ambulanta').css('display', 'none');
    $('#ambulanta').css('display', 'none');
    $('#furnizor').css('display', 'none');
    $('#medic').css('display', 'none');
    $('#asistent').css('display', 'none');
    $('#unitate').css('display', 'none');
}

function showProprietatiForms() {
  $("#form-select").change(function () {
    var selected_option = $('#form-select').val();
  
    if (selected_option === 'nomenclator') {
        $('#nomenclator').dataTable({
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": true,
            retrieve: true,
            "oLanguage": {
                "sLengthMenu": "Arata _MENU_ pozitii",
                "sSearch": "Cauta:",
                "sInfo": "Am afisat _END_ pozitii din _TOTAL_",
                "oPaginate": {
                    "sNext": "Inainte",
                    "sPrevious": "Inapoi"
                  }
              }
        });
        $('#substatie').DataTable().destroy();
        $('#tip-ambulanta').DataTable().destroy();
        $('#ambulanta').DataTable().destroy();
        $('#furnizor').DataTable().destroy();
        $('#medic').DataTable().destroy();
        $('#asistent').DataTable().destroy();
        $('#unitate').DataTable().destroy();
        $('#nomenclator').css('display', 'inline-table');
        $('#tip-ambulanta').css('display', 'none');
        $('#ambulanta').css('display', 'none');
        $('#substatie').css('display', 'none');
        $('#furnizor').css('display', 'none');
        $('#medic').css('display', 'none');
        $('#asistent').css('display', 'none');
        $('#unitate').css('display', 'none');
    }
  
    if (selected_option === 'substatie') {
        $('#substatie').dataTable({
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": true,
            retrieve: true,
            "oLanguage": {
                "sLengthMenu": "Arata _MENU_ pozitii",
                "sSearch": "Cauta:",
                "sInfo": "Am afisat _END_ pozitii din _TOTAL_",
                "oPaginate": {
                    "sNext": "Inainte",
                    "sPrevious": "Inapoi"
                  }
              }
        });
        $('#nomenclator').DataTable().destroy();
        $('#tip-ambulanta').DataTable().destroy();
        $('#ambulanta').DataTable().destroy();
        $('#furnizor').DataTable().destroy();
        $('#medic').DataTable().destroy();
        $('#asistent').DataTable().destroy();
        $('#unitate').DataTable().destroy();
        $('#nomenclator').css('display', 'none');
        $('#tip-ambulanta').css('display', 'none');
        $('#substatie').css('display', 'inline-table');
        $('#ambulanta').css('display', 'none');
        $('#furnizor').css('display', 'none');
        $('#medic').css('display', 'none');
        $('#asistent').css('display', 'none');
        $('#unitate').css('display', 'none');
    }
  
    if (selected_option === 'tip-ambulanta') {
        $('#tip-ambulanta').dataTable({
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": true,
            retrieve: true,
            "oLanguage": {
                "sLengthMenu": "Arata _MENU_ pozitii",
                "sSearch": "Cauta:",
                "sInfo": "Am afisat _END_ pozitii din _TOTAL_",
                "oPaginate": {
                    "sNext": "Inainte",
                    "sPrevious": "Inapoi"
                  }
              }
        });
        $('#nomenclator').DataTable().destroy();
        $('#ambulanta').DataTable().destroy();
        $('#substatie').DataTable().destroy();
        $('#furnizor').DataTable().destroy();
        $('#medic').DataTable().destroy();
        $('#asistent').DataTable().destroy();
        $('#unitate').DataTable().destroy();
        $('#nomenclator').css('display', 'none');
        $('#tip-ambulanta').css('display', 'inline-table');
        $('#substatie').css('display', 'none');
        $('#ambulanta').css('display', 'none');
        $('#furnizor').css('display', 'none');
        $('#medic').css('display', 'none');
        $('#asistent').css('display', 'none');
        $('#unitate').css('display', 'none');
    }
  
    if (selected_option === 'ambulanta') {
        $('#ambulanta').dataTable({
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": true,
            retrieve: true,
            "oLanguage": {
                "sLengthMenu": "Arata _MENU_ pozitii",
                "sSearch": "Cauta:",
                "sInfo": "Am afisat _END_ pozitii din _TOTAL_",
                "oPaginate": {
                    "sNext": "Inainte",
                    "sPrevious": "Inapoi"
                  }
              }
        });
        $('#nomenclator').DataTable().destroy();
        $('#tip-ambulanta').DataTable().destroy();
        $('#substatie').DataTable().destroy();
        $('#furnizor').DataTable().destroy();
        $('#medic').DataTable().destroy();
        $('#asistent').DataTable().destroy();
        $('#unitate').DataTable().destroy();
        $('#nomenclator').css('display', 'none');
        $('#tip-ambulanta').css('display', 'none');
        $('#substatie').css('display', 'none');
        $('#ambulanta').css('display', 'inline-table');
        $('#furnizor').css('display', 'none');
        $('#medic').css('display', 'none');
        $('#asistent').css('display', 'none');
        $('#unitate').css('display', 'none');
    }
  
    if (selected_option === 'furnizor') {
        $('#furnizor').dataTable({
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": true,
            retrieve: true,
            "oLanguage": {
                "sLengthMenu": "Arata _MENU_ pozitii",
                "sSearch": "Cauta:",
                "sInfo": "Am afisat _END_ pozitii din _TOTAL_",
                "oPaginate": {
                    "sNext": "Inainte",
                    "sPrevious": "Inapoi"
                  }
              }
        });
        $('#nomenclator').DataTable().destroy();
        $('#ambulanta').DataTable().destroy();
        $('#substatie').DataTable().destroy();
        $('#tip-ambulanta').DataTable().destroy();
        $('#medic').DataTable().destroy();
        $('#asistent').DataTable().destroy();
        $('#unitate').DataTable().destroy();
        $('#nomenclator').css('display', 'none');
        $('#tip-ambulanta').css('display', 'none');
        $('#substatie').css('display', 'none');
        $('#ambulanta').css('display', 'none');
        $('#furnizor').css('display', 'inline-table');
        $('#medic').css('display', 'none');
        $('#asistent').css('display', 'none');
        $('#unitate').css('display', 'none');
    }

    if (selected_option === 'medic') {
        $('#medic').dataTable({
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": true,
            retrieve: true,
            "oLanguage": {
                "sLengthMenu": "Arata _MENU_ pozitii",
                "sSearch": "Cauta:",
                "sInfo": "Am afisat _END_ pozitii din _TOTAL_",
                "oPaginate": {
                    "sNext": "Inainte",
                    "sPrevious": "Inapoi"
                  }
              }
        });
        $('#nomenclator').DataTable().destroy();
        $('#ambulanta').DataTable().destroy();
        $('#substatie').DataTable().destroy();
        $('#tip-ambulanta').DataTable().destroy();
        $('#asistent').DataTable().destroy();
        $('#unitate').DataTable().destroy();
        $('#furnizor').DataTable().destroy();
        $('#nomenclator').css('display', 'none');
        $('#tip-ambulanta').css('display', 'none');
        $('#substatie').css('display', 'none');
        $('#ambulanta').css('display', 'none');
        $('#furnizor').css('display', 'none');
        $('#medic').css('display', 'inline-table');
        $('#asistent').css('display', 'none');
        $('#unitate').css('display', 'none');
    }

    if (selected_option === 'asistent') {
        $('#asistent').dataTable({
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": true,
            retrieve: true,
            "oLanguage": {
                "sLengthMenu": "Arata _MENU_ pozitii",
                "sSearch": "Cauta:",
                "sInfo": "Am afisat _END_ pozitii din _TOTAL_",
                "oPaginate": {
                    "sNext": "Inainte",
                    "sPrevious": "Inapoi"
                  }
              }
        });
        $('#nomenclator').DataTable().destroy();
        $('#ambulanta').DataTable().destroy();
        $('#substatie').DataTable().destroy();
        $('#tip-ambulanta').DataTable().destroy();
        $('#unitate').DataTable().destroy();
        $('#furnizor').DataTable().destroy();
        $('#medic').DataTable().destroy();
        $('#nomenclator').css('display', 'none');
        $('#tip-ambulanta').css('display', 'none');
        $('#substatie').css('display', 'none');
        $('#ambulanta').css('display', 'none');
        $('#furnizor').css('display', 'none');
        $('#medic').css('display', 'none');
        $('#asistent').css('display', 'inline-table');
        $('#unitate').css('display', 'none');
    }

    if (selected_option === 'unitate') {
        $('#unitate').dataTable({
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": true,
            retrieve: true,
            "oLanguage": {
                "sLengthMenu": "Arata _MENU_ pozitii",
                "sSearch": "Cauta:",
                "sInfo": "Am afisat _END_ pozitii din _TOTAL_",
                "oPaginate": {
                    "sNext": "Inainte",
                    "sPrevious": "Inapoi"
                  }
              }
        });
        $('#nomenclator').DataTable().destroy();
        $('#ambulanta').DataTable().destroy();
        $('#substatie').DataTable().destroy();
        $('#tip-ambulanta').DataTable().destroy();
        $('#medic').DataTable().destroy();
        $('#asistent').DataTable().destroy();
        $('#furnizor').DataTable().destroy();
        $('#nomenclator').css('display', 'none');
        $('#tip-ambulanta').css('display', 'none');
        $('#substatie').css('display', 'none');
        $('#ambulanta').css('display', 'none');
        $('#furnizor').css('display', 'none');
        $('#medic').css('display', 'none');
        $('#asistent').css('display', 'none');
        $('#unitate').css('display', 'inline-table');
    }
  });
}

$(document).ready(function () {
    hideProprietatiForms();
    showProprietatiForms();
    
    $('.dataTables_length').addClass('bs-select');
  });