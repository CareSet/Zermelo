<div id="app">
	<div class="container-fluid">
		<div>
			<h1> {{ $report->getReportName()  }}</h1>
		</div>
		<div>
			{!! $report->getReportDescription() !!}
		</div>

		<div id="user-variables" style="display:none">
			<input type="hidden" id="clear_cache" value=""/>
			<input type="hidden" id="cache_expires" value=""/>
		</div>

		<div style='display: none' id='json_error_message' class="alert alert-danger" role="alert">

		</div>

		<table class="display table table-bordered table-condensed table-striped table-hover" id="report_datatable" style="width:100%;"></table>


		<footer class="{{ $report->GetReportFooterClass() }}">
			{!! $report->GetReportFooter() !!}
		</footer>

	</div>

	<div id="bottom_locator" style="
		position: fixed;
		bottom: 10px;
	"></div>


	<!-- Data View Modal -->
	<div class="modal fade" id="current_data_view" tabindex="-1" role="dialog" aria-labelledby="current_data_view" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<form id="sockets-form">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Data Options</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						@if ($report->hasActiveWrenches())
						<div class="row">
							<div class="col-5">
								<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
									@foreach ($report->getActiveWrenches() as $wrench)
										<a class="nav-link {{ ($loop->first) ?  'active' : '' }}" id="v-pills-{{$wrench->id}}-tab" data-toggle="pill" href="#v-pills-{{$wrench->id}}" role="tab" aria-controls="v-pills-{{$wrench->id}}" aria-selected="true">{{ $wrench->wrench_label }}</a>
									@endforeach
								</div>
							</div>
							<div class="col-7">
								<div class="tab-content" id="v4-pills-tabContent">
									@foreach ($report->getActiveWrenches() as $wrench )
										<div class="tab-pane fade show {{ ($loop->first) ?  'active' : '' }}" id="v-pills-{{$wrench->id}}" role="tabpanel" aria-labelledby="v-pills-{{$wrench->id}}-tab">
											@foreach ( $wrench->sockets as $socket )
												<div class="custom-control custom-radio">
													<input {{ $report->isActiveSocket($socket->id) ? 'checked' : '' }} type="radio" data-wrench-id="{{$wrench->id}}" data-socket-id="{{$socket->id}}" id="wrench-{{$wrench->id}}-socket-{{$socket->id}}" name="sockets[{{$wrench->id}}]" value="{{$socket->id}}" data-wrench-label="{{ $wrench->wrench_label }}" data-socket-label="{{$socket->socket_label}}" class="socket custom-control-input">
													<label class="custom-control-label" for="wrench-{{$wrench->id}}-socket-{{$socket->id}}">{{$socket->socket_label}}</label>
												</div>
											@endforeach
										</div>
									@endforeach
								</div>
							</div>
						</div>
						@else
						<!-- we only get here if there are no active wrenches -->
						<div class="row">
							<div class='col-12'>
								No Data Options have been configured for this report
							</div>
						</div>
						@endif

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="button" id="save-sockets" class="btn btn-primary">Save changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Report Download Modal -->
	<div class="modal fade" id="report_download_modal" tabindex="-1" role="dialog" aria-labelledby="report_download_modal" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<form id="report-download-form" method="POST" action="{!! $download_uri !!}">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Download Options</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="card">
							<div class="card-body">
								<h5 class="card-title">Data View Download Options</h5>
								<table class="table table-striped table-bordered" id="download-data-options-table">
								</table>
							</div>
						</div>
						<br>
						<div class="card">
							<div class="card-body">
								<h5 class="card-title">Search Filter Download Options</h5>
								<table class="table table-striped table-bordered" id="download-search-filter-options-table">
								</table>
							</div>
						</div>
						<br>
						<div class="card">
							<div class="card-body">
								<h5 class="card-title">URL Parameter Download Options</h5>
								<table class="table table-striped table-bordered" id="download-url-params-options-table">
								</table>
							</div>
						</div>
						<br>

						<div class="btn-group" style="display:flex;">
							<input style="font-size:60%; width:100%" type="text" id="current-download-link" value=""/>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="button" id="initiate-report-download" class="btn btn-primary">Download</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="/vendor/CareSet/zermelo/core/js/jquery.min.js"></script>
<script type="text/javascript" src="/vendor/CareSet/zermelo/zermelobladetabular/datatables/datatables.js"></script>
<script type="text/javascript" src="/vendor/CareSet/zermelo/core/bootstrap/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="/vendor/CareSet/zermelo/core/js/moment.min.js"></script>
<script type="text/javascript" src="/vendor/CareSet/zermelo/core/js/daterangepicker.js"></script>
<script type="text/javascript" src="/vendor/CareSet/zermelo/core/js/jquery.doubleScroll.js"></script>
<script type="text/javascript" src="/vendor/CareSet/zermelo/zermelobladetabular/js/jquery.dataTables.yadcf.js"></script>
<script type="text/javascript" src="/vendor/CareSet/zermelo/core/js/zermelo.js"></script>

<script type="text/javascript">

    $(document).ready(function() {

        var zermelo = new Zermelo(
            '{{ $report_uri }}', // Pass the required Report Base URI
            '{{ $download_uri }}', // Pass the required Download URI
            {
                // Optional parameters

				// Get the JWT Token
                token: '{{ $report->getToken() }}',

				// These are parameters passed to us from the server
				passthrough_params: {!! $report->getRequestFormInput( true ) !!}
            }
        );

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Our dataTable instance
        var ReportTable = null;

        // Object that represents options passed to datables, this is saved in it's own variable should
		// we have to re-instansiate the table, as in the case of fixed columns
		var dataTableOptions = {};

		// Stores our column header and meta data
        var columnMap = [];

        // How many columns to the left should be "anchored" when scrolling horizontally
        var fixedColumns = null;

        // Socket API payload
        var sockets = {};
        var activeWrenchNames = [];

        // Set up the default sort Order if there is one.
		var defaultSortOrder = [];

        // Refresh sockets on page reload, in case we had options set, and did a "refresh"
        refresh_sockets();

        function refresh_sockets() {

            // Get the socket inputs by selecting from socket form, using socket class
            let form_data = $("#sockets-form .socket").serializeArray();

            // Empty sockets array before we refill it
            sockets = {};

            // The active wrnch names are used for download optons to display the data options that are in-use
            activeWrenchNames = [];

            jQuery.each( form_data, function( i, field ) {

                // name attribute of input contains wrench id
                let name = field.name;

                // socket id is in value attribute
                let socketId = field.value;

                // Wrench ID is in brackets, need to parse out
                let wrenchId = name.slice(name.indexOf('[') +1,name.indexOf(']'));

                // Store the wrenches/sockets in the same format as they would be submitted by form
                sockets[wrenchId]= socketId;

                // Build the id, which contains both wrench id and socket id
                let id = "wrench-"+wrenchId+"-socket-"+socketId;

                // Now store the labels if we need to display active data options
                let wrenchLabel = $('#'+id).attr('data-wrench-label');
                let socketLabel = $('#'+id).attr('data-socket-label');
                activeWrenchNames.push({
                    wrenchLabel: wrenchLabel,
                    socketLabel: socketLabel
                });
            });

            // Tell the api (for download)
            zermelo.setSockets(sockets);
		}

		function setup_download_modal_for_display() {
			// Add currrently selected options to download form
			refresh_sockets();

			if ( sockets.length == 0 ) {

				// There are no data view options, tell user
				$("#download-data-options-table").html("<tr><td colspan='2'>No data view options in use</td><tr>");

			} else {

				// Now dynamically pupulate the download options table with our current sockets
				$("#download-data-options-table").html("");
				$.each(activeWrenchNames, function (key, option) {
					$("#download-data-options-table").append("<tr><td>" + option.wrenchLabel + "</td><td>" + option.socketLabel + "</td></tr>");
				});
			}

			// Dynamically populate the search filter options table with our active search filters
			$("#download-search-filter-options-table").html("");
			if ( zermelo.getSearchFilters().length > 0 ) {
				$.each(zermelo.getSearchFilters(), function (key, option) {
					for (var i in option) {
						$("#download-search-filter-options-table").append("<tr><td>" + i + "</td><td>" + option[i] + "</td></tr>");
					}
				});
			} else {
				$("#download-search-filter-options-table").html("<tr><td colspan='2'>No table filters in use</td><tr>");
			}

			// Dynamically populate the URL Parameter options table with our active parameters from the address bar
			$("#download-url-params-options-table").html("");
			if ( Object.keys(zermelo.getUrlSearchParams()).length > 0 ) {
				$.each(zermelo.getUrlSearchParams(), function (key, option) {
					for (var i in option) {
						$("#download-url-params-options-table").append("<tr><td>" + i + "</td><td>" + option[i] + "</td></tr>");
					}
				});
			} else {
				$("#download-url-params-options-table").html("<tr><td colspan='2'>No URL parameters in use</td><tr>");
			}
		}

        $("#save-sockets").click( function(e) {
            // Get the sockets from the Data Options form
			refresh_sockets();
            $('#current_data_view').modal('toggle');
            $("#report_datatable").DataTable().ajax.reload();
        });

        // On the report download modal, click "download" event
        $("#initiate-report-download").click( function(e) {
            e.preventDefault();
            e.stopPropagation();
			let form = $("#report-download-form");
			// Clear the hidden data view options
			$("#data-view-options-form-download").empty();

			// Execute the download based on current options
			zermelo.serverDownloadRequest().done( function() {
                $('#report_download_modal').modal('toggle');
            });
        });

        function set_cache_timer()
		{
            setInterval(function(){
                // Check to see if cache is about to expire (<1 minute)
				var cacheExpires = Date.parse( $("#cache_expires").val() );
				var now = Date.now();
				if ( ( cacheExpires - now ) > 0 &&
					( cacheExpires - now ) < 10000 ) {
				    if ( $("#cache-icon").hasClass( "text-warning" ) ) {
                        $("#cache-icon").removeClass("text-danger");
                        $("#cache-icon").removeClass("text-warning");
                        $("#cache-icon").addClass("text-primary");
					} else {
                        $("#cache-icon").removeClass("text-primary");
                        $("#cache-icon").removeClass("text-danger");
                        $("#cache-icon").addClass("text-warning");
					}

				} else if ( ( now - cacheExpires ) > 0 ) {
                    $("#cache-icon").removeClass("text-primary");
                    $("#cache-icon").removeClass("text-danger");
                    $("#cache-icon").addClass("text-warning");
				}
            }, 1000 );
		}

        set_cache_timer();
        var api_params = zermelo.getAllApiParams();

        // This is the summary API call that will get the column headers
		// If this call succeeds, we call the server to get the data
        $.getJSON(
            '{{ $summary_uri }}',
			api_params
		).fail(function( jqxhr, textStatus, error) {

            console.log(jqxhr);
            console.log(textStatus);
            console.log(error);

            var is_admin = true; //this should be set via a variable on the report

            if(is_admin){
                if(typeof jqxhr.responseJSON.message !== 'undefined'){
                    $('#json_error_message').html("<h1> You had a error </h1> <p> " + jqxhr.responseJSON.message + "</p>");
                }else{
                    $('#json_error_message').html("<h1> You had a error, bad enough that there was no JSON  </h1>");
                }
            }else{
                $('#json_error_message').html("<h1> There was an error generating this report</h1>");
            }
            $('#json_error_message').show();

	    }).done(function(data) {

            /**
			 * The only way to reset the number of fixed columns is to destroy and re-initiailize the table,
			 * This method does that with a value of how many columns to anchor. If it's greater than Zero,
			 * We use the value to initialize the number of columns fixed to the left
			 *
			 * TODO This is not used, but left here for future development, and the button that invokes
			 * this function is commented out.
			 * */
            function refreshFixedColumns(value)
			{
                if (fixedColumns !== null) {
					// We have to destroy the datatable and recreate it if we are changing the fixed-columns
                    ReportTable.destroy();
                    fixedColumns = null;

                    // Reinitialize the table
                    ReportTable = $('#report_datatable').DataTable( dataTableOptions );

                    ReportTable.on( 'column-reorder', function () {
                        if(fixedColumns !== null) {
                            $("#report_datatable").dataTable().api().fixedColumns().update();
                        }
                    } );

                    yadcf.init(ReportTable,filter_array);

                }

                if (value > 0 ) {
                    fixedColumns = new $.fn.dataTable.FixedColumns( ReportTable, {
                        leftColumns: value
                    });
                }
			}

            var formatHeader = function (header_data, columnIdx) {
                var jHtmlObject = jQuery('<p>' + header_data + '</p>');
                var parent = jQuery("<p>").append(jHtmlObject);
                parent.find(".yadcf-filter ").remove();
                var newHtml = parent.text();
                return newHtml;
            };

            var buttons = [
				@if ($report->hasActiveWrenches()) // Only show the Data Options button if there are active wrenches on this report
				{
					name: 'dataview',
					text: 'Data Options',
					className: 'text-icon',
					action: function(e,dt,node,config) {
						$('#current_data_view').modal('toggle');
					}
				},
				@endif
                {
                    extend: 'colvis',
                    text: '&nbsp;<span class="fa fa-columns"></span>&nbsp;',
                    titleAttr: 'Column Visibility',
					init: function ( dt, node, config ) { $(node).tooltip(); }
                },
				/*
                TODO removing the Fixed-Colum icon for now, which fixes a colum, or columns to the left of the table
                so the user can scroll off the screen to columns on the right, and keep the left-most columns in view.
                This mostly works, but there is an issue with column filtering interacting with the fixed-column behavior.
            	See: https://github.com/CareSet/Zermelo/issues/56
            	*/
				/*
                {
                    extend: 'collection',
					name: 'fixedcols',
					attr: {
                        id: 'report_table_freeze_selector'
					},
                    autoClose: true,
                    text: '&nbsp;<span id="fixedcols-icon" class="fa fa-anchor"></span>&nbsp;',
                    titleAttr: 'Number of columns to anchor to the left',
                    className: 'fixedcols',
                    init: function ( dt, node, config ) { $(node).tooltip(); },
                    buttons : [
                        {
                            text: 'None',
                            action: function ( e, dt, node, config ) {
								refreshFixedColumns(0);
                            }
                        },
                        {
                            text: '1',
                            action: function ( e, dt, node, config ) {
                                refreshFixedColumns(1);
                            }
                        },
                        {
                            text: '2',
                            action: function ( e, dt, node, config ) {
                                refreshFixedColumns(2);
                            }
                        },
                        {
                            text: '3',
                            action: function ( e, dt, node, config ) {
                                refreshFixedColumns(3);
                            }
                        },
                        {
                            text: '4',
                            action: function ( e, dt, node, config ) {
                                refreshFixedColumns(4);
                            }
                        },
                    ]
                },*/
                {
                    name: 'Expand',
                    text: '&nbsp;<span class="fa fa-expand"></span>&nbsp;',
                    titleAttr: 'Maximize View',
                    init: function ( dt, node, config ) { $(node).tooltip(); },
                    action: function(e,dt,node,config) {
                        $(".report-table-wrapper").toggleClass('full_screen');
                        $(node).toggleClass('toggled');
                    }
                },
                {
					extend: 'collection',
					name: 'downloads',
					attr: {
						id: 'download-button'
					},
					autoClose: true,
                    text: '&nbsp;<span class="fa fa-download"></span>&nbsp;',
					className: 'download-info',
					init: function (dt, node, config) {},
					buttons : [{
						extend: 'csv',
						text: '&nbsp;<span class="fa fa-file-csv"></span> Download CSV&nbsp;',
                    titleAttr: 'Download CSV',
                    init: function ( dt, node, config ) { $(node).tooltip(); },
					action: function(e,dt,node,config) {

                        // Set up the display for the download options before we show the modal
							setup_download_modal_for_display();

							// Add the fully built download URI to the modal (this can be used to link to the download)
							zermelo.setDownloadFileType('csv');
							var downloadURI = zermelo.getDownloadURI();
							$("#current-download-link").val(downloadURI);

							$('#report_download_modal').modal('toggle');
						}
					}, {
							extend: 'csv',
							text: '&nbsp;<span class="fa fa-file-excel"></span> Download Excel&nbsp;',
							titleAttr: 'Download Excel',
							init: function ( dt, node, config ) { $(node).tooltip(); },
							action: function(e,dt,node,config) {

								// Set up the display for the download options before we show the modal
								setup_download_modal_for_display();

                        // Add the fully built download URI to the modal (this can be used to link to the download)
								zermelo.setDownloadFileType('excel')
                        var downloadURI = zermelo.getDownloadURI();
                        $("#current-download-link").val(downloadURI);

                        $('#report_download_modal').modal('toggle');
					}
						}]
                },
                {
                    extend: 'print',
                    text: '&nbsp;<span class="fa fa-print"></span>&nbsp;',
                    title:  function() {
                        var title = window.document.title;
                        return title;
                    },
                    titleAttr: 'Print',
                    init: function ( dt, node, config ) { $(node).tooltip(); },
					exportOptions: {
                        stripHtml: false // Do not strip HTML so font-awesome icons and other html are printed (#31)
					}
                },
                {
                    extend: 'collection',
                    name: 'cache',
                    attr: {
                        id: 'cache-meta-button'
                    },
                    autoClose: true,
                    text: '&nbsp;<span id="cache-icon" class="fa fa-database"></span>&nbsp;',
                    className: 'cache-meta-info',
                    init: function (dt, node, config) {
                        var cache_enabled = data.cache_meta_cache_enabled;
                        var generated_this_request = data.cache_meta_generated_this_request;
                        if ( cache_enabled ) {
                            if ( !generated_this_request ) {
                                $(node).find(".fa-database").addClass("text-danger");
                            } else {
                                $(node).find(".fa-database").addClass("text-primary");
                            }
                        }
                        var info = "Last Generated: " + data.cache_meta_last_generated+"<br/>";
                        info += "Expires: " + data.cache_meta_expire_time;

                        $("#cache_expires").val( data.cache_meta_expire_time );

                        $(node).tooltip({
							html: true,
                            title: info,
                            template: '<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner large"></div></div>'
                        });
                    },
					buttons : [
                        {
                            text: 'Clear Cache',
                            action: function ( e, dt, node, config ) {

                                $("#clear_cache").val( true );
                                $("#report_datatable").DataTable().ajax.reload( function ( json ) {
                                    var cache_enabled = json.cache_meta_cache_enabled;
                                    var generated_this_request = json.cache_meta_generated_this_request;
                                    if ( cache_enabled ) {
                                        if ( !generated_this_request ) {
                                            $("#cache-icon").removeClass("text-primary");
                                            $("#cache-icon").addClass("text-danger");
                                        } else {
                                            $("#cache-icon").removeClass("text-danger");
                                            $("#cache-icon").addClass("text-primary");
                                        }
                                    }
                                    var info = "Last Generated: " + json.cache_meta_last_generated+"<br/>";
                                    info += "Expires: " + json.cache_meta_expire_time;

                                    $("#cache_expires").val( json.cache_meta_expire_time );
                                    $("#cache-meta-button").attr("data-original-title", info);
                                    $("#clear_cache").val( "" );
								});

                            }
                        },
						{
							text: 'Clear Local Storage',
							action: function ( e, dt, node, config ) {
								localStorage.clear();
							}
						}
					]
                }
            ]; // End of buttons array

            var columnHeaders = []; /* for DataTables */
            var index = 0;

            var filter_array = [];
            data.columns.forEach(function(item) {

                /*
                    This is for yadcf column filter
                */
                filter_array.push({
                    column_number: index,
                    filter_type: "text",
                    filter_default_label: "Refine",
                    filter_reset_button_text: false,
                    filter_delay: 500
                });


                /*
                    Restructure the columnMap based on the field_name as the key
                    This will make looking up the field meta data easier
                */
                columnMap["_"+item.field] = {
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
                    columnMap["_"+item.field]['summary'] = item.summary;
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

			// We have to convert the column-name : direction format into column-index : direction format
			var defaultSortOrderParam = zermelo.getPassthroughParam('order');
			for (var sortOrderColumn in defaultSortOrderParam) {
				var obj = defaultSortOrderParam[sortOrderColumn];
				let column = '';
				let direction = '';
				for ([column, direction] of Object.entries(obj)) {}
				var columnMapKey = "_"+`${column}`;
				var orderItem = [];
				orderItem.push(columnMap[columnMapKey].index);
				orderItem.push(direction);
				defaultSortOrder.push(orderItem);
			}

            var defaultPageLength = localStorage.getItem("Zermelo_defaultPageLength");
            if ( defaultPageLength == "undefined" ) {
                defaultPageLength = '{{ $page_length }}'; // This is a string, but we do parseInt later
            }

            // If we have search filters applied, let the user know, and give the user
			// an oppertunity to clear them when the results are empty
            var emptyTableString = "<span id='emptyTableString'>No data available in table</span>";

            var detailRows = [];
            dataTableOptions = {

                dom: '<"report-table-wrapper"<"table-control"<"float-left control-box"Blf<"after-menu-addition">><"float-right"ip><"clearfix"><"#report_menu_wrapper">>tr>',

                /*
                 * Save state in local storage, including the
                 * current data filters and sort order
                 */
                stateSave: true,
                colReorder: true,
                scrollX: true,
                scrollY: '100%',
				scrollCollapse: true,
				paging: true,

				/*
				 * Set the default sort order based on configuration
				 */
				order: defaultSortOrder,

				/*
					Send all processing to server side
				*/
				serverSide: true,
				processing: true,

                /*
                    Define the length, first array is 'visible' text,
                    and the 2nd array is what gets sent back to the server
                */
                lengthMenu: [50,100,500,1000],
                pageLength: parseInt( defaultPageLength ),

                buttons: buttons,

                /*
                    Disable the search delay, use the Enter Key to trigger a search
                */
                searchDelay: 500,

                /*
                    This is the header decoration,
                    we need to define the header fetching the data
                */
                columns: columnHeaders,

                language: {
                    "emptyTable": emptyTableString,
                    "processing": '<div class="loader"></div>'
                },

                /*
                    Override every ajax call to the server.
                    Pass over the sort, filter, and what records to fetch
                */
                ajax: function (data, callback, settings) {

                    var columns = data.columns;
                    var order = data.order;

                    // Clear the search filters before we apply the currently set filters
                    zermelo.clearSearchFilters();

                    // Tell our zermelo state about each column filter
                    columns.forEach(function(item) {
                        if ( item.search.value != "" ) {
                            zermelo.pushColumnSearchFilter( item.data, item.search.value );
                        }
                    });

                    // Tell our zermelo state about the global filter
                    if( data.search.value != "" ) {
                        zermelo.pushGlobalSearchFilter( data.search.value );
                    }

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
                    var page = 1;
                    var length = defaultPageLength;
                    if ( data.length != "undefined" ) {
                        page = (data.start / data.length) + 1;
                        length = data.length;
                    }

                    // Get the parameters passed in via URI
                    var passthrough_params = zermelo.getPassthroughParams();

                    // Set up the ajax API parameters
                    var merge_get_params = {
                        'token': '{{ $report->getToken() }}',
                        'page': parseInt(page),
                        "order": callbackOrder,
                        "length": parseInt(length),
                        "filter": zermelo.getSearchFilters(),
                        "clear_cache": $("#clear_cache").val() ,
                        "sockets": sockets // Pass sockets for "Data Options"
                    };

                    // Merge the URI Parameters with the ajax parameters
                    var merge = $.extend({}, passthrough_params, merge_get_params);

                    localStorage.setItem("Zermelo_defaultPageLength",length);
                    $("[name='report_datatable_length']").val(length);

                    var merge_clone = $.extend({},merge);
                    delete merge_clone['token'];

                    var param = decodeURIComponent( $.param(merge) );

                    // This is the AJAX call to the zermelo API to get the data for datatables
                    $.getJSON('{{ $report_uri }}', param
                    ).always(function(data) {
                        settings.json = data; // Make sure to set setting so callbacks have data
                        callback({
                            data: data.data,
                            recordsTotal: data.total,
                            recordsFiltered: data.total,
                        });

                        // Update the cache UI
                        $("#clear_cache").val("");

                        var cache_enabled = data.cache_meta_cache_enabled;
                        var generated_this_request = data.cache_meta_generated_this_request;
                        if ( cache_enabled ) {
                            if ( !generated_this_request ) {
                                $("#cache-icon").removeClass("text-primary");
                                $("#cache-icon").removeClass("text-warning");
                                $("#cache-icon").addClass("text-danger");
                            } else {
                                $("#cache-icon").removeClass("text-danger");
                                $("#cache-icon").removeClass("text-warning");
                                $("#cache-icon").addClass("text-primary");
                            }
                        }
                        var info = "Last Generated: " + data.cache_meta_last_generated+"<br/>";
                        info += "Expires: " + data.cache_meta_expire_time;

                        $("#cache_expires").val( data.cache_meta_expire_time );
                        $("#cache-meta-button").attr("data-original-title", info);

                        // If we have zero results, and we have search filters applied, let the user know
                        // This text will replace the default "No data available in table"
                        var search_filters = zermelo.getSearchFilters();
                        if (search_filters.length > 0) {
                            var emptyTableString = "<p>No data available in table, possibly because you have the following search filters applied:</p>";
                            $.each(zermelo.getSearchFilters(), function (key, option) {
                                var name =  '';
                                var value = '';
                                for (var i in option) {
                                    name = i;
                                    value = option[i];
                                }
                                emptyTableString += "<p>" + name + "=>" + value + "</p>";
                            });
                            emptyTableString += "<p>Click to clear all filters and reload table</p><p><button class='btn btn-primary clear-all-search-filters' href='#'>Clear Filters</button></p>";
                            $("#emptyTableString").html(emptyTableString);
                        }
                    });
                },

                initComplete: function(settings, json) {
                    // Place initialization code here
                },

                rowCallback: function( row, data, index ) {

                    /*
                        Map each row according to the format and tags
                    */
                    var col_index = 0;

                    var order_reverse = $("#report_datatable").DataTable().colReorder.order();
                    var order=[];
                    $.each(order_reverse, function(i, el) {
                        order[el]=i;
                    });

                    for(var field in columnMap)
                    {
                        var value = columnMap[field];
                        field = field.toString().substring(1); /* strip out the _ */
                        var format = value['format'];
                        var tags = value['tags'];
                        var index = value['index'];


                        if(ReportTable.column(index).visible())
                        {
                            var new_index = order[col_index];
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

                            } else if(format=="DETAIL")
                            {
                                $("td:eq("+new_index+")",row).addClass('details-control').attr('detail-field',field);
                                $("td:eq("+new_index+")",row).html('').attr('title',data[field]);
                                data[field]=$("<span class='hide'></span>").html(data[field])[0].outerHTML;
                            }
                            if(format!="DETAIL")
                            {
                                $("td:eq("+new_index+")",row).html(data[field]);
                            }
                            col_index++;
                        }
                    }

                } /* end rowCallback */


            } // End of dataTable Options


			// Initialize our dataTable with the "original" options
            ReportTable = $('#report_datatable').DataTable( dataTableOptions );

            ReportTable.on( 'column-reorder', function () {
                if(fixedColumns !== null) {
                    $("#report_datatable").dataTable().api().fixedColumns().update();
                }
            });

            yadcf.init(ReportTable,filter_array);

            // This is the button that appears when there are no results, likely because
			// of column filters applied. We have to put scope on "body" because of the way the button is generated
            $("body").on("click", ".clear-all-search-filters", function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Use the yadcf API to reset all filters
                yadcf.exResetAllFilters(ReportTable);
            });


            $('#report_datatable tbody').on( 'click', 'tr td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = ReportTable.row( tr );
                var idx = $.inArray( tr.attr('id'), detailRows );

                if ( row.child.isShown() ) {
                    tr.removeClass( 'details' );
                    row.child.hide();

                    // Remove from the 'open' array
                    detailRows.splice( idx, 1 );
                }
                else if($(this)[0].hasAttribute('detail-field')) {
                    tr.addClass( 'details' );

                    var field_name = $(this).attr('detail-field');
                    var row_data = row.data();

                    var padder = $("<div></div>").html( $(row_data[field_name]).html() ).addClass('row_detail');
                    row.child( padder ).show();

                    // Add to the 'open' array
                    if ( idx === -1 ) {
                        detailRows.push( tr.attr('id') );
                    }
                }
            });
        }); /* end always on get Summary */
    });
</script>
<script type="text/javascript">
	{!! $report->GetReportJS() !!}
</script>
