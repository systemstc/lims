<!DOCTYPE html>
<html lang="zxx" class="js">
@include('layouts.backLayout.head')

<body class="nk-body bg-lighter npc-default has-sidebar">
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
    @yield('scripts')
    @stack('scripts')
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


        // Toast / SweetAlert for session messages 
        const hasMessage = @json(Session::has('message'));
        const message = @json(Session::get('message'));
        const type = @json(Session::get('type'));

        const hasSwal = @json(Session::has('swal_message'));
        const swalMessage = @json(Session::get('swal_message'));
        const swalTitle = @json(Session::get('swal_title') ?? 'Notice');
        const swalIcon = @json(Session::get('swal_icon') ?? 'error');

        $(document).ready(function() {
            if (hasSwal && swalMessage) {
                Swal.fire({
                    title: swalTitle,
                    text: swalMessage,
                    icon: swalIcon,
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            } else if (hasMessage && type && message) {
                toastr.clear();
                NioApp.Toast(message, type, {
                    position: 'top-right'
                });
            }
        });
    </script>

    {{--  for loader --}}
    <script>
        const loader = document.getElementById('global-loader');
        let loaderCount = 0;
        let autoHideTimer = null;
        const AUTO_HIDE_TIMEOUT_MS = 25000; // 25s fallback safety timeout in case navigation is aborted or fails

        function showLoader(customTimeout) {
            loaderCount++;
            if (loader && loader.style.display !== 'flex') {
                loader.style.display = 'flex';
            }
            // Clear previous safety timer
            if (autoHideTimer) {
                clearTimeout(autoHideTimer);
                autoHideTimer = null;
            }
            const timeout = (typeof customTimeout === 'number' && customTimeout > 0) ? customTimeout : AUTO_HIDE_TIMEOUT_MS;
            autoHideTimer = setTimeout(() => {
                console.warn('Loader safety auto-hide triggered after ' + timeout + 'ms');
                loaderCount = 0;
                hideLoader(true);
            }, timeout);
        }

        function hideLoader(force = false) {
            if (force) {
                loaderCount = 0;
            } else {
                loaderCount = Math.max(0, loaderCount - 1);
            }
            if (loaderCount === 0) {
                if (loader) {
                    loader.style.display = 'none';
                }
                if (autoHideTimer) {
                    clearTimeout(autoHideTimer);
                    autoHideTimer = null;
                }
            }
        }

        // Global functions for custom scripts or AJAX to control loader
        window.showLoader = showLoader;
        window.hideLoader = hideLoader;

        // Form submission: show loader on legitimate page submits
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || form.nodeName !== 'FORM') return;

            const submitter = e.submitter || form.querySelector('[type="submit"]');
            if (submitter && (submitter.matches('.no-loader') || submitter.dataset.noLoader !== undefined)) return;
            if (form.dataset.noLoader !== undefined || form.classList.contains('no-loader')) return;

            // Form submitting in a new window/tab
            if (form.target === '_blank') return;

            // Stop if HTML5 native validation prevents submit
            if (form.checkValidity && !form.checkValidity()) return;

            const action = form.getAttribute('action');
            if (action && (action.trim() === '#' || action.trim().startsWith('javascript:'))) {
                return;
            }

            // Defer until after submit event has bubbled, ensuring preventDefault was not called
            setTimeout(() => {
                if (!e.defaultPrevented) {
                    showLoader();
                }
            }, 0);
        }, true);

        // Link click: show loader when navigating to another URL
        document.addEventListener('click', function(e) {
            // Only process primary left clicks without modifier keys
            if (e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;

            const clickable = e.target.closest('a, button');
            if (!clickable) return;

            // Opt-outs
            if (clickable.dataset.noLoader !== undefined || clickable.classList.contains('no-loader')) return;
            if (clickable.closest('.no-loader, [data-no-loader], .swal2-container')) return;

            // Bootstrap modal / dropdown / tab / collapse toggles & dismissers
            if (clickable.hasAttribute('data-bs-toggle') || clickable.hasAttribute('data-toggle') ||
                clickable.hasAttribute('data-bs-dismiss') || clickable.hasAttribute('data-dismiss')) {
                return;
            }

            const tag = clickable.tagName.toLowerCase();

            // Native buttons are handled by form submit or custom scripts, ignore here
            if (tag === 'button') {
                return;
            }

            if (tag === 'a') {
                // Ignore download links
                if (clickable.hasAttribute('download')) return;

                // Ignore new tab/window links
                if (clickable.target === '_blank') return;

                const href = clickable.getAttribute('href');
                if (!href) return;
                const cleanHref = href.trim();
                if (cleanHref === '' || cleanHref === '#' || cleanHref.startsWith('#') ||
                    cleanHref.startsWith('javascript:') || cleanHref.startsWith('mailto:') || cleanHref.startsWith('tel:')) {
                    return;
                }

                try {
                    const targetUrl = new URL(cleanHref, location.href);
                    // Same-page hash navigation
                    if (targetUrl.origin === location.origin &&
                        targetUrl.pathname === location.pathname &&
                        targetUrl.search === location.search &&
                        targetUrl.hash) {
                        return;
                    }
                    // Common file downloads that don't cause page unloads
                    if (targetUrl.pathname.match(/\.(xlsx?|csv|zip|rar|docx?|txt)$/i)) {
                        return;
                    }
                } catch (err) {}

                // Defer to check if another click handler calls e.preventDefault()
                setTimeout(() => {
                    if (!e.defaultPrevented) {
                        showLoader();
                    }
                }, 0);
            }
        }, true);

        // Optional jQuery AJAX support: only show if explicitly requested with { showLoader: true }
        if (window.jQuery) {
            $(document).ajaxSend(function(event, jqXHR, settings) {
                if (settings && settings.showLoader) {
                    showLoader();
                }
            });
            $(document).ajaxComplete(function(event, jqXHR, settings) {
                if (settings && settings.showLoader) {
                    hideLoader();
                }
            });
        }

        // Hide loader when page loads or restored from browser back-forward cache (bfcache)
        window.addEventListener('load', () => hideLoader(true));
        window.addEventListener('pageshow', () => hideLoader(true));
        document.addEventListener('DOMContentLoaded', () => hideLoader(true));

        // Allow pressing Escape to dismiss loader if user cancelled navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                hideLoader(true);
            }
        });
    </script>

</body>

</html>
