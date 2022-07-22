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
    console.log('inside if');
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

function returnModal() {
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

 $('#add-product-return').on('click', function() {

    if (parseInt($('#product-quantity').val()) > parseInt($('#product-quantity').attr('max'))) {
        $('#modal-alert').css('display','block');
        $('#modal-alert-404').css('display','none');
        $('#modal-alert-fields').css('display','none');
        return;
    }
    
    if (parseInt($('#product-quantity').val()) < parseInt($('#product-quantity').attr('min'))) {
        $('#modal-alert').css('display','block');
        $('#modal-alert-404').css('display','none');
        $('#modal-alert-fields').css('display','none');
        return;
    }

    if(!$('#product-quantity').val()) {
      $('#modal-alert').css('display','block');
      return;
    }

    if(!$('#product-name').val()) {
      $('#modal-alert-404').css('display','block');
      $('#modal-alert').css('display','none');
      $('#modal-alert-fields').css('display','none');
      return;
    }

    if(!$('#reason').val()) {
      $('#modal-alert-fields').css('display','block');
      $('#modal-alert-404').css('display','none');
      $('#modal-alert').css('display','none');
      return;
    }

    let from = $("#from-location option:selected").val();

    $('#from-location-id').attr('value', from);

    $('#from-location').prop('disabled', 'disabled');

    $('#modal-alert').css('display','none');

    $('#modal-alert').css('display','none');

  let i = $('#medstable').find('tbody tr').length;

  let productId = $('#meds').find(':selected').val();

  let productQuantity = $('#product-quantity').val();

  //let foundTr = $('#medstable').find('> tbody > tr[data-productid="'+productId+'"]');

  // if (foundTr.length) {
  //   let oldQte = foundTr.attr('data-productquantity');
  //   let newQte = parseInt(oldQte) + parseInt(productQuantity);
  //   foundTr.find('._productQte').val(newQte);
  //   foundTr.find('>td:nth-child(3)').text(newQte);

  // } else {

    // let i = $("#checklist").data('lineCounter');
    // if (i == undefined) {
    //   i = 0;
    // }

  let productName = $('#meds').find(':selected').text();

  let med_name = productName.split("[/]");

  let productUmText = $('#um').find(':selected').text();

  let productReason = $("#reason").val();

  let productUM = $('#um').find(':selected').val();

  let productAmbId = $('#ambulance-select').find(':selected').val();

  let containerForm = $('<div></div>', {style: "display:none"});

  let newInput = '<input form="return-items" name="product['+i+'][productId]" value=' + productId + ' />';
  let newInput2 = '<input form="return-items" name="product['+i+'][productName]" value="' + med_name[0] + '" />';
  let newInput3 = '<input form="return-items" name="product['+i+'][productUmText]" value="' + med_name[1].replace(/ /g,'') + '" />';
  let newInput4 = '<input form="return-items" name="product['+i+'][productReason]" value="' + productReason + '" />';
  let newInput5 = '<input form="return-items" class="_productQte" name="product['+i+'][productQty]" value=' + productQuantity + ' />';
  let newInput6 = "";

  if(!productAmbId) {
    newInput6 = '<input form="return-items" class="_productAmb" name="product['+i+'][productAmb]" value="" />';
  } else {
    newInput6 = '<input form="return-items" class="_productAmb" name="product['+i+'][productAmb]" value=' + productAmbId + ' />';
  }

  containerForm.append(newInput);
  containerForm.append(newInput2);
  containerForm.append(newInput3);
  containerForm.append(newInput4);
  containerForm.append(newInput5);
  containerForm.append(newInput6);

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

    // tr.attr('data-productid', productId);
    // tr.attr('data-productquantity', productQuantity);
      $('#meds-modal input:not([type=hidden], [type=checkbox]), #meds-modal select').each(function() {
        if(!$(this).is("select")) {
          tr.append($('<td>' + $(this).val() + '</td>'));
        } else if ($(this).is('select')) {
          tr.append('<td>'+$('#ambulance-select option:selected').text()+'</td>');
        } else if (!(this).val()) {
          tr.append('<td></td>');
        } 
         
      
      });

      let actionTd = $('<td></td>');
      actionTd.append('<button type="button" class="btn btn-danger" id="delete-row">Sterge</button>');
      actionTd.append(containerForm);

      tr.append(actionTd);

      i++;

      $("#return-items").data('lineCounter', i);

      $('#medstable tbody').append(tr);

    //}
      let oldText = $('#meds').find('option:selected').text();
      let oldTextArray = oldText.split('[/]');

      let newNumber = parseInt(oldTextArray[2]) - parseInt($('#product-quantity').val());

      

      let newText = oldTextArray[0] + ' [/] ' + oldTextArray[1] + ' [/] ' + newNumber + ' [/] ' + oldTextArray[3] + ' [/] ' + oldTextArray[4];

      console.log(newText);
      $('#meds').find('option:selected').text(newText);
      //$('#meds').trigger('change.select2');
      $('#meds').select2('destroy');
      $('#meds').select2();

  
    testInputs();
    $('#modal-alert').css('display','none');
    $('#modal-alert-404').css('display','none');
    $('#modal-alert-fields').css('display','none');
    $('#meds-modal').modal('toggle');
    $('#meds-modal form')[0].reset();

    $('#ambulance-select').val(0).trigger('change.select2');

});
}

$('#ambulance-select').select2({
  dropdownParent: $('#meds-modal .modal-content')
});

function deleteRow() {
  $('#medstable').on('click', '#delete-row', function(){
    
    let oldText = $('#meds').find('option:selected').text();
    let oldTextArray = oldText.split('[/]');

    let td = $(this).closest ('tr').find('td:nth-child(3)');

    let newNumber = parseInt(oldTextArray[2]) + parseInt(td.text());

    console.log(newNumber);

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
      swal("Documentul urmeaza sa fie inserat..", {
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
  sure();
  getInventoryItems();
  returnModal();
  deleteRow();
  $('#document-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
});