<!DOCTYPE html>
<html>
<head>
    <title>{{ $journal->journal_number }}</title>
    <style>
        @page { margin: 20px; }
        body { font-family: sans-serif; font-size: 11px; line-height: 1.3; color: #333; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        
        .company-header { margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .company-name { font-size: 18px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .company-info { font-size: 10px; color: #555; }

        .voucher-title { font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 15px 0; letter-spacing: 1px; }

        table.meta { width: 100%; margin-bottom: 15px; }
        table.meta td { padding: 3px 0; vertical-align: top; }

        table.entries { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.entries th, table.entries td { border: 1px solid #ccc; padding: 5px 8px; }
        table.entries th { background-color: #f0f0f0; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 9px; color: #777; border-top: 1px solid #ddd; padding-top: 5px; }
        
        .signatures { margin-top: 50px; width: 100%; }
        .signature-box { display: inline-block; width: 30%; border-top: 1px solid #333; text-align: center; padding-top: 5px; margin-right: 3%; }
    </style>
</head>
<body>
    <div class="company-header text-center">
        @if($company)
            <div class="company-name">{{ $company->company_name }}</div>
            <div class="company-info">{{ $company->address }}</div>
            <div class="company-info">Phone: {{ $company->phone }} | Email: {{ $company->email }}</div>
        @else
            <div class="company-name">HUMALYTIX</div>
        @endif
        <div class="voucher-title">JOURNAL VOUCHER</div>
    </div>

    <table class="meta">
        <tr>
            <td width="60%">
                <strong>Journal No:</strong> {{ $journal->journal_number }}<br>
                <strong>Reference:</strong> {{ $journal->reference ?? 'N/A' }}
            </td>
            <td width="40%" class="text-end">
                <strong>Date:</strong> {{ $journal->date->format('d-M-Y') }}<br>
                <strong>Fiscal Year:</strong> {{ $journal->fiscalYear->name ?? '-' }}
            </td>
        </tr>
    </table>

    <table class="entries">
        <thead>
            <tr>
                <th width="15%">Code</th>
                <th width="35%">Account</th>
                <th width="30%">Description</th>
                <th width="10%" class="text-end">Debit</th>
                <th width="10%" class="text-end">Credit</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDebit = 0; $totalCredit = 0; @endphp
            @foreach($journal->entries as $entry)
            @php $totalDebit += $entry->debit; $totalCredit += $entry->credit; @endphp
            <tr>
                <td>{{ $entry->account->code }}</td>
                <td>{{ $entry->account->name }}</td>
                <td>{{ $entry->description }}</td>
                <td class="text-end">{{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}</td>
                <td class="text-end">{{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f9f9f9;">
                <td colspan="3" class="text-end fw-bold">TOTAL</td>
                <td class="text-end fw-bold">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($totalCredit, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="mb-3">
        <strong>In Words:</strong> {{ \NumberFormatter::create('en', \NumberFormatter::SPELLOUT)->format($totalDebit) }} only.
    </div>

    @if($journal->description)
    <div class="mb-3">
        <strong>Narration:</strong> {{ $journal->description }}
    </div>
    @endif

    <div class="signatures">
        <div class="signature-box">Prepared By</div>
        <div class="signature-box">Checked By</div>
        <div class="signature-box">Approved By</div>
    </div>

    <div class="footer text-center">
        Generated on {{ date('d-M-Y h:i A') }}
    </div>
</body>
</html>
