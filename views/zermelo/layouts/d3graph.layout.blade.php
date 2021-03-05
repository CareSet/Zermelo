<!doctype html>
<html lang="en">
<head>

    	<title>{{ $report->getReportName() }}</title>
    	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<meta name="description" content="Cube is a map of the healthcare system">
    	<meta name="author" content="CareSet Team">

	

    	<!-- standard styles -->
	<link type="text/css" rel="stylesheet" href="/vendor/CareSet/zermelobladegraph/css/bootstrap.4.3.1.min.css">
    	<link type="text/css" rel="stylesheet" href="/vendor/CareSet/zermelobladegraph/fontawesome-free-5.10.2-web/css/all.css">

    	<link type="text/css" rel="stylesheet" href="/vendor/CareSet/zermelobladegraph/css/colors.css">
    	<link type="text/css" rel="stylesheet" href="/vendor/CareSet/zermelobladegraph/css/taxonomyChooser.css">
	        <!-- custom css -->
        <link type="text/css" rel="stylesheet" href="/vendor/CareSet/zermelobladegraph/css/colors.css"/>
        <link type="text/css" rel="stylesheet" href="/vendor/CareSet/zermelobladegraph/css/noselect.css"/>
        <link type="text/css" rel="stylesheet" href="/vendor/CareSet/zermelobladegraph/css/print.css"/>
        <link type="text/css" rel="stylesheet" href="/vendor/CareSet/zermelobladegraph/css/floating.feedback.css"/>

    	<!-- standard javascript -->
    	<script type="text/javascript" language="javascript" src="/vendor/CareSet/zermelobladegraph/js/jquery-3.4.1.min.js"></script>
	<script type="text/javascript" src="/vendor/CareSet/zermelobladegraph/js/bootstrap.4.3.1.min.js"></script>

  <script type='text/javascript' language='javascript' src='/vendor/CareSet/zermelobladegraph/js/d3.3.5.17.min.js'></script>
	<script type="text/javascript" src="/vendor/CareSet/zermelobladegraph/js/saveSvgAsPng.js"></script>


	<!-- custom javascript -->
	<script type="text/javascript" src="/vendor/CareSet/zermelobladegraph/js/util.js"></script>
	<script type="text/javascript" src="/vendor/CareSet/zermelobladegraph/js/careset_api.js"></script>
  	<script type="text/javascript" src="/vendor/CareSet/zermelobladegraph/js/html2canvas.js"></script>
  
	<!-- font awesome js -->
	<script type="text/javascript" language="javascript" src="/vendor/CareSet/zermelobladegraph/fontawesome-free-5.10.2-web/js/all.js"></script>


<!-- end dust_html.tpl -->

    	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    	<!--[if lt IE 9]>
      		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    	<![endif]-->


 <script>
  $(document).ready(function()
       {

			

		//a function that only calls the url in data-url, and does nothing else.
		$('body').on('click','.press_url_element',function() {
//			alert('been clicked');
     			url = $(this).attr('data-url');
     			$.get(url);
			$(this).addClass("btn-success");
		});

       }
  );

  </script>

</head>
<body>

@include('Zermelo::d3graph')

</body>
</html>
