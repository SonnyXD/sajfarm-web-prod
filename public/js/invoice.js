function facturaModal() {
    $('#cim-checkbox').click(function(){
      if($(this).prop("checked") == true) {
        $("#cim-input").css( "display", "block" );
        $('#cim-code').show();
        $('#cim-label').show();
      } else {
        $("#cim-input").css( "display", "none" );
      }
    });

    $('#add-in-preview').click(function () {
      let selected_med = $("#meds option:selected").text();
      $('#product-name').val(selected_med);
   });

   $("#product-price, #product-tva").change(function(){
      let price = $("#product-price").val();
      let tva_proc = $("#product-tva").val();
      let tva_price = ((tva_proc/100) * price) + (+price);
      let quantity = $("#product-quantity").val();
      let value = (+quantity) * (+tva_price);
      value = value.toFixed(4);
      tva_price = tva_price.toFixed(4);
      $("#product-tva-price").val(tva_price);
      $("#product-value").val(value);
  });
}

function addProductToNir() {
  let invoice_id = $('nir-number').val();
  let rowCount = 0;

  
  $('#document-number, #document-date, #due-date, #discount-procent, #discount-value, #total-value, #insertion-date').change(function() {
      testInputs();
  });

  function testInputs() {
    let valid = true;

    $('#document-number, #document-date, #due-date, #discount-procent, #discount-value, #total-value, #insertion-date').each(function() {
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


  // $('#factura-modal input').on('keyup', function() {
  //   let empty = false;

  //   $('#factura-modal input').each(function() {
  //     empty = $(this).val().length == 0;
  //   });

  //   if (empty)
  //     $('#add-product').attr('disabled', 'disabled');
  //   else
  //     $('#add-product').attr('disabled', false);
  // });

  $('#add-product').on('click', function() {

    if(!$('#product-code').val()) {
      $('#modal-alert').css('display','block');
      return;
    }

    if(!$('#product-quantity').val()) {
      $('#modal-alert').css('display','block');
      return;
    }

    if(!$('#product-availability').val()) {
      $('#modal-alert').css('display','block');
      return;
    }

    if(!$('#product-lot').val()) {
      $('#modal-alert').css('display','block');
      return;
    }

    if(!$('#product-price').val()) {
      $('#modal-alert').css('display','block');
      return;
    }

    if(!$('#product-tva').val()) {
      $('#modal-alert').css('display','block');
      return;
    }

    let i = $("#intrare-factura").data('lineCounter');
    if (i == undefined) {
      i = 0;
    }

    //let i = $('#medstable').find('tbody tr').length;

    let productId = $('#meds').find(':selected').val();

    let productName = $('#meds').find(':selected').text();

    let productUmText = $('#um').find(':selected').text();

    let productCim = $('#cim-code').val();

    let productCode = $('#product-code').val();

    let productQuantity = $('#product-quantity').val();

    let productExp = $('#product-availability').val();

    let productLot = $('#product-lot').val();

    let productUM = $('#um').find(':selected').val();

    let productPrice = $('#product-price').val();

    let productTva = $('#product-tva').val();

    let productTvaPrice = $('#product-tva-price').val();

    let productValue = $('#product-value').val();

    let containerForm = $('<div></div>', {style: "display:none"});

    let newInput = '<input form="intrare-factura" name="product['+i+'][productId]" value="' + productId + '" />';
    let newInput2 = '<input form="intrare-factura" name="product['+i+'][productCim]" value="' + productCim + '" />';
    let newInput3 = '<input form="intrare-factura" name="product['+i+'][productCode]" value="' + productCode + '" />';
    let newInput4 = '<input form="intrare-factura" name="product['+i+'][productQty]" value="' + productQuantity + '" />';
    let newInput5 = '<input form="intrare-factura" name="product['+i+'][productExp]" value="' + productExp + '" />';
    let newInput6 = '<input form="intrare-factura" name="product['+i+'][productLot]" value="' + productLot + '" />';
    let newInput7 = '<input form="intrare-factura" name="product['+i+'][productUm]" value="' + productUM + '" />';
    let newInput8 = '<input form="intrare-factura" name="product['+i+'][productPrice]" value="' + productPrice + '" />';
    let newInput9 = '<input form="intrare-factura" name="product['+i+'][productTva]" value="' + productTva + '" />';
    let newInput10 = '<input form="intrare-factura" name="product['+i+'][productTvaPrice]" value="' + productTvaPrice + '" />';
    let newInput11 = '<input form="intrare-factura" name="product['+i+'][productValue]" value="' + productValue + '" />';
    let newInput12 = '<input form="intrare-factura" name="product['+i+'][productUmText]" value="' + productUmText + '" />';
    let newInput13 = '<input form="intrare-factura" name="product['+i+'][productName]" value="' + productName + '" />';

    // <input name="test[abc][1]" value="test" />
    /*
    $_POST['test']['abc']['1'] = "test"
    */

    containerForm.append(newInput);
    containerForm.append(newInput2);
    containerForm.append(newInput3);
    containerForm.append(newInput4);
    containerForm.append(newInput5);
    containerForm.append(newInput6);
    containerForm.append(newInput7);
    containerForm.append(newInput8);
    containerForm.append(newInput9);
    containerForm.append(newInput10);
    containerForm.append(newInput11);
    containerForm.append(newInput12);
    containerForm.append(newInput13);

    // let output = '<tr>';
    // $('#meds-modal input:not([type=hidden], [type=checkbox]), #meds-modal select').each(function() {
    //   if(!$(this).is("select"))
    //     output += '<td>' + $(this).val() + '</td>';
    //   else if($(this).is("select")) 
    //     output += '<td>' + $('#um option:selected').text() + '</td>';
    //   else if( !$(this).val() )
    //     output += '<td>' + '' + '</td>';
    // });

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

    // output += '<td><button type="button" class="btn btn-danger" id="delete-row">Sterge</button></td>';

    // output += '</tr>';


    i++;
    $("#intrare-factura").data('lineCounter', i);



    $('#medstable tbody').append(tr);
    testInputs();
    $('#cim-code').hide();
    $('#cim-label').hide();
    $('#meds-modal').modal('toggle');
    $('#meds-modal form')[0].reset();

    // $('#submit1, #submit2').click(function () {

    // });

    // let tb = $('#medstable:eq(0) tbody');
    // let size = tb.find("tr").length;
    // console.log("Number of rows : " + size);
    // tb.find("tr").each(function(index, element) {
    //   let colSize = $(element).find('td').length;
    //   console.log("  Number of cols in row " + (index + 1) + " : " + colSize);
    //   $(element).find('td').each(function(index, element) {
    //     let colVal = $(element).text();
    //     console.log("    Value in col " + (index + 1) + " : " + colVal.trim());
    //   });
    // });
  });
}

function deleteRow() {
  $('#medstable').on('click', '#delete-row', function(){
    $(this).closest ('tr').remove();
});
}


jQuery(document).ready(() => {
    facturaModal();
    addProductToNir();
    deleteRow();
    $('#document-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
    $('#insertion-date').attr('max', new Date(new Date().getTime() - new Date().getTimezoneOffset() * 60000).toISOString().split("T")[0]);
});