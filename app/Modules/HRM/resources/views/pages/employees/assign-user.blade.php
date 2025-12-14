@extends('Main::widgets.modal.ajaxify-modal', ['data' => $data ?? ''])

@section('form')
    <!-- Radio Toggle -->
    <div class="mb-3">
        <label class="form-label font-weight-bold">Action Type</label>
        <div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="action_type" id="type_link" value="link" checked onchange="toggleType()">
                <label class="form-check-label" for="type_link">Link Existing User</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="action_type" id="type_create" value="create" onchange="toggleType()">
                <label class="form-check-label" for="type_create">Create New User</label>
            </div>
        </div>
    </div>

    <!-- Link User Section -->
    <div id="section_link">
        <div class="mb-3">
            <label class="form-label required">Select User</label>
            <div class="form-group">
                <select name="user_id" class="form-control select2" style="width: 100%">
                    <option value="">Select User</option>
                    @foreach($data->users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>
            <small class="text-muted">Only users NOT already linked to an employee are shown.</small>
        </div>
    </div>

    <!-- Create User Section -->
    <div id="section_create" style="display:none;">
        <div class="mb-3">
            <label class="form-label required">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $data->item->email }}" placeholder="Enter Email">
        </div>
        <div class="mb-3">
            <label class="form-label required">Password</label>
            <input type="text" name="password" class="form-control" value="12345678">
            <small class="text-muted">Default: 12345678</small>
        </div>
    </div>

    <script>
        function toggleType() {
            if (document.getElementById('type_create').checked) {
                document.getElementById('section_create').style.display = 'block';
                document.getElementById('section_link').style.display = 'none';
            } else {
                document.getElementById('section_create').style.display = 'none';
                document.getElementById('section_link').style.display = 'block';
            }
        }
        
        setTimeout(function() {
            // Fix for Select2 search focus inside Bootstrap Modal
            // We must remove tabindex="-1" from the modal wrapper or select2 search input won't be writable
            var $modal = $('.modal');
            $modal.removeAttr('tabindex');

            $('.select2').each(function() { 
                var $parent = $(this).closest('.modal');
                if ($parent.length === 0) {
                    $parent = $('#commonModal');
                }
                
                if ($(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2('destroy');
                }
                
                // If a modal parent is found, use it. Otherwise use default (body).
                if ($parent.length > 0) {
                    $(this).select2({ dropdownParent: $parent, width: '100%' }); 
                } else {
                    $(this).select2({ width: '100%' });
                }
            });
        }, 500);
    </script>
@endsection
