<table class="table table-hover mb-0">
    <thead class="bg-light">
        <tr>
            <th class="border-top-0">Name</th> 
            <th class="border-top-0">Email & Mobile</th> 
            <th class="border-top-0">NID & Date of Birth</th>
            <th class="border-top-0">Roles</th>
            @if(!($isPdf ?? false))
            <th class="border-top-0">API Token</th> 
            @endif
            <th class="border-top-0">Status</th>
            <th class="border-top-0">Created Info</th>
            <th class="border-top-0">Updated Info</th>
            @if(!($isPdf ?? false))
            <th class="border-top-0">Action</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @if(!empty($result))
        @foreach($result as $item) 
        <tr class="{{ empty($item->password) ? 'text-danger' : '' }}">
            <td>
                <span class="d-block {{ !($isPdf ?? false) ? 'text-muted' : '' }} font-weight-bold">{{ $item->name ?? null }}</span>
            </td>
            <td>
                {{ $item->email ?? null }}
                <span class="d-block text-muted">{{ $item->mobile ?? null }}</span>
            </td>
            <td>
                {{ $item->nid ?? null }}
                <span class="d-block text-muted">{{ dbToDate($item->date_of_birth ?? null) }}</span>
            </td>
            <td>
                @foreach($item->getRoleNames() as $role) 
                    <span class="badge bg-soft-primary text-primary">{{ $role ?? null }}</span>
                @endforeach
            </td> 
            
            @if(!($isPdf ?? false))
            <td>
                <i class="badge bg-soft-warning text-warning copy"> 
                    @if (auth()->user()->isSuperadmin())
                    {{ $item->api_token }}
                    @else
                    {{ mask($item->api_token) }}
                    @endif
                </i>
            </td>
            @endif

            <td>
                @if ($item->status == \App\Modules\Main\Enums\UserStatusEnum::Pending)
                <span class="badge bg-soft-warning text-warning">Pending</span>
                @elseif ($item->status == \App\Modules\Main\Enums\UserStatusEnum::Verified)
                <span class="badge bg-soft-success text-success">Verified</span>
                @elseif ($item->status == \App\Modules\Main\Enums\UserStatusEnum::Inprogress)
                <span class="badge bg-soft-info text-info">Inprogress</span>
                @elseif ($item->status == \App\Modules\Main\Enums\UserStatusEnum::Completed)
                <span class="badge bg-soft-primary text-primary">Completed</span>
                @elseif ($item->status == \App\Modules\Main\Enums\UserStatusEnum::Approved)
                <span class="badge bg-soft-success text-success">Approved</span>
                @elseif ($item->status == \App\Modules\Main\Enums\UserStatusEnum::Rejected)
                <span class="badge bg-soft-danger text-danger">Rejected</span>
                @endif
            </td>
            <td>
                {{ $item->createdBy->name ?? null }}
                <span class="d-block text-muted">{{ dbToDate($item->created_at) ?? null }}</span>
            </td>
            <td>
                {{ $item->updatedBy->name ?? null }}
                <span class="d-block text-muted">{{ dbToDate($item->updated_at) ?? null }}</span>
            </td>
            
            @if(!($isPdf ?? false))
            <td>
                @can("user-update")
                    <a class="mb-1 btn btn-primary" href="javascript:void(0)" route="{{ route('dashboard.user.edit', $item->id) }}" data-toggle="dynamicModal" title="Edit"><i class="fa fa-edit"></i> </a>
                @endcan

                @can("user-delete") 
                    @if (!in_array($item->payment_status, [\App\Modules\Main\Enums\PaymentStatusEnum::Processing, \App\Modules\Main\Enums\PaymentStatusEnum::Paid])) 
                        <a class="mb-1 btn btn-danger" href="javascript:void(0)" onclick="return deleteConfirm(this);" route="{{ route('dashboard.user.delete', $item->id) }}" title="Delete" ><i class="fa fa-trash"></i> </a>
                    @endif
                @endcan
                
                @can("user-reset-password")
                    <a class="mb-1 btn btn-warning" href="javascript:void(0)" route="{{ route('dashboard.user.reset-password', $item->id) }}" data-toggle="dynamicModal" title="Reset Password"><i class="fa fa-key"></i> </a>
                @endcan
                @can("user-pretend-login")
                    <a class="mb-1 btn btn-success" href="javascript:void(0)" onclick="return pretendConfirm(this);" route="{{ route('dashboard.user.pretend-login', $item->id) }}" title="Pretend Login" ><i class="fa fa-arrow-right"></i> </a>
                @endcan
            </td>
            @endif
        </tr>
        @endforeach
        @endif 
    </tbody>
</table>
