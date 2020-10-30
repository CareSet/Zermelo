<?php
/**
 * This is the base report class for all report types.
 * Each report sub-class has it's own file in Zermelo/Reports
 * such as Zermelo/Reports/Tabular/AbstractTabularReport.php
 * This file contains functionality that pertains to all report
 * types, where the more specific report sub-classes contain
 * functionality specific to their usage.
 *
 */
namespace CareSet\Zermelo\Models;
use CareSet\Zermelo\Interfaces\ZermeloReportInterface;
use CareSet\Zermelo\Services\SocketService;
use Mockery\Exception;
use \Request;

abstract class ZermeloReport implements ZermeloReportInterface
{
	/**
	 * $_code
	 * Holds first optional parameter after the URI
	 * This is to help with selecting different groups or options
	 * Example: /Zermelo/ExampleReport/MyGroupCode
	 *
	 * @var string
	 */
	private $_code = null;

	/**
	 * $_parameters
	 * Holds additional optional paramters after the URI, after the $_code
	 * Example: /Zermelo/ExampleReport/MyGroupCode/MyOption1/MyOption2/MyOption3
	 *
	 * @var array
	 */
	private $_parameters = [];

	/**
	 * $_input
	 * Where GET and POST parameter submitted to the main display VIEW will be stored
	 *
	 * @var array
	 */
	protected $_input = [];

    	/**
     	 * @var array
	 * This is where custom view varialbes are stored, can access these varialbe on the view without having
	 * to pass them through the API
     	 */
	protected $_view_variables = [];

    	/**
     	 * @var null
	 *
	 * User remember token passed to view
     	*/
	protected $_token = null;

	/**
	 * @var bool
	 *
	 * Specify whether the cache is enabled, or not. NOTE: Reports are always
	 * run from the "cache" but disabling this will regenerate the cache on each load,
	 * and not retain the result of the report query in the cache table
	 */
	private $_isCacheEnabled = false; //cache is always off by default.

	/**
	 * @var null
	 *
	 * When cache is enabled, how long to retain the results of the report query in the cache table
	 */
	private $_howLongToCacheInSeconds = null;

	private $_socketService = null;

	private $_activeWrenches = []; // Array wrenches that are "in use" for this report
	private $_activeSockets = []; // Array of sockets that are "currently selected" for the active wrenches

    	/**
     	 * Should we enable the cache on this table?
     	 * This will improve the performance of very large and complex queries by only running the SQL once and then storing
     	 * the results in a dynamically creqted table in the _cache database.
     	 * But it also creates hard to debug update errors that are very confusing when changing GetSQL() contents.
     	 */
    	protected $CACHE_ENABLED = true;


    	/**
      	 * How much time should pass (in seconds) before you update your _cache table for this report?
     	 * this only has an effect when isCacheEnabled is turned on.
     	 */
    	protected $HOW_LONG_TO_CACHE_IN_SECONDS = 600;

		/**
		 * @var bool
		 *
		 * This enables the sql print feature for this report, which enables the route
		 * for printing the SQL that generates a report. There is also a global
		 * configuration zermelo.SQL_PRINT_ENABLE in the zeremelo config file which also
		 * needs to be enabled FIRST if you want to enable on the report level.
		 */
    	protected $SQL_PRINT_ENABLED = false;

    	/**
     	 * $INDICIES
     	 * The system will attempt to create an index out of these columns when creating the cache
     	 *
     	 * @var array
     	 */
    	public $INDICIES = [];

	/**
	 * __construct
	 * Model will not attempt to parse URI or Request input
	 * This is up to the controller or pre-processing step to allow this
	 * model to be flexible.
	 * IMPORTANT: Code and Parameters are used to determine cache while $Input is NOT.
	 * a model with $Code = "Code", $Parameters = ["A","B"] will be cache separately then $Code="Code",$Paramters = ["A","C"]
	 * while $Input has no impact on caching
	 *
	 * @param string $Code - Optional parameter that can be used with model, usally through URI input
	 * @param array $Parameters - Optional additional parameters that can be used with model, usually through URI input
	 * @param array $Input - Additional optional parameters, usually through Request type input
	 * @return void
	 */
	public function __construct(?string $Code, array $Parameters = [], array $Input = [])
	{
		$this->_code = $Code;
		$this->_parameters = $Parameters;
		$this->_input = $Input;
		$this->setIsCacheEnabled( $this->CACHE_ENABLED );
		$this->setHowLongToCacheInSeconds( $this->HOW_LONG_TO_CACHE_IN_SECONDS );

		//the only way to communicate sockets is by indentifying them in the form input..
		//which means that if they are not here in the Input, then they should not exist...
        	$socketService = new SocketService();
        	if ( isset($Input['sockets']) ) {
            		$socketService->setSocketsFromApiInput( $Input[ 'sockets' ] );
        	}


		$this->_socketService = $socketService;
	}

	public function pushViewVariable($key, $value)
	{
		$this->_view_variables[$key] = $value;
	}

	public function getViewVariables()
	{
		return $this->_view_variables;
	}

    	public function getToken()
    	{
        	return $this->_token;
    	}

    	public function setToken( $token )
    	{
        	$this->_token = $token;
    	}


    	/**
     	 * @param string|null $wrenchName
	 *
	 * Get all of the sockets and their labels for a given wrenchName
	 *
	 * The last setting is saved before we get here in the ReportBuilder
     	 */
	public function getAllSockets( string $wrenchName = null )
	{
		$sockets = null;
		if ( $wrenchName ) {

            		// Get the user selected socket for this wrench
            		$sockets = $this->_socketService->fetchAllSocketsForWrenchKey( $wrenchName );

            		if ( $sockets === null ) {
                		throw new Exception("Zermelo SocketWrench Error: getAllSockets: No sockets for wrench name $wrenchName");
            		}

        	} else {
			throw new Exception("Zermelo SocketWrench Error: getAllSockets:  No wrench name provided");
		}

        	return $sockets;
	}
    	/**
     	 * @param string|null $wrenchName
	 *
	 * Get the user-selected socket for this wrench, or the default
	 * if a user_socket is not set.
	 *
	 * The last setting is saved before we get here in the ReportBuilder
     	 */
	public function getSocket( string $wrenchName = null )
	{
		$socket = null;
		if ( $wrenchName ) {

            		// Get the user selected socket for this wrench
            		$socket = $this->_socketService->fetchSocketForWrenchKey( $wrenchName );

            		if ( $socket === null ) {
                		throw new Exception("Zermelo SocketWrench Error: No socket for wrench name $wrenchName");
            		}

            		$this->_activeSockets[$socket->id]= $socket;

            		// Then, make sure we make socket options available to view
            		if ( $socket->wrench ) {
                		$this->_activeWrenches[$socket->wrench->id] = $socket->wrench;
            		}

        	} else {
			throw new Exception("Zermelo SocketWrench Error: getSocket function called, but No wrench name provided");
		}

        	return $socket->socket_value;
	}

	public function isActiveSocket($id)
	{
		$active = false;
		if (isset($this->_activeSockets[$id])) {
			$active = true;
		}

		return $active;
	}

	public function getActiveWrenches()
	{
		return $this->_activeWrenches;
	}

	/**
	*	will return true if there are wrenches that have been configured for this report, false if not
	*/
	public function hasActiveWrenches(): bool {
		if(count($this->_activeWrenches) > 0){
			return(true);
		}else{
			return(false);
		}
	}


    	/**
     	 * Get the URI key for the resource.
     	 * @return string
     	 */
    	public static function uriKey()
    	{
    		return class_basename(get_called_class());
        	// return Str::plural(Str::snake(class_basename(get_called_class()), '-'));
    	}

		/**
		 * @return bool
		 *
		 * Return true if the print function is enabled both in the config file AND
		 * in the child report, return false OW
		 *
		 * There is only a getter because this will be set by an attribute by the same
		 * name in the child class, and we also check the configuration in zermelo config
		 */
		public function isSQLPrintEnabled(): bool
		{
			if (config('zermelo.SQL_PRINT_ENABLED', false)) {
				return $this->SQL_PRINT_ENABLED;
			}

			return false;
		}

    	/**
     	 * Should we enable the cache on this table?
     	 * This will improve the performance of very large and complex queries by only running the SQL once and then storing
     	 * the results in a dynamically creqted table in the _cache database.
     	 * But it also creates hard to debug update errors that are very confusing when changing GetSQL() contents.
         * By default, we set this to false in the template. We expect this function to be overridden in most implementing classes
	 * TODO Should this be an abstract function to ensure that debugging is possible?
     	 */
    	public function isCacheEnabled()
	{
        	return $this->_isCacheEnabled;
    	}
	/**
	 * Turn the cache on or off.
	 *
	 * @param boolean $isCacheEnabled whether this should be on or off...
	 */
    	public function setIsCacheEnabled( $isCacheEnabled = false)
	{
		$this->_isCacheEnabled = $isCacheEnabled;
	}


    	/**
     	 * How much time should pass (in seconds) before you update your _cache table for this report?
     	 * this only has an effect when isCacheEnabled is turned on.
     	 */
    	public function howLongToCacheInSeconds()
	{
        	return $this->_howLongToCacheInSeconds; //ten minutes by default
    	}

    	/**
	 * @param null $howLongToCacheInSeconds
	 */
    	public function setHowLongToCacheInSeconds( $howLongToCacheInSeconds )
    	{
        	$this->_howLongToCacheInSeconds = $howLongToCacheInSeconds;
    	}

	/**
	 * getCode
	 * Retrieve code parameter used by model
	 *
	 * @return string
	 */
	public function getCode(): ?string
	{
		return $this->_code;
	}


	/**
	 * getNumericCode
	 * This is a convenience wrapper to getCode()
	 * Frequently you want to get the code if an only if it is numeric...
         * This function simplfies this, and returns an number if the argument is numeric, but returns false if it is not numeric
	 *
	 * @return int or boolean
	 */
	public function getNumericCode()
	{
		if(is_numeric($this->_code)){
			return $this->_code;
		}else{
			return false;
		}
	}



    	/**
     	 * @param $key
     	 * @return bool|mixed
	 *
	 * Get value of an input parameter by key, return false OW
     	 */
	public function getParameter( $key )
	{
		if ( isset( $this->_parameters[$key] ) ) {
            		return $this->_parameters[ $key ];
        	}

        	return false;
	}

	/**
	 * getParameters
	 * Retrieve optional parameters used by model
	 *
	 * @return void
	 */
	public function getParameters(): ?array
	{
		return $this->_parameters;
	}


	/**
	 * setInput
 	 * a useful but dangerous function that allows for specific reports to override the input that comes from a user before it is used.
	 * if you want to set the input to a default, without overriding the user input (which this does, then look at setDefaultInput
         * or the setDefaultSortOrder command... setDefaultSortOrder understands how to sort by more than one thing!!!
	 * @return void
	 */
	public function setInput($key, $new_value)
	{
		$this->_input[$key] = $new_value;
		return(true);
	}

	/**
	 * setInput
 	 * a useful but dangerous function that allows for specific reports to override the input that comes from a user before it is used.
	 * if you want to set the input to a default, without overriding the user input (which this does, then look at setDefaultInput
	 * @return void
	 */
	public function setDefaultInput($key, $new_value)
	{

		if(isset($this_input[$key])){
			//then do nothing.. the value has already been set...
		}else{
			$this->_input[$key] = $new_value;
		}
		return(true);
	}

	/**
	 * setDefaultSortOrder( array $sort_array )
	 *
	 * @param array $sort_array - an array of arrays indicating order of default sorting
	 *
	 * This accepts an array of [column => 'asc'/'desc'] values... and will then call setDefaultInput to put into the right place...
	 * for example $sort_array = [[ 'order_count' => 'desc'],['name' => 'desc']]
	 * would return a list that is ordered with the most orders at the top, and when the order count is the same,
	 * it would alphabetize on name after that. We use an array of arrays as the parameter type here rather than
	 * an associative array in order to guarantee that the indicies persist and we get the actual order we want.
	 *
	 * @return void
	 */
	public function setDefaultSortOrder(array $sort_array)
	{
		// Set this variable, which gets passed to initial UI for initial default ordering
		$this->pushViewVariable('default_sort_order', $sort_array);

		// Get the sort order, if any, which can come from the jQuery DataTable
		$current_sort_order = $this->getInput('order') ?? [];

		// Check to see if we already have these default columns set
		// by the user from the UI, and only add to the sort if it's not already set
		// The default sort_order comes in as an array of arrays, like this:
		// [ [column1 => direction], [column2 => direction] ]
		$defaults_sort_array = [];
		foreach ($sort_array as $sort_pair) {
			if (is_array($sort_pair)) {
				foreach ($sort_pair as $sort_array_column => $sort_array_dir) {
					$found = false;
					foreach ($current_sort_order as $order) {
						// Check to see if this colum is already being sorted on
						if (isset($order[$sort_array_column])) {
							$found = true;
						}
					}

					if (!$found) {
						// We didn't find this sort column already found, so we can add it to our default sort array
						// If we don't find it, we don't add it because we don't want to override UI input
						$defaults_sort_array[] = [$sort_array_column => $sort_array_dir];
					}
				}
			} else {
				throw new \Exception("Check the format of the parameters on your call to setDefaultSortOrder()");
			}
		}

		// Merge our current sort order, with the existing. We've already made sure that the sort columns are unique
		return $this->setDefaultInput('order', array_merge($current_sort_order, $defaults_sort_array));
	}

	/**
	 * getInput
	 * Retrieve optional inputs used by model
	 *
	 * @return void
	 */
	public function getInput($key = null)
	{
		$inputs = $this->_input;
		if($key!==null)
		{
			if(isset($inputs[$key])){
				return $inputs[$key];
			}else{
				return null;
			}
		}
		return $this->_input;
	}

    /**
     * @return array
	 *
	 * Merge the code with parameters
     */
	public function getMergedParameters()
	{
		$merged =  array_merge([$this->getCode()], $this->getParameters());
		return $merged;
	}

	/**
	 * GetSQL
	 * SQL statement to build the model based on optional $Code,$Parameters,$Input
	 * This will accept a single string SQL statement or an array of SQL statements
	 *
	 * @return array/string
	 */
	public function GetSQL()
	{
                //this function should have been overridden by a child class (i.e. a specific report) if this is being called
                //then the child report is missing the GetSQL() function, which is one of the few required functions.
		$this_class = get_class($this);
                $error = "The requested Zermelo Report class ($this_class)  exists, but does not have the GetSQL() function defined, this is a required function";
                throw new Exception($error);
	}
	/**
	 * MapRow
	 * When displaying to the tabular view,
	 * Model can chose to modify the content of each row cell.
	 * NOTE: Header name CAN be changed, but columns cannot be added or removed
	 *
	 * @param array $row
	 * @return void
	 */
	public function MapRow(array $row, int $row_number)
	{
		return $row;
	}

	/**
	 * OverrideHeader
	 * Override a default column format or add additional column tag to be sent back to the front end
	 * This returns the value as a reference parameter
	 *
	 * @param array &$format
	 * @param array &$tags
	 * @return void
	 */
	public function OverrideHeader(array &$format, array &$tags): void
	{
		//doing nothing is the default
	}

	/**
	 * GetReportName
	 * Return the name of the report,
         * This function must be defined in the called class
	 *
	 * @return void
	 */
	public function GetReportName(): string
	{
                //this function should have been overridden by a child class (i.e. a specific report) if this is being called
                //then the child report is missing the GetReportName() function, which is one of the few required functions.
                $error = "The requested Zermelo Report class exists, but does not have the GetReportName() function defined, this is a required function";
                throw new Exception($error);
	}
	/**
	 * GetReportDescription
	 * Return the description of the report,
	 * By default, this will return the const $DESCRIPTION.
	 * This function can be used to change the report description based on $code,$parameters,$input
	 * This supports returning HTML
	 *
	 * @return string
	 */
	public function GetReportDescription(): ?string
	{
                //this function should have been overridden by a child class (i.e. a specific report) if this is being called
                //then the child report is missing the GetReportDescription() function, which is one of the few required functions.
                $error = "The requested Zermelo Report class exists, but does not have the GetReportDescription() function defined, this is a required function";
                throw new Exception($error);
	}

    	/**
     	 * @return null|string
	 *
	 * Return the footer of the report. This string will be placed in the footer element
     	 * at the bottom of the page.
     	 */
	public function GetReportFooter(): ?string
	{
		$footer = "<!-- placeholder for Zermelo Report Footer HTML -->";

		return $footer;
	}

    	/**
     	 * @return null|string
	 *
	 * Add a string here to put in the class of the footer element of the report layout
     	 */
	public function GetReportFooterClass(): ?string
	{
		// Add "fixed centered" to have your footer fixed to the bottom, and/or centered
		// This will be put in the class= attribute of the footer
		return "";
	}

    	/**
     	 * @return null|string
	 *
	 * This will place the enclosed Javascript in a <script> tag just before
	 * the body of your view. Note, there is no need to include a script tag
	 * in this string. The content of this string is not HTML encoded, and is passed
	 * raw to the view.
     	 */
	public function GetReportJS(): ?string
	{
		$javascript = <<<JS
			//alert("placeholder for report javascript");
JS;
		return $javascript;
	}


	public function setRequestFormInput( $request_form_input )
	{
		$this->_request_form_input = $request_form_input;
	}

    	/**
     	 * @param bool $json
      	 * @return array|string
	 *
	 * If json is true, return JSON, otherwise return parameter string
     	 */
	public function getRequestFormInput( $json = true )
	{
		if ( $json ) {
			return json_encode($this->_input);
		}

		return http_build_query( $this->_input );
	}



	/**
	 * GetClassName
	 * Return the Report Class Name
	 *
	 * @return void
	 */
	public function GetClassName(): string
	{
		return substr(strrchr(get_class($this), '\\'), 1);
	}

	/**
	 * getFileLocation
	 * Returns the actual path to the report file.
	 * This is used to determine the modify date to see if cache needs to be updated
	 *
	 * @return string
	 */
	public function getFileLocation(): string
	{
		$reflector = new \ReflectionClass(get_class($this));
		$fn = $reflector->getFileName();
		return $fn;
	}


	/**
	 * optional code that can serve to index the cache table.
         * SQL must use the string {{_CACHE_TABLE_}} in place of the cache table in the index commands
         * it returns either null or an array of index SQL commands that can be run against the index table.
	 *
	 * @return array of SQL templates or false
	 */
	public function GetIndexSQL(): ?array
	{
		//by default this function returns null, which causes the cache indexer to do nothing...
		return null;
	}

	/**
	 * This is a convience function that is intended to make it easier to quote database parameters
	 * That originate from the user.
	 *
	 * @return string of escaped SQL
	 */
	public function quote($quote_me): ?string
	{

		return \DB::connection()->getPdo()->quote($quote_me);

	}




	/**
	 * Return a unique identifier for a given SQL command
	 * This function gets string that indicates the current state of the SQL, without considering things that could
	 * be temporarily modifying what is shown on the screen, (like order and filter commands from datatables)
         * This function should be used to generate the name of the cache table, as well as data download functionality..
         * Basically, this determines when a specific data request is different.
         * There are lots of inputs to the system that might be considered..
         * But if they do not change the SQL from GetSQL() in the end they do not matter
         * So we actually use an md5 on the SQL from the report to make the key.
         * If the SQL changes, then the inputs matter enough to be cached in a different table
         * And if the SQL output does not change, then it is really the same cache..
	 *
	 * @param string $prefix a short prefix to put ahead of the md5 when we make the identifier
	 **/
	public function getDataIdentityKey($prefix = ''): string{

		// If the prefix is greater than 31 characters, shorten it to 31
		$shortenedPrefix = substr( $prefix, 0, min( strlen( $prefix ), 31 ) );

        // Get the report key, can be a maximum of 64 chars
        //   md5 = 32
        // + "_" = 1
        // + min( ReportClassName, 31 )
        // = 64

        //when any of the following strings change then it really is a different
        //ending data output... which means it needs to have a different data cache...
                $sql = $this->GetSQL();
                if(!is_array($sql)){
                        $sql = [$sql]; //make it an array..
                }
                $identity_string =      $this->getClassName() . '-' .
                                        $this->getCode() . '-' . //note that we do this just to make table and directory listings easier to read.. the data is fully captured in the SQL
                                        implode('-',$sql);	//which is why we do not need to add paramaters (etc) to this identity function...

        //lets make this into something short that can be used to make a good table name in the cache.
        	$key = $shortenedPrefix."_".md5($identity_string);
        	return $key;

	}



	/**
	 * 	Function to that provides custom Code/Input/Form to run a test report
         *	This function must either return false, or an array with the parameters to call the constructor for the report!!
	 *
	 * 	@return must return either boolean false or an array of tests where each test is formed with 'Code' => string, 'Parameters' => [], 'Input' => [])
	 */
	public static function testMeWithThis(){


		$return_structure = [
			'Code' => 'first_uri_arg',
			'Parameters' => ['second_uri_arg', 'third_uri_arg', 'and_so_on'],
			'Input' => ['GET_variable_one' => 'GET_value_one','GET_variable_two' => 'GET_value_two','POST_variable_one' => 'POST_value_one','POST_and_so_on' => 'POST_and_so_forth'],
			];


		$all_the_tests = []; //we can have many tests..
		$all_the_tests[] = $return_structure;

		//return($return_structure); //its just an example...

		return(false); //unless the child report cares enough to implement the testMeWithThis class


	}

}

?>
