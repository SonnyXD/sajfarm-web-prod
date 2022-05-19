function conditions() {

$('#from-date, #until-date, #document-date').change(function() {
    testInputs();
});

function testInputs() {
  let valid = true;

  $('#from-date, #until-date, #document-date').each(function() {
    //console.log({input: $(this), valid: $(this).val().length === 0, value: $(this).val(), length: $(this).val().length});//
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