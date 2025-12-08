<!-- JAVASCRIPT -->
<script src="{{ asset('assets/libs/axios/axios.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
<!-- pace js -->
<script src="{{ asset('assets/libs/pace-js/pace.min.js') }}"></script>
<!-- apexcharts -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<!-- Plugins js-->
<script src="{{ asset('assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('assets/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js') }}"></script> 
{{-- moment js --}}
<script src="{{ asset('assets/libs/momentjs/momentjs.min.js') }}"></script> 
<!-- daterangepicker  -->
<script src="{{ asset('assets/libs/daterangepicker/daterangepicker.min.js') }}"></script> 
<script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- select2 js -->
<script src="{{ asset('assets/js/pages/select2.min.js') }}"></script>
<!-- twitter-bootstrap-wizard js -->
<script src="{{ asset('assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>
<script src="{{ asset('assets/libs/twitter-bootstrap-wizard/prettify.js') }}"></script>
<!-- form wizard init -->
<script src="{{ asset('assets/js/pages/form-wizard.init.js') }}"></script>
{{-- app js --}}
<script src="{{ asset('assets/js/app.js') }}"></script>
{{-- Common Js --}}
<script src="{{ asset('assets/js/common.js') }}"></script>

<script>
    $(document).ready(function() {
        // Fix for vertical menu initialization
        if ($.fn.metisMenu) {
            $("#side-menu").metisMenu();
        }

        // Fix for feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Auto-activate current menu item and expand parent menus
        var currentUrl = window.location.href.split(/[?#]/)[0];
        $("#sidebar-menu a").each(function() {
            if (this.href === currentUrl) {
                $(this).addClass("active");
                $(this).parents("ul").addClass("mm-show");
                $(this).parents("li").addClass("mm-active");
            }
        });

        // Hide Preloader
        $(window).on('load', function() {
            $('#preloader').css({
                'opacity': '0',
                'visibility': 'hidden'
            });
            setTimeout(function() {
                $('#preloader').remove();
            }, 500);
        });
    });
</script>

@stack('scripts')
  