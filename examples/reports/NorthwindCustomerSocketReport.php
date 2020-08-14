<?php

namespace App\Reports;

class NorthwindCustomerSocketReport extends ParentTabularReport
{

    /*
    * Get the Report Name
    */
    public function GetReportName(): string
    {
	return('Zermelo Demo: NorthWind Customer Socket Report');
    }

    /*
    * Get the Report Description, can return html
    */
    public function GetReportDescription(): ?string
    {

        return "This is the customer database filtered using the socket/wrench system which can be configured 
            using the `Data Options` button on the UI
<br>
What to test here: 
<ul>
	<li> Do you see several data options when you click the 'Data Options' button? </li>
	<li> Choose an option and confirm that it works </li> 
	<li> Choose an option that is impossible to do with just search and/or sort in datatabels and confirm that this works </li>
	<li> Make sure that the data comes back when you turn off the data options </li>
	<li> Make sure that two data options correctly work together </li>
	<li> Confirm that there are seperate caches created for each data option in _zermelo_cache </li>
	<li> Confirm that when you download the csv file, you are downloading with the data options applied </li>
</ul>
";

    }

	/**
    * This is what builds the report. It will accept a SQL statement or an Array of sql statements.
    * Can be used in conjunction with Inputs to determine different output based on URI parameters
    * Additional URI parameters are passed as
    *	$this->getCode() - which will give the first url segment after the report name
    *   $this->getParameters() - which will give an array of every later url segment after the getCode value
    *   $this->getInput() - which will give _GET parameters (etc?)
    **/
    public function GetSQL()
    {

	$customer_id = $this->getCode();


	$filters = [];
	$filters[] = $this->getSocket('job_title_filter');
	$filters[] = $this->getSocket('big_state_filter');

	if(!is_numeric($customer_id)){
		//this means that there was no customer_id passed in on the url...
		//so we have a SQL that will return all of the customers information
        	$sql = "
SELECT 
	customer.id AS customer_id, 
	companyName, 
	lastName, 
	firstName, 
	emailAddress, 
	jobTitle, 
	businessPhone, 
	homePhone, 
	mobilePhone,
	stateProvince
FROM MyWind_northwind_model.customer
";

		$where_then_and = ' WHERE ';

		foreach($filters as $this_filter){
			if($this_filter){ //blank  strings will not be used, etc
				$sql .= " $where_then_and $this_filter"; 	
			
				$where_then_and = "\n AND "; //we only need the WHERE on the first filter, but we need AND after that
			}
		}

	
	}else{
		//here we know that $customer_id is numeric, and we should search the database for a mathing customer
        $sql = "
SELECT
	customer.id AS customer_id, 
	companyName, 
	lastName, 
	firstName, 
	emailAddress, 
	jobTitle, 
	businessPhone, 
	homePhone, 
	mobilePhone,
	stateProvince
FROM MyWind_northwind_model.customer
WHERE customer.id = '$customer_id'
";

	}

	$is_debug = false;

	if($is_debug){
		echo "<pre> $sql";
		exit();
	}

    	return $sql;
    }


    /**
    * Each row content will be passed to MapRow.
    * Values and header names can be changed.
    * Columns cannot be added or removed
    *
    */
    public function MapRow(array $row, int $row_number) :array
    {

    	/*
		//this logic would ensure that every cell in the TABLE_NAME column, was converted to a link to
		//a table drilldown report
		$table_name = $row['TABLE_NAME'];
		$row[''] = "<a href='/Zermelo/TableDrillDownReport/$table_name/'>$table_name</a>";

	*/

        return $row;
    }

	//look in ParentTabularReport.php for more settings


}
