<html>
<head>

<title>{{ $Report_Name }}</title>


<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.12/af-2.1.2/b-1.2.2/b-colvis-1.2.2/b-html5-1.2.2/b-print-1.2.2/cr-1.3.2/fc-3.2.2/fh-3.1.2/kt-2.1.3/r-2.1.0/rr-1.1.2/sc-1.4.2/se-1.2.0/datatables.min.css"/>


</head>
<body>


<div class="container">
<div>
<h1>{{ $Report_Name  }}</h1>
</div>
<div>
{{ $Report_Description }}
</div>


<div class="row">
	<div class="col-xs-12">
	<table class="table table-bordered table-condensed table-striped table-hover" id="report_datatable" style="width:100%"></table>
	</div>
</div>
</div>

</body>
</html>

<style>
	.text-bold  { font-weight: bold; }
	.text-italic { font-style: italic; }
</style>

<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.12/af-2.1.2/b-1.2.2/b-colvis-1.2.2/b-html5-1.2.2/b-print-1.2.2/cr-1.3.2/fc-3.2.2/fh-3.1.2/kt-2.1.3/r-2.1.0/rr-1.1.2/sc-1.4.2/se-1.2.0/datatables.min.js"></script>

<script>

$(function() {

	var columnMap = [];
	$.getJSON('{{ $summary_url }}',
	{
	'token': '{{ $token }}',
	}).always(function(data) {




		var columnHeaders = []; /* for DataTables */
		var index = 0;



		data.columns.forEach(function(item) {

			/*
				Restructure the columnMap based on the field_name as the key
				This will make looking up the field meta data easier
			*/
			columnMap[item.field] = {
				index: index++,
				title: item.title,
				field: item.field,
				format: item.format,
				tags: item.tags
			};

			/*
				If Column has summary, push the summary to the columnMap
			*/
			if(item.hasOwnProperty('summary'))
			{
				columnMap[item.field]['summary'] = item.summary;
			}

			/*
				Create the header to be used by DataTable.
				Also add custom class based on the format and tags of the column
			*/
			var header_element = {
					data: item.field,  /* field it uses from the data */
					title: item.title, /* the title to display */
				};


			/*
				If the column is a numeric-type column
				or if the tag 'RIGHT' exists,
				automatically add the class text-right
			*/
			if(
				item.format=="NUMBER" || 
				item.format=="DECIMAL" || 
				item.format=="CURRENCY" || 
				item.format =="PERCENT"	||
				$.inArray("RIGHT",item.tags) >= 0
			)
				header_element['className'] = 'text-right';



			/*
				If the tag 'BOLD' exists, either append the className or set it 
				depending on if there is already an existing value
			*/
			if($.inArray("BOLD",item.tags) >= 0)
			{
				if(header_element.hasOwnProperty("className"))
					header_element['className']+=' text-bold';
				else
					header_element['className']='text-bold';
			}

			/*
				If the tag 'ITALIC' exists, either append the className or set it
				depending on if there is already an existing value
			*/
			if($.inArray("ITALIC",item.tags) >= 0)
			{
				if(header_element.hasOwnProperty("className"))
					header_element['className']+=' text-italic';
				else
					header_element['className']='text-italic';
			}

			/*
				If tag 'HIDDEN' exists, set the visible flag to false to hide the column
			*/
			if($.inArray("HIDDEN",item.tags) >= 0)
			{
				header_element['visible'] = false;
			}


			columnHeaders.push(header_element);	

		}); /* end forEach data.columns */






			var ReportTable = $('#report_datatable').DataTable( {

				/*
					Define the length, first array is 'visible' text, 
					and the 2nd array is what gets sent back to the server
				*/
				lengthMenu: [
									[50, 100, 1000], 
									[50, 100, 1000]
							  ],
				
				/*
					Disable the search delay, use the Enter Key to trigger a search
				*/
				searchDelay: 999999999,

				/*
					This is the header decoration, 
					we need to define the header fetching the data
				*/
				columns: columnHeaders,

				
				/*
					Override every ajax call to the server.
					Pass over the sort, filter, and what records to fetch
				*/
				ajax: function (data, callback, settings) {

					var columns = data.columns;
					var order = data.order;

					/*
						Support multi column ordering
					*/
					var callbackOrder = [];
					order.forEach(function(item) {
						var pair = {};
							pair[ columns[item.column].data ] = item.dir;
						callbackOrder.push(pair);
					});

					/*
						Fetch the data via getJSON and pass it back using the 'callback' provided by DataTable
					*/
					$.getJSON('{{ $api_url }}',
					{
					'data-option': '{{$input_bolt}}',
					'request-form-input': '{{$request_form_input}}',
					'token': '{{ $token }}',
					'page': (data.start / data.length) + 1,
					"order": callbackOrder,
					"length": data.length,
					"filter": data.search.value,
					}).always(function(data) {
						callback({
							data: data.data,
							recordsTotal: data.total,
							recordsFiltered: data.total,
						});
					});
				},


			/*
				Send all processing to server side
			*/
			serverSide: true,
			processing: true,
			paging: true,


			initComplete: function(settings, json) {

				/*
					Disable the auto search function on keypress.
					Only Search on enter key
				*/
				$('#report_datatable_filter input').unbind();
				$('#report_datatable_filter input').bind('keyup', function(e) {
					if(e.keyCode == 13) {
						ReportTable.search( this.value ).draw();
					}
				}); 

			},


			rowCallback: function( row, data, index ) {

				/*
					Map each row according to the format and tags					
				*/

				for(var field in columnMap)
				{
					var value = columnMap[field];
					var format = value['format'];
					var tags = value['tags'];
					var index = value['index'];

					if(format=="CURRENCY")
					{
						data[field] = "$ "+(data[field]*1).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
					}
					else if(format=="NUMBER")
					{
						data[field] = data[field].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
					}
					else if(format=="DECIMAL")
					{
						data[field] = (data[field]*1).toFixed(4);
					}
					else if(format=="PERCENT")
					{
						data[field] = (data[field]*100).toFixed(2) + " %";
					}
					else if(format=="URL")
					{
						data[field] = "<a href='"+data[field]+"'>"+data[field]+"</a>";
					}
					else if(format=="DATE")
					{

					}
					else if(format=="DATETIME")
					{

					}
					else if(format=="TIME")
					{

					}
					
					$("td:eq("+index+")",row).html(data[field]);
				}
				
			} /* end rowCallback */


		} ); /* end DataTable */

	}); /* end always on get Summary */


});
</script>