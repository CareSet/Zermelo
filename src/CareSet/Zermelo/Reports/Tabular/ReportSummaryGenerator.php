<?php

namespace CareSet\Zermelo\Reports\Tabular;

use CareSet\Zermelo\Interfaces\CacheInterface;
use CareSet\Zermelo\Interfaces\GeneratorInterface;
use CareSet\Zermelo\Models\ZermeloReport;
use CareSet\Zermelo\Exceptions\InvalidDatabaseTableException;
use CareSet\Zermelo\Exceptions\InvalidHeaderFormatException;
use CareSet\Zermelo\Exceptions\InvalidHeaderTagException;
use CareSet\Zermelo\Exceptions\UnexpectedHeaderException;
use CareSet\Zermelo\Exceptions\UnexpectedMapRowException;
use \DB;

class ReportSummaryGenerator extends ReportGenerator implements GeneratorInterface
{

    public function toJson()
    {
        return [
            'Report_Name' => $this->cache->getReport()->GetReportName(),
            'Report_Description' => $this->cache->getReport()->GetReportDescription(),
            'selected-data-option' => $this->cache->getReport()->getParameter( 'data-option' ),
            'columns' => $this->runSummary(),
            'cache_meta_generated_this_request' => $this->cache->getGeneratedThisRequest(),
            'cache_meta_last_generated' => $this->cache->getLastGenerated(),
            'cache_meta_expire_time' => $this->cache->getExpireTime(),
            'cache_meta_cache_enabled' => $this->cache->getReport()->isCacheEnabled()
        ];
    }

    public function runSummary()
    {
        return $this->getHeader(true);
    }
}
