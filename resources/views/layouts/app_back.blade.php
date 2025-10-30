<!DOCTYPE html>
<html lang="zxx" class="js">
@include('layouts.backLayout.head')

<body class="nk-body bg-lighter npc-default has-sidebar">
    <!-- Loader Overlay -->
    <!-- Loader Overlay -->
    <div id="global-loader" style="display:none;">
        <div class="loader">
            <div class="dot dot1"></div>
            <div class="dot dot2"></div>
        </div>
        <div class="loader-text">Loading...</div>
    </div>

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
    <script>
        // Function that toggles status between active and inactive 
        function bindToggleStatus(className = '.eg-swal-av3', route = null) {
            $(document).on('click', className, function(e) {
                e.preventDefault();
                let recordId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Change status?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: route || "/toggle-status",
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: recordId,
                            },
                            success: function(data) {
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Updated!',
                                        text: data.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire('Error!', data.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });
        }


        // Toaster that toast on top right side for each session messages 
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

    {{--  for loader --}}
    <script>
        // Show loader on any form submit
        document.addEventListener('submit', function(e) {
            document.getElementById('global-loader').style.display = 'flex';
        });

        // Show loader on link click (optional, for non-AJAX page reloads)
        document.querySelectorAll("a").forEach(link => {
            link.addEventListener("click", function() {
                if (this.getAttribute("href") && this.getAttribute("href") !== "#") {
                    document.getElementById('global-loader').style.display = 'flex';
                }
            });
        });

        // Hide loader after full page load
        window.addEventListener('load', function() {
            document.getElementById('global-loader').style.display = 'none';
        });

        // For jQuery AJAX
        $(document).ajaxStart(function() {
            $('#global-loader').show();
        }).ajaxStop(function() {
            $('#global-loader').hide();
        });
    </script>

</body>

</html>
