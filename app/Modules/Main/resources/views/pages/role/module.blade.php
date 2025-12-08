@extends("Main::layouts.app")


@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">                        
            <h2>{{ __('Modules') }}</h2>
        </div>            
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}"><i class="fas fa-home"></i></a></li>                            
                <li class="breadcrumb-item">Role & Permission</li>
                <li class="breadcrumb-item active">Modules</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix"> 
    <div class="col-lg-12 col-md-12">
        <div class="card bg-white"> 
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Module List - (showing {{ $result->firstItem()??0 }} to {{ $result->lastItem()??0 }} of total {{ $result->total()??0 }} entries)</h6>

                    <div class="d-flex align-items-center gap-2">
                        @can('module-create')
                        <a href="javascript:void(0)" route="{{  route('dashboard.role.module.create')  }}" data-toggle="dynamicModal" class="btn btn-info btn-sm d-flex align-items-center font-weight-medium">
                            <i class="mdi mdi-plus me-1"></i> Add New Module
                        </a>
                        @endcan 
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#moduleFilter" aria-controls="moduleFilter">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter Modules
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Filters Section -->
            @if(request()->hasAny(['name', 'start_date', 'end_date']))
                <x-Main::active-filters :url="route('dashboard.role.module')">
                    <x-Main::active-filter-item key="name" label="Name" :value="request('name')" />
                    <x-Main::active-filter-item key="start_date" label="Start Date" :value="request('start_date')" />
                    <x-Main::active-filter-item key="end_date" label="End Date" :value="request('end_date')" />
                </x-Main::active-filters>
            @endif

            <div class="card-body p-0"> 
                <div class="table-responsive rounded-10 border">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-top-0">SL. No.</th>
                                <th class="border-top-0">Name</th>
                                <th class="border-top-0">Status</th>
                                <th class="border-top-0">Created Date</th>
                                <th class="border-top-0">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($result) && $result->count() > 0)
                            @foreach($result as $item)
                            <tr>
                                <td>{{ $loop->index + ($result->firstItem()??0) }}</td>
                                <td><span>{{ $item->name }}</span></td>
                                <td>{!! $item->status==1?'<span class="text-success">Active</span>':'<span class="text-danger">Inactive</span>' !!}</td>
                                <td>{{ dbToDateTime($item->created_at) }}</td>
                                <td>
                                    @can("module-update")
                                    <a href="javascript:void(0)" route="{{  route('dashboard.role.module.edit', $item->id)  }}" data-toggle="dynamicModal" class="btn btn-primary " title="Edit"><i class="fa fa-edit"></i> Edit</a>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end mt-3 mb-0 px-3"> 
                        @if (!empty($result) && $result->count() > 0)
                            {{ $result->appends($_REQUEST)->render() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include("Main::pages.role.module-filter")
@endsection