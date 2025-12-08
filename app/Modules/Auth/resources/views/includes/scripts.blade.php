<!-- JAVASCRIPT -->
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
<!-- pace js -->
<script src="{{ asset('assets/libs/pace-js/pace.min.js') }}"></script>
<!-- validation init -->
<script src="{{ asset('assets/js/pages/validation.init.js') }}"></script>
<!-- password addon init -->
<script src="{{ asset('assets/js/pages/pass-addon.init.js') }}"></script>

<script>
$(document).ready(function() {
    // form common fix - change validation error on change
    $("body").on("keyup", "input", function() {
        $(this).removeClass("is-invalid");
    });
    $("body").on("change", "select", function() {
        $(this).removeClass("is-invalid");
    });
});

// dark & light mode
document.addEventListener('DOMContentLoaded', function() {
    const currentMode = localStorage.getItem('mode');
    if (currentMode) {
        document.body.setAttribute('data-layout-mode', currentMode);
        if (currentMode === 'dark') {
            document.querySelector('.layout-mode-dark').style.display = "block";
            document.querySelector('.layout-mode-light').style.display = "none";
        }
    } 
});
</script>

@stack('scripts')
