// function conditions() {
//     $('#provider-name, #provider-office, #provider-address, #provider-regc, #provider-cui').change(function() {
//         testInputs();
//     });

//     function testInputs() {
//         let valid = true;
    
//         $('#provider-name, #provider-office, #provider-address, #provider-regc, #provider-cui').each(function() {
//           //console.log({input: $(this), valid: $(this).val().length === 0, value: $(this).val(), length: $(this).val().length});//
//           if ($(this).val().length === 0) {
//             valid = false;
//           }
//         });
    
//         if (!valid)
//         $('#print').attr('disabled', 'disabled');
//       else
//         $('#print').attr('disabled', false);
    
//       }
//       testInputs();
// }

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
      swal("Inserez proprietatile..", {
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
    //conditions();
});