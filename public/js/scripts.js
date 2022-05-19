function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("medstable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

function tabSwitchTitle() {

    jQuery('.nav-tabs .nav-item .nav-link').on('click', function() {
      let title = jQuery(this).data('title');
      jQuery('#section-title').html(title);
    });
  
}

function hideProprietatiForms() {
    $('#add-nomenclator').css('display', 'none');
    $('#add-substatie').css('display', 'none');
    $('#add-tip-ambulanta').css('display', 'none');
    $('#add-ambulanta').css('display', 'none');
    $('#add-furnizor').css('display', 'none');
    $('#add-medic').css('display', 'none');
    $('#add-asistent').css('display', 'none');
    $('#add-unitate').css('display', 'none');
}

function showProprietatiForms() {
  $("#form-select").change(function () {
    var selected_option = $('#form-select').val();
  
    if (selected_option === 'nomenclator') {
      $('#add-nomenclator').css('display', 'block');
      $('#add-substatie').css('display', 'none');
      $('#add-tip-ambulanta').css('display', 'none');
      $('#add-ambulanta').css('display', 'none');
      $('#add-furnizor').css('display', 'none');
      $('#add-medic').css('display', 'none');
      $('#add-asistent').css('display', 'none');
      $('#add-unitate').css('display', 'none');
    }
  
    if (selected_option === 'substatie') {
      $('#add-nomenclator').css('display', 'none');
      $('#add-substatie').css('display', 'block');
      $('#add-tip-ambulanta').css('display', 'none');
      $('#add-ambulanta').css('display', 'none');
      $('#add-furnizor').css('display', 'none');
      $('#add-medic').css('display', 'none');
      $('#add-asistent').css('display', 'none');
      $('#add-unitate').css('display', 'none');
    }
  
    if (selected_option === 'tip-ambulanta') {
      $('#add-nomenclator').css('display', 'none');
      $('#add-substatie').css('display', 'none');
      $('#add-tip-ambulanta').css('display', 'block');
      $('#add-ambulanta').css('display', 'none');
      $('#add-furnizor').css('display', 'none');
      $('#add-medic').css('display', 'none');
      $('#add-asistent').css('display', 'none');
      $('#add-unitate').css('display', 'none');
    }
  
    if (selected_option === 'ambulanta') {
      $('#add-nomenclator').css('display', 'none');
      $('#add-substatie').css('display', 'none');
      $('#add-tip-ambulanta').css('display', 'none');
      $('#add-ambulanta').css('display', 'block');
      $('#add-furnizor').css('display', 'none');
      $('#add-medic').css('display', 'none');
      $('#add-asistent').css('display', 'none');
      $('#add-unitate').css('display', 'none');
    }
  
    if (selected_option === 'furnizor') {
      $('#add-nomenclator').css('display', 'none');
      $('#add-substatie').css('display', 'none');
      $('#add-tip-ambulanta').css('display', 'none');
      $('#add-ambulanta').css('display', 'none');
      $('#add-furnizor').css('display', 'block');
      $('#add-medic').css('display', 'none');
      $('#add-asistent').css('display', 'none');
      $('#add-unitate').css('display', 'none');
    }
  
    if (selected_option === 'medic') {
      $('#add-nomenclator').css('display', 'none');
      $('#add-substatie').css('display', 'none');
      $('#add-tip-ambulanta').css('display', 'none');
      $('#add-ambulanta').css('display', 'none');
      $('#add-furnizor').css('display', 'none');
      $('#add-medic').css('display', 'block');
      $('#add-asistent').css('display', 'none');
      $('#add-unitate').css('display', 'none');
    }
  
    if (selected_option === 'asistent') {
      $('#add-nomenclator').css('display', 'none');
      $('#add-substatie').css('display', 'none');
      $('#add-tip-ambulanta').css('display', 'none');
      $('#add-ambulanta').css('display', 'none');
      $('#add-furnizor').css('display', 'none');
      $('#add-medic').css('display', 'none');
      $('#add-asistent').css('display', 'block');
      $('#add-unitate').css('display', 'none');
    }
  
    if (selected_option === 'unitate') {
      $('#add-nomenclator').css('display', 'none');
      $('#add-substatie').css('display', 'none');
      $('#add-tip-ambulanta').css('display', 'none');
      $('#add-ambulanta').css('display', 'none');
      $('#add-furnizor').css('display', 'none');
      $('#add-medic').css('display', 'none');
      $('#add-asistent').css('display', 'none');
      $('#add-unitate').css('display', 'block');
    }
  });
}

function select2Selects() {
  $('.meds-select').select2();

  $('.meds-single-select').select2();
}

function transferConditions() {

  $( "#from-location" ).change(function() {
    let selected_option = $('#from-location option:selected').val();

    if(selected_option === 'stoc3') {
      $("#stoc3-checkbox").prop ("disabled", true);
      $("#stoc3-checkbox").prop( "checked", false );
    } else {
      $("#stoc3-checkbox").prop ("disabled", false);
    }
  });

    $('#stoc3-checkbox').click(function(){
      if($(this).prop("checked") == true) {
        $("#substation-checkbox").prop( "checked", false );
        $('#sub-picker').prop('selectedIndex', 0);
        $("#sub-picker").css( "display", "none" );
      }
    });

    $('#substation-checkbox').click(function(){
      if($(this).prop("checked") == true) {
        $("#stoc3-checkbox").prop( "checked", false );
        $("#sub-picker").css( "display", "block" );
      } else {
        $('#sub-picker').prop('selectedIndex', 0);
        $("#sub-picker").css( "display", "none" );
      }
    });
}

function avizConditions() {
    $('#donation-checkbox').click(function(){
      if($(this).prop("checked") == true) {
        $("#sponsor-checkbox").prop( "checked", false );
      }
    });

    $('#sponsor-checkbox').click(function(){
      if($(this).prop("checked") == true) {
        $("#donation-checkbox").prop( "checked", false );
      }
  });
}

function reportConditions() {
  $( "#report-type" ).change(function() {
    let selected_option = $('#report-type option:selected').val();

    if(selected_option === 'consum') {
      $("#sub-picker").css("display", "block");
    } else {
      $("#sub-picker").css("display", "none");
    }
  });
}

function facturaModal() {
    $('#cim-checkbox').click(function(){
      if($(this).prop("checked") == true) {
        $("#cim-input").css( "display", "block" );
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
      $("#product-tva-price").val(tva_price);
      $("#product-value").val(value);
  });
}

function checkboxConditions() {
    $('#medic-checkbox').click(function(){
      if($(this).prop("checked") == true) {
        $("#div-medic-select").css( "display", "block" );
        $("#div-patient-number").css( "display", "block" );
        $("#div-substation-select").css( "display", "none" );
        $("#substation-checkbox").attr("disabled", true);
        $( "#substation-checkbox" ).prop( "checked", false );
      } else {
        $("#div-medic-select").css( "display", "none" );
        $("#div-patient-number").css( "display", "none" );
        $("#substation-checkbox").attr("disabled", false);
      }
    });

    $('#substation-checkbox').click(function(){
      if($(this).prop("checked") == true) {
        $("#div-substation-select").css( "display", "block" );
      } else {
        $("#div-substation-select").css( "display", "none" );
      }
    });
}

function treeviewDisplay() {
  jQuery('#medstable tbody tr:not(.treeview)').click(function() {
      const treeview = jQuery('#medstable tbody tr.treeview.tr-' + jQuery(this).data('count'));

      if( treeview.hasClass('active') ) {
        treeview.removeClass('active');
      } else {
        jQuery('#medstable tbody tr.treeview').removeClass('active');
        treeview.addClass('active');
      }
      
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

console.log( {valid, rowCount});

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

function getInventoryItems() {
//   $('#from-location').on('change', function() {
//     let inventoryId = $(this).val();
    
//     $.ajax({
//         type: "GET",
//         data: {
//             inventory: inventoryId
//         },
//         url: "/inventory-products",
//         success: function(response) {
//             $('#meds').empty().append(response).select2();
//         }
//     });

// });

// $('#from-location').on('change.select2', function() {
//   let inventoryId = $(this).val();
//   loadSubstation(inventoryId);

// });

let selectedSubstation = $('#from-location').val();
if (selectedSubstation == undefined) {
  console.log('inside if');
  selectedSubstation = $('#from-location option:first()').val();
}

console.log(selectedSubstation);
//loadSubstation(selectedSubstation);

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
    let med_name = selected_med.split("/");
    $('#product-name').val(med_name[0]);
    $('#um').val(med_name[1]);

    $("#product-quantity").attr({
      "max" : med_name[2],     
      "min" : 1         
    });
    
 });

 $('#add-product-transfer').on('click', function() {

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
    $('#meds-modal').modal('toggle');
    $('#meds-modal form')[0].reset();

});
}

function checklistModal() {
  $('#add-in-preview').click(function () {
    let selected_med = $("#meds option:selected").text();
    let med_name = selected_med.split("/");
    $('#product-name').val(med_name[0]);
    $('#um').val(med_name[1]);

    $("#product-quantity").attr({
      "max" : med_name[2],     
      "min" : 1         
    });
    
 });

 $('#add-product-checklist-sub').on('click', function() {

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
    $('#meds-modal').modal('toggle');
    $('#meds-modal form')[0].reset();

});
}

function checklistMedicModal() {
  $('#add-in-preview').click(function () {
    let selected_med = $("#meds option:selected").text();
    let med_name = selected_med.split("/");
    $('#product-name').val(med_name[0]);
    $('#um').val(med_name[1]);

    $("#product-quantity").attr({
      "max" : med_name[2],     
      "min" : 1         
    });
    
 });

 $('#add-product-checklist-medic').on('click', function() {

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
    $('#meds-modal').modal('toggle');
    $('#meds-modal form')[0].reset();

});
}

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

jQuery(document).ready(() => {
  checkboxConditions();
  //facturaModal();
  reportConditions();
  //avizConditions();
  transferConditions();
  select2Selects();
  //treeviewDisplay();
  tabSwitchTitle();
  hideProprietatiForms();
  showProprietatiForms();
  //addProductToNir();
  //getInventoryItems();
  //transferModal();
  //checklistModal();
  //checklistMedicModal();
});

