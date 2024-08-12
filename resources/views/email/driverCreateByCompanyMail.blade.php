<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>driver account created</title>
</head>
<body>
    <section class="contact-main-div">
        <div class="contact-us-content">
            <h2 style="text-align: center;font-weight: 400;color: #000;">Your account created by {{ $data['comapny_name'] }}</h2>

            <table style="width:580px;border-collapse:separate;border-spacing:0;table-layout:auto;border-radius:8px;margin-top:24px;padding:0;border:1px solid #eee" class="m_-3343331283272028414email-body" bgcolor="#fff" align="center">
                <tbody>
                    <tr style="padding:0">
                        <td style="padding: 24px 32px 30px;">
                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Dear {{ $data['name'] }},</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">We hope this email finds you well.</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">We wanted to inform you that one of your orders with us is approaching its expiration date. Please take note of the following details:</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Name: {{ $data['name'] }}</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Email: {{ $data['email'] }}</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Mobile: {{ $data['mobile'] }}</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Password: {{ $data['password'] }}</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Approval Status: {{ $approve }}</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">If you have any questions or concerns regarding your order, please don't hesitate to contact us. We're here to assist you.</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Thank you for choosing us for your purchase. We appreciate your business.</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Best regards,</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">{{ env('APP_NAME') }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>
