<?php

namespace App\Reports;

class NorthwindOrderReport extends ParentTabularReport
{

    /*
    * Get the Report Name
    */
    public function GetReportName(): string {
	return('Zermelo Demo: Northwind Orders Report');
    }

    /*
    * Get the Report Description, can be html
    */
    public function GetReportDescription(): ?string {
	return('Description of Northwind Orders Report');
    }

	/**
    * This is what builds the report. It will accept a SQL statement or an Array of sql statements.
    * Can be used in conjunction with Inputs to determine different output based on URI parameters
    * Additional URI parameters are passed as
    *	$this->getCode() - which will give the first url segment after the report name
    *   $this->getParameters() - which will give an array of every later url segment after the getCode value
    *   $this->getInputs() - which will give _GET parameters (etc?)
    **/
    public function GetSQL()
    {
        $sql = "
SELECT 
	order_id, 
	product_id, 
	companyName AS company_name,
	shipName, 
	shipCity,
	customer_id 
FROM northwind_data.order
JOIN northwind_data.orderDetail ON 
	orderDetail.order_id = 
	order.id
JOIN northwind_model.customer ON 
	customer.id =
	order.customer_id
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
		//this logic would ensure that every cell in the TABLE_NAME column, was converted to a link to
		//a table drilldown report
		$customer_id = $row['customer_id'];
		$company_name = $row['company_name'];
		$row['company_name'] = "<a href='/Zermelo/NorthwindCustomerReport/$customer_id/'>$company_name</a>";

        return $row;
    }

	//look in ParentTabularReport.php for more of the typical layout settings

}
