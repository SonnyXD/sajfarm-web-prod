function conditions() {

  let selected_option = $('#report-type option:selected').val();

  if(selected_option === '2') {
    $("#ambulance-select").select2().next().hide();
    $("#amb-label").hide();
    $("#substation-select option:first").removeAttr('disabled','');
  } else {
    $("#ambulance-select").select2().next().show();
    $("#amb-label").show();
    $("#substation-select option:first").attr('disabled','disabled');
    $('#substation-select option:eq(1)').prop('selected', true)
    //$("#substation-select option:selected").prop("selected", false)
  }

  $( "#report-type" ).change(function() {
    let selected_option = $('#report-type option:selected').val();
    
    if(selected_option === '2') {
      $("#ambulance-select").select2().next().hide();
      $("#amb-label").hide();
      $("#substation-select option:first").removeAttr('disabled','');
    } else {
      $("#ambulance-select").select2().next().show();
      $("#amb-label").show();
      $("#substation-select option:first").attr('disabled','disabled');
      $('#substation-select option:eq(1)').prop('selected', true)
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
