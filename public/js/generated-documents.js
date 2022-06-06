function hideProprietatiForms() {
    $('#nirs').css('display', 'none');
    $('#transfers').css('display', 'none');
    $('#consumptions').css('display', 'none');
    $('#returnings').css('display', 'none');
    $('#entries').css('display', 'none');
}

function showProprietatiForms() {
  $("#form-select").change(function () {
    var selected_option = $('#form-select').val();
  
    if (selected_option === 'nirs') {
        $('#nirs').dataTable({
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
        $('#transfers').DataTable().destroy();
        $('#consumptions').DataTable().destroy();
        $('#returnings').DataTable().destroy();
        $('#entries').DataTable().destroy();
        $('#nirs').css('display', 'inline-table');
        $('#transfers').css('display', 'none');
        $('#consumptions').css('display', 'none');
        $('#returnings').css('display', 'none');
        $('#entries').css('display', 'none');
    }
  
    if (selected_option === 'transfers') {
        $('#transfers').dataTable({
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
        $('#nirs').DataTable().destroy();
        $('#consumptions').DataTable().destroy();
        $('#returnings').DataTable().destroy();
        $('#entries').DataTable().destroy();
        $('#nirs').css('display', 'none');
        $('#transfers').css('display', 'inline-table');
        $('#consumptions').css('display', 'none');
        $('#returnings').css('display', 'none');
        $('#entries').css('display', 'none');
    }
  
    if (selected_option === 'consumptions') {
        $('#consumptions').dataTable({
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
        $('#nirs').DataTable().destroy();
        $('#transfers').DataTable().destroy();
        $('#returnings').DataTable().destroy();
        $('#entries').DataTable().destroy();
        $('#nirs').css('display', 'none');
        $('#transfers').css('display', 'none');
        $('#consumptions').css('display', 'inline-table');
        $('#returnings').css('display', 'none');
        $('#entries').css('display', 'none');
    }
  
    if (selected_option === 'returnings') {
        $('#returnings').dataTable({
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
        $('#nirs').DataTable().destroy();
        $('#transfers').DataTable().destroy();
        $('#consumptions').DataTable().destroy();
        $('#entries').DataTable().destroy();
        $('#nirs').css('display', 'none');
        $('#transfers').css('display', 'none');
        $('#consumptions').css('display', 'none');
        $('#returnings').css('display', 'inline-table');
        $('#entries').css('display', 'none');
    }
  
    if (selected_option === 'entries') {
        $('#entries').dataTable({
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
        $('#nirs').DataTable().destroy();
        $('#transfers').DataTable().destroy();
        $('#consumptions').DataTable().destroy();
        $('#returnings').DataTable().destroy();
        $('#nirs').css('display', 'none');
        $('#transfers').css('display', 'none');
        $('#consumptions').css('display', 'none');
        $('#returnings').css('display', 'none');
        $('#entries').css('display', 'inline-table');
    }
  });
}

$(document).ready(function () {
    hideProprietatiForms();
    showProprietatiForms();
    
    $('.dataTables_length').addClass('bs-select');
  });