function conditions() {

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
