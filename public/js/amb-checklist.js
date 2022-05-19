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
    console.log('inside if');
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
      let med_name = selected_med.split("/");
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
    console.log($('#product-quantity').val(), $('#product-quantity').attr('max'));

    if (parseInt($('#product-quantity').val()) > parseInt($('#product-quantity').attr('max'))) {
        $('#modal-alert').css('display','block');
        console.log('enter here');
        return;
    }
    
    if (parseInt($('#product-quantity').val()) < parseInt($('#product-quantity').attr('min'))) {
        $('#modal-alert').css('display','block');
        return;
    }

    $('#modal-alert').css('display','none');
  
    let i = $('#medstable').find('tbody tr').length;
  
    let productId = $('#meds').find(':selected').val();
  
    let productName = $('#meds').find(':selected').text();
  
    let med_name = productName.split("/");
  
    let productUmText = $('#um').find(':selected').text();
  
    let productQuantity = $('#product-quantity').val();
  
    let productUM = $('#um').find(':selected').val();
  
    let container = $('#test');
  
    let newInput = '<input name="product['+i+'][productId]" value=' + productId + ' />';
    let newInput2 = '<input name="product['+i+'][productName]" value="' + med_name[0] + '" />';
    let newInput3 = '<input name="product['+i+'][productUmText]" value="' + med_name[1].replace(/ /g,'') + '" />';
    //let newInput4 = '<input name="product['+i+'][productUm]" value="' + productUM + '" />';
    let newInput5 = '<input name="product['+i+'][productQty]" value=' + productQuantity + ' />';
  
    container.append(newInput);
    container.append(newInput2);
    container.append(newInput3);
    //container.append(newInput4);
    container.append(newInput5);
  
    let output = '<tr>';
      $('#meds-modal input:not([type=hidden], [type=checkbox]), #meds-modal select').each(function() {
        if(!$(this).is("select"))
          output += '<td>' + $(this).val() + '</td>';
        else if($(this).is("select")) 
          output += '<td>' + $('#um option:selected').text() + '</td>';
        else if( !$(this).val() )
          output += '<td>' + '' + '</td>';
      });
  
      output += '</tr>';
  
      $('#medstable tbody').append(output);
      testInputs();
      $('#meds-modal').modal('toggle');
      $('#meds-modal form')[0].reset();
  
  });
  }

jQuery(document).ready(() => {
    getInventoryItems();
    checklistModal();
});