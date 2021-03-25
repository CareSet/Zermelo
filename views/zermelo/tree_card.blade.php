<div class="container-fluid">
	<div>
		<h1> {{ $report->GetReportName()  }}</h1>
	</div>
	<div>
		{!! $report->GetReportDescription() !!}
	</div>

	<div style='display: none' id='json_error_message' class="alert alert-danger" role="alert">

	</div>

@if ($report->is_fluid())
	<div class='container-fluid'>
@else
	<div class='container'>
@endif

	<div id='div_for_cards'>





	</div>



</div>




<script type="text/javascript" src="/vendor/CareSet/core/js/jquery.min.js"></script>
<script type="text/javascript" src="/vendor/CareSet/core/botstrap/bootstrap.bundle.min.js"></script>

<script type="text/javascript">



function doh_ajax_failed(jqxhr, textStatus, error){

                var is_admin = true; //this should be set via a call to the presenter

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

}


    $(function() {

        var columnMap = [];
        var fixedColumns = null;

        $.getJSON('{{ $presenter->getSummaryUri() }}',
            {
                'token': '{{ $presenter->getToken() }}',
                'request-form-input': '{!! urlencode($presenter->getReport()->getRequestFormInput(true)) !!}',
            }).fail(function(jqxhr, textStatus, error) {
            		doh_ajax_failed(jqxhr, textStatus, error);
		})
		.done(function(header_data) { //this means I have clean results in the data variable...


                    var columns = header_data.columns;
                    var order = header_data.order;
                    var searches = [];



                    /*
                        Support multi column ordering
                    */
                    var callbackOrder = [];

                    var passthrough_params = {!! $presenter->getReport()->getRequestFormInput( true ) !!};
                    var merge_get_params = {
                        'data-option': '',
                        'token': '{{ $presenter->getToken() }}',
                        'page': (header_data.start / header_data.length) + 1,
                        "order": callbackOrder,
                        "length": header_data.length,
                        "filter": searches,
                    };
                    var merge = $.extend({}, passthrough_params, merge_get_params)
                    localStorage.setItem("Zermelo_defaultPlageLength",header_data.length);

                    var merge_clone = $.extend({},merge);
                    delete merge_clone['token'];

                    var param = decodeURIComponent( $.param(merge) );

		    var json_url_to_get = '{{ $presenter->getReportUri() }}';


			//now lets get the actual data...
                    $.getJSON(json_url_to_get, param
                    ).fail(function (jqxhr, textStatus, error){
            		doh_ajax_failed(jqxhr, textStatus, error);
			console.log('I get to this fail');
			}
			)
		    .done(function(data) {


			var cards_html = "<div class='row justify-content-left'>";
			var i = 0;
			var new_row = false;
			var is_empty = true;

			var card_width = '{{ $presenter->getReport()->cardWidth() }}';

			data.data.forEach(function(this_card) {
				is_empty = false; //we hqve at least one.

				if(isset(this_card.url)){
					//there is a root url..
					real_card_header = `<div class='card-header'> <a target='_blank' href='${this_card.url}'> ${this_card.label} </a>  </div>`;
				}else{
					//no url version
					real_card_header = `<div class='card-header'> ${this_card.label}  </div>`;
				}

				if(isset(this_card.sub_tree)){
					branch_list = "<ul class='list-group list-group-flush'>";
					this_card.sub_tree.forEach(function(branch_item) {
						if(isset(branch_item.url)){
							branch_list += `<li class='list-group-item'> <a target='_blank' href='${branch_item.url}'>  ${branch_item.label} </a> `;
						}else{
							branch_list += `<li class='list-group-item'> ${branch_item.label} `;
						}

						if(isset(branch_item.sub_tree)){
							branch_list += "<ul class='list-group'>";
							//then there is a leaf here too...
							branch_item.sub_tree.forEach(function (leaf_item) {
								if(isset(leaf_item.url)){
									branch_list += `<li class='list-group-item'> <a target='_blank' href='${leaf_item.url}'> ${leaf_item.label} </a> </li>`;
								}else{
									branch_list += `<li class='list-group-item'>  ${leaf_item.label}   </li>`;
								}

							});
							branch_list  += '</ul>';
						}

						//finishe up the list item..
						branch_list += '</li>';

					});
					branch_list += '</ul>';
				}else{
					branch_list = '';
				}




				cards_html += `
<div class="col-md-3">
	<div style='width: ${card_width}' class='card' >
  		${real_card_header}
		${branch_list}
	</div>
</div>
`;

				i++;

			})

			cards_html += "</div>";

			$('#div_for_cards').html(cards_html);

                    });


        }); /* end always on get Summary */


    });

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
