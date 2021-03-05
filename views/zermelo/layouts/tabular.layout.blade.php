<!doctype html>
<html lang="en">
<head>

    <title>{{ $report->getReportName()  }}</title>

    <link href='{{ asset("vendor/CareSet/zermelobladetabular/fontawesome-free-5.10.2-web/css/all.css") }}' rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href='{{ $bootstrap_css_location }}' />
    <link rel="stylesheet" type="text/css" href='{{ asset("vendor/CareSet/zermelobladetabular/css/caresetreportengine.report.css") }}' />
    <link rel="stylesheet" type="text/css" href='{{ asset("vendor/CareSet/zermelobladetabular/datatables/datatables.min.css") }}' />
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>


@include('Zermelo::tabular')

</body>
</html>

