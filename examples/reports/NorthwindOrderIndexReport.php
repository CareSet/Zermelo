<?php

namespace App\Reports;

class NorthwindOrderIndexReport extends ParentTabularReport
{

    /*
    * Get Indexes for cache table
    * Because results are saved to a cache table, and then exported from there later searching, using the front end... can be very slow
    * This returns an array of Index commands that will be run against the cache table
    * because we do not know the name of the cache table in advance, these index commands must use the string '{{_CACHE_TABLE_}}' instead of 
    * the name of a specific table... 
    */
    public function GetIndexSQL(): ?array {

		$index_sql = [
//the results of a GROUP_CONCAT are stored by MySQL as LONGTEXT, but that is not convenient to simply index...
//so to start lets convert it to a VARCHAR
"ALTER TABLE {{_CACHE_TABLE_}}  CHANGE `product_list` `product_list` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;",
//lets do the same thing with shipAddress
"ALTER TABLE {{_CACHE_TABLE_}}  CHANGE `shipAddress` `shipAddress` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;",
//which we can then index with a simple index command
"ALTER TABLE {{_CACHE_TABLE_}}  ADD INDEX(`product_list`);",
"ALTER TABLE {{_CACHE_TABLE_}}  ADD INDEX(`shipAddress`);",
"ALTER TABLE {{_CACHE_TABLE_}}  ADD PRIMARY KEY(`order_id`);",
"ALTER TABLE {{_CACHE_TABLE_}}  ADD INDEX(`shipName`);",
"ALTER TABLE {{_CACHE_TABLE_}}  ADD INDEX(`employee_first_name`);",
"ALTER TABLE {{_CACHE_TABLE_}}  ADD INDEX(`employee_last_name`);",
"ALTER TABLE {{_CACHE_TABLE_}}  ADD INDEX(`customer_company`);",
"ALTER TABLE {{_CACHE_TABLE_}}  ADD INDEX(`shipCity`);",
"ALTER TABLE {{_CACHE_TABLE_}}  ADD INDEX(`shipStateProvince`);",
"ALTER TABLE {{_CACHE_TABLE_}}  ADD INDEX(`orderDate`);",
"ALTER TABLE {{_CACHE_TABLE_}}  ADD INDEX( `employee_id`, `customer_id`);",
			];

		return($index_sql);

    }




    /**
    * to really make good use of the indexes, you need to enable the cache
    */
   public function isCacheEnabled(){
        return(true);
   }

    /**
    * to prevent frequent re-indexing...
    */
   public function howLongToCacheInSeconds(){
        return(120); //two minutes by default
   }

 
    /*
    * Get the Report Name
    */
    public function GetReportName(): string {
	return('Zermelo Demo: Northwind Order Index Report');
    }

    /*
    * Get the Report Description, can be html
    */
    public function GetReportDescription(): ?string {

                $html = "<p>
The performance of this report should be pretty zippy for ordering and for searching various fields... because the cache table has been indexed. <br>
Compare this to  <a href='/Zermelo/NorthwindOrderSlowReport'>Northwind Order Slow Report</a> 
which is the same report but without indexes<br>
Test this report for </p>
<ul>
	<li>To make sure that the caching is functioning correctly. It has a very short cache time to facilitate this </li>
	<li>To make sure that cache indexing is working correctly. Confirm that the back-end cache in _zermelo_cache (presumably) is properly indexed </li>
	<li>The index SQL stage can run any SQL, including SQL that changes the field types of the cache, make sure those work </li>
	<li>Do some searches and filters that are designed to test the backend system and make sure that indexing is effective </li>
</ul>
";

	return($html);
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

        	$sql = "
SELECT 
	employee.lastName AS employee_last_name,
	employee.firstName AS employee_first_name,
	customer.companyName AS customer_company,
	orderDate,
    	COUNT(DISTINCT(orderDetail.id)) AS distinct_products_ordered,
    	GROUP_CONCAT(DISTINCT productName) AS product_list,
	shipStateProvince,
	shipCity,
	shipAddress,
	shipName,
	employee.id AS employee_id,
	customer.id AS customer_id, 
	order.id AS order_id
FROM DURC_northwind_data.`order` 
JOIN DURC_northwind_model.employee ON 
	employee.id =
    	employee_id
JOIN DURC_northwind_model.customer ON 
	customer.id =
    	customer_id
JOIN DURC_northwind_data.orderDetail ON 
	orderDetail.order_id =
    	`order`.id
JOIN DURC_northwind_model.product ON 	
	orderDetail.product_id =
    	product.id 
GROUP BY `order`.id
";
		
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

	//look in ParentTabularReport.php for further typical report settings

}
