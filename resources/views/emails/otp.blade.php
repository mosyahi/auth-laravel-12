<!DOCTYPE html>
<html>

<body>
    <p>Halo {{ $name }},</p>

    <p>Kode OTP Anda adalah:</p>

    <h2 style="letter-spacing: 5px; font-size: 28px; font-weight: bold;">
        {{ $otp }}
    </h2>

    <p>Kode ini berlaku selama {{ $expiry_minutes }} menit.</p>

    <p>Jika Anda tidak meminta kode ini, abaikan email ini.</p>

    <br>
    <p>Terima kasih,<br>Tim Admin</p>
</body>

</html>
