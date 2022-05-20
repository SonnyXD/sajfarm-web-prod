function conditions() {

    $('#stoc-min-farm, #stoc-min-stoc3').change(function() {
        testInputs();
    });
  
    function testInputs() {
      let valid = true;
  
      $('#stoc-min-farm, #stoc-min-stoc3').each(function() {
        
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
});