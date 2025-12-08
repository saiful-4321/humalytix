<table class="table table-hover mb-0">
    <thead class="bg-light">
        <tr>
            <th class="border-top-0">SL. No.</th>
            <th class="border-top-0">Permission Name</th>
            <th class="border-top-0">Module Name</th>
            <th class="border-top-0">Created Date</th>
            <th class="border-top-0">Updated Date</th>
            @if(!($isPdf ?? false))
            <th class="border-top-0">#</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @if (!empty($result) && $result->count() > 0)
        @foreach($result as $item)
        <tr>
            <td>
                @if($isPdf ?? false)
                    {{ $loop->iteration }}
                @else
                    {{ $loop->index + ($result->firstItem()??0) }}
                @endif
            </td>
            <td>
                @php
                    $moduleName = 'N/A';
                    if(isset($item->module)) {
                        if(is_object($item->module)) {
                            $moduleName = $item->module->name ?? 'N/A';
                        } else {
                            $moduleName = $item->module;
                        }
                    }
                @endphp
                <span class="font-weight-bold">{{ $item->name }}</span>
            </td>
            <td><span>{{ $moduleName }}</span></td>
            <td>{{ dbToDateTime($item->created_at) }}</td>
            <td>{{ dbToDateTime($item->updated_at) }}</td>
            
            @if(!($isPdf ?? false))
            <td>
                @can("permission-update")
                <a href="javascript:void(0)" route="{{ route('dashboard.role.permission.edit', $item->id)  }}" data-toggle="dynamicModal" class="btn btn-primary " title="Edit"><i class="fa fa-edit"></i> Edit</a>
                @endcan
            </td>
            @endif
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
