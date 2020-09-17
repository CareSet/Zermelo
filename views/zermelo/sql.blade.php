<div class="container-fluid">
	<div class="card-header row">
		<h1> {{ $report->GetReportName()  }}</h1>
	</div>

	<div class="report-description">
		{!! $report->GetReportDescription() !!}
	</div>

	<div class="formatted-sql">
		{!!  $formatted_sql !!}
	</div>
</div>
