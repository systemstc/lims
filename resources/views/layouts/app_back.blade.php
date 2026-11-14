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

    {{-- =========================================================================
         UNIFIED GLOBAL LOADER & ASYNC TASK MONITOR
         - Intercepts full-page form submissions & programmatic form.submit()
         - Intercepts jQuery AJAX ($.ajax, $.post, $.get) with ref-counting
         - Intercepts native window.fetch with ref-counting
         - Intercepts navigation links with download auto-dismissal
         - Handles HTML5 validation, bfcache pageshow, Esc key, and timeouts
         ========================================================================= --}}
    <script>
        (function() {
            let loaderCount = 0;
            let autoHideTimer = null;
            const DEFAULT_TIMEOUT_MS = 25000; // 25s fallback safety timeout

            function getLoaderElement() {
                return document.getElementById('global-loader');
            }

            function showLoader(customText, customTimeout) {
                loaderCount++;
                const loader = getLoaderElement();
                if (loader) {
                    if (typeof customText === 'string' && customText.trim()) {
                        const textEl = loader.querySelector('.loader-text');
                        if (textEl) textEl.textContent = customText.trim();
                    }
                    if (loader.style.display !== 'flex') {
                        loader.style.display = 'flex';
                    }
                }

                if (autoHideTimer) {
                    clearTimeout(autoHideTimer);
                }

                let timeout = DEFAULT_TIMEOUT_MS;
                if (typeof customTimeout === 'number' && customTimeout > 0) {
                    timeout = customTimeout;
                } else if (typeof customText === 'number' && customText > 0) {
                    timeout = customText;
                }

                autoHideTimer = setTimeout(() => {
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
                    const loader = getLoaderElement();
                    if (loader) {
                        loader.style.display = 'none';
                        const textEl = loader.querySelector('.loader-text');
                        if (textEl) textEl.textContent = 'Loading...';
                    }
                    if (autoHideTimer) {
                        clearTimeout(autoHideTimer);
                        autoHideTimer = null;
                    }
                }
            }

            // Expose globally so all page scripts can use them directly
            window.showLoader = showLoader;
            window.hideLoader = hideLoader;

            // 1. Hook programmatic HTMLFormElement.prototype.submit()
            if (window.HTMLFormElement && HTMLFormElement.prototype.submit) {
                const originalSubmit = HTMLFormElement.prototype.submit;
                HTMLFormElement.prototype.submit = function() {
                    const form = this;
                    if (form && !form.classList.contains('no-loader') && form.dataset.noLoader === undefined && form.target !== '_blank') {
                        const action = form.getAttribute('action') || '';
                        const isDownload = form.hasAttribute('download') || action.match(/(generate_pdf|download|export|\.pdf|\.xlsx|\.csv|\.zip)/i);
                        if (isDownload) {
                            showLoader('Generating & downloading file...', 8000);
                        } else {
                            showLoader('Submitting...');
                        }
                    }
                    return originalSubmit.apply(this, arguments);
                };
            }

            // 2. Intercept standard form submissions (submit event)
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (!form || form.nodeName !== 'FORM') return;

                // Opt-outs
                if (form.classList.contains('no-loader') || form.dataset.noLoader !== undefined || form.dataset.bypassLock === 'true') {
                    return;
                }
                const submitter = e.submitter || form.querySelector('[type="submit"]');
                if (submitter && (submitter.classList.contains('no-loader') || submitter.dataset.noLoader !== undefined)) {
                    return;
                }

                // New tab/window submission
                if (form.target === '_blank') return;

                // HTML5 native validation
                if (form.checkValidity && !form.checkValidity()) {
                    return;
                }

                // Empty / dummy actions
                const action = form.getAttribute('action');
                if (action && (action.trim() === '#' || action.trim().startsWith('javascript:'))) {
                    return;
                }

                const isDownload = form.hasAttribute('download') ||
                                   (action && action.match(/(generate_pdf|download|export|\.pdf|\.xlsx|\.csv|\.zip)/i)) ||
                                   (submitter && (submitter.name === 'generate_pdf' || submitter.classList.contains('btn-download')));

                // Defer until submit event finishes bubbling to check if preventDefault() was called (e.g. by AJAX handlers)
                setTimeout(() => {
                    if (!e.defaultPrevented) {
                        if (isDownload) {
                            showLoader('Generating & downloading file...', 8000);
                        } else {
                            showLoader('Submitting...');
                        }
                    }
                }, 0);
            }, true);

            // 3. Intercept navigation link clicks (and explicit action buttons)
            document.addEventListener('click', function(e) {
                if (e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;

                const clickable = e.target.closest('a, button');
                if (!clickable) return;

                // Opt-outs
                if (clickable.classList.contains('no-loader') || clickable.dataset.noLoader !== undefined) return;
                if (clickable.closest('.no-loader, [data-no-loader], .swal2-container, .select2-container, .note-editor')) return;

                // Bootstrap component toggles / dismissers
                if (clickable.hasAttribute('data-bs-toggle') || clickable.hasAttribute('data-toggle') ||
                    clickable.hasAttribute('data-bs-dismiss') || clickable.hasAttribute('data-dismiss')) {
                    return;
                }

                // Buttons explicitly requesting loader
                if (clickable.tagName.toLowerCase() === 'button') {
                    if (clickable.dataset.showLoader !== undefined || clickable.classList.contains('show-loader')) {
                        showLoader();
                    }
                    return;
                }

                // Handle <a> links
                if (clickable.tagName.toLowerCase() === 'a') {
                    if (clickable.target === '_blank') return;

                    const href = clickable.getAttribute('href');
                    if (!href) return;
                    const cleanHref = href.trim();
                    if (cleanHref === '' || cleanHref === '#' || cleanHref.startsWith('#') ||
                        cleanHref.startsWith('javascript:') || cleanHref.startsWith('mailto:') || cleanHref.startsWith('tel:')) {
                        return;
                    }

                    const isDownload = clickable.hasAttribute('download') ||
                                       cleanHref.match(/(generate_pdf|download|export|\.xlsx?|\.csv|\.zip|\.rar|\.docx?)/i);

                    try {
                        const targetUrl = new URL(cleanHref, location.href);
                        if (targetUrl.origin === location.origin &&
                            targetUrl.pathname === location.pathname &&
                            targetUrl.search === location.search) {
                            return;
                        }
                    } catch (err) {}

                    setTimeout(() => {
                        if (!e.defaultPrevented) {
                            if (isDownload) {
                                showLoader('Preparing download...', 8000);
                            } else {
                                showLoader();
                            }
                        }
                    }, 0);
                }
            }, true);

            // 4. Intercept jQuery AJAX requests ($.ajax, $.post, $.get)
            if (window.jQuery) {
                $(document).ajaxSend(function(event, jqXHR, settings) {
                    if (settings && (settings.noLoader === true || settings.suppressLoader === true)) {
                        return;
                    }
                    if (settings && settings.headers && (settings.headers['X-No-Loader'] || settings.headers['x-no-loader'])) {
                        return;
                    }

                    const url = (settings && settings.url) ? settings.url : '';
                    if (url.indexOf('validate_field') !== -1 || url.indexOf('validate-field') !== -1) {
                        return;
                    }

                    // Skip DataTables server-side pagination / search (DataTables displays its own processing indicator)
                    if (settings && settings.data) {
                        if (typeof settings.data.draw !== 'undefined' ||
                            (typeof settings.data === 'string' && settings.data.indexOf('draw=') !== -1)) {
                            return;
                        }
                    }
                    if (url.indexOf('draw=') !== -1) {
                        return;
                    }

                    // Skip Select2 live typing search
                    if (settings && (settings._select2 || (typeof settings.data === 'string' && settings.data.indexOf('_type=query') !== -1))) {
                        return;
                    }

                    jqXHR._globalLoaderActive = true;
                    showLoader();
                });

                $(document).ajaxComplete(function(event, jqXHR, settings) {
                    if (jqXHR && jqXHR._globalLoaderActive) {
                        delete jqXHR._globalLoaderActive;
                        hideLoader();
                    }
                });
            }

            // 5. Intercept native window.fetch requests
            if (window.fetch) {
                const originalFetch = window.fetch;
                window.fetch = function(...args) {
                    const resource = args[0];
                    const config = args[1] || {};
                    const url = (typeof resource === 'string') ? resource : (resource && resource.url ? resource.url : '');

                    const bypass = config.noLoader === true ||
                                   config.suppressLoader === true ||
                                   (config.headers && (config.headers['X-No-Loader'] || config.headers['x-no-loader']));

                    const isBackground = typeof url === 'string' &&
                                         (url.indexOf('validate_field') !== -1 || url.indexOf('validate-field') !== -1);

                    if (bypass || isBackground) {
                        return originalFetch.apply(this, args);
                    }

                    showLoader();
                    return originalFetch.apply(this, args)
                        .then(function(response) {
                            hideLoader();
                            return response;
                        })
                        .catch(function(error) {
                            hideLoader();
                            throw error;
                        });
                };
            }

            // 6. Page lifecycle & edge case safety listeners
            document.addEventListener('DOMContentLoaded', () => hideLoader(true));
            window.addEventListener('load', () => hideLoader(true));
            window.addEventListener('pageshow', (event) => hideLoader(true));
            window.addEventListener('popstate', () => hideLoader(true));

            // Dismiss loader if Escape key is pressed
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    hideLoader(true);
                }
            });

            // Dismiss file download loader on window focus after short delay
            window.addEventListener('focus', function() {
                if (loaderCount > 0) {
                    setTimeout(() => {
                        const loader = getLoaderElement();
                        const textEl = loader ? loader.querySelector('.loader-text') : null;
                        if (textEl && textEl.textContent.indexOf('download') !== -1) {
                            hideLoader(true);
                        }
                    }, 1200);
                }
            });
        })();
    </script>

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

</body>

</html>
