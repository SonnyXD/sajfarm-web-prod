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
      $('#medic-select').on('change', function() {
        let medicId = $(this).val();
        
        $.ajax({
            type: "GET",
            data: {
                medic: medicId
            },
            url: "/medic-checklist",
            success: function(response) {
                $('#med-checklists tr:not(:first)').empty();
                $('#med-checklists').append(response);
            }
        });
    
    });
    
    $('#medic-select').on('change.select2', function() {
      let medicId = $(this).val();
      loadMedic(medicId);
    
    });
    
    let selectedMedic = $('#medic-select').val();
    if (selectedMedic == undefined) {
      selectedMedic = $('#medic-select option:first()').val();
    }
    
    //console.log(selectedSubstation);
    loadMedic(selectedMedic);
    
    function loadMedic(selectedMedic) {
      $.ajax({
        type: "GET",
        data: {
            medic: selectedMedic
        },
        url: "/medic-checklist",
        success: function(response) {
          $('#med-checklists tr:not(:first)').empty();
          $('#med-checklists').append(response);
        }
    });
    }
    
    }

    function treeviewDisplay() {
      jQuery('#med-checklists > tbody').on('click', '> tr:not(.treeview)', function() {
          const treeview = jQuery('#med-checklists tbody tr.treeview.tr-' + jQuery(this).data('count'));
    
          if( treeview.hasClass('active') ) {
            treeview.removeClass('active');
          } else {
            jQuery('#med-checklists tbody tr.treeview').removeClass('active');
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