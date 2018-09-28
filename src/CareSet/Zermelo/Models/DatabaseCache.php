<?php

namespace CareSet\Zermelo\Models;

use Carbon\Carbon;
use CareSet\Zermelo\Interfaces\ReportInterface;
use Illuminate\Support\Facades\DB;

class DatabaseCache implements ReportInterface
{
    protected $exists = false;
    protected $doClearCache = false;
    protected $generatedThisRequest = false;
    protected $columns = [];
    protected $cache_table = null;
    protected $report = null;
    protected $key = null;

    public function __construct( ZermeloReport $report )
    {
        $this->report = $report;

        $clear_cache = $report->getInput( 'clear_cache' ) == true ? true : false;
        $this->setDoClearCache( $clear_cache );

        $this->key = $this->keygen();
        $this->cache_table = ZermeloDatabase::connection()->table("{$this->key}");

        if ( $this->exists() === false ||
            $report->isCacheEnabled() === false ||
            $this->getDoClearCache() == true ||
            $this->isCacheExpired() === true ) {
            $this->createTable();
            $this->generatedThisRequest = true;
        }

        $this->columns = ZermeloDatabase::getTableColumnDefinition( $this->getTableName() );

        return true;
    }

    public function keygen()
    {
        // Get the report key, can be a maximum of 64 chars
        //   md5 = 32
        // + "_" = 1
        // + max( ReportClassName, 31 )
        // < 64
        $key = substr( $this->report->getClassName(), 0, max( strlen( $this->report->getClassName() ), 31 ) ) ."_".md5($this->report->getClassName() . "-" . $this->report->getCode() . "-" . $this->report->GetBoltId() . "-" . implode("-", $this->report->getParameters() ) );
        return $key;
    }

    public function getTable()
    {
        return $this->cache_table;
    }

    public function getTableName()
    {
        return $this->cache_table->from;
    }

    public function getReport()
    {
        return $this->report;
    }

    public function exists(): bool
    {
        $hasTable = ZermeloDatabase::hasTable( $this->cache_table->from );
        return $hasTable;
    }

    public function setDoClearCache( $doClearCache )
    {
        $this->doClearCache = $doClearCache;
    }

    public function getDoClearCache()
    {
        return $this->doClearCache;
    }

    public function isCacheExpired()
    {
        $expired = false;
        $now = Carbon::now();
        $nowTimestamp = $now->timestamp;
        $expiredTime = $this->getExpireTime();
        $expireTimestamp = Carbon::parse( $expiredTime )->timestamp;
        if ( $nowTimestamp > $expireTimestamp ) {
            $expired = true;
        }

        return $expired;
    }

    public function MapRow( array $row, int $row_number )
    {
        return $this->report->MapRow( $row, $row_number );
    }

    public function OverrideHeader( array &$format, array &$tags ): void
    {
        $this->report->OverrideHeader( $format, $tags );
    }

    public function getIndividualQueries()
    {
        $sql = $this->report->getSQL();

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

        return $all_queries;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * If the table exists, drop it and create it from the queries
     * in the report.
     */
    public function createTable()
    {
        if ( $this->exists() ) {
            ZermeloDatabase::drop($this->cache_table->from);
        }

        foreach ( $this->getIndividualQueries() as $index => $query ) {

            if ( strpos( strtoupper( $query ), "SELECT", 0 ) === 0 ) {
                if ( $index == 0 ) {
                    ZermeloDatabase::connection()->statement(DB::raw("CREATE TABLE {$this->cache_table->from} AS {$query}"));
                } else {
                    ZermeloDatabase::connection()->statement(DB::raw("INSERT INTO {$this->cache_table->from} {$query}"));
                }
            } else {
                ZermeloDatabase::connection()->statement(DB::raw($query));
            }
        }

        /*
        Lets try to be clever and attempt to index any 'subject' we have on the table.
         */
        if ( config("zermelo.AUTO_INDEX" ) ) {
            $data_row = $this->cache_table->first();
            if ( $data_row ) {
                $data_row = json_decode( json_encode( $data_row ), true );
                $columns = array_keys( $data_row );

                $to_index = [];
                foreach ( $columns as $column ) {
                    if ( ZermeloDatabase::isColumnInKeyArray( $column, $this->report->SUBJECTS ) ) {
                        $to_index[] = "ADD INDEX(`{$column}`)";
                    }
                }
                if ( !empty( $to_index ) ) {
                    $to_index = "ALTER TABLE {$this->getTableName()} " . implode( ",", $to_index ) . ";";
                    ZermeloDatabase::connection()->statement( $to_index );
                }

            }
        }
    }

    /**
     *  Get the formatted time when this cache was last created
     *
     * @return false|string
     */
    public function getLastGenerated()
    {
        $stats = DB::select("SELECT CURRENT_TIMESTAMP, CREATE_TIME,
                                    TIMESTAMPDIFF(MINUTE,CREATE_TIME, CURRENT_TIMESTAMP) as age
                                FROM information_schema.tables WHERE table_schema=? and table_name = ?", [config("zermelo.ZERMELO_DB"), $this->getTableName() ]);

        if (!$stats) {
            return true;
        }

        $tz = DB::select('SELECT TIME_FORMAT( TIMEDIFF(NOW(), UTC_TIMESTAMP), "%H:%i" ) as TZ;');

        $stats = $stats[0];

        $time = $stats->CREATE_TIME;
        $offset = $tz[0]->TZ;
        if ( $offset == '00:00' ) {
            $offset = "+$offset";
        }

        $carbonTime = Carbon::createFromFormat('Y-m-d H:i:s', $time, $offset  );
        $carbonTime->setTimezone( config('app.timezone' ) );
        $lastGeneratedTime = $carbonTime->toDateTimeString();
        return $lastGeneratedTime;
    }

    public function getExpireTime()
    {
        $expireTime = false;
        if ( $this->report->isCacheEnabled() ) {

            $expireTimeCarbon = Carbon::parse( $this->getLastGenerated() )->addSeconds( $this->report->howLongToCacheInSeconds() );
            $expireTime = date( 'Y-m-d H:i:s', $expireTimeCarbon->timestamp );
        }

        return $expireTime;
    }

    public function getGeneratedThisRequest()
    {
        return $this->generatedThisRequest;
    }
}