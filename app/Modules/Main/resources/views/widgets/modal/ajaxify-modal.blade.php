<div class="modal-dialog {{ $data->modal_size ?? '' }}">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="title font-weight-bold mb-0" id="defaultModalLabel">{{ $data->method ?? '' }} {{ $data->page ?? '' }}</h6>
        </div>
        {!! Form::model($data->item ?? '', ['url' => $data->action ?? '', 'class' => 'dynamicModal', 'files'=> true]) !!}
        @if (!empty($data->method) && $data->method == 'Update')
            {{ Form::hidden('_method', 'PUT') }}
        @endif
        <div class="modal-body">
            @yield('form')
        </div>
        <div class="modal-footer border-0 pt-2">
            @if(isset($data->modal_only) && $data->modal_only == true)
                <button type="button" class="btn btn-outline-danger " data-bs-dismiss="modal">{{ ucfirst(($data->button ?? 'Close')) }}</button>
            @else
                <button type="button" class="btn btn-outline-danger " data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary ">{{ ucfirst(($data->button ?? ($data->method ?? ''))) }}</button>
            @endif
        </div>
        {!! Form::close() !!}
    </div>
</div>


@once('scripts')
<script type="text/javascript">
    document.querySelector(".dynamicModal").addEventListener('submit', function(e) {
        e.preventDefault();

        var form     = e.target;
        var formData = new FormData(form);

        // Remove existing error elements
        var errorElements = form.querySelectorAll('.invalid-feedback');
        errorElements.forEach(function(errorElement) {
            errorElement.remove();
        });

        axios({
            method  : form.method,
            url     : form.action,
            data    : formData,
            headers : {
                'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content'),
                'Accept'       : 'application/json',
                'Authorization': 'Bearer ' + '{{ auth()->user()->api_token ?? "" }}',
            },
            onUploadProgress: function(progressEvent) {
                var percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent
                    .total);
                // Handle progress if needed
            }
        })
        .then(function(response) {

            // Remove existing error elements
            var errorElements = form.querySelectorAll('.invalid-feedback');
            errorElements.forEach(function(errorElement) {
                errorElement.remove();
            });

            var responseData = response.data;

            if (responseData.status) {
                swalWithHtmlAlert('success', responseData.message);
                setTimeout(function() {
                    if (responseData.redirect) {
                        window.location.href = responseData.redirect;
                    } else {
                        window.location.reload();
                    }
                }, 2000);
            } else {
                swalWithHtmlAlert('error', responseData.message);
                form.querySelector('button[type=submit]').innerHTML =
                    '<i class="fa fa-refresh"></i> Try again';
            }
        })
        .catch(function(xhr) {

            var res = xhr.response;
            if (res.status == 422) {
                var errors = res.data.errors;
                // var errorHtml = '<ul class="list-unstyled">';
                for (var name in errors) { 
                    if (errors.hasOwnProperty(name)) {
                        var message = errors[name];
                        // errorHtml += '<li>' + message + '</li>';
                        var fieldName = name.includes('.') ? name.split('.')[0] + '[' + name.split('.')[1] + ']' : name;
                        var fieldElement = form.querySelector('[name="' + fieldName + '"]');
                        if (fieldElement) {
                            // Remove existing error elements
                            // var errorElements = fieldElement.parentNode.querySelectorAll('.invalid-feedback');
                            // errorElements.forEach(function(errorElement) {
                            //     errorElement.remove();
                            // });
 
                            var errorElement = document.createElement('div');
                            errorElement.classList.add('invalid-feedback');
                            errorElement.textContent = message;
                            fieldElement.parentNode.appendChild(errorElement);
                            fieldElement.classList.add('is-invalid');
                        } else {
                            swalWithHtmlAlert('warning', message);
                        }
                    }
                }
                // errorHtml += '</ul>';
                // swalWithHtmlAlert('warning', res.statusText + errorHtml);
            } else { 
                swalWithHtmlAlert('error', res.statusText);
            }
 
            form.querySelector('button[type=submit]').innerHTML =
                '<i class="fa fa-refresh"></i> Try again';
        });
    });
</script>
@endonce
