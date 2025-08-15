<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Логи</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <td>{{ $log->request_count }}</td>
            <td>{{ $log->url }}</td>
            <td>{{ $log->browser }}</td>
        </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Общее число запросов</h2>
        <div>
        {!! $requestsChart->container() !!}
        </div>

    <h2>Процентная доля трёх самых популярных браузеров</h2>
    <div>
        {!! $browsersChart->container() !!}
    </div>

        {!! $requestsChart->script() !!}
        {!! $browsersChart->script() !!}

</body>
</html>