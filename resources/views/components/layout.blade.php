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