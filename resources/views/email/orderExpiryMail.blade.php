<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Expiry Notification</title>
</head>
<body>
    <section class="contact-main-div" style="margin: 0 auto; max-width: 600px;">
        <div class="contact-us-content">
            <table style="width:100%; border-collapse: collapse; border-spacing: 0; table-layout: auto; border-radius: 8px; margin-top: 24px; padding: 0; border: 1px solid #eee;" bgcolor="#fff" align="center">
                <tbody>
                    <tr>
                        <td style="padding: 24px 32px 30px;">
                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Dear {{ $data['name'] }},</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">We hope this email finds you well.</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">We wanted to inform you that one of your orders with us is approaching its expiration date. Please take note of the following details:</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Package Name: {{ $data['package_name'] }}</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Order Date: {{ $data['orderDate'] }}</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Expiry Date: {{ $data['expiryDate'] }}</p>

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
