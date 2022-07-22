function conditions() {

$('#from-date, #until-date, #document-date, #ambulance-select').change(function() {
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

  if ($('#ambulance-select').val() == null){
    valid = false;
 }

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
  $('#ambulance-select').empty();
  loadAvailableAmbulances(substationId);
  let ambulanceId = $('#ambulance-select').val();
  loadAmbulance(ambulanceId, substationId);
  conditions();
});

$('#ambulance-select').on('change.select2', function() {
  let ambulanceId = $(this).val();
  let substationId = $('#substation-select').val();
  // $('#ambulance-select').select2('destroy');
  // $('#ambulance-select').select2();
  //loadAmbulance(ambulanceId, substationId);
});


let selectedSub = $('#substation-select').val();
if (selectedSub == undefined) {
  selectedSub = $('#substation-select option:first()').val();
}

loadAvailableAmbulances(selectedSub);

function loadAvailableAmbulances(selectedSub) {
  $.ajax({
    type: "GET",
    data: {
        substation: selectedSub
    },
    url: "/available-ambulances",
    success: function(response) {
      //$('#ambulance-select tr:not(:first)').empty();
      $('#ambulance-select').append(response);
      let selectedAmb = $('#ambulance-select').val();
      if (selectedAmb == undefined) {
        selectedAmb = $('#ambulance-select option:first()').val();
      }

      loadAmbulance(selectedAmb, selectedSub);
      conditions();
          }
});
}

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

function sure() {
  $('#print').on('click',function(e){
  e.preventDefault();
  let form = $(this).parents('form');
  swal({
    title: "Esti sigur ca ai finalizat?",
    text: "Odata apasat butonul de confirmare, nu mai poti modifica nimic. Recomandam verificarea multipla a datelor inainte de inserarea acestora",
    icon: "warning",
    buttons: true,
    dangerMode: false,
  })
  .then((ok) => {
    if (ok) {
      swal("Documentul urmeaza sa fie generat..", {
        icon: "success",
        timer: 1000
      })
      .then(()=>  {
        form.submit();
      })
    } else {
      swal("Ai anulat inserarea documentului!");
    }
  });
});
}

jQuery(document).ready(() => {
    //conditions();
    sure();
    getChecklists();
    treeviewDisplay();
    $('#from-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
    $('#until-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
    $('#document-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
});