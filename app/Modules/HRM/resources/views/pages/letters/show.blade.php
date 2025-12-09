@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Letter Preview</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.letters.index') }}">Letters</a></li>
                <li class="breadcrumb-item active">Preview</li>
            </ul>
        </div>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-3 no-print">
                    <a href="{{ route('hrm.letters.download', $letter) }}" class="btn btn-primary"><i class="bx bx-download"></i> Download PDF</a>
                    <button onclick="window.print()" class="btn btn-secondary ms-2"><i class="bx bx-printer"></i> Print</button>
                </div>
                <div class="letter-content p-5 border">
                    {!! $letter->content !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
