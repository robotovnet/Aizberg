<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ \Modules\Forms\Mail\FeedbackMail::SUBJECT }}</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; line-height: 1.6;">
        {{-- Данные уже очищены от HTML в контроллере, переводы строк заменены на <br> --}}
        <p style="margin: 5px 0;">
            <strong>Имя:</strong> {!! $data['name'] !!}
        </p>
        <p style="margin: 5px 0;">
            <strong>Email:</strong> {!! $data['email'] !!}
        </p>
        @if(!empty($data['phone']))
            <p style="margin: 5px 0;">
                <strong>Телефон:</strong> {!! $data['phone'] !!}
            </p>
        @endif

        <p style="margin: 5px 0;">&nbsp;</p>

        <p style="margin: 5px 0;">
            <strong>Сообщение:</strong><br>{!! $data['message'] !!}
        </p>
    </div>
</body>
</html>
