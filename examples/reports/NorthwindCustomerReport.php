<?php

namespace App\Reports;

class NorthwindCustomerReport extends ParentTabularReport
{

    /*
    * Get the Report Name
    */
    public function GetReportName(): string
    {
	return('Zermelo Demo: NorthWind Customer Report');
    }

    /*
    * Get the Report Description, can return html
    */
    public function GetReportDescription(): ?string
    {
	$customer_id = $this->getCode();

	if(!is_numeric($customer_id)){
		//this means that there was no customer_id passed in on the url...
		//so we have a SQL that will return all of the customers information
$html = "
<p> This is a basic list of all Northwind Customers. <br>
On this interface test:</p>
<ul>
	<li> There is an initial sort on this report, which means that it should start with company Z and go backwards down the alphabet (DESC sort) </li>
	<li> The field search functions should work. For instance, type something in the search box about 'LastName' and make sure the results are correct </li>
	<li> Assuming you are using the randomized data, there is no relationship between the fields... so that you can search for different name in the email field, for instance </li>
	<li>Test the print view </li>
	<li>Test that the print view only has filtered rows </li>
	<li> Test that sorting works on all the column types (numbers, words and dates at least) </li>
	<li> TEst that you can hide and unhide columns </li>
	<li> test that column hiding stays on when the print view is used </li>
	<li> Test that maximizing and minimizing the description (i.e. this list goes away and then comes back) works correctly </li>

</ul>
";
		return($html);
	}else{
		//we have only one customer here... so we will only see one customer.
		//we need to give users a way to get back to the list of all customers...
		$html = "
<p>This is a filtered report, showing just one NorthWind Customer</p>
<a class='btn btn-primary btn-small' href='/Zermelo/NorthwindCustomerReport/' role='button'>Show All Customers</a>
<br><br>
";
		return($html);
	}

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
	mobilePhone
FROM DURC_northwind_model.customer
";

		//lets order this report by companyName to start:
		//this nessecary, instead of an ORDER BY on the SQL
		//because the ORDER BY will impact the SQL -> cache table
		//but this controls the cache table -> front end connection
        $this->setDefaultSortOrder([['companyName' =>'desc']]);


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
	mobilePhone
FROM DURC_northwind_model.customer
WHERE customer.id = '$customer_id'
";

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

	//look for the other typical report settings in ParentTabularReport.php

}
