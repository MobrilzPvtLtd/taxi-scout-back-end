<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Approval User Email</title>
</head>
<body>
    <section class="contact-main-div">
        <div class="contact-us-content">
            @if ($data['is_approval'] == 1)
                <h2 style="text-align: center;font-weight: 400;color: #000;">A New Email for approval from {{ ucfirst($data['admin_name']) }}</h2>
            @else
                <h2 style="text-align: center;font-weight: 400;color: #000;">A New Email for disapproval from {{ ucfirst($data['admin_name']) }}</h2>
            @endif

            <table style="width:580px;border-collapse:separate;border-spacing:0;table-layout:auto;border-radius:8px;margin-top:24px;padding:0;border:1px solid #eee" class="m_-3343331283272028414email-body" bgcolor="#fff" align="center">
                <tbody>
                    <tr style="padding:0">
                        <td style="border-collapse:collapse!important;word-break:break-word;padding:24px 32px 30px"
                            class="m_-3343331283272028414content" align="left" valign="top">
                            <p style="font-size:14px;padding-bottom:10px;margin:0color: #000;">Hello {{ $data['name'] }} priyanka</p>

                            <p style="font-size:14px;padding-bottom:10px;margin:0color: #000;">
                                Welcome to your new Let's get you started with the right product:
                            </p>
                            <br>
                            @if ($data['is_approval'] == 1)
                                <h2 style="padding-bottom:5px;word-break:normal;font-size:16px;font-weight:700;margin:0color: #000;">Company Id:
                                </h2>
                                <p style="font-size:14px;padding-bottom:10px;margin:0color: #000;">{{ $data['company_key'] }}</p>
                            @endif
                            <br>
                            <p style="font-size:14px;padding-bottom:10px;margin:0color: #000;">In case you have more questions or just feel like saying hi, reply to this email!</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>
