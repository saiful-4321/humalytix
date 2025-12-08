<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? "" }}</title>
    <style>
        /* Reset CSS */
        body, h1, h2, h3, h4, h5, h6, p, ul, ol, li {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        /* Container */
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        /* Header */
        .header {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        /* Footer */
        .footer {
            background-color: #f5f5f5;
            text-align: center;
            padding: 10px 0;
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
        }
        /* Content */
        .content {
            padding: 20px 0;
        }
        h2 {
            color: #007bff;
            margin-bottom: 20px;
        }
        p {
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('common.cms.short_title') }}</h1>
        </div>
        <div class="content">
            <h2>{!! $subject ?? "" !!}</h2>
            <div>{!! $body ?? "" !!}</div>
        </div>
        @include("Main::includes.footer")
    </div>
</body>
</html>
