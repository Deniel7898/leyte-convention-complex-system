<!DOCTYPE html>
<html>
<head>
    <title>QR Label - {{ $qr->code }}</title>

    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
            padding: 40px;
        }

        .print-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 40px;
            width: 350px;
            margin: auto;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
        }

        .code {
            font-size: 14px;
            color: #4a5568;
            margin-bottom: 15px;
        }

        .qr-box {
            margin: 20px 0;
        }

        .status {
            margin-top: 10px;
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            background: #d4edda;
            color: #155724;
        }

        .print-btn {
            margin-top: 20px;
            padding: 10px 18px;
            background: #4c6ef5;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        /* PRINT SETTINGS */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .print-btn {
                display: none;
            }

            .print-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>

    <div class="print-card">

        <div class="title">LCC System</div>

        <div class="code">
            {{ $qr->code }}
        </div>

        <div class="qr-box">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qr->code) !!}
        </div>

        <div class="status">
            {{ ucfirst($qr->status) }}
        </div>

        <button class="print-btn" onclick="window.print()">Print</button>

    </div>

</body>
</html>
