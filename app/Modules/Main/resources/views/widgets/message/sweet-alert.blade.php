<script>
$(document).ready(function() {
    @if(session()->has('success'))
        Swal.fire({
            title: 'Success!',
            icon: 'success',
            html: "{{ session()->get('success') }}",
            timer: 30000
        });
    @endif

    @if($errors->any())
        var html = "<ul class='list-unstyled'>";
        @foreach ($errors->all() as $error)
            html += "<li>{{ $error }}</li>";
        @endforeach
        html += "</ul>"; 

        Swal.fire({
            title: 'Error!',
            icon: 'error',
            html: html,
            timer: 30000
        });
    @endif
});
</script>
