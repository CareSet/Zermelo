<?php
/*
	The whole point of this report is to demonstrate that searches are slower when a given report 
	does not use indexes.. 

	So this report is identical to NorthwindOrderIndexReport except that it does not have any indexes

	Note that you really need to use the inflate function in the MyWind test daataset to really see any usefulness here. 

*/
namespace App\Reports;

class NorthwindOrderSlowReport extends ParentTabularReport
{

    /*
    * Get Indexes for cache table
    * Because results are saved to a cache table, and then exported from there later searching, using the front end... can be very slow
    * This returns an array of Index commands that will be run against the cache table
    * because we do not know the name of the cache table in advance, these index commands must use the string '{{_CACHE_TABLE_}}' instead of 
    * the name of a specific table... 
    */
    public function GetIndexSQL(): ?array {
	return null;
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
	return('Zermelo Demo: Northwind Order Slow Report');
    }

    /*
    * Get the Report Description, can be html
    */
    public function GetReportDescription(): ?string {

		$html = "<p>This report is just like <a href='/Zermelo/NorthwindOrderIndexReport'>Northwind Order Index Report</a> except that it does not use indexes. So for sufficiently large data.. it should be slower.
<br>
Test the following on this report </p>
<ul>
	<li> It should search slower than the indexed version of the report </li>
	<li> The field type for product_list on the backend should be LONGTEXT, assuming that is what the mysql engine continues to do with GROUP_CONCAT created fields </li>
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
FROM MyWind_northwind_data.`order` 
JOIN MyWind_northwind_model.employee ON 
	employee.id =
    	employee_id
JOIN MyWind_northwind_model.customer ON 
	customer.id =
    	customer_id
JOIN MyWind_northwind_data.orderDetail ON 
	orderDetail.order_id =
    	`order`.id
JOIN MyWind_northwind_model.product ON 	
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
