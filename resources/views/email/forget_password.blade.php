@extends('email.layout')

@section('title', 'Reset Your Password')

@section('content')
    <h2 style="color: #2b6cb0; text-align: center; margin-top: 0; margin-bottom: 24px;">Reset Your Password</h2>
    <p>Hello,</p>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <div style="text-align: center; margin: 35px 0;">
        <a href="{{ $resetUrl }}" style="background-color: #3182ce; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block; box-shadow: 0 2px 4px rgba(49, 130, 206, 0.3);">Reset Password</a>
    </div>
    <p>This password reset link will expire in 60 minutes.</p>
    <p>If you did not request a password reset, no further action is required.</p>
    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">
    <p style="font-size: 12px; color: #718096; line-height: 1.5;">
        If you are having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:
    </p>
    <p style="font-size: 12px; color: #3182ce; word-break: break-all; margin: 10px 0 0 0;">
        <a href="{{ $resetUrl }}" style="color: #3182ce; text-decoration: none;">{{ $resetUrl }}</a>
    </p>
@endsection
