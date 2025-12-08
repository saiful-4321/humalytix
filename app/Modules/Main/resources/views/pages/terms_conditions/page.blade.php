<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ (config('common.online_bo.short_title') . " Bo Account") }} {{ !empty($pageName) ? " | {$pageName}" : null }}</title>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <style type="text/css"> 
        .agreeModal h1 {
            color: black;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 11pt;
        }
        .agreeModal p {
            color: black;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 11pt;
            margin: 0pt;
        }
        .agreeModal .s1 {
            color: black;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 6pt;
        }
        .agreeModal .s2 {
            color: black;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 8pt;
        }
        .agreeModal .s3 {
            color: black;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 5pt;
            vertical-align: 2pt;
        }
        .agreeModal .s4 {
            color: #9A9A9A;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 8pt;
        }
        .agreeModal li {
            display: block;
        }
    
        .agreeModal #l1 {
            padding-left: 0pt;
            counter-reset: c1 1;
        }
        .agreeModal #l1>li>*:first-child:before {
            counter-increment: c1;
            content: counter(c1, decimal)". ";
            color: black;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 11pt;
        }
        .agreeModal #l1>li:first-child>*:first-child:before {
            counter-increment: c1 0;
        }
        .agreeModal #l2 {
            padding-left: 0pt;
            counter-reset: c2 1;
        }
        .agreeModal #l2>li>*:first-child:before {
            counter-increment: c2;
            content: "(" counter(c2, lower-latin)") ";
            color: black;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 11pt;
        }
        .agreeModal #l2>li:first-child>*:first-child:before {
            counter-increment: c2 0;
        }
    
        .agreeModal #l3 {
            padding-left: 0pt;
            counter-reset: d1 1;
        }
        .agreeModal #l3>li>*:first-child:before {
            counter-increment: d1;
            content: counter(d1, lower-latin)") ";
            color: black;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 11pt;
        }
        .agreeModal #l3>li:first-child>*:first-child:before {
            counter-increment: d1 0;
        }
        .agreeModal #l4 {
            padding-left: 0pt;
            counter-reset: c2 1;
        }
        .agreeModal #l4>li>*:first-child:before {
            counter-increment: c2;
            content: "(" counter(c2, lower-latin)") ";
            color: black;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 11pt;
        }
        .agreeModal #l4>li:first-child>*:first-child:before {
            counter-increment: c2 0;
        }
        .agreeModal #l5 {
            padding-left: 0pt;
            counter-reset: e1 1;
        }
        .agreeModal #l5>li>*:first-child:before {
            counter-increment: e1;
            content: counter(e1, lower-latin)") ";
            color: black;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 11pt;
        }
        .agreeModal #l5>li:first-child>*:first-child:before {
            counter-increment: e1 0;
        }
        .agreeModal #l6 {
            padding-left: 0pt;
            counter-reset: e2 1;
        }
        .agreeModal #l6>li>*:first-child:before {
            counter-increment: e2;
            content: "(" counter(e2, lower-roman)") ";
            color: black;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 11pt;
        }
        .agreeModal #l6>li:first-child>*:first-child:before {
            counter-increment: e2 0;
        }
        .agreeModal #l7 {
            padding-left: 0pt;
            counter-reset: c2 1;
        }
        .agreeModal #l7>li>*:first-child:before {
            counter-increment: c2;
            content: "(" counter(c2, lower-latin)") ";
            color: black;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 11pt;
        }
        .agreeModal #l7>li:first-child>*:first-child:before {
            counter-increment: c2 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <br/>
            @include("Main::pages.terms_conditions.text")
        <br/>
        <br/>
    </div>
</body>
</html>

             