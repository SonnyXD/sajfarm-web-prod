<html>
    <head>
        <x-header/>
    </head>
    <body>
        <div class="container-scroller">
            <x-navbar/>
            <div class="container-fluid page-body-wrapper">
                <x-sidebar/>
                <div class="main-panel">
                    <div class="content-wrapper">
                        <div class="row">
                        {{ $slot }}
                        </div>
                    </div>
                </div>
                <x-settings/>
            </div>
        <x-copyright/>
        <x-footer/>
    </body>
</html>

<script>
$(document).inactivity( {
    timeout: 7200000, // the timeout until the inactivity event fire [default: 3000]
    mouse: true, // listen for mouse inactivity [default: true]
    keyboard: false, // listen for keyboard inactivity [default: true]
    touch: false, // listen for touch inactivity [default: true]
    customEvents: "customEventName", // listen for custom events [default: ""]
    triggerAll: true, // if set to false only the first "activity" event will be fired [default: false]
});

$(document).on("inactivity", function(){
    // function that fires on inactivity
    swal({
    title: "Ai fost inactiv pentru 2 ore!",
    text: "Sistemul a detectat ai fost inactiv pentru o perioada mare de timp. Te rugam sa te conectezi din nou!",
    icon: "warning",
    buttons: true,
    dangerMode: false,
  })
  .then((ok) => {
    if (ok) {
      swal("Te-ai deconectat..", {
        icon: "success",
        timer: 1000
      })
      .then(()=>  {
        window.location.href = "/logout";
      })
    } else {
        window.location.href = "/logout";
    }
  });
});
</script>