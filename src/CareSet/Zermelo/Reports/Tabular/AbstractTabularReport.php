<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 1/14/19
 * Time: 9:51 AM
 */
namespace CareSet\Zermelo\Reports\Tabular;

use CareSet\Zermelo\Models\ZermeloReport;

abstract class AbstractTabularReport extends ZermeloReport
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
     * setDefaultSortOrder
     * This will set an input value unless one has already been set, allows report to define things like default sorts (etc)
     * But if the user changes things, it will be allowed to override.
     * @return void
     */
    public function setDefaultSortOrder($column, $direction)
    {
        // Make sure that there is an order component of the input,
        // if not, just ignore
        if ( isset( $this->_input['order'] ) ) {

            // Check to see if we already have this column set
            // by the user from the UI
            $alreadySet = false;
            foreach ( $this->_input[ 'order' ] as $order ) {
                if ( isset( $order[$column] ) ) {
                    $alreadySet = true;
                    break;
                }
            }

            // If the column isn't set by the UI, then impose our will (the default behavior)
            if ( !$alreadySet ) {
                array_push( $this->_input[ 'order' ], [ $column => $direction ] );
            }
        }
    }
}
