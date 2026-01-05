<!DOCTYPE html>
<html>
<head>
    <title>Journal Ledger</title>
    <style>
        @page { margin: 15px; }
        body { font-family: sans-serif; font-size: 10px; line-height: 1.2; color: #333; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .company-header { margin-bottom: 15px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        .company-name { font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 3px; }
        .company-info { font-size: 9px; color: #555; }
        
        .report-title { font-size: 14px; font-weight: bold; margin: 10px 0; text-transform: uppercase; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 4px 6px; vertical-align: middle; }
        th { background-color: #f2f2f2; font-weight: bold; font-size: 9px; text-transform: uppercase; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 8px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="company-header text-center">
        @if($company)
            <div class="company-name">{{ $company->company_name }}</div>
            <div class="company-info">{{ $company->address }}</div>
        @else
            <div class="company-name">HUMALYTIX</div>
        @endif
        <div class="report-title">Journal Register</div>
        <div class="company-info">
            Generated on: {{ date('d-M-Y h:i A') }}
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="10%">Date</th>
                <th width="10%">Journal No</th>
                <th width="15%">Reference</th>
                <th width="10%">Type</th>
                <th width="35%">Description</th>
                <th width="10%">Status</th>
                <th width="10%" class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($journals as $journal)
            <tr>
                <td>{{ $journal->date->format('d-M-Y') }}</td>
                <td class="fw-bold">{{ $journal->journal_number }}</td>
                <td>{{ $journal->reference }}</td>
                <td>{{ ucfirst($journal->type) }}</td>
                <td>{{ Str::limit($journal->description, 50) }}</td>
                <td>{{ ucfirst($journal->status) }}</td>
                <td class="text-end">{{ number_format($journal->entries->sum('debit'), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer text-center">
        {{ $company ? $company->company_name : 'Humalytix' }} - Journal Register
    </div>
</body>
</html>
