<!doctype html>
<html lang="en">
<head>

    <title>{{ $report->getReportName()  }}</title>

    <link href='{{ asset("vendor/CareSet/zermelo/core/fontawesome-free-5.10.2-web/css/all.min.css") }}' rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href='{{ $bootstrap_css_location }}' />
    <link rel="stylesheet" type="text/css" href='{{ asset("vendor/CareSet/zermelo/core/css/caresetreportengine.report.css") }}' />
    <link rel="stylesheet" type="text/css" href='{{ asset("vendor/CareSet/zermelo/zermelobladetabular/datatables/datatables.min.css") }}' />
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>


@include('Zermelo::tabular')

</body>
</html>

