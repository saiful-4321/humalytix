<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title ?? 'Report' }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
        
        @page { margin: 100px 25px 50px 25px; }
        
        header { position: fixed; top: -85px; left: 0px; right: 0px; height: 75px; border-bottom: 1px solid #333; }
        footer { position: fixed; bottom: -40px; left: 0px; right: 0px; height: 30px; border-top: 1px solid #ccc; padding-top: 5px; color: #666; font-size: 9px; }
        
        .header-table { width: 100%; border-collapse: collapse; border: none; }
        .header-table td { border: none; padding: 0; vertical-align: middle; }
        
        .company-logo { height: 60px; max-width: 150px; margin: 0 auto; display: block; }
        
        .company-info-left { font-size: 9px; line-height: 1.3; text-align: left; }
        .company-info-right { font-size: 9px; line-height: 1.3; text-align: right; }
        .company-name { font-size: 11px; text-transform: uppercase; font-weight: bold; }
        
        .footer-table { width: 100%; border-collapse: collapse; border: none; }
        .footer-table td { border: none; padding: 0; vertical-align: middle; }
        
        .page-number:after { content: counter(page); }
        
        /* Content Styling */
        main { padding-top: 10px; }
        .content-title { text-align: center; margin-bottom: 15px; }
        .content-title h2 { margin: 0; font-size: 14px; text-transform: uppercase; text-decoration: underline; }
        
        /* Table Styling Overrides for PDF */
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; page-break-inside: auto; }
        th, td { padding: 5px; border: 1px solid #ddd; text-align: left; vertical-align: middle; }
        th { background: #f0f0f0; font-weight: bold; text-transform: uppercase; font-size: 9px; color: #333; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        
        /* Utilities */
        .badge { display: inline-block; padding: 1px 3px; font-size: 8px; border-radius: 2px; border: 1px solid #eee; margin-right: 3px; }
        .d-block { display: block; }
        .text-muted { color: #777; font-size: 8px; }
        .font-weight-bold { font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        @stack('css')
    </style>
</head>
<body>
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 30%;">
                    <div class="company-info-left">
                        <div class="company-name">{{ $company->company_name ?? config('app.name') }}</div>
                        {{ $company->email ?? '' }}<br>
                        {{ $company->phone ?? '' }}
                    </div>
                </td>
                <td style="width: 40%; text-align: center;">
                    @if(!empty($company->logo_dark) && file_exists(public_path($company->logo_dark)))
                        <img src="{{ public_path($company->logo_dark) }}" class="company-logo" alt="Logo">
                    @elseif(!empty($company->logo_white) && file_exists(public_path($company->logo_white)))
                        <img src="{{ public_path($company->logo_white) }}" class="company-logo" alt="Logo">
                    @else
                        <h3>{{ config('app.name') }}</h3>
                    @endif
                </td>
                <td style="width: 30%;">
                    <div class="company-info-right">
                        {{ $company->address ?? '' }}
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table class="footer-table">
            <tr>
                <td style="width: 25%; text-align: left;">Printed: {{ now()->format('d M, Y') }}</td>
                <td style="width: 25%; text-align: center;">By: {{ auth()->user()->name }}</td>
                <td style="width: 25%; text-align: center;">Dev by: {{ config('app.developer', 'Sylnovia') }}</td>
                <td style="width: 25%; text-align: right;">Page <span class="page-number"></span></td>
            </tr>
        </table>
    </footer>

    <main>
        @if(isset($title))
        <div class="content-title">
            <h2>{{ $title }}</h2>
        </div>
        @endif
        
        @yield('content')
    </main>
</body>
</html>
