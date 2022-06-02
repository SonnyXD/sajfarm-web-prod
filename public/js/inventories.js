function treeviewDisplay() {
    jQuery('#medstable > tbody > tr:not(.treeview)').click(function() {
      //console.log('test');
        const treeview = jQuery('#medstable tbody tr.treeview.tr-' + jQuery(this).data('count'));
  
        if( treeview.hasClass('active') ) {
          treeview.removeClass('active');
        } else {
          jQuery('#medstable tbody tr.treeview').removeClass('active');
          treeview.addClass('active');
          treeview[0].style.display = "";
        }
        
    });
  }

  function myFunction() {
    // Declare variables
    
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("medstable");
    tr = $(table).find('> tbody > tr');
  
    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[0];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }

  jQuery(document).ready(() => {
    treeviewDisplay();
    myFunction();
});