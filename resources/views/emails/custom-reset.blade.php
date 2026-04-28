<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 40px 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .header {
            background: #0ea5e9;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }

        .content {
            padding: 40px;
            color: #374151;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: #0ea5e9;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            margin: 20px 0;
        }

        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 style="margin:0;">Absensi SMK Katolik Santa</h1>
        </div>
        <div class="content">
            <h2>Halo, {{ $name }}!</h2>
            <p>Kami menerima permintaan untuk mengatur ulang password akun Anda. Silakan klik tombol di bawah ini untuk
                melanjutkan:</p>

            <div style="text-align: center;">
                <a href="{{ $url }}" class="btn">Atur Ulang Password</a>
            </div>

            <p style="font-size: 14px; margin-top: 30px;">Tautan ini akan kedaluwarsa dalam 15 menit. Jika Anda tidak
                merasa melakukan permintaan ini, abaikan saja email ini.</p>
            <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 30px 0;">
            <p style="font-size: 12px; color: #6b7280;">Jika tombol bermasalah, salin link berikut: <br>
                {{ $url }}</p>
        </div>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} SMK Katolik Santa. <br>
        Sistem Absensi Digital Terintegrasi.
    </div>
</body>

</html>
