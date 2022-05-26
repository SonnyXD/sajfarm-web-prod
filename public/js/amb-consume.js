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

function getChecklists() {
  $('#ambulance-select').on('change', function() {
    let ambulanceId = $(this).val();
    
    $.ajax({
        type: "GET",
        data: {
            ambulance: ambulanceId
        },
        url: "/ambulance-checklist",
        success: function(response) {
            $('#amb-checklists tr:not(:first)').empty();
            $('#amb-checklists').append(response);
        }
    });

});

$('#ambulance-select').on('change.select2', function() {
  let ambulanceId = $(this).val();
  loadAmbulance(ambulanceId);

});

let selectedAmb = $('#ambulance-select').val();
if (selectedAmb == undefined) {
  selectedAmb = $('#ambulance-select option:first()').val();
}

//console.log(selectedSubstation);
loadAmbulance(selectedAmb);

function loadAmbulance(selectedAmb) {
  $.ajax({
    type: "GET",
    data: {
        ambulance: selectedAmb
    },
    url: "/ambulance-checklist",
    success: function(response) {
      $('#amb-checklists tr:not(:first)').empty();
      $('#amb-checklists').append(response);
    }
});
}

}

jQuery(document).ready(() => {
    conditions();
    getChecklists();
});