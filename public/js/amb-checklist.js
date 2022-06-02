function getInventoryItems() {
    $('#substation-select').on('change', function() {
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
  
  $('#fsubstation-select').on('change.select2', function() {
    let inventoryId = $(this).val();
    loadSubstation(inventoryId);
  
  });
  
  let selectedSubstation = $('#substation-select').val();
  if (selectedSubstation == undefined) {
    selectedSubstation = $('#substation-select option:first()').val();
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

function checklistModal() {
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
  
   $('#add-product-checklist-sub').on('click', function() {

    if (parseInt($('#product-quantity').val()) > parseInt($('#product-quantity').attr('max'))) {
        $('#modal-alert').css('display','block');
        console.log('enter here');
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

    let from = $("#substation-select option:selected").val();

    $('#from-location-id').attr('value', from);

    $('#substation-select').prop('disabled', 'disabled');

    $('#modal-alert').css('display','none');

    let i = $("#medstable").data('lineCounter');
      if (i == undefined) {
        i = 0;
      }
  
    //let i = $('#medstable').find('tbody tr').length;
  
    let productId = $('#meds').find(':selected').val();
  
    let productName = $('#meds').find(':selected').text();
  
    let med_name = productName.split("[/]");
  
    let productUmText = $('#um').find(':selected').text();
  
    let productQuantity = $('#product-quantity').val();
  
    let productUM = $('#um').find(':selected').val();
  
    // let container = $('#test');

    let containerForm = $('<div></div>', {style: "display:none"});
  
    let newInput = '<input form="checklist" name="product['+i+'][productId]" value=' + productId + ' />';
    let newInput2 = '<input form="checklist" name="product['+i+'][productName]" value="' + med_name[0] + '" />';
    let newInput3 = '<input form="checklist" name="product['+i+'][productUmText]" value="' + med_name[1].replace(/ /g,'') + '" />';
    //let newInput4 = '<input name="product['+i+'][productUm]" value="' + productUM + '" />';
    let newInput5 = '<input form="checklist" name="product['+i+'][productQty]" value=' + productQuantity + ' />';
  
    containerForm.append(newInput);
    containerForm.append(newInput2);
    containerForm.append(newInput3);
    //container.append(newInput4);
    containerForm.append(newInput5);
  
    // let output = '<tr>';
    //   $('#meds-modal input:not([type=hidden], [type=checkbox]), #meds-modal select').each(function() {
    //     if(!$(this).is("select"))
    //       output += '<td>' + $(this).val() + '</td>';
    //     else if($(this).is("select")) 
    //       output += '<td>' + $('#um option:selected').text() + '</td>';
    //     else if( !$(this).val() )
    //       output += '<td>' + '' + '</td>';
    //   });
  
    //   output += '</tr>';

    let tr = $('<tr></<tr>');
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
      let oldText = $('#meds').find('option:selected').text();
      let oldTextArray = oldText.split('[/]');

      let newNumber = parseInt(oldTextArray[2]) - parseInt($('#product-quantity').val());

      let newText = oldTextArray[0] + ' [/] ' + oldTextArray[1] + ' [/] ' + newNumber + ' [/] ' + oldTextArray[3];

      $('#meds').find('option:selected').text(newText);
      //$('#meds').trigger('change.select2');
      $('#meds').select2('destroy');
      $('#meds').select2();

      i++;

      $("#medstable").data('lineCounter', i);
  
      $('#medstable tbody').append(tr);
      testInputs();
      $('#meds-modal').modal('toggle');
      $('#meds-modal form')[0].reset();
  
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

      if (rowCount === 0)
        $('#print').attr('disabled', 'disabled');
      else if($('#document-date').val() == "")
        $('#print').attr('disabled', 'disabled');
      else 
      $('#print').attr('disabled', false);
      

  });
  }

jQuery(document).ready(() => {
    getInventoryItems();
    checklistModal();
    deleteRow();
    $('#document-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
});