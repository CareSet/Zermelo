<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 9/24/18
 * Time: 2:47 PM
 */

namespace CareSet\Zermelo\Reports\Graph;


use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Models\ZermeloReport;
use CareSet\Zermelo\Models\ZermeloDatabase;
use \DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CachedGraphReport extends DatabaseCache
{
    protected $graph_nodes_table = null;
    protected $graph_weights_table = null;

    protected $node_table = null;
    protected $link_table = null;

    protected $node_types = [];
    protected $link_types = [];

    protected $visible_node_types = [];
    protected $visible_link_types = [];

    public function __construct( ZermeloReport $report, $connectionName )
    {
        // Cache the "main" query
        parent::__construct( $report, $connectionName );

        $this->node_table = $this->keygen( 'GraphNode' );
        $this->link_table = $this->keygen( 'GraphLinks' );

        $input_node_types = [];
        if ($this->getReport()->getInput('node_types') && is_array($this->getReport()->getInput('node_types'))) {
            $input_node_types = $this->getReport()->getInput('node_types');
        }

        $input_link_types = [];
        if ($this->getReport()->getInput('link_types') && is_array($this->getReport()->getInput('link_types'))) {
            $input_link_types = $this->getReport()->getInput('link_types');
        }

        // Go ahead to build the auxilliary tables that represent the nodes and links of the graph
        $fields = ZermeloDatabase::getTableColumnDefinition( $this->getTableName(), $this->connectionName );
        $node_index = 0;
        $link_index = 0;

        $NodeColumns = $this->getReport()->SUBJECTS;
        $LinkColumns = $this->getReport()->WEIGHTS;

        foreach ( $fields as $field ) {
            $column = $field[ 'Name' ];
            $title = ucwords( str_replace( '_', ' ', $column ), "\t\r\n\f\v " );
            if ( ZermeloDatabase::isColumnInKeyArray( $column, $NodeColumns ) ) {
                $subjects_found[] = $column;
                $this->node_types[ $node_index ] = [
                    'id' => $node_index,
                    'field' => $column,
                    'name' => $title,
                    'visible' => in_array( $node_index, $input_node_types )
                ];
                $this->visible_node_types[ $node_index ] = $this->node_types[ $node_index ][ 'visible' ];
                ++$node_index;
            }
            if ( ZermeloDatabase::isColumnInKeyArray( $column, $LinkColumns ) ) {
                $weights_found[] = $column;
                $this->link_types[ $link_index ] = [
                    'id' => $link_index,
                    'field' => $column,
                    'name' => $title,
                    'visible' => in_array( $link_index, $input_link_types )
                ];
                $this->visible_link_types[ $link_index ] = $this->link_types[ $link_index ][ 'visible' ];
                ++$link_index;
            }
        }

        if ( !is_array( $this->node_types ) || empty( $this->node_types ) ) {
            for ( $i = 2, $len = count( $this->node_types ); $i < $len; ++$i ) {
                $this->node_types[ $i ][ 'visible' ] = false;
                $this->visible_node_types[ $i ] = false;
            }
        }

        // If we generated the base table this request, then rebulild the auxilliary tables
        if ( $this->generatedThisRequest == true ) {
            $this->buildGraphTable();
        }
    }

    public function getNodeTable()
    {
        return $this->node_table;
    }

    public function getLinkTable()
    {
        return $this->link_table;
    }

    public function getNodeTypes()
    {
        return $this->node_types;
    }

    public function getLinkTypes()
    {
        return $this->link_types;
    }

    public function getVisibleNodeTypes()
    {
        return $this->visible_node_types;
    }

    public function getVisibleLinkTypes()
    {
        return $this->visible_link_types;
    }

    private function buildGraphTable()
    {
        $NodeColumns = $this->getReport()->SUBJECTS;
        $LinkColumns = $this->getReport()->WEIGHTS;

        $weight_table = $this->keygen('GraphWeight');

        /*
            Determine the subjects and the weights column
        */
        $subjects_found = [];
        $weights_found = [];

        $fields = ZermeloDatabase::getTableColumnDefinition( $this->getTableName(), $this->connectionName );
        foreach ($fields as $field) {
            $column = $field['Name'];
            if (ZermeloDatabase::isColumnInKeyArray($column, $NodeColumns)) {
                $subjects_found[] = $column;
            }
            if (ZermeloDatabase::isColumnInKeyArray($column, $LinkColumns)) {
                $weights_found[] = $column;
            }
        }


        /*
            Create the cache table
        */
        ZermeloDatabase::connection($this->connectionName)->statement("DROP TABLE IF EXISTS {$this->node_table}");
        ZermeloDatabase::connection($this->connectionName)->statement("DROP TABLE IF EXISTS {$this->link_table}");
        ZermeloDatabase::connection($this->connectionName)->statement("DROP TABLE IF EXISTS {$weight_table}");

        Schema::connection($this->connectionName)->create( $this->node_table, function ( Blueprint $table ) {
            $table->bigIncrements('id');
            $table->integer('type')->nullable(false);
            $table->string('value')->nullable(false);
            $table->decimal('size', 5, 2);
            $table->integer('sum_weight');
            $table->integer('degree')->default(0);
            //$table->temporary();
            //$table->unique(['type', 'value']);
        });

        Schema::connection($this->connectionName)->create( $this->link_table, function ( Blueprint $table ) {
            $table->bigInteger('source')->nullable(true);
            $table->bigInteger('target')->nullable(true);
            $table->integer('link_type')->nullable(false);
            $table->integer('weight')->nullable(false)->default(0);
            //$table->temporary();
            //  $table->unique(['source', 'target', 'link_type']);
            // $table->index(['source','target']);
            // $table->index('target');
        });

        /* populating the nodes table */
        foreach ($subjects_found as $index => $subject) {

//            /* each subject will be its own node type for now */
//            $this->node_types[$index] = [
//                'id' => $index,
//                'field' => $subject
//            ];
            /*
                If we need to build the table, Insert into the node table, all the nodes possible
            */
            if( $this->node_types[$index] ) {
                ZermeloDatabase::connection($this->connectionName)->statement("INSERT INTO {$this->node_table}(type,value,size,sum_weight) SELECT distinct ?,`{$subject}`,0,0 from {$this->getTableName()}", [$index]);
            }
        }

        ZermeloDatabase::connection($this->connectionName)->statement("UPDATE {$this->node_table} A SET A.id = A.id-1;");

        foreach ($weights_found as $index => $weight) {
//            $this->link_types[$index] = [
//                'id' => $index,
//                'field' => $weight,
//            ];

            /*
                Actually has links
            */
            if (count($this->node_types) > 1) {
                foreach ($this->node_types as $AIndex => $ASubject) {
                    foreach ($this->node_types as $BIndex => $BSubject) {

                        if ($BIndex <= $AIndex) {
                            continue;
                        }

                        if( $this->link_types[$index] )
                            ZermeloDatabase::connection($this->connectionName)->statement("INSERT INTO {$this->link_table}(source,target,link_type,weight)
                                            SELECT
                                            A.id as source,
                                            B.id as target,
                                            ? as link_type,
                                            sum(COALESCE(`{$weight}`,0)) as weight
                                            FROM {$this->getTableName()} as MASTER
                                            LEFT JOIN {$this->node_table} AS A on (MASTER.`{$ASubject['field']}` = A.value and A.type = ?)
                                            LEFT JOIN {$this->node_table} AS B on (MASTER.`{$BSubject['field']}` = B.value and B.type = ?)
                                            group by A.id, B.id
                                            HAVING sum(COALESCE(`{$weight}`,0)) > 0
                                            ;
                                            ", [$index, $AIndex, $BIndex]
                            );
                    }
                }

            } else {

                /*
                    Does not have any link, but we need to insert a null link to determine the size of the nodes
                */
                $AIndex = 0;
                $ASubject = $this->node_types[0];
                ZermeloDatabase::connection($this->connectionName)->statement("INSERT INTO {$this->link_table}(source,target,link_type,weight)
                                SELECT
                                A.id as source,
                                null as target,
                                ? as link_type,
                                sum(COALESCE(`{$weight}`,0)) as weight
                                FROM {$this->getTableName()} as MASTER
                                INNER JOIN {$this->node_table} AS A on (MASTER.`{$ASubject['field']}` = A.value and A.type = ?)
                                group by A.id
                                HAVING sum(COALESCE(`{$weight}`,0)) > 0
                                ;
                                ", [$index, $AIndex]
                );
            }

        }

        /*
            Calculate the sum_weight per each node.
            This is cross-SQL friendly
        */
        ZermeloDatabase::connection($this->connectionName)->statement("CREATE TABLE {$weight_table} AS
            SELECT A.id,sum(COALESCE(B.weight,0) + COALESCE(C.weight,0)) as sum_weight FROM {$this->node_table} AS A
            LEFT JOIN  {$this->link_table} AS B ON B.source = A.id
            LEFT JOIN  {$this->link_table} AS C ON C.target = A.id
            GROUP BY A.id;
        ");
        ZermeloDatabase::connection($this->connectionName)->statement("ALTER TABLE {$weight_table} add primary key(id);");
        ZermeloDatabase::connection($this->connectionName)->statement("UPDATE {$this->node_table} AS A 
                            SET 
                                A.sum_weight = (SELECT sum_weight from {$weight_table} AS B WHERE B.id = A.id),
                                A.degree = (SELECT count(distinct C.source, C.target) from {$this->link_table} as C WHERE (C.source = A.id OR C.target = A.id) AND (C.source IS NOT NULL and C.target IS NOT NULL))
        
        ;");

        /* scale the size by the min/max of that type */
        $results = ZermeloDatabase::connection($this->connectionName)->select("select type, min(sum_weight) as min, max(sum_weight) as max, (max(sum_weight) - min(sum_weight)) as localize_max from {$this->node_table} group by type order by type");
        foreach ($results as $index => $result) {
            $type = $result->type;
            $min = $result->min;
            $max = $result->max;
            $local_max = $result->localize_max;
            ZermeloDatabase::connection($this->connectionName)->statement("UPDATE {$this->node_table} SET size = COALESCE(((sum_weight - ?) / ?) * 100,0) WHERE type = ?", [$min, $local_max, $type]);
        }
    }

}