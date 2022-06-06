  function getInventoryItems() {
        $('#from-location').on('change', function() {
          let inventoryId = $(this).val();
          
          $.ajax({
              type: "GET",
              data: {
                  inventory: inventoryId
              },
              url: "/inventory-products",
              success: function(response) {
                  $('#meds').empty().append(response).select2();
              }
          });
      
      });
      
      $('#from-location').on('change.select2', function() {
        let inventoryId = $(this).val();
        loadSubstation(inventoryId);
      
      });
      
      let selectedSubstation = $('#from-location').val();
      if (selectedSubstation == undefined) {
        selectedSubstation = $('#from-location option:first()').val();
      }
      
      //console.log(selectedSubstation);
      loadSubstation(selectedSubstation);
      
      function loadSubstation(inventoryId) {
        $.ajax({
          type: "GET",
          data: {
              inventory: inventoryId
          },
          url: "/inventory-products",
          success: function(response) {
              $('#meds').empty().append(response).select2();
          }
      });
      }
      
  }

  function transferModal() {
      $('#add-in-preview').click(function () {
        let selected_med = $("#meds option:selected").text();
        let med_name = selected_med.split("[/]");
        $('#product-name').val(med_name[0]);
        $('#um').val(med_name[1]);
    
        $("#product-quantity").attr({
          "max" : med_name[2].trim(),     
          "min" : 1         
        });
        
    });

    $('#document-date').change(function() {
      testInputs();
  });

  function testInputs() {
    let valid = true;

    $('#document-date').each(function() {
      //console.log({input: $(this), valid: $(this).val().length === 0, value: $(this).val(), length: $(this).val().length});//
      if ($(this).val().length === 0) {
        valid = false;
      }
    });

    let rowCount = $('#medstable tbody tr').length;

    if (!valid || rowCount === 0)
    $('#print').attr('disabled', 'disabled');
  else
    $('#print').attr('disabled', false);

  }


    
    $('#add-product-transfer').on('click', function() {

      if (parseInt($('#product-quantity').val()) > parseInt($('#product-quantity').attr('max'))) {
          $('#modal-alert').css('display','block');
          return;
      }
      
      if (parseInt($('#product-quantity').val()) < parseInt($('#product-quantity').attr('min'))) {
          $('#modal-alert').css('display','block');
          return;
      }

      if(!$('#product-quantity').val()) {
        $('#modal-alert').css('display','block');
        return;
      }

      if(!$('#product-name').val()) {
        $('#modal-alert-404').css('display','block');
        return;
      }

      let from = $("#from-location option:selected").val();

      let to = $("#to-location option:selected").val();

      $('#from-location-id').attr('value', from);

      $('#to-location-id').attr('value', to);

      $('#from-location').prop('disabled', 'disabled');

      $('#to-location').prop('disabled', 'disabled');

      $('#modal-alert').css('display','none');

      $('#modal-alert').css('display','none');


      let productId = $('#meds').find(':selected').val();
      let productQuantity = $('#product-quantity').val();

      let foundTr = $('#medstable').find('> tbody > tr[data-productid="'+productId+'"]');
      

      if (foundTr.length) {
        let oldQte = foundTr.attr('data-productquantity');
        let newQte = parseInt(oldQte) + parseInt(productQuantity);
        foundTr.find('._productQte').val(newQte);
        foundTr.find('>td:nth-child(3)').text(newQte);

      } else {

        let i = $("#bon-transfer").data('lineCounter');
        if (i == undefined) {
          i = 0;
        }
      
        //let i = $('#medstable').find('tbody tr').length;
      
        
      
        let productName = $('#meds').find(':selected').text();
      
        let med_name = productName.split("[/]");
      
        let productUmText = $('#um').find(':selected').text();
      
        
      
        let productUM = $('#um').find(':selected').val();
      
        let containerForm = $('<div></div>', {style: "display:none"});
      
        let newInput = '<input form="bon-transfer" name="product['+i+'][productId]" value=' + productId + ' />';
        let newInput2 = '<input form="bon-transfer" name="product['+i+'][productName]" value="' + med_name[0] + '" />';
        let newInput3 = '<input form="bon-transfer" name="product['+i+'][productUmText]" value="' + med_name[1].replace(/ /g,'') + '" />';
        //let newInput4 = '<input name="product['+i+'][productUm]" value="' + productUM + '" />';
        let newInput5 = '<input form="bon-transfer" class="_productQte" name="product['+i+'][productQty]" value=' + productQuantity + ' />';
      
        containerForm.append(newInput);
        containerForm.append(newInput2);
        containerForm.append(newInput3);
        //container.append(newInput4);
        containerForm.append(newInput5);
      
    

        let tr = $('<tr></<tr>');

        tr.attr('data-productid', productId);
        tr.attr('data-productquantity', productQuantity);

        $('#meds-modal input:not([type=hidden], [type=checkbox]), #meds-modal select').each(function() {
          if(!$(this).is("select")) {
            tr.append($('<td>' + $(this).val() + '</td>'));
          } else if ($(this).is('select')) {
            tr.append('<td>'+$('#um option:selected').text()+'</td>');
          } else if (!(this).val()) {
            tr.append('<td></td>');
          }
        
        });

        let actionTd = $('<td></td>');
        actionTd.append('<button type="button" class="btn btn-danger" id="delete-row">Sterge</button>');
        actionTd.append(containerForm);

        tr.append(actionTd);
        // let oldText = $('#meds').find('option:selected').text();
        // let oldTextArray = oldText.split('[/]');

        // let newNumber = parseInt(oldTextArray[2]) - parseInt($('#product-quantity').val());

        // let newText = oldTextArray[0] + ' [/] ' + oldTextArray[1] + ' [/] ' + newNumber + ' [/] ' + oldTextArray[3];
        // // $('#meds').find('option:selected').text(newText);
        // // //$('#meds').trigger('change.select2');
        // // $('#meds').select2('destroy');
        // // $('#meds').select2();


        i++;
        $("#bon-transfer").data('lineCounter', i);
      
        $('#medstable tbody').append(tr);

    }

    let oldText = $('#meds').find('option:selected').text();
    let oldTextArray = oldText.split('[/]');

    let newNumber = parseInt(oldTextArray[2]) - parseInt($('#product-quantity').val());

    let newText = oldTextArray[0] + ' [/] ' + oldTextArray[1] + ' [/] ' + newNumber + ' [/] ' + oldTextArray[3];
    $('#meds').find('option:selected').text(newText);
    //$('#meds').trigger('change.select2');
    $('#meds').select2('destroy');
    $('#meds').select2();
    
    testInputs();
    $('#meds-modal').modal('toggle');
    $('#meds-modal form')[0].reset();

    
    
    });

  }

    function selects() {

      $('#from-location').on('change', function() {
        let fromValue = $('#from-location').val();
        let options = $('#to-location option').show();
        if( $(this).val() !== '' ){
            options.siblings('option[value="' + fromValue + '"]').hide()
        }
    });
    $('#to-location').on('change', function() {
        let toValue = $('#to-location').val();
        let options = $('#from-location option').show();
        if( $(this).val() !== '' ){
            options.siblings('option[value="' + toValue + '"]').hide()
        }
    });
    }

    function deleteRow() {
      $('#medstable').on('click', '#delete-row', function(){
        
        let oldText = $('#meds').find('option:selected').text();
        let oldTextArray = oldText.split('[/]');
  
        let td = $(this).closest ('tr').find('td:nth-child(3)');

        let newNumber = parseInt(oldTextArray[2]) + parseInt(td.text());
  
        let newText = oldTextArray[0] + ' [/] ' + oldTextArray[1] + ' [/] ' + newNumber + ' [/] ' + oldTextArray[3];
        $('#meds').find('option:selected').text(newText);
        //$('#meds').trigger('change.select2');
        $('#meds').select2('destroy');
        $('#meds').select2();
        
        $(this).closest ('tr').remove();

        let rowCount = $('#medstable tbody tr').length;
        console.log(rowCount);

        if (rowCount === 0)
          $('#print').attr('disabled', 'disabled');
        else
          $('#print').attr('disabled', false);

    });
    }

  jQuery(document).ready(() => {
      getInventoryItems();
      transferModal();
      selects();
      deleteRow();
      $('#document-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
  });