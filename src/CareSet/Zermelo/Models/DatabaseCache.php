<?php

namespace CareSet\Zermelo\Models;

use CareSet\Zermelo\Generators\ReportGenerator;
use CareSet\Zermelo\Interfaces\CacheInterface;
use CareSet\Zermelo\Models\ZermeloReport;
use Illuminate\Support\Facades\DB;

class DatabaseCache implements CacheInterface
{
    protected $cache_table_stub = null;
    protected $cache_table = null;
    protected $cache_db = null;
    protected $cacheable = false;
    protected $exists = false;

    public function init( ZermeloReport $Report ): bool
    {
        $report_name = trim($Report->getClassName());
        $Parameters = $Report->getParameters();
        $Code = $Report->getCode();

        $cache_key = md5($Report->getClassName() . "-" . $Code . "-" . $Report->GetBoltId() . "-" . implode("-", $Parameters));
        $this->cache_table_stub = "Report_{$cache_key}";
        $this->cache_db = config("zermelo.CACHE_DB");
        $this->cache_table = "{$this->cache_db}.{$this->cache_table_stub}";

        DB::statement(DB::raw("CREATE DATABASE IF NOT EXISTS " . config("zermelo.CACHE_DB") . ";"));
        DB::statement(DB::raw("SET SESSION group_concat_max_len = 1000000;"));

        /*
        Check to see if the cache table already exists, if it does not, create it
         */
        $this->exists = count(DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema=? and table_name = ?", [config("zermelo.CACHE_DB"), $this->cache_table_stub])) > 0;
        $this->cacheable = config("zermelo.CACHABLE");

        return true;
    }

    public function getCacheDB()
    {
        return $this->cache_db;
    }

    public function getCacheTableStub()
    {
        return $this->cache_table_stub;
    }

    public function exists(): bool
    {
        return $this->exists;
    }

    public function isCacheable(): bool
    {
        return $this->cacheable;
    }

    /**
     * CachedReport
     * This takes a ZermeloReport and create a cache table inside $cache_table with the result.
     *
     * @param string $cache_table
     * @param ZermeloReport $Report
     * @return bool
     */
    public function CacheReport( ZermeloReport $Report ): bool
    {

        $input_bolt = $Report->getParameter('data-option' );
        if ($input_bolt == "") {
            $input_bolt = false;
        }

        $Report->SetBolt($input_bolt);
        $request_form_input = $Report->getInput();

        $sql = $Report->getSQL();

        if (!$sql) {
            return false;
        }

        $all_queries = [];
        if (!is_array($sql)) {
            $sql = [$sql];
        }

        /*
        break up each queries by semi colon,
        we will run each query separately
         */
        foreach ($sql as $query) {
            $query = explode(";", $query);
            foreach ($query as $single_query) {
                if (!empty(trim($single_query))) {
                    $all_queries[] = trim($single_query);
                }
            }

        }

        /*
        On first run,
        we need to create the table instead of inserting into the table,
        if not cachable, then create a temporary table.
         */
        $first_loop = true;

        foreach ($all_queries as $s) {
            $s = trim($s);

            if (strpos(strtoupper($s), "SELECT", 0) === 0) {
                if ($first_loop) {
                    $first_loop = false;

                    DB::statement(DB::raw("DROP TABLE IF EXISTS {$this->cache_table}"));
                    DB::statement(DB::raw("CREATE TABLE {$this->cache_table} AS {$s}"));
                } else {
                    DB::statement(DB::raw("INSERT INTO {$this->cache_table} {$s}"));
                }
            } else {
                DB::statement(DB::raw($s));
            }
        }

        /*
        Lets try to be clever and attempt to index any 'subject' we have on the table.
         */
        if (config("zermelo.AUTO_INDEX")) {
            $data_row = DB::table($this->cache_table)->first();
            if ($data_row) {
                $data_row = json_decode(json_encode($data_row), true);
                $columns = array_keys($data_row);

                $to_index = [];
                foreach ($columns as $column) {
                    if (AbstractGenerator::isColumnInKeyArray($column, $Report->SUBJECTS)) {
                        $to_index[] = "ADD INDEX(`{$column}`)";
                    }
                }
                if (!empty($to_index)) {
                    $to_index = "ALTER TABLE {$this->cache_table} " . implode(",", $to_index) . ";";
                    DB::statement($to_index);
                }

            }
        }

        return true;

    }

    /**
     * CheckUpdateCacheForReport
     * Determine if a report should be updated based on the file being updated or the cache timeout being expired
     *
     * @param ZermeloReport $Report
     * @return bool
     */
    public function CheckUpdateCacheForReport(ZermeloReport $Report): bool
    {
        $Parameters = $Report->getParameters();
        $Code = $Report->getCode();
        $report_name = $Report->getClassName();

        $cache_key = md5($Report->getClassName() . "-" . $Code . "-" . $Report->GetBoltId() . "-" . implode("-", $Parameters));
        $cache_table_stub = "Report_{$cache_key}";

        /*
        Check to see if the report is pass its age, but only if the option is enabled
         */
        if (config("zermelo.CACHE_TIMEOUT") * 1 > 0) {
            $stats = DB::select("SELECT CURRENT_TIMESTAMP, CREATE_TIME,
                                    TIMESTAMPDIFF(MINUTE,CREATE_TIME, CURRENT_TIMESTAMP) as age
                                FROM information_schema.tables WHERE table_schema=? and table_name = ?", [config("zermelo.CACHE_DB"), $cache_table_stub]);

            if (!$stats) {
                return true;
            }

            $stats = $stats[0];

            $age = $stats->age;
            if ($age > config("zermelo.CACHE_TIMEOUT") * 1) {
                return true;
            }
        }

        /*
        Check to see if the report file has been updated since the caching as occured.
        This is get the UTC time
         */
        $modified_at = new \DateTime();
        $modified_at = $modified_at->setTimestamp(filemtime($Report->getFileLocation()));
        $modified_at_utc_iso = $modified_at->format("Y-m-d H:i:s");

        $result = DB::select("select
                    ? > (CONVERT_TZ(UPDATE_TIME, @@session.time_zone, '+00:00') ) as cache_outdated
                    FROM information_schema.tables WHERE table_schema = ? AND table_name = ?", [$modified_at_utc_iso, config("zermelo.CACHE_DB"), $cache_table_stub]);

        if (!$result) {
            return true;
        }

        $result = $result[0]->cache_outdated;

        return $result == 1;
    }
}