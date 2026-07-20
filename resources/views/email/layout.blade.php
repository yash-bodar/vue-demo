<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'Laravel + Vue App'))</title>
    <style>
        body {
            background-color: #f7fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f7fafc;
            padding: 40px 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        .email-header {
            background-color: #ffffff;
            padding: 30px 40px;
            text-align: center;
            border-bottom: 1px solid #edf2f7;
        }
        .email-logo {
            max-height: 48px;
            vertical-align: middle;
            margin-right: 10px;
        }
        .email-brand {
            font-size: 22px;
            font-weight: 700;
            color: #2d3748;
            text-decoration: none;
            vertical-align: middle;
            display: inline-block;
        }
        .email-body {
            padding: 40px;
            color: #4a5568;
            line-height: 1.6;
            font-size: 16px;
        }
        .email-footer {
            background-color: #f7fafc;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #edf2f7;
        }
        .email-footer p {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: #a0aec0;
        }
        .email-footer a {
            color: #718096;
            text-decoration: underline;
        }
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 20px 0;
            }
            .email-body {
                padding: 25px;
            }
            .email-header, .email-footer {
                padding: 20px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                @if(file_exists(public_path('app_logo.png')))
                    <img src="{{ $message->embed(public_path('app_logo.png')) }}" alt="{{ config('app.name', 'App') }} Logo" class="email-logo" />
                @endif
                <span class="email-brand">{{ config('app.name', 'Vue Demo') }}</span>
            </div>

            <!-- Content -->
            <div class="email-body">
                @yield('content')
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Vue Demo') }}. All rights reserved.</p>
                <p style="font-size: 11px; line-height: 1.4;">
                    You are receiving this email because a request was made for your account. If you did not make this request, you can safely ignore this email.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
