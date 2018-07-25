<?php

namespace CareSet\Zermelo\Interfaces;

use CareSet\Zermelo\Models\ZermeloReport;

interface ControllerInterface
{

    /**
     * @param ZermeloReport $report
     * @return mixed
     */
    public function show( ZermeloReport $report );

    public function prefix() : string;
}
