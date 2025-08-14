<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<h2>Таблица логов</h2>
<table>
    <thead>
    <tr>
        <th>Дата</th>
        <th>Число запросов</th>
        <th>Самый популярный URL</th>
        <th>Самый популярный браузер</th>
    </tr>
    </thead>
    <tbody>
    @foreach($logs as $log)
        <tr>
            <td>{{ $log->date }}</td>
            <td>{{ $log->requests_count }}</td>
            <td>{{ $log->top_url }}</td>
            <td>{{ $log->top_browser }}</td>
        </tr>
    @endforeach
    </tbody>
</table>


</body>
</html>