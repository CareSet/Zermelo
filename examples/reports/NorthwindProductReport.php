<?php

namespace App\Reports;

class NorthwindProductReport extends ParentTabularReport
{

    /*
    * Get the Report Name
    */
    public function GetReportName(): string {
	return('Zermelo Demo: Northwind Product Report');
    }

    /*
    * Get the Report Description, can be html
    */
    public function GetReportDescription(): ?string {
    	$html = "<p>
The list of all northwind products.
<br>
List of things to test:</p>
<ul>
	<li>Currently, not very much, but soon, we will use this report to test the auto-column summary features of Zermelo </li>
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
        $sql = "SELECT productCode, productName, standardCost, listPrice
                FROM MyWind_northwind_model.product";
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
