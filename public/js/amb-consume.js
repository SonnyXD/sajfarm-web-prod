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
    let subId = $('#substation-select').val();
    
    $.ajax({
        type: "GET",
        data: {
            ambulance: ambulanceId,
            substation: subId
        },
        url: "/ambulance-checklist",
        success: function(response) {
            $('#amb-checklists tr:not(:first)').empty();
            $('#amb-checklists').append(response);
        }
    });

});

$('#substation-select').on('change.select2', function() {
  let substationId = $(this).val();
  let ambulanceId = $('#ambulance-select').val();
  loadAmbulance(ambulanceId, substationId);
});

$('#ambulance-select').on('change.select2', function() {
  let ambulanceId = $(this).val();
  let substationId = $('#substation-select').val();
  loadAmbulance(ambulanceId, substationId);

});

let selectedAmb = $('#ambulance-select').val();
if (selectedAmb == undefined) {
  selectedAmb = $('#ambulance-select option:first()').val();
}

let selectedSub = $('#substation-select').val();
if (selectedSub == undefined) {
  selectedSub = $('#substation-select option:first()').val();
}

//console.log(selectedSubstation);
loadAmbulance(selectedAmb, selectedSub);

function loadAmbulance(selectedAmb, selectedSub) {
  $.ajax({
    type: "GET",
    data: {
        ambulance: selectedAmb,
        substation: selectedSub
    },
    url: "/ambulance-checklist",
    success: function(response) {
      $('#amb-checklists tr:not(:first)').empty();
      $('#amb-checklists').append(response);
    }
});
}

}

// function getChecklists() {
//     $('#substation-select').on('change', function() {
//     let subId = $(this).val();

//   //   $('#ambulance-select').on('change', function() {
//   //   let ambulanceId = $(this).val();
//   //   $.ajax({
//   //       type: "GET",
//   //       data: {
//   //           ambulance: ambulanceId,
//   //           substation: subId
//   //       },
//   //       url: "/ambulance-checklist",
//   //       success: function(response) {
//   //           $('#amb-checklists tr:not(:first)').empty();
//   //           $('#amb-checklists').append(response);
//   //       }
//   //   });

//   // });

// });

//   function loadAmbulance(selectedAmb) {
//     $.ajax({
//       type: "GET",
//       data: {
//           ambulance: selectedAmb
//       },
//       url: "/ambulance-checklist",
//       success: function(response) {
//         $('#amb-checklists tr:not(:first)').empty();
//         $('#amb-checklists').append(response);
//       }
//   });
//   }
// }

function treeviewDisplay() {
  jQuery('#amb-checklists > tbody').on('click', '> tr:not(.treeview)', function() {
      const treeview = jQuery('#amb-checklists tbody tr.treeview.tr-' + jQuery(this).data('count'));

      if( treeview.hasClass('active') ) {
        treeview.removeClass('active');
      } else {
        jQuery('#amb-checklists tbody tr.treeview').removeClass('active');
        treeview.addClass('active');
        treeview[0].style.display = "";
      }
      
  });
}

jQuery(document).ready(() => {
    conditions();
    getChecklists();
    treeviewDisplay();
    $('#from-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
    $('#until-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
    $('#document-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
});