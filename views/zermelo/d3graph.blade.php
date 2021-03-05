
<!-- Data View Modal -->
<div class="modal fade" id="current_data_view" tabindex="-1" role="dialog" aria-labelledby="current_data_view" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="sockets-form" method="post">
                {{ csrf_field() }}
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
</div> <!-- end of data view options modal -->
		  <div class="container-fluid">
	  <div class="row">
      <div class="col-md-2">
        <div class="card" style="width: 98%;" >
          <div class="card-body" style="width: 98%; padding: .25rem;">
{{ $report->getReportName() }}
            <hr>

@if ($report->hasActiveWrenches())
  <button type="button" class="btn btn-primary btn-block view-data-options"> Data Options </button>
@endif
              
<br>
<div class="accordion" id="menuAccordion">
  <div class="card">
    <div class="card-header" id="headingTwo">
      <h2 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
	Layout Options
        </button>
      </h2>
    </div>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#menuAccordion">
      <div class="card-body">

    Gravity:<br>
              <input id="gravitySlider" type="range" onchange="updateForce();" min="0.01" max="0.37" step="0.001" value="0.04">
              <input type="hidden" name='gravityInput'  id="gravityInput" value="0.04"><br>
    Charge:<br>
              <input id="chargeSlider" type="range" onchange="updateForce();" min="-25000" max="0" step="10" value="-3000">
              <input type="hidden" name='chargeInput'  id="chargeInput" value="-3000"><br>
    Zoom:<br>
              <input id="zoomSlider" type="range" onchange="updateForce();" min="0.5" max="5" step="0.05" value="1.7">
              <input type="hidden" name='zoomInput'  id="zoomInput" value="1.7">
              <br/>
    <input type="checkbox" name="hideLonelyNode" id="hideLonelyNode"  onchange="updateForce();" value="1"> Hide Lonely Node <br/><br/>




<button class="btn btn-xs btn-primary" type="button" data-toggle="collapse" data-target="#advancedControls" aria-expanded="false" aria-controls="collapseExample">
  Show Advanced Controls
</button>

    <div class='collapse' id='advancedControls'>
    <div style='display: none'>
                linkDistance<br>
              <input id="linkDistanceSlider" type="range" onchange="updateForce();" min="1" max="200" step="1" value="30">
              <input type="hidden" name='linkDistanceInput' id="linkDistanceInput" value="30"><br>
    </div>

                linkStrength<br>
              <input id="linkStrengthSlider" type="range" onchange="updateForce();" min="0.001" max="1" step=".01" value="0.55">
              <input type="hidden" name='linkStrengthInput' id="linkStrengthInput" value="0.55"><br>
    Friction<br>
              <input id="frictionSlider" type="range" onchange="updateForce();" min="0.01" max="1" step=".05" value="0.7">
              <input type="hidden" name='frictionInput' id="frictionInput" value="0.7"><br>
    Charge Distance<br>
              <input id="chargeDistanceSlider" type="range" onchange="updateForce();" min="1" max="1000" step="1" value="300">
              <input type="hidden" name='chargeDistanceInput'  id="chargeDistanceInput" value="300"><br>
    Theta<br>
              <input id="thetaSlider" type="range" onchange="updateForce();" min="0.01" max="1" step=".05" value="0.35">
              <input type="hidden" name='thetaInput'  id="thetaInput" value="0.35"><br>
    Alpha<br>
              <input id="alphaSlider" type="range" onchange="updateForce();" min="0.01" max="2" step=".1" value="0.5">
              <input type="hidden"  name='alphaInput' id="alphaInput" value="0.5"><br>
    </div>
        <div class="checkbox">
    <label>
<!--
      <input id='reset_parameters' name='reset_parameters' value='off' type="checkbox"> Reset Parameters
-->
    </label>
  </div>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="headingThree">
      <h2 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
	Animation Options
        </button>
      </h2>
    </div>
    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#menuAccordion">
      <div class="card-body">

<!-- Animation Options Form -->
<div class="btn-group-vertical btn-group-toogle" data-toggle="buttons">
  <label class="btn btn-primary" for='option_animate' style='margin-bottom: 0px'
  onClick="true_unfreeze();" >
    	<input type="radio" name="options" id="option_animate" autocomplete="off" style='display: none;'>
		<i class="fas fa-play"></i> Animate Graph
  </label>
  <label class="btn btn-primary" for='option_freeze' style='margin-bottom: 0px'
  onClick="force.stop();" >
    <input type="radio" name="options" id="option_freeze" autocomplete="off" style='display: none;'>
                <i class="fas fa-pause"></i> Freeze Graph
  </label>

  <label class="btn btn-primary" for='option_unfix' style='margin-bottom: 0px'
  onClick="free_nodes();" >
    <input type="radio" name="options" id="option_unfix" autocomplete="off" style='display: none;'>
                <i class="fas fa-lock-open"></i> Unfix All Nodes
  </label>

  <label class="btn btn-primary" for='option_center' style='margin-bottom: 0px'
  onClick="console.log('trying center'); gravityMode = 'centered'; force_settle('3000','gravity center');"
>
    <input type="radio" name="options" id="option_center" autocomplete="off"  style='display: none;'>
    		<i class="fas fa-align-center"></i> Center Gravity
  </label>
  <label class="btn btn-primary" for='option_right' style='margin-bottom: 0px'
  onClick="console.log('trying right'); gravityMode = 'right'; force_settle('3000','gravity right');"
>
    <input type="radio" name="options" id="option_right" autocomplete="off" style='display: none;'>
    		<i class="fas fa-align-right"></i> Right Gravity
  </label>
  <label class="btn btn-primary" for='option_left' style='margin-bottom: 0px'
  onClick="console.log('trying left'); gravityMode = 'left'; force_settle('3000','gravity left');"
>
    <input type="radio" name="options" id="option_left" autocomplete="off" style='display: none;'>
    		<i class="fas fa-align-left"></i> Left Gravity
  </label>
  <label class="btn btn-primary" for='option_group' style='margin-bottom: 0px'
  onClick="console.log('trying group'); gravityMode = 'group'; force_settle('3000','gravity group');"
>
    <input type="radio" name="options" id="option_group" autocomplete="off" style='display: none;'>
      		<i class="fas fa-expand-arrows-alt"></i> Group Gravity
  </label>
<!-- Animation Options Form -->

      </div>
    </div>
  </div>
</div>

<hr>
<a class='btn btn-primary btn-block' id='down_svg_link' onclick='downloadSVG();'>Download Image</a>
<!-- TODO consider reimplementing ? -->
<!--
<hr>
        	<h5> Admin Only Menu </h5>
                <span><small>
		<ul>
		<li><a target='_blank' id='get_debug_link' href="">json url</a></li>
		<li><a target='_blank' id='debug_link' href="{{$graph_uri}}">json contents (includes JSON POST results)</a></li>
		</ul>
                </small></span>
-->
<br>
<hr>

<div id="node_left_panel">
<!-- this gets replaced by the literal panel templates when a node is clicked -->
</div>
<div id="node_admin_left_panel">
<!-- this gets replaced by the literal panel templates when a node is clicked -->
</div>
<div id='node_menu_left_panel'>
<!-- this gets replaced by a different literal  panel -->
</div>

  </div> <!-- end left panel -->
</div>

</div>
<!-- resuming the bootstrap parts -->

</div> <!-- end left side main card div -->

<div class='col-md-10'>
<div class='row'>
	<div class='col-md-6'>
        			<ol class="breadcrumb">

						  						<li class='breadcrumb-item'>{{ $report->getReportName() }}</li>
						  						<li class="breadcrumb-item active">{{ $report->getReportDescription() }}</li>
          			</ol>
<!--
  <div class="form-group float-righ">
					    	<input class="form-control" id="nodeTextMatch" name="nodeTextMatch" type="text" />
			</div></form>

    <label for="inputEmail3" class="col-sm-2 col-form-label">Button</label>
-->
	</div>
	<div class='col-md-6'>
<!-- the graph search form -->
<form onsubmit="event.preventDefault(); return false;">
  <div class="float-left form-group row">
    <div class="col-sm-6">
      <input type="text" class="form-control" id="nodeTextMatch" name='nodeTextMatch'  placeholder="search the graph">
    </div>
    						<button class='btn btn-primary btn-xs' onclick="searchNodeText();">search</button>
&nbsp;
    						<button class='btn btn-primary btn-xs' onclick="restoreSearch();">restore graph</button>
  </div>
</form>
</div> <!-- ending the row -->

<div id='right_pane' class='col-md-12'>
	<div id='loading_div' class="row">
<div id='inner_loading'>
  <div class="col-md-3"></div>
  <div class="col-md-7">
    <h1>
	<i class="fas fa-circle-notch fa-spin"></i>
      Loading your results
    </h1>
<h3> Please be patient. This is harder than it looks ;)</h3>
  </div>
  <div class="col-md-2"></div>
</div>
<div id='inner_error' style='display: none'>
  <div class="col-md-3"></div>
  <div class="col-md-7">
    <h1>
      <i class='fa fa-bug fa-3x'></i>
      Data loading error
    </h1>
<h3> 	There was a problem loading your data, please report this bug to
	<a href='mailto:support@careset.com?subject=Data+load+error&body=Trouble%20loading%20data%20at%20cube.careset.com%20%2FFORCE%2Fdandelion_simple%2F1114904687'
	>
	support@careset.com</a></h3>
  </div>
  <div class="col-md-2"></div>
</div>
</div>
		<!-- this is where the svg is going to live in the end -->

		<div style='height: 1000px; display: none;' id='viz'>
		</div>
		<script>

            // Data view modal and sockets control
            $(".view-data-options").click(function(){
                $('#current_data_view').modal('toggle');
            });

            // Socket API payload
            var sockets = {};
            var activeWrenchNames = [];

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
            }

            $("#save-sockets").click( function(e) {
                // Get the sockets from the Data Options form
                refresh_sockets();
                $('#current_data_view').modal('toggle');
                $("#sockets-form").submit();
            });



//we need the debug link to continue to work, even though we are not POSTing json to the url in question...
//so we are going to override the debug links onclick and make it put the data in a new window...

$("#debug_link").on('click', function(){
	$.post(json_url, config_data, function (data) {
    		var w = window.open('about:blank', 'windowname');
    		w.document.write(JSON.stringify(data));
    		w.document.close();
	});
	return(false);
});


    var svgCSS = "\n\n.emphasis_node {\n        font-size: 27px;\n        stroke:  black;\n        stroke-width:  2px;\n        stroke-opacity:  1;\n        fill:  black;\n        fill-opacity:  1;\n\n	}\n\n.emphasis_nodetext {\n	transform: rotate(-5deg);\n        font-size: 27px;\n        stroke:  black;\n        stroke-width:  2px;\n        stroke-opacity:  1;\n        fill:  black;\n        fill-opacity:  1;\n\n}\n\n.emphasis_link {\n        stroke-opacity:  .9;\n}\n\n.emphasis_link_red {\n        stroke:  red;\n        stroke-opacity:  .9;\n}\n\n.light_node {\n        font-size:  17px;\n        stroke: grey;\n        stroke-width: 2px;\n        stroke-opacity: 0.2;\n        fill: grey;\n        fill-opacity: 0.2;\n}\n\n.light_nodetext {\n	transform: rotate(-35deg);\n        font-size:  17px;\n        stroke: grey;\n        stroke-width: 2px;\n        stroke-opacity: 0.2;\n        fill: grey;\n        fill-opacity: 0.2;\n}\n\n.light_link {\n        stroke-opacity: .2;\n}\n\n.default_node {\n        font-size: 22px;\n        stroke: dimgrey;\n        stroke-width: 2px;\n        stroke-opacity: 0.8;\n        fill: black;\n        fill-opacity: 0.8;\n}\n\n.default_nodetext {\n	transform: rotate(-5deg);\n        font-size: 22px;\n        stroke: dimgrey;\n        stroke-width: 2px;\n        stroke-opacity: 0.8;\n        fill: black;\n        fill-opacity: 0.8;\n}\n\n.default_link {\n        stroke-opacity: .5;\n        stroke-linecap: round;\n\n}\n";

    var hoveringTimeout = null;
    var is_currently_search = false;

    var linkedByIndex = {};

    var need_to_load_static_positions = false;
    var static_positions = [];

    var found_types = new Map();
    var found_groups = new Map();
    var found_link_types = new Map();

    var 	real_width = 1000,
        real_height = 1000;

    var 	zoom_factor = 1.5;

    var 	virtual_width = real_width * zoom_factor;
    var 	virtual_height = real_height * zoom_factor;

    var 	gravityMode = 'centered';

    var json_url = '{{ $graph_uri }}';

    var color = d3.scale.category10();

    /*
        color('throw');
        color('away');
        color('all');
        color('of');
        color('these');
        color('colors');
    */
    var config_data = {
        "threshold":"10",
        "sockets": sockets
    };

    var myGravity = 0.04;
    var myGravityXYRatio = 3;
    var myAlpha = 0.5;

    var force = d3.layout.force()
        .linkDistance(30)
        .linkStrength(0.55)
        .friction(0.7)
        .charge(-3000)
        .chargeDistance(300)
        .theta(0.35)
        .gravity(0.04)
        .alpha(0.5)
        .size([virtual_width, virtual_height]);


    var svg = null;
    var graph; //this needs to be a global for functions to work...
    var link;
    var node;

    var first_node_ceiling = 200;
    var first_links_ceiling = 5000;
    var second_node_ceiling = 500;
    var second_links_ceiling = 10000;
    var initial_settle_time = 30000; //in milliseconds.. so 1000 is 1 second.

    $.post(json_url, config_data, function (json_data, json_textStatus, json_jqXHR) {

        svg = d3.select("#viz").append("svg")
            .attr("width", "100%")
            .attr("height", "100%")
            .attr('id','viz_svg');

//lets add our stylesheet..
        svg.append('style').text(svgCSS);

//so that we can see what we are dealing with...
        svg.append("rect")
            .attr("width", "100%")
            .attr("height", "100%")
            .attr("stroke",'grey')
            .attr("fill", "white");

        graph = json_data;	//lets use the global from now on..

        //lets do a little work to make sure that this is ok..
        if (typeof graph.summary === 'undefined') {
            console.log('there is a problem with the graph data. Take a look: ');
            console.log(graph);
            return (false);
        }


//Lets check to see if this Graph is simple enough to display....
//if not... then lets show another view...

        if (graph.summary.nodes_count > second_node_ceiling) {
            if (confirm('This graph has ' + graph.summary.nodes_count + ' nodes, which is way to many nodes to show at one time... are you sure you want to continue? ')) {
                //we do nothing... which will end up showing everything!!
            } else {
                //the user does not want to continue... let them see why...
                tooManyTooShow(graph);
                return (false);
            }
        } else {
            if (graph.summary.links_count > second_links_ceiling) {
                if (confirm('This graph has ' + graph.summary.links_count + 'edges, which is way to many connections to show at one time... are you sure you want to continue? ')) {
                    //we do nothing... which will end up showing everything!!
                } else {
                    //the user does not want to continue... let them see why...
                    tooManyTooShow(graph);
                    return (false);
                }
            }
        }

        mySummary = graph.summary;

        /**
         ***     DATA stuff
         **/

            //create a connectedness lookup

            //in order to size the links properly we must understand the range
            //of possible values this gets us the maximum...
        var maxLinkValue = 0;
        var minLinkValue = 1000000000;
        for (var i = 0; i < graph.links.length; i++) {
            if (graph.links[i].weight > maxLinkValue) {
                maxLinkValue = graph.links[i].weight;
            }
            if (graph.links[i].weight < minLinkValue) {
                minLinkValue = graph.links[i].weight;
            }
            linkedByIndex[graph.links[i].source + "," + graph.links[i].target] = 1;
        }

        var widthScale = function () {
            return 3;
        };
        var lengthScale = function () {
            return 40;
        };
        if (minLinkValue != maxLinkValue) {
            //with that maximum in hand, we can use a d3 scale
            //to always have the widths go from 1 to 25, where 25 is the max!!
            var widthScale = d3.scale.linear()
                .domain([maxLinkValue / 3, maxLinkValue])
                .range([10, 30]);

            var lengthScale = d3.scale.log()
                .clamp(true)
                .domain([maxLinkValue / 5, maxLinkValue])
                .range([100, 10]);

        }


        force.nodes(graph.nodes)
            .links(graph.links)
            .linkDistance(function (d) {
                my_scaled_length = lengthScale(d.weight);
                if (my_scaled_length < 90) {
                    //	console.log(d.weight + ' scaled to ' + my_scaled_length);
                }
                return (my_scaled_length);
            });


        //before adding images or graphics, lets iterate the
        //layout so that it starts out settled.
        //this does not seem to do anything
        /*
            n = 10000;
              for (var i = n; i > 0; --i){
                console.log('init tick' + i);
                 force.tick();
            }
        */

        myGravity = myGravity + myGravity;
        force_settle(initial_settle_time, 'end of initial settling'); // a long initial settling

        graph.nodes.forEach(function (o, i) {
            static_positions[i] = {saved: false};
        });

        dynamic_resize();

        force.drag()
            .on("dragend", function (d) {
                this_node = d3.select(this); //this sets the data to be fixed and sets the css class to have 'fixed' all in one step.
                node_fix(this_node, d);
                force_settle(20000, 'node finished drag');
            });


        /**
         ***    LEGEND Stuff...
         **/
        //lets make a map that contains the actual groups and types that we see in the data...
        graph.nodes.forEach(function (o, i) {
            found_types.set(graph.types[o.type].label, o.type);
            found_groups.set(graph.groups[o.group].name, o.group);
        });

        //lets do the same for links...
        graph.links.forEach(function (link, i) {
            found_link_types.set(graph.link_types[link.link_type].label, link.link_type);
        });


        //the legend is a loop that is going to use math to place the legend...
        //these are the parameters for this math

        legend_x = 10;
        legend_y = 20;
        symbol_more_x = 20;
        symbol_more_y = 5;
        png_more_x = 10;
        png_more_y = -5;
        symbol_size = 250;
        text_more_x = 40;
        text_more_y = 10;
        rect_width = 35;
        rect_height = 10;
        line_width = 135;
        line_height = 20;
        y_drop = 20;
        y_main_label_drop = 10;

        svg.append('svg:text')	//text for the label
            .attr('x', legend_x)
            .attr('y', legend_y)
            .text('Node Types:');	//and we put the name in
        legend_y += y_main_label_drop;


        //this loop handles the types, which are coded with shapes
        found_types.forEach(function (type_index, label) {
            if (graph.types[type_index].is_img) {

                svg.append('svg:image')
                    .attr("xlink:href", function () {
                        return '/images/' + graph.types[type_index].img_stub + '.png';
                    }) //the black version of the icons...
                    .attr("x", function () {
                        return legend_x + png_more_x;
                    })
                    .attr("y", function () {
                        return legend_y + png_more_y;
                    })
                    .attr("height", function () {
                        return symbol_size / 10
                    })
                    .attr("width", function () {
                        return symbol_size / 10
                    });


            } else {
                svg.append("path")
                    .attr("transform", function (d) {
                        return "translate(" + (legend_x + symbol_more_x) + "," + (legend_y + symbol_more_y) + ")";
                    })
                    .attr('fill', 'black')
                    .attr("d", d3.svg.symbol()	//here we get a symbol
                        .size(function () {
                            return symbol_size;
                        })	//how big is the symbol
                        .type(function () {
                            return d3.svg.symbolTypes[type_index];
                        }));	//and which symbol should we use?
                //note: we always pass in all of the types in our json data,
                //even if we do not use them, so that the symbols remain constant...
            }


            svg.append('svg:text')	//text for the label
                .attr('x', legend_x + text_more_x)
                .attr('y', legend_y + text_more_y)
                .text(label + ' (' + graph.types[type_index].type_count + ' nodes)');	//and we put the name in
            legend_y += y_drop;

        });


        legend_y += y_main_label_drop;
        svg.append('svg:text')	//text for the label
            .attr('x', legend_x)
            .attr('y', legend_y)
            .text('Edge Types:');	//and we put the name in
        legend_y += y_drop;


        found_link_types.forEach(function (link_type_index, label) {

            //console.log('index ' + link_type_index + ' label ' + label);

            svg.append('line')      //draws the example line for edge types
                .attr('x1', legend_x)
                .attr('y1', legend_y + 7)
                .attr('x2', legend_x + line_width)
                .attr('y2', legend_y + 7)
                .attr('stroke-width', 4) //key not using defaults
                .attr('stroke', '#5C5C5C')
                .attr('stroke-opacity', .7) //key not using defaults
                .attr('stroke-linecap', 'round') //key not using defaults
                .attr('stroke', function (d) {
                    return (edgeColorLookup[link_type_index % dasharrayCount] );
                })
                .attr('stroke-dasharray', function (d) {
                    return (dasharrayLookup[link_type_index % dasharrayCount]);
                });

            svg.append('svg:text')  //the text for the label
                .attr('x', legend_x)
                .attr('y', legend_y)
                .text(label);   //and here we put the name in

            legend_y += y_drop;


        });


        legend_y += y_main_label_drop;
        svg.append('svg:text')	//text for the label
            .attr('x', legend_x)
            .attr('y', legend_y)
            .text('Node Groups:');	//and we put the name in
        legend_y += y_main_label_drop;


        //this loop handles the groups which are coded with colors
        found_groups.forEach(function (group_index, label) {

            svg.append('rect')	//draws the rectangle
                .attr('x', legend_x)
                .attr('y', legend_y)
                .attr('fill', color(group_index))	//fills it with the groups color
                .attr('stroke', 'black') //not using defaults
                .attr('height', rect_height)
                .attr('width', rect_width);

            svg.append('svg:text')	//the text for the label
                .attr('x', legend_x + text_more_x)
                .attr('y', legend_y + text_more_y)
                .text(label + ' (' + graph.groups[group_index].group_count + ' nodes)');	//and we put the name in

            legend_y += y_drop;


        });


        /**
         ***    LINK Stuff...
         **/

//we have solves the scaling problems for our links
//back in the DATA section
        link = svg.selectAll(".link")
            .data(graph.links)
            .enter().append("line")
            .attr('class', 'default_link')// all static properties are now set through the class..
            .attr('stroke-width', function (d) { //this is where we give thick lines to larger weights
                my_scaled_width = widthScale(d.weight);
                if (my_scaled_width < 2) {
                    return (2);
                } else {
                    return (my_scaled_width);
                }
            })
            .attr('stroke-dasharray', function (d) { //this is where we support link 'types'
                return (dasharrayLookup[d.link_type % dasharrayCount] );
            })
            .attr('stroke', function (d) { //and here...
                return (edgeColorLookup[d.link_type % dasharrayCount] );
            });

        /**
         ***    NODE section...
         **/

//This is the creation of the node.
//we use a "g" which is a grouping element for svg.
        node = svg.selectAll('.node')
            .data(graph.nodes)
            .enter().append('g')
            .attr('class', 'node')
            .classed('default_node', true)
            //	.attr('font-family', 'san-serif')
            //	.attr('font-size',  '22px' )
            .call(force.drag);


//This is what attaches the symbols to their respective functions...
        node.append("path")
            .filter(function (d) {
                return (!d.img)
            })	//basically run this if it is not an image...
            .attr("d", d3.svg.symbol()
                .size(function (d) {
                    return d.size;
                })
                .type(function (d) {
                    return d3.svg.symbolTypes[d.type];
                }))
            .style("fill", function (d) {
                return color(d.group);
            })
            .style("stroke", "white") //stroke for shapes is not defaulted
            .style("stroke-width", "2px") //stroke-width for shapes is not defaulted
        ;//.call(force.drag);

        var image_strings = ['.png', '.jpg', '.svg', '.gif'];


        node.append("svg:image")
            .filter(function (d) {
                if (d.img) {
                    return (true)
                }
            })	//Its an IMAGE!!
            .attr("xlink:href", function (d) {
                is_full_link = false;
                //for (var file_type of image_strings) { //soon...
                for (var i = 0; i < image_strings.length; i++) {
                    file_type = image_strings[i];
                    if (d.img.indexOf(file_type) > -1) {
                        //console.log(file_type + ' matches against ' + d.img);
                        is_full_link = true;
                    }
                }
                if (is_full_link) {
                    return d.img; //then it can stand alone..
                } else {
                    //then we are using our system of building colored icons
                    color_code = color(d.group);
                    img_url = '/images/cache/' + encodeURIComponent(d.img + '.' + color_code + '.png');
                    return img_url;
                }
            })
            .attr("x", function (d) {
                return -25;
            })
            .attr("y", function (d) {
                return -25;
            })
            .attr("height", function (d) {
                return d.size / 10
            })
            .attr("width", function (d) {
                return d.size / 10
            });


// This is what shows the names on the nodes..
        node.append('text')
            .classed('noselect', true)	//this css is in css/noselect.css and hopefully will prevent the "selection" tool from interfering with the node grabbing process
            .classed('default_nodetext', true) //this class is what handles the text rotations
            .attr('dx', 12)
            .attr('dy', '.35em')
            //	.attr("transform", "rotate(-25)")
            .text(function (d) {
                return d.short_name;
                //return d.name;
            });

// this provides hoverover text that is not needed when we have the text showing all the time..
// perhaps if we have other info to show...
//  		node.append("title")
//      		.text(function(d) { return d.name; });

        /**
         ***    Node Interactions...
         **/


        node.on('mousedown', function (d) {

            //it is not a requirement to have a actual url..
            //if you put the blank string... we just do nothing..
            if (d.json_url.length == 0) {
                console.log('no json_url found, not attempting to build node card');
                return;
            }

            this_url = d.json_url;

            $('#node_left_panel').fadeOut("fast");
            //populate REST-born data for the custom node panel
            $.getJSON(this_url, function (data) {

                /*
                    //the old way
                                        dust.render(this_dust, data, function(err, out){
                              $('#node_left_panel').html(out);
                              $('#node_left_panel').fadeIn("slow");
                                                 });
                */


		if(isset(data.card_img_top)){
			card_img = `<img class='card-img-top' src="${data.card_img_top}">`;
		}else{
			card_img = '';
		}


                //the new way just uses literal templates https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Template_literals
                card_html = `
<div class='card' style='width: 97%; padding .25rem;'>
	${card_img}
	${data.card_body}
</div>
`;

                $('#node_left_panel').html(card_html);
                $('#node_left_panel').fadeIn("slow");


                // TODO reimplement?
                /*
                        //this js exists because is_admin = true...

                        this_admin_dust = 'admin.' + this_dust;
                                            dust.render(this_admin_dust, data, function(err, out){
                                  $('#node_admin_left_panel').html(out);
                                  $('#node_admin_left_panel').fadeIn("slow");
                                                     });

                */


            });
            //populate the generic node panel from the existing node data
            //first we need printable versions of the type and group

            //TODO reimplement using literals!!
            /*
                d.type_print = graph.types[d.type].label;
                d.group_print = graph.groups[d.group].name;
                dust.render('all_node_dust',d,function(err,out){
                          $('#node_menu_left_panel').html(out);
                    });

            */
        });

        node.on('dblclick', function (d) {
            d.fixed = false;
            static_positions[d.index].saved = false;
            force_settle();
        });

//The functionality we want here is to highlight the edges for a specific node...
//but we want to avoid the fireworks... to do that... we set a timer...
//if you are still hovering after three seconds... we light it up...
        var still_hovering = false;
        node.on('mouseover', function (d) { //on mouseout we calculate if we are in our boxes


            if (is_currently_search) return;

            //console.log('mouse over now');
            still_hovering = d.id;
            force.stop();
            //restoreOpacity();
            if (hoveringTimeout) clearTimeout(hoveringTimeout);


            hoveringTimeout = setTimeout(function () {

                if (still_hovering == d.id) { //still hovering after 2 seconds... light it up!!
                    //console.log('mouse over timeout running');

                    link.attr('class', (function (l) {
                        if (d === l.source || d === l.target)
                            return ('emphasis_link_red');
                        else
                            return ('light_link');
                    }));

                    //first we make the node style sensible
                    node.attr('class', function (o) {
                        if (isConnected(d, o)) {
                            return ('noselect emphasis_node');
                        } else {
                            return ('noselect light_node');
                        }

                    });

                    //then we make the text style compatible;
                    d3.selectAll('text.noselect')
                        .attr('class',
                            function () {
                                //lets get everything from the parent...
                                this_parent_g = d3.select(this.parentNode);
                                new_class = this_parent_g.attr('class') + 'text';

                                return new_class;

                            }
                        );


                } else {
                    //then we are not still hovering??

                }

            }, 2000);
        });

        node.on('mouseout', function (d) {
            //console.log('mouseout');

            still_hovering = false;//will prevent a light up if it has not already happened
            if (hoveringTimeout) clearTimeout(hoveringTimeout);

            if (!is_currently_search)
                restoreOpacity();

        })

        ;
        /**
         ***    FORCE FUNCTIONS
         ***/
//this window resize function does not work as an external function
//perhaps because of variable scoping...

        var starting_tick_ignore, tick_ignore_countdown = 200;


// The tick function is your way to influence what happens to the nodes and links on every tick of the force loop...
        force.on("tick", function (e) {

//		if(need_to_load_static_positions){
//			console.log('need to load the save positions');
            graph.nodes.forEach(function (o, i) {
                if (o.fixed) {
                    if (static_positions[i].saved) {
                        //console.log("node "+i);
                        //console.log("using saved x: " + static_positions[i].x);

                        o.x = static_positions[i].x;
                        o.y = static_positions[i].y;
                    }
                }
            });
//			console.log(static_positions);
//			need_to_load_static_positions = false;
//		}
            skip_this_tick = false;
            if (tick_ignore_countdown > 0) {
                skip_this_tick = true;
            }


            node.each(defineGravity(e.alpha));

            if (!skip_this_tick) {

                link.attr("x1", function (d) {
                    return d.source.x;
                })
                    .attr("y1", function (d) {
                        return d.source.y;
                    })
                    .attr("x2", function (d) {
                        return d.target.x;
                    })
                    .attr("y2", function (d) {
                        return d.target.y;
                    });

                node.attr("transform", function (d) {
                    return "translate(" + d.x + "," + d.y + ")";
                });


                force_settle(20000, 'running tick'); //no matter what stop the graph from moving after an initial settle period
            }
            tick_ignore_countdown--;
        });

        $('#loading_div').toggle();
        $('#viz').toggle();

    }, 'json').fail(function () {
        //this means the json did not load...
        $('#inner_loading').toggle();
        $('#inner_error').toggle();
        $('#viz').toggle();

    }); //this closes out the entire json getting function...



</script>

<script>

// These functions are loaded from FORCE_functions.tpl

// we use the svg stroke-dasharray to show our edge types
// https://developer.mozilla.org/en-US/docs/Web/SVG/Attribute/stroke-dasharray
var dasharrayLookup = [	//7 options for now...
	'',	//position 0 is solid, so if you only have one edge type... it will be solid...
	'5,5',
	'20,10,1,10',
	'20,5,1,5,1,5,1,5',
	'10,10',
	'30,10',
	'30,10,30'
	];
var dasharrayCount = dasharrayLookup.length - 1;

var edgeColorLookup = 	//taken from http://colorbrewer2.org/?type=qualitative&scheme=Dark2&n=6
['#1b9e77','#d95f02','#7570b3','#e7298a','#66a61e','#e6ab02']

var edgeColorCount = edgeColorLookup.length - 1;

// hope it works https://stackoverflow.com/q/24703464/144364
function isConnected(a, b) {
    return linkedByIndex[a.index + "," + b.index] || linkedByIndex[b.index + "," + a.index] || a.index == b.index;
}



function ContainsFromGo(s, substr) {
  //  discuss at: http://locutus.io/golang/strings/Contains
  // original by: Kevin van Zonneveld (http://kvz.io)
  //   example 1: Contains('Kevin', 'K')
  //   returns 1: true
  // modified to be caseless..
	haystack = s.toLowerCase();
	needle = substr.toLowerCase();
  return (haystack + '').indexOf(needle) !== -1
}
function restoreSearch()
{
	$("#nodeTextMatch").val('');
	is_currently_search = false;
	restoreOpacity();
}
function restoreOpacity(){

		//console.log('running restore opacity');
		link.attr('stroke', function(d){
                                        return(edgeColorLookup[d.link_type % dasharrayCount] );
                                    })
              	link.attr('class', 'default_link');


		d3.selectAll('text.noselect')
			.attr('class','noselect default_nodetext');
				//all of the text is too big

		node.attr('class','default_node');

	return(false);
}


function searchNodeText(){

	var searchstring_el = $('#nodeTextMatch');
	var searchstring = searchstring_el.val();

	if(strlenFromPHP(searchstring) > 2){

		node.attr('class',function(d)
		{
			this_text = d3.select(this.childNodes[2]).text();
			this_search_result = ContainsFromGo(this_text,searchstring);

			if(this_search_result){
				return 'noselect emphasis_node';
			}else{
				return 'noselect light_node';
			}


			my_text = d3.select(this).select('text');
			my_text.text(d.name);

		});


		link.attr('class', function(l) {
			if(ContainsFromGo(l.source.name,searchstring) || ContainsFromGo(l.target.name,searchstring) )
			{
				d3.selectAll("g").filter(function(d) { return d.id === l.source.id || d.id === l.target.id })
					.attr('class','noselect emphasis_node');
				return 'emphasis_link';
			}
			else
			{
				return 'light_link';;
			}

		 });

		d3.selectAll('text.noselect')
			.attr('class', function(){
					this_text = d3.select(this).text();
					this_search_result = ContainsFromGo(this_text,searchstring);
					if(this_search_result){
						return 'noselect emphasis_nodetext';
					}else{
						return 'noselect light_nodetext';
					}
				}
			);

		is_currently_search = true;
	}
	return(false);
}

function free_nodes(){
	graph.nodes.forEach(function (o,i) {
		static_positions[i] = { saved: false};
		o.fixed = false;
	});
	true_unfreeze();
}

function free_one_node(node_index){

	static_positions[node_index] = { saved: false};
	graph.nodes[node_index].fixed = false;

	true_unfreeze();
}

//this function handles the proccess of fixing an node in space.
//given that we want to be able to save this fixed locations
//and that we want them to survive resizing... this is not a trivial process.
function node_fix(this_node,d){
	this_node.classed("fixed", d.fixed = true); //this sets the data to be fixed and sets the css class to have 'fixed' all in one step.

	//this will add ry and rx to the data..
	save_relative(d);

	static_positions[d.index].saved = true;
	static_positions[d.index].x = d.x;
	static_positions[d.index].y = d.y;
	static_positions[d.index].rx = d.rx;
	static_positions[d.index].ry = d.ry;
	static_positions[d.index].obj_id = d.id;
	static_positions[d.index].obj_type = d.type;

	post_static_positions(static_positions);
}

function post_static_positions(static_position_array){

	clean_array = [];

	for (var i = 0; i < static_position_array.length; i++) {
		if(static_position_array[i].saved){
			tmp = {};
			tmp.rx = static_position_array[i].rx;
			tmp.ry = static_position_array[i].ry;
			tmp.obj_id = static_position_array[i].obj_id;
			tmp.obj_type = graph.types[static_position_array[i].obj_type].id;	//does a lookup on the json...
			//tmp.id = statuc_position_array.id;
			clean_array.push(tmp);
		}
	}
	send_me = {
		'static_positions': clean_array,
		'careset_code':  '1114904687' ,
		'report':  'dandelion_simple'
	};

	//console.log('Attempting Save Position with: ');
	//console.log(send_me);
	//TODO repliment.
	/*
	$.ajax({
    		type: "POST",
    		url: "/FORCEPOSITIONS/",
    		data: send_me,
    		dataType: 'json',
//    		contentType: 'application/json',
    		success: function(got_this_back){
        		//console.log(got_this_back);
    		}
	});
	*/

}


function defineGravity(alpha){

	return function(d) {

		halfX = virtual_width / 2;
		halfY = virtual_height / 2;
		thirdX = virtual_width / 3;
		thirdY = virtual_height / 3;
		fourthX = virtual_width / 4;
		fourthY = virtual_height / 4;
		tenthX = virtual_width / 10;
		tenthY = virtual_height / 10;

		switch(gravityMode){



			case 'centered':
				if(d.fixed == true){
					return;
				}

				fociCenterY = Math.round(halfY + tenthY); //shifted down to account for labels
				fociCenterX = halfX;

				group_factor = d.group + 2;

				myGravity_x = myGravity;
				myGravity_y = myGravity * myGravityXYRatio;

				x_factored_gravity = (myGravity_x * 2) * (1 / group_factor) ;
				y_factored_gravity = (myGravity_y * 2) * (1 / group_factor) ;

				xk = x_factored_gravity * alpha;
				yk = y_factored_gravity * alpha;

				d.y += (fociCenterY - d.y) * yk;
				d.x += (fociCenterX - d.x) * xk;
				return;

			break;

			case 'right':
				if(d.fixed == true){
					return;
				}

				fociCenterY = Math.round(halfY + tenthY);
				fociCenterX = 3 * (fourthX); //this makes it right

				factored_gravity = (myGravity * 2);
				xk = factored_gravity * alpha;
				yk = myGravity * alpha;

				d.x += (fociCenterX - d.x) * xk;
				d.y += (fociCenterY - d.y) * yk;
				return;

			break;
			case 'left':
				if(d.fixed == true){
					return;
				}

				fociCenterY = Math.round(halfY + tenthY);
				fociCenterX = (fourthX); //this makes it to the left

				factored_gravity = (myGravity * 2);
				xk = factored_gravity * alpha;
				yk = myGravity * alpha;

				d.x += (fociCenterX - d.x) * xk;
				d.y += (fociCenterY - d.y) * yk;
				return;

			break;

			case 'group':
				if(d.fixed == true){
					return;
				}

				slot_count = (graph.groups.length * 2) + 1;
				my_slot = (d.group * 2) + 1

				fociCenterX = Math.round((virtual_width / slot_count) * my_slot);
				fociCenterY = Math.round(halfY + tenthY);


				factored_gravity = (myGravity * 3);

				yk = myGravity * alpha;
				xk = factored_gravity * alpha;

				new_y = d.y + Math.round((fociCenterY - d.y) * yk);
				new_x = d.x + Math.round((fociCenterX - d.x) * xk);
				d.y = new_y;
				d.x = new_x;

				return;

			break;




		}
	}
}

function save_relative(d){
	d.rx = d.x / virtual_width;
	//console.log('saving relative x: '+d.rx);
	d.ry = d.y / virtual_height;
}

//We really want the default status of the force to be off...
//So rather than starting it, lets start it for 3 seconds and turn it off again...

//This function allows the attraction and charge sliders to work...
function updateForce() {
	//console.log('force stop: due to window resize');
  force.stop();

//look in FORCEReportController for the default values that drive these sliders...
  var newGravity = document.getElementById('gravitySlider').value;
  var newCharge = document.getElementById('chargeSlider').value;
  var newZoom = document.getElementById('zoomSlider').value;
  document.getElementById('chargeInput').value = newCharge;
  document.getElementById('zoomInput').value = newZoom;
  document.getElementById('gravityInput').value = newGravity;



  var newLinkDistance = 30;
  var newLinkStrength = 0.55;
  var newFriction = 0.7;
  var newChargeDistance = 300;
  var newTheta = 0.35
  var newGravityXYRatio = 3;
  var newAlpha = 0.5;






  force
 // .linkDistance(newLinkDistance)
  .linkStrength(newLinkStrength)
  .friction(newFriction)
  .charge(newCharge)
  .chargeDistance(newChargeDistance)
  .theta(newTheta)
  .gravity(0)
  .alpha(newAlpha);


	myGravity = newGravity;// I manage the gravity not... not D3
	myGravityXYRatio = newGravityXYRatio;
	myAlpha = newAlpha;

  zoom_factor = newZoom;

  dynamic_resize();

  force_settle(20000,'user adjusted parameters');
}

var need_to_load_static_positions = false;
var static_positions = [];

// This function makes the graph fit the webpage...
//How did we get this to work. Apparently height set once in the div and then does not want to change at all...
function dynamic_resize(){

	if (typeof graph  === "undefined") {
		//console.log('waiting for json data to run save position logic...');
	}else{
    //width = window.innerWidth - 500;	//this breaks when the left side menu stacks b/c of responsive...
    		real_height = window.innerHeight - 150;	//we use the window to get the height because we need to resize the div
    		real_width = $('#viz').width() - 10; //this lets us respect the responsive design from bootstrap for width..
    		viz_height = real_height + 1;

		virtual_height = real_height * zoom_factor;
		virtual_width = real_width * zoom_factor;

    		svg
			.attr('viewBox','0 0 ' + virtual_width + ' ' + virtual_height)
			.attr('preserveAspectRatio','xMidYMid meet')
			.attr('width', real_width)
			.attr('height', real_height);

    		$('#viz').height(viz_height);
    		force.size([virtual_width, virtual_height]).resume();

                 graph.nodes.forEach(function (o,i){
                        if(o.fixed){
                                //console.log("node "+i);
                                new_x = Math.floor(o.rx * virtual_width);
				//console.log("Calculating " + new_x + " from relative position " + o.rx);
                                new_y = Math.floor(o.ry * virtual_height);
				if(!isNaN(new_x)){
                                	//console.log('calculate new x position ' + new_x);
				}
					//console.log(o);
					//console.log('width ' + width);
					//console.log('new x' + new_x);
                                static_positions[i].saved = true;
                                static_positions[i].x = new_x;
                                static_positions[i].y = new_y;
                                static_positions[i].rx = o.rx;
                                static_positions[i].ry = o.ry;
                                static_positions[i].obj_id = o.id;
                                static_positions[i].obj_type = o.type;
                     	}
                });
        //lets try calling force.tick which should translate our changes to the data... to the g..
		need_to_load_static_positions = true;
        	force_settle(initial_settle_time,'resize settle');
	}

}
// When we resize the window, lets dynamically resize everything...
d3.select(window).on('resize', dynamic_resize);
//Since we do not know how big the window is to start, lets resize just once to get going...
dynamic_resize();


var been_settling_since = Date.now();	//we use this to ensure that we do not get tons of random force.stops..
				//just the last run from force_settle will stop the animation...
var how_many_ticks = 0;
var too_many_ticks = 70;

setInterval( function () {

	if(how_many_ticks > too_many_ticks){
		force.stop();
		how_many_ticks = 0;
	}

	//console.log('How many Ticks: ' +how_many_ticks);

},3000);

//Our way of ensuring that the default is not moving...
function force_settle( time_to_run , reason ){
        if (typeof time_to_run === "undefined") {
                time_to_run = 3000;
        }
        if (typeof reason === "undefined") {
                reason = "no reason given";
        }
	//too chatty unless debugging settling
	//console.log("force_settle: " + reason);
        force.start();
	how_many_ticks++;
}

//This variable tracks whether the graph is frozen by the user...
var trulyFrozen = false;
var userForcedPlay = false; //this has the effect of allowing movement to continue
				//independent of other "settle" calls, if the user specifically asked for it...
//Called via  a button this function stops the graph from moving...
//this is not used currently
function true_freeze(){
        trulyFrozen = true;
        force.stop();
}

//Called via  a button this function starts the graph if it is stopped...
function true_unfreeze(){
        trulyFrozen = false;
	userForcedPlay = true;
        force_settle(20000,'user clicked via true_unfreeze');
	setTimeout(function () { userForcedPlay = false},20000);
}

function downloadSVG(){
	saveSvgAsPng(document.getElementById('viz_svg'), "diagram.png");
}

function tooManyTooShow(graph){
	//replace me with dust...
	$("#loading_div").html(	"<h1> Too much data to browse with the graph browser </h1><ul><li> Node Count: " +
			graph.summary.nodes_count + " > " + second_node_ceiling + "  </li> <li> Connection Count " +
			graph.summary.links_count + " > " + second_links_ceiling + " </li> </ul>" +
			"<p> You might try to increase the patient flow threshold to get a simpler graph... </p>"
		);
	$('#viz').html('');
}


function strlenFromPHP(the_string) {
  //  discuss at: http://locutus.io/php/strlen/
  // original by: Kevin van Zonneveld (http://kvz.io)
  // improved by: Sakimori
  // improved by: Kevin van Zonneveld (http://kvz.io)
  //    input by: Kirk Strobeck
  // bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
  //  revised by: Brett Zamir (http://brett-zamir.me)
  //      note 1: May look like overkill, but in order to be truly faithful to handling all Unicode
  //      note 1: characters and to this function in PHP which does not count the number of bytes
  //      note 1: but counts the number of characters, something like this is really necessary.
  //   example 1: strlen('Kevin van Zonneveld')
  //   returns 1: 19
  //   example 2: ini_set('unicode.semantics', 'on')
  //   example 2: strlen('A\ud87e\udc04Z')
  //   returns 2: 3
  var str = the_string + ''
  var iniVal = (typeof require !== 'undefined' ? require('../info/ini_get')('unicode.semantics') : undefined) || 'off'
  if (iniVal === 'off') {
    return str.length
  }
  var i = 0
  var lgth = 0
  var getWholeChar = function (str, i) {
    var code = str.charCodeAt(i)
    var next = ''
    var prev = ''
    if (code >= 0xD800 && code <= 0xDBFF) {
      // High surrogate (could change last hex to 0xDB7F to
      // treat high private surrogates as single characters)
      if (str.length <= (i + 1)) {
        throw new Error('High surrogate without following low surrogate')
      }
      next = str.charCodeAt(i + 1)
      if (next < 0xDC00 || next > 0xDFFF) {
        throw new Error('High surrogate without following low surrogate')
      }
      return str.charAt(i) + str.charAt(i + 1)
    } else if (code >= 0xDC00 && code <= 0xDFFF) {
      // Low surrogate
      if (i === 0) {
        throw new Error('Low surrogate without preceding high surrogate')
      }
      prev = str.charCodeAt(i - 1)
      if (prev < 0xD800 || prev > 0xDBFF) {
        // (could change last hex to 0xDB7F to treat high private surrogates
        // as single characters)
        throw new Error('Low surrogate without preceding high surrogate')
      }
      // We can pass over low surrogates now as the second
      // component in a pair which we have already processed
      return false
    }
    return str.charAt(i)
  }
  for (i = 0, lgth = 0; i < str.length; i++) {
    if ((getWholeChar(str, i)) === false) {
      continue
    }
    // Adapt this line at the top of any loop, passing in the whole string and
    // the current iteration and returning a variable to represent the individual character;
    // purpose is to treat the first part of a surrogate pair as the whole character and then
    // ignore the second part
    lgth++
  }
  return lgth
}



</script>








</div>
</div>
</div>

</div> <!-- end main container-fluid -->

<div class='visible-print-block'>
<p class='text-center'>
<small>
Copyright © CareSet 2019
</small>
<p>
</div>



<script>
  $(document).ready(function()
       {
                $('#dataOptionsModal').on('show.bs.modal', function (e) {

                        url = '/CARESETREPORTJSON/referral_start/1114904687/';

                        $.getJSON(url,function(data){

				data.FORCE_view_stub = 'dandelion_simple';
				data.return_url = '/FORCE/dandelion_simple/1114904687/10/';
				graph.FORCE_view_stub = 'dandelion_simple';
				graph.return_url = '/FORCE/dandelion_simple/1114904687/10/';


                                dust.render("FORCE_data_options_tax", data,
                                        function(err,out){
                                                $('#modal_tax_chooser').html(out);

                				$('#sorted_tax_table').tablesorter({
                       	 				widgets : ["uitheme","filter","zebra","stickyHeaders"],
                        				theme: 'bootstrap',
                        				widthFixed: true,
                        				widgetOptions : {
                               			 		zebra: ['even', 'odd'],
                                				filter_hideFilters : false,
                               	 				stickyHeaders_offset: 51
                                			}
                				});//end of tablesorter..
                             	});//end of dust render

					//graph should be defined as a global variable and populated with
					//the json data from force json call..
                                dust.render("FORCE_data_options_datasource", graph,
                                        function(err,out){
                                                $('#modal_data_chooser').html(out);

                                                $('#sorted_tax_table').tablesorter({
                                                        widgets : ["uitheme","filter","zebra","stickyHeaders"],
                                                        theme: 'bootstrap',
                                                        widthFixed: true,
                                                        widgetOptions : {
                                                                zebra: ['even', 'odd'],
                                                                filter_hideFilters : false,
                                                                stickyHeaders_offset: 51
                                                        }
                                                });//end of tablesorter..
                                });//end of dust render



                        });//end of getJSON
                }); //end of on(show.bs.modal)
 	});//end of jquery ready


function isset () {
  //  discuss at: http://locutus.io/php/isset/
  // original by: Kevin van Zonneveld (http://kvz.io)
  // improved by: FremyCompany
  // improved by: Onno Marsman (https://twitter.com/onnomarsman)
  // improved by: Rafał Kukawski (http://blog.kukawski.pl)
  //   example 1: isset( undefined, true)
  //   returns 1: false
  //   example 2: isset( 'Kevin van Zonneveld' )
  //   returns 2: true

  var a = arguments
  var l = a.length
  var i = 0
  var undef


  if (l === 0) {
    throw new Error('Empty isset')
  }

  while (i !== l) {
    if (a[i] === undef || a[i] === null || a[i] === '') {
      return false
    }
    i++
  }

  return true
}

</script>






