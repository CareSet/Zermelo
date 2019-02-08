<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 1/14/19
 * Time: 9:51 AM
 */
namespace CareSet\Zermelo\Reports\Graph;

use CareSet\Zermelo\Models\ZermeloReport;

class AbstractGraphReport extends ZermeloReport
{
    /**
     * $SUBJECTS
     * What the engine should consider as the 'noun' or 'subject'.
     * This field will determine which field to be used as nodes on a graph
     *
     * @var array
     */
    public $SUBJECTS = [];

    /**
     * $WEIGHTS
     * What the engine should consider the weight between the subjects.
     * This field is used to generate 'links' and link size between each nodes.
     *
     * @var array
     */
    public $WEIGHTS = [];
}
