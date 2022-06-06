<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>SAJFARM</title>
<!-- plugins:css -->
<link rel="stylesheet" href="/css/styles.css">
<link rel="stylesheet" href="/css/feather/feather.css">
<link rel="stylesheet" href="/css/mdi/css/materialdesignicons.min.css">
<link rel="stylesheet" href="/css/ti-icons/css/themify-icons.css">
<link rel="stylesheet" href="/css/typicons/typicons.css">
<link rel="stylesheet" href="/css/simple-line-icons/css/simple-line-icons.css">
<link rel="stylesheet" href="/css/css/vendor.bundle.base.css">
<link rel="stylesheet" href="/js/select2/select2.min.css">
<link rel="stylesheet" href="/js/select2-bootstrap-theme/select2-bootstrap.min.css"> 
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap.min.css">

<!-- endinject -->
<!-- Plugin css for this page -->
<link rel="stylesheet" href="/css/select.dataTables.min.css">
<!-- End plugin css for this page -->
<!-- inject:css -->
<link rel="stylesheet" href="/css/vertical-layout-light/style.css">
<!-- endinject -->
<link rel="shortcut icon" href="images/favicon.png" />

@if(Session::has('fileToDownload'))
    <script>
        window.open("{{ Session::get('fileToDownload') }}", '_blank').focus();
    </script>
@endif
