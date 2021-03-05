

function my_notify(message,alert_type,div_id){

	alert_type = pick(alert_type,'success');
	div_id = pick(div_id,'my_notify');

	$('#'+div_id).html(message);
	$('#'+div_id).addClass("alert alert-"+alert_type);

}

function pick(arg, def) {
   return (typeof arg == 'undefined' ? def : arg);
}


function old_jah(url,target,callback) {
    // native XMLHttpRequest object
    myTarget = document.getElementById(target);
    myTarget.style.display = ''; //just in case it was 'none'!!
    myTarget.innerHTML = 'loading... this might take some time';
    if (window.XMLHttpRequest) {
        req = new XMLHttpRequest();
        req.onreadystatechange = function() {jahDone(target,callback);};
        req.open("GET", url, true);
        req.send(null);
    // IE/Windows ActiveX version
    } else if (window.ActiveXObject) {
        req = new ActiveXObject("Microsoft.XMLHTTP");
        if (req) {
            req.onreadystatechange = function() {jahDone(target,callback);};
            req.open("GET", url, true);
            req.send();
        }
    }
}    

function jah(url,target,callback) {

	$.ajax({
		url: url,
		type: 'GET',
		dataType: 'html',
		success: function (data, textStatus, xhr){
			$('#'+target).html(data);
			callback();
		},
		error: function (data, textStatus, xhr){
			$('#'+target).html(textStatus);
		}
	});
}

function jahDone(target,callback) {
	//deprecated in favor jquery ajax!!
    // only if req is "loaded"
    if (req.readyState == 4) {
        // only if "OK"
        if (req.status == 200) {
            results = req.responseText;
            $('#'+target).html(results); //so that jquery stuff will work!!
        } else {
            document.getElementById(target).innerHTML="jah error:\n" +
                req.statusText;
        }

	callback();

    }
}

function toggle(obj) {
		var el = document.getElementById(obj);
		if ( el.style.display != 'none' ) {
			el.style.display = 'none';
		}
		else {
			el.style.display = '';
	//		window.scroll(0,findPos(el));
		}
		return false;
	}
function turn_on(obj) {
		var el = document.getElementById(obj);
		if(el){
			el.style.display = '';
		}
	//	window.scroll(0,findPos(el));
	}
function turn_off(obj) {
		var el = document.getElementById(obj);
		if(el){
			el.style.display = 'none';
		}
	}

function findPos(obj) {
	var curtop = 0;
	if (obj.offsetParent) {
		do {
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
	return [curtop];
	}
}

