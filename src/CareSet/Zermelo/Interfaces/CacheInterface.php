<?php

namespace CareSet\Zermelo\Interfaces;


use CareSet\Zermelo\Models\ZermeloReport;

interface CacheInterface
{

    /* TODO this is closer to what cache interface should be
     *     public function exists ($key);
    public function set ($key, $value, $expire);
    public function get ($key);
    public function delete ($key);
    public function clear ();
    public function expire ($key);
    public function reload ();

     */

    /**
     * THis should be moved ???
     */
    public function getCacheDB();
    /**
     * THis should be moved ???
     */
    public function getCacheTableStub();

    public function init( ZermeloReport $report ) : bool;

    public function exists() : bool;

    public function isCacheable() : bool;

    /**
     * CachedReport
     * This takes a ZermeloReport and create a cache table inside $cache_table with the result.
     *
     * @param string $cache_table
     * @param ZermeloReport $Report
     * @return bool
     */
    public function CacheReport( ZermeloReport $Report ): bool;


    /**
     * CheckUpdateCacheForReport
     * Determine if a report should be updated based on the file being updated or the cache timeout being expired
     *
     * @param ZermeloReport $Report
     * @return bool
     */
    public function CheckUpdateCacheForReport( ZermeloReport $Report ): bool;
}
