<!DOCTYPE html>
<html lang="zxx" class="js">
@include('layouts.backLayout.head')

<body class="nk-body bg-lighter npc-default has-sidebar">
    <div class="nk-app-root">
        <div class="nk-main">
            @include('layouts.backLayout.sidebar')
            <div class="nk-wrap">
                @include('layouts.backLayout.header')
                <div class="nk-content">
                    @yield('content')
                </div>
                @include('layouts.backLayout.footer')
            </div>
        </div>
    </div>
    @include('layouts.backLayout.scripts')
    {{-- <script>
        const hasMessage = @json(Session::has('message'));
        const hasErrors = @json($errors->any());
        const message = @json(Session::get('message'));
        const type = @json(Session::get('type'));
        const errorList = @json($errors->all());

        $(document).ready(function() {
            if (hasMessage && type && message) {
                toastr.clear();
                NioApp.Toast(message, type, {
                    position: 'top-right'
                });
            }

            if (hasErrors) {
                toastr.clear();
                errorList.forEach(function(error) {
                    NioApp.Toast(error, "error", {
                        position: "top-right"
                    });
                });
            }
        });
    </script> --}}

    <script>
        const hasMessage = @json(Session::has('message'));
        const message = @json(Session::get('message'));
        const type = @json(Session::get('type'));

        $(document).ready(function() {
            if (hasMessage && type && message) {
                toastr.clear();
                NioApp.Toast(message, type, {
                    position: 'top-right'
                });
            }
        });
    </script>


</body>

</html>
