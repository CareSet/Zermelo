<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 1/14/19
 * Time: 9:51 AM
 */
namespace CareSet\Zermelo\Reports\Cards;

use CareSet\Zermelo\Models\ZermeloReport;

abstract class AbstractCardsReport extends ZermeloReport
{

    /**
     * $VALID_COLUMN_FORMAT
     * Valid Format a column header can be. This is used to validate OverrideHeader
     *
     * @var array
     */
    public $VALID_COLUMN_FORMAT = ['TEXT','DETAIL','URL','CURRENCY','NUMBER','DECIMAL','DATE','DATETIME','TIME','PERCENT'];


    /**
     * $DETAIL
     * Header stub that will determine if a header is a 'SENTENCE' format
     *
     * @var array
     */
    public $DETAIL     = ['Sentence'];

    /**
     * $URL
     * Header stub that will determine if a header is a 'URL' format
     *
     * @var array
     */
    public $URL        = ['URL'];

    /**
     * $CURRENCY
     * Header stub that will determine if a header is a 'CURRENCY' format
     *
     * @var array
     */
    public $CURRENCY   = ['Amt','Amount','Paid','Cost'];

    /**
     * $NUMBER
     * Header stub that will determine if a header is a 'NUMBER' format
     *
     * @var array
     */
    public $NUMBER     = ['id','#','Num','Sum','Total','Cnt','Count'];

    /**
     * $DECIMAL
     * Header stub that will determine if a header is a 'DECIMAL' format
     *
     * @var array
     */
    public $DECIMAL    = ['Avg','Average'];

    /**
     * $PERCENT
     * Header stub that will determine if a header is a 'PERCENTAGE' format
     *
     * @var array
     */
    public $PERCENT    = ['Percent','Ratio','Perentage'];


    /**
     * $SUGGEST_NO_SUMMARY
     * This will mark the column that should not be used for statistical summary.
     * Any column found with a a 'NO_SUMMARY' flag attached to its column header
     *
     * @var array
     */
    public $SUGGEST_NO_SUMMARY = [];

    /**
     * Each row content will be passed to MapRow.
     * Values and header names can be changed.
     * Columns cannot be added or removed
     *
     */
    public function MapRow(array $row, int $row_number) :array
    {
        return $row;
    }
}
