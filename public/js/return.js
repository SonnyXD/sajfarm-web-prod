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
  
    $('#substation-select').on('change', function() {
      let subId = $(this).val();
      
      $.ajax({
          type: "GET",
          data: {
              substation: subId
          },
          url: "/returning-checklist",
          success: function(response) {
              $('#returning-checklists tr:not(:first)').empty();
              $('#returning-checklists').append(response);
          }
      });
  
  });
  
  $('#substation-select').on('change.select2', function() {
    let substationId = $(this).val();
    loadInventory(substationId);
    conditions();
  });
  
  
  let selectedSub = $('#substation-select').val();
  if (selectedSub == undefined) {
    selectedSub = $('#substation-select option:first()').val();
  }
  
  loadAvailableInventories(selectedSub);
  
  function loadAvailableInventories(selectedSub) {
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
  
        loadInventory(selectedSub);
        conditions();
            }
  });
  }
  
  function loadInventory(selectedSub) {
  
    $.ajax({
      type: "GET",
      data: {
          substation: selectedSub
      },
      url: "/returning-checklist",
      success: function(response) {
        $('#returning-checklists tr:not(:first)').empty();
        $('#returning-checklists').append(response);
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
    jQuery('#returning-checklists > tbody').on('click', '> tr:not(.treeview)', function() {
        const treeview = jQuery('#returning-checklists tbody tr.treeview.tr-' + jQuery(this).data('count'));
  
        if( treeview.hasClass('active') ) {
          treeview.removeClass('active');
        } else {
          jQuery('#returning-checklists tbody tr.treeview').removeClass('active');
          treeview.addClass('active');
          treeview[0].style.display = "";
        }
        
    });
  }
  
  jQuery(document).ready(() => {
      //conditions();
      getChecklists();
      treeviewDisplay();
      $('#from-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
      $('#until-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
      $('#document-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
  });