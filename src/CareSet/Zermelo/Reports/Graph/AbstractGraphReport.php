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
     * $nodeDefinitions
     * What the engine should consider as the 'noun' or 'subject'.
     * This field will determine which field to be used as nodes on a graph
     *
     * @var array
     */
    protected $nodeDefinitions = [];

    /**
     * $linkDefinitions
     * What the engine should consider the weighted links between the subjects.
     * This field is used to generate 'links' and link size between each nodes.
     *
     * @var array
     */
    protected $linkDefinitions = [];

    /**
     * Return an array of all of the node ID columns
     */
    public function getNodeIdColumns()
    {
        $nodeIdColumns = [];
        foreach ($this->nodeDefinitions as $nodeDefinition) {
            $nodeIdColumns[]= $nodeDefinition->getNodeId();
        }

        return $nodeIdColumns;
    }

    /**
     * Return an array of all of the node Type columns
     */
    public function getNodeTypeColumns()
    {
        $nodeTypeColumns = [];
        foreach ($this->nodeDefinitions as $nodeDefinition) {
            $nodeTypeColumns[]= $nodeDefinition->getNodeType();
        }

        return $nodeTypeColumns;
    }

    /**
     * @return array
     */
    public function getNodeDefinitions(): array
    {
        return $this->nodeDefinitions;
    }

    /**
     * @param array $nodeDefinitions
     */
    public function setNodeDefinitions(array $nodeDefinitions)
    {
        $this->nodeDefinitions = $nodeDefinitions;
    }


    /**
     * @return array
     */
    public function getLinkDefinitions(): array
    {
        return $this->linkDefinitions;
    }

    /**
     * @param array $linkDefinitions
     */
    public function setLinkDefinitions(array $linkDefinitions)
    {
        $this->linkDefinitions = $linkDefinitions;
    }
}
