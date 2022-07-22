function conditions() {

    $('#from-date, #until-date, #document-date, #medic-select').change(function() {
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

      if ($('#medic-select').val() == null){
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
      $('#medic-select').on('change', function() {
        let medicId = $(this).val();
        let subId = $('#substation-select').val();
        
        $.ajax({
            type: "GET",
            data: {
                medic: medicId,
                substation: subId
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
      //loadMedic(medicId);
    });

    $('#substation-select').on('change.select2', function() {
      let substationId = $(this).val();
      $('#medic-select').empty();
      loadAvailableMedics(substationId);
      let medicId = $('#medic-select').val();
      loadMedic(medicId, substationId);
      conditions();
    });

    let selectedSub = $('#substation-select').val();
    if (selectedSub == undefined) {
      selectedSub = $('#substation-select option:first()').val();
    }

    loadAvailableMedics(selectedSub);

    // let selectedMedic = $('#medic-select').val();
    // if (selectedMedic == undefined) {
    //   selectedMedic = $('#medic-select option:first()').val();
    // }
    
    //console.log(selectedSubstation);
    //loadMedic(selectedMedic);

    function loadAvailableMedics(selectedSub) {
      $.ajax({
        type: "GET",
        data: {
          substation: selectedSub
      },
        url: "/available-medics",
        success: function(response) {
          $('#medic-select').append(response);
          let selectedMed = $('#medic-select').val();
          if (selectedMed == undefined) {
            selectedMed = $('#medic-select option:first()').val();
          }
          loadMedic(selectedMed, selectedSub);
          conditions();
              }
    });
    }
    
    function loadMedic(selectedMedic, selectedSub) {
      $.ajax({
        type: "GET",
        data: {
            medic: selectedMedic,
            substation: selectedSub
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