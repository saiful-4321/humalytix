<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ ucfirst($letter->type) }} Letter</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 14px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 40px; }
        .content { margin-bottom: 50px; }
        .footer { margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ ucfirst($letter->type) }} Letter</h2>
    </div>
    <div class="content">
        {!! $letter->content !!}
    </div>
    <div class="footer">
        <p>This is a computer-generated document.</p>
    </div>
</body>
</html>
