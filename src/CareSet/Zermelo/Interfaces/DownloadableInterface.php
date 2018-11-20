<?php

namespace CareSet\Zermelo\Interfaces;

use CareSet\Zermelo\Models\ZermeloReport;

interface DownloadableInterface
{

    /**
     * @param ZermeloReport $report
     * @return mixed
     */
    public function download( ZermeloReport $report );
}
