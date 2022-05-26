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

  
  $('#document-number, #document-date, #due-date, #discount-procent, #discount-value, #total-value').change(function() {
      testInputs();
  });

  function testInputs() {
    let valid = true;

    $('#document-number, #document-date, #due-date, #discount-procent, #discount-value, #total-value').each(function() {
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

    let i = $('#medstable').find('tbody tr').length;

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

    let container = $('#test');

    let newInput = '<input name="product['+i+'][productId]" value="' + productId + '" />';
    let newInput2 = '<input name="product['+i+'][productCim]" value="' + productCim + '" />';
    let newInput3 = '<input name="product['+i+'][productCode]" value="' + productCode + '" />';
    let newInput4 = '<input name="product['+i+'][productQty]" value="' + productQuantity + '" />';
    let newInput5 = '<input name="product['+i+'][productExp]" value="' + productExp + '" />';
    let newInput6 = '<input name="product['+i+'][productLot]" value="' + productLot + '" />';
    let newInput7 = '<input name="product['+i+'][productUm]" value="' + productUM + '" />';
    let newInput8 = '<input name="product['+i+'][productPrice]" value="' + productPrice + '" />';
    let newInput9 = '<input name="product['+i+'][productTva]" value="' + productTva + '" />';
    let newInput10 = '<input name="product['+i+'][productTvaPrice]" value="' + productTvaPrice + '" />';
    let newInput11 = '<input name="product['+i+'][productValue]" value="' + productValue + '" />';
    let newInput12 = '<input name="product['+i+'][productUmText]" value="' + productUmText + '" />';
    let newInput13 = '<input name="product['+i+'][productName]" value="' + productName + '" />';

    // <input name="test[abc][1]" value="test" />
    /*
    $_POST['test']['abc']['1'] = "test"
    */

    container.append(newInput);
    container.append(newInput2);
    container.append(newInput3);
    container.append(newInput4);
    container.append(newInput5);
    container.append(newInput6);
    container.append(newInput7);
    container.append(newInput8);
    container.append(newInput9);
    container.append(newInput10);
    container.append(newInput11);
    container.append(newInput12);
    container.append(newInput13);

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


jQuery(document).ready(() => {
    facturaModal();
    addProductToNir();
});