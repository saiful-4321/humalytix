@extends('Main::layouts.app')

@section('title', 'My Contracts')

@section('content')
<div class="row">
    <div class="col-12">
        <h4 class="card-title mb-4">My Documents & Contracts</h4>
    </div>
    @forelse($contracts as $contract)
    <div class="col-xl-4 col-md-6">
        <div class="card border {{ $contract->status == 'pending' || $contract->status == 'sent' ? 'border-warning' : 'border-light' }}">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm me-3">
                        <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-20">
                            <i class="bx bxs-file-doc"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="font-size-15 mb-1">{{ $contract->title }}</h5>
                        <p class="text-muted mb-0">{{ $contract->type }}</p>
                    </div>
                    <div>
                        @if($contract->status == 'signed')
                            <span class="badge bg-success">Signed</span>
                        @else
                            <span class="badge bg-warning">Action Req.</span>
                        @endif
                    </div>
                </div>

                <div class="text-muted font-size-13 mb-3">
                    <p class="mb-1"><i class="bx bx-calendar me-1"></i> Effective: {{ $contract->start_date->format('d M, Y') }}</p>
                    <p class="mb-0"><i class="bx bx-time me-1"></i> Status: {{ ucfirst($contract->status) }}</p>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ Storage::url($contract->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="bx bx-show me-1"></i> View Document
                    </a>
                    
                    @if($contract->status == 'sent')
                    <button type="button" class="btn btn-primary btn-sm" onclick="openSignModal({{ $contract->id }}, '{{ $contract->title }}')">
                        <i class="bx bx-pen me-1"></i> Sign Now
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">No contracts or documents found for you.</div>
    </div>
    @endforelse
</div>

<!-- Signature Modal -->
<div class="modal fade" id="signModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sign Document: <span id="modalContractTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="signForm" method="POST">
                @csrf
                <div class="modal-body text-center">
                    <p class="mb-3 text-muted">Draw your signature below to sign this document legally.</p>
                    <div class="border rounded d-inline-block" style="background: #f8f9fa;">
                        <canvas id="sig-canvas" width="400" height="160" style="cursor: crosshair;"></canvas>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-light" id="sig-clearBtn">Clear</button>
                    </div>
                    <input type="hidden" name="signature_image" id="sig-dataUrl">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="sig-submitBtn">Confirm & Sign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Canvas Logic
    const canvas = document.getElementById("sig-canvas");
    const ctx = canvas.getContext("2d");
    let isDrawing = false;

    function getMousePos(canvas, evt) {
        var rect = canvas.getBoundingClientRect();
        return {
            x: evt.clientX - rect.left,
            y: evt.clientY - rect.top
        };
    }

    canvas.addEventListener("mousedown", function(e) {
        isDrawing = true;
        const pos = getMousePos(canvas, e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
    });

    canvas.addEventListener("mousemove", function(e) {
        if (isDrawing) {
            const pos = getMousePos(canvas, e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        }
    });

    canvas.addEventListener("mouseup", function() { isDrawing = false; });
    canvas.addEventListener("mouseout", function() { isDrawing = false; }); // Stop drawing if mouse leaves canvas

    // Touch support
    canvas.addEventListener("touchstart", function(e) {
        e.preventDefault(); // Prevent scrolling
        var touch = e.touches[0];
        var mouseEvent = new MouseEvent("mousedown", {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    }, false);

    canvas.addEventListener("touchend", function(e) {
         var mouseEvent = new MouseEvent("mouseup", {});
         canvas.dispatchEvent(mouseEvent);
    }, false);

    canvas.addEventListener("touchmove", function(e) {
        var touch = e.touches[0];
        var mouseEvent = new MouseEvent("mousemove", {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    }, false);


    document.getElementById("sig-clearBtn").addEventListener("click", function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    });

    document.getElementById("sig-submitBtn").addEventListener("click", function(e) {
        // Simple check if empty
        // In prod, check pixel data
        document.getElementById("sig-dataUrl").value = canvas.toDataURL();
    });

    function openSignModal(id, title) {
        document.getElementById("modalContractTitle").innerText = title;
        document.getElementById("signForm").action = "/hrm/contracts/" + id + "/sign";
        ctx.clearRect(0, 0, canvas.width, canvas.height); // Clear canvas on open
        new bootstrap.Modal(document.getElementById('signModal')).show();
    }
</script>
@endsection
