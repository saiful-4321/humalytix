<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { width: 100%; border-bottom: 2px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        .header-table { width: 100%; }
        .company-name { font-size: 18px; font-weight: bold; }
        .company-info { text-align: right; font-size: 10px; }
        
        .title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: -1;
            width: 300px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <!-- Watermark -->
    @php
        $logo = public_path('assets/images/logo-sm.png'); // Fallback or dynamic
        // If you have a settings provider, use it. For now using a placeholder or default.
    @endphp
    <img src="{{ $logo }}" class="watermark">

    <!-- Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="border:none;">
                    <div class="company-name">QUANT</div>
                    <div style="font-size: 10px;">Corporate Member: Dhaka & Chittagong Stock Exchange Ltd.</div>
                    <div style="font-size: 10px;">Full Service Depository Participant of CDBL</div>
                </td>
                <td style="border:none; text-align: right;">
                    <div style="font-size: 10px;">PFI Tower, (2nd floor), 56-57, Dilkusha C/A, Dhaka-1000, Bangladesh</div>
                    <div style="font-size: 10px;">Tel: 01841333028; Fax: 123456789</div>
                    <div style="font-size: 10px;">Web: calbd.com.bd, E-mail: info@uftcl.com</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="title">Chart of Accounts</div>

    <!-- Content -->
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Group</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $account)
            <tr>
                <td>{{ $account->code }}</td>
                <td>
                    <span style="{{ $account->is_group ? 'font-weight:bold;' : '' }}">
                        {{ $account->name }}
                    </span>
                </td>
                <td>{{ $account->type->name ?? '-' }}</td>
                <td>{{ $account->is_group ? 'Yes' : 'No' }}</td>
                <td>{{ $account->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <table style="width: 100%; border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 33%;">Print Date: {{ date('d-M-Y h:i A') }} | Printed By: {{ auth()->user()->name ?? 'Admin' }}</td>
                <td style="border: none; width: 33%; text-align: center;">Powered By: qOffice | Version: 1.0.0</td>
                <td style="border: none; width: 33%; text-align: right;">Page <span class="page-number"></span> of <span class="page-number"></span></td> <!-- Page count usually difficult in basic HTML/CSS without specific pdf lib support, often standard dompdf handles page numbers automatically in footer with specific css -->
            </tr>
        </table>
        <div style="text-align: center; margin-top: 5px; font-size: 9px;">Errors/ Omission if any, please refer for correction. This is a computer-generated statement. No signature is required.</div>
    </div>
</body>
</html>
