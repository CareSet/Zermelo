<!doctype html>
<html lang="en">
<head>

<title>{{ $report->getReportName()  }}</title>

<link rel="stylesheet" type="text/css" href='{{ $bootstrap_css_location }}'/>
<link rel="stylesheet" type="text/css" href='{{ asset("vendor/CareSet/zermelobladecard/datatables/datatables.min.css") }}'/>
<link href='{{ asset("vendor/CareSet/zermelobladecard/fontawesome-free-5.10.2-web/css/all.css") }}' rel="stylesheet" />
<style type="text/css">
    h1.card-title {
        display: inline-block;
    }
    button.view-data-options {
        margin-left: 20px;
        margin-top: auto;
        margin-bottom: auto;
        float: right;
        width: auto;
        height: min-content;
        vertical-align: middle;
    }
</style>
</head>
<body>


@include('Zermelo::card')

</body>
</html>

