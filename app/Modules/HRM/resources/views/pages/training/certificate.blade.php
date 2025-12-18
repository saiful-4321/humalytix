<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificate of Completion</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #444; /* Dark grey text */
            background-color: #ffffff;
        }
        .sidebar {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 70px;
            background-color: #fbbc04; /* Google Yellow */
            z-index: 10;
        }
        .sidebar-accent {
            position: absolute;
            left: 70px;
            top: 0;
            bottom: 0;
            width: 15px;
            background-color: #ea4335; /* Google Red */
            z-index: 9;
        }
        .container {
            position: absolute;
            top: 0;
            left: 85px; /* width of sidebars */
            right: 0;
            bottom: 0;
            padding: 60px 80px;
        }
        /* Top Right Accents */
        .circle-1 {
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background-color: #4285f4; /* Google Blue */
            opacity: 0.1;
            z-index: 0;
        }
        .circle-2 {
            position: absolute;
            top: 80px;
            right: 80px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #34a853; /* Google Green */
            opacity: 0.2;
            z-index: 0;
        }

        /* Content */
        .header { margin-bottom: 60px; }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #5f6368;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .title-section { margin-bottom: 50px; }
        .cert-title {
            font-size: 48px;
            font-weight: 300; /* Light weight for modern look */
            color: #202124;
            margin: 0;
            line-height: 1.2;
        }
        .cert-subtitle {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #5f6368;
            margin-top: 10px;
        }

        .recipient-section { margin-bottom: 50px; }
        .presented-to {
            font-size: 14px;
            color: #80868b;
            margin-bottom: 10px;
        }
        .recipient-name {
            font-size: 42px;
            font-weight: bold;
            color: #202124;
            border-bottom: 2px solid #e8eaed; /* Subtle underline */
            display: inline-block;
            padding-bottom: 10px;
            min-width: 400px;
        }

        .details-section { 
            margin-bottom: 80px; 
            font-size: 18px;
            line-height: 1.6;
            color: #5f6368;
        }
        .course-name {
            color: #1a73e8; /* Link Blue */
            font-weight: bold;
        }

        .footer {
            border-top: 1px solid #e8eaed;
            padding-top: 30px;
            display: table;
            width: 100%;
        }
        .sig-col {
            display: table-cell;
            width: 33%;
            vertical-align: top;
        }
        .sig-name {
            font-weight: bold;
            font-size: 16px;
            color: #202124;
        }
        .sig-title {
            font-size: 12px;
            color: #80868b;
        }
        
        .meta-col {
            display: table-cell;
            width: 33%;
            text-align: right;
            vertical-align: top;
        }
        .meta-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #80868b;
            letter-spacing: 1px;
        }
        .meta-value {
            font-size: 12px;
            font-family: 'Courier New', monospace;
            color: #202124;
            margin-bottom: 10px;
        }

    </style>
</head>
<body>
    <!-- Design Elements -->
    <div class="sidebar"></div>
    <div class="sidebar-accent"></div>
    <div class="container">
        <div class="circle-1"></div>
        <div class="circle-2"></div>

        <div class="header">
            <div class="logo">Humalytix Academy</div>
        </div>

        <div class="title-section">
            <h1 class="cert-title">Certificate of Completion</h1>
            <div class="cert-subtitle">Recognizing Excellence</div>
        </div>

        <div class="recipient-section">
            <div class="presented-to">This certificate is proudly presented to</div>
            <div class="recipient-name">{{ $employee->full_name }}</div>
        </div>

        <div class="details-section">
            For successfully completing the professional training logic:<br>
            <span class="course-name">{{ $training_title }}</span><br>
            <span style="font-size: 14px; color: #80868b;">{{ $extra_info }} • Completed on {{ $date }}</span>
        </div>

        <div class="footer">
            <div class="sig-col">
                <div class="sig-name">{{ $trainer_name }}</div>
                <div class="sig-title">Trainer & Instructor</div>
            </div>
            <div class="sig-col">
                <div class="sig-name">Management Board</div>
                <div class="sig-title">Director of L&D, Humalytix</div>
            </div>
            <div class="meta-col">
                <div class="meta-label">Certificate ID</div>
                <div class="meta-value">{{ $certificate_id }}</div>
                <div class="meta-label">Verify Authenticity</div>
                <div class="meta-value">humalytix.com/verify</div>
            </div>
        </div>
    </div>
</body>
</html>
