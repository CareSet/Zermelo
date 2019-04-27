<?php

namespace App\Reports;

class NorthwindOrderReport extends ParentTabularReport
{

    /*
    * Get the Report Name
    */
    public function GetReportName(): string {
	return('Zermelo Demo: Northwind Order Report');
    }

    /*
    * Get the Report Description, can be html
    */
    public function GetReportDescription(): ?string {

	//here is a good place to make bootstrap forms https://bootstrapformbuilder.com/
	$bootstrap_html_form = "
<p>
This report demonstrates how you can add bootstrap HTML forms to your reports, and how to work with the form data inside the report. <br>
Using this report, you can specify a start and end date and limit the orders to those dates.
<br>
Use this report to test for: 
</p>
<ul>
	<li> Make sure that a GET Form an communicate with the backed report class </li>
	<li> Make sure that the backend creates a different cache for the GET search results </li>
	<li> Make sure that the results of the GET form can be downloaded in its limited form (i.e. not the whole data report) </li>
	 
</ul>

<form method='GET'>
  <div class='form-group row'>
    <label for='start_date' class='col-4 col-form-label'>Starting Date</label>
    <div class='col-8'>
      <div class='input-group'>
        <div class='input-group-prepend'>
          <div class='input-group-text'>
            <i class='fa fa-calendar-minus-o'></i>
          </div>
        </div>
        <input id='start_date' name='start_date' type='date' class='form-control' required='required'>
      </div>
    </div>
  </div>
  <div class='form-group row'>
    <label for='text' class='col-4 col-form-label'>Ending Date</label>
    <div class='col-8'>
      <div class='input-group'>
        <div class='input-group-prepend'>
          <div class='input-group-text'>
            <i class='fa fa-calendar-plus-o'></i>
          </div>
        </div>
        <input id='end_date' name='end_date' type='date' class='form-control' required='required'>
      </div>
    </div>
  </div>
  <div class='form-group row'>
    <div class='offset-4 col-8'>
      <button name='submit' type='submit' value='true' class='btn btn-primary'>Submit</button>
    </div>
  </div>
</form>
";

	return($bootstrap_html_form);

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

	$start_date = $this->getInput('start_date',false);
	$end_date = $this->getInput('end_date',false);

	$start_date_sql = date("Y-m-d", strtotime($start_date));
	$end_date_sql = date("Y-m-d", strtotime($end_date));

	if($start_date && $end_date){
        	$sql = "
SELECT 
	employee.lastName AS employee_last_name,
	employee.firstName AS employee_first_name, 
	customer.companyName AS customer_company,
	orderDate,
    	COUNT(DISTINCT(orderDetail.id)) AS distinct_products_ordered,
    	GROUP_CONCAT(DISTINCT productName) AS product_list
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
WHERE orderDate > '$start_date_sql' AND orderDate < '$end_date_sql'
GROUP BY `order`.id
";
		
	}else{

        	$sql = "
SELECT 
	employee.lastName AS employee_last_name,
	employee.firstName AS employee_first_name, 
	customer.companyName AS customer_company,
	orderDate,
    	COUNT(DISTINCT(orderDetail.id)) AS distinct_products_ordered,
    	GROUP_CONCAT(DISTINCT productName) AS product_list
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

	//look in ParentTabularReport.php for further typical report settings

}
