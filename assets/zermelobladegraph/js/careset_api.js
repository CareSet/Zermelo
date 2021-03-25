
var careset_api_request;
/*
 *	Accepts data in the following format
 *	data.name = the name of the careset
 *	data.notes = notes about the careset
 *	data.npis = an array of the npis that belong in this careset
 *	data.source = the source of this careset
 *	The callback should accept true/false as the success/fail of the call
 *	the second argument will be the careset_code on success
 */
function save_careset(data, callback){

	if(careset_api_request){
		careset_api_request.abort();
	}

	if(typeof data.careset_code != 'undefined'){	
		url = '/caresets/' + data.careset_code;
	}else{
		url = '/caresets';
	}

	careset_api_request = $.ajax({
		type: "POST",
		url: url,
		data: data
	});

	careset_api_request.done(function (response, textStatus, jqXHR){
		callback(true,response.careset_code);
	});

	careset_api_request.fail(function (jqXHR, textStatus, errorThrown){
        	console.error(
            		"The following error occured: "+
           		textStatus + errorThrown
        	);
		callback(false);
	});

	careset_api_request.always( function() {
		//nothing here for now...
	});
}

function delete_careset(careset_code, callback){

	console.log(careset_code);
	careset_api_request = $.ajax({
		type: "POST",
		url: "/caresets/delete/" + careset_code
	});

	console.log(url);
	careset_api_request.done(function (response, textStatus, jqXHR){
		callback(true);
	});

	careset_api_request.fail(function (jqXHR, textStatus, errorThrown){
        	console.error(
            		"The following error occured: "+
           		textStatus, errorThrown
        	);
		callback(false);
	});

	careset_api_request.always( function() {
		//nothing here for now...
	});
}

/**
 * This is designed to be the callback for add_npi_to_careset()
 * It changes the button color, and removes the spinner from the button
 */
function blinkButton(data){
        console.log(JSON.stringify(data));
        $('#'+data.npi+ '_button').removeClass('btn-link');
        $('#'+data.npi+ '_button').addClass('btn-success');
        $('#'+data.npi+ '_plus_span').removeClass('glyphicon-plus-sign');
        $('#'+data.npi+ '_plus_span').addClass('glyphicon-ok');
        $('#'+data.npi+'_4spin').html("");
        $('.'+data.npi+ '_button').removeClass('btn-link');
        $('.'+data.npi+ '_button').addClass('btn-success');
        $('.'+data.npi+'_4spin').html("");
}

/** 
 * This adds a spinner to the _4spin div (that should preexist) presumably inside the button that was just pressed.
 * and it intended to be called just after add_npi_to_careset
 */
function add_spin(npi){
        $('#'+npi+'_4spin').html("<i class='fa fa-refresh fa-spin'></i>");
}

/** 
 * add a single npi to a given careset, using REST, and then run a callback
 *
 */
function add_npi_to_careset(careset_code, npi, callback){

        add_url = "/api/caresets/" + careset_code + "/npi/" + npi;

     jQuery.ajax({
         type: "GET",
         url: add_url,
         contentType: "application/json; charset=utf-8",
         dataType: "json",
         success: function (data, status, jqXHR) {
                        callback(data);
                       },
         error: function (jqXHR, status) {
                        console.log("add_npi_to_careset failed hitting "+add_url);
                       }
         });

}


function delete_npi_from_careset(careset_code, npi, callback){

	delete_url = "/api/caresets/" + careset_code + "/delete/npi/" + npi;


     jQuery.ajax({
         type: "GET",
         url: delete_url,
         contentType: "application/json; charset=utf-8",
         dataType: "json",
         success: function (data, status, jqXHR) {
			callback(data);
                       },
      	 error: function (jqXHR, status) {
                	console.log("delete_npi_from_careset failed hitting "+delete_url);
                       }
         });

}
