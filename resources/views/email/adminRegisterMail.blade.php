<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Company Registered</title>
</head>
<body>
    <section class="contact-main-div" style="margin: 0 auto; max-width: 600px;">
        <div class="contact-us-content">
            <table style="width:100%; border-collapse: collapse; border-spacing: 0; table-layout: auto; border-radius: 8px; margin-top: 24px; padding: 0; border: 1px solid #eee;" bgcolor="#fff" align="center">
                <tbody>
                    <tr>
                        <td style="padding: 24px 32px 30px;">
                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Hello {{ $data['name'] }},</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Welcome to our company!</p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">Company Id: <strong>{{ $data['owner_id'] }}</strong>
                            </p>

                            <p style="font-size: 14px; padding-bottom: 10px; margin: 0; color: #000;">In case you have more questions or just feel like saying hi, reply to this email!</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>
