<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>

    <style>
        body {
            padding: 10px;
        }

        .button {
            background-color: #008CBA;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        .link:hover {
            cursor: pointer;
        }
    </style>
</head>
<body>
<tr>
    <td>
		<span class="m_-8466801751154875603mb_text"
              style="font-family:Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;font-size:16px;line-height:21px;color:#141823">Hi {{$user->firstName}} {{$user->lastName}},
			<p>Kindly enter this code to reset your password:</p>
			<table border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
				<tbody>
					<tr>
						<td style="font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:10px;background-color:#f2f2f2;border-left:1px solid #ccc;border-right:1px solid #ccc;border-top:1px solid #ccc;border-bottom:1px solid #ccc">
							<span class="m_-8466801751154875603mb_text"
                                  style="font-family:Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;font-size:16px;line-height:21px;color:#141823">{{$otp}}</span>
						</td>
					</tr>

				</tbody>
			</table>
        </span>
    </td>
</tr>

<p>
    Please contact us for any query, we are always happy to help you. <br>
</p>


<p>
    Kind regards <br>

</p>
<img src="{{$logo}}" alt="{{ env('APP_NAME') }}" style="width: 100px;height: 100px;">
</body>
</html>
