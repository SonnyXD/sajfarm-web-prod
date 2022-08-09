function conditions() {

    $( "#type-select" ).change(function() {

    let selected_option = $('#type-select option:selected').val();

    if(selected_option === '3') {
      $('#inventory-select option:eq(0)').prop('selected', true)
      $("#inventory-select option:not(:first)").attr('disabled','disabled');
    } else {
      // $("#inventory-select option:all").removeAttr('disabled','');
      $("#inventory-select option").each(function()
      {
        $(this).removeAttr('disabled','');
      });
      //$("#substation-select option:selected").prop("selected", false)
    }
  });

    $('#from-date, #until-date').change(function() {
        testInputs();
    });
  
    function testInputs() {
      let valid = true;
  
      $('#from-date, #until-date').each(function() {
        
        if ($(this).val().length === 0) {
          valid = false;
        }
      });
  
      if (!valid)
      $('#print').attr('disabled', 'disabled');
    else
      $('#print').attr('disabled', false);
  
    }

    testInputs();

    
}



jQuery(document).ready(() => {
    conditions();
    $('#from-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
    $('#until-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
});
