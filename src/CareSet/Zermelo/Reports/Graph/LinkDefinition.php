<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 8/20/19
 * Time: 10:28 AM
 */

namespace CareSet\Zermelo\Reports\Graph;


class LinkDefinition implements LinkDefinitionIF
{
    protected $source_node_definition;
    protected $target_node_definition;
    protected $link_type;
    protected $weight;


    /**
     * LinkDefinition constructor.
     * @param NodeDefinitionIF $source_node_definition
     * @param NodeDefinitionIF $target_node_definition
     * @param $link_type
     * @param $weight
     */
    public function __construct(NodeDefinitionIF $source_node_definition, NodeDefinitionIF $target_node_definition, $link_type, $weight)
    {
        $this->source_node_definition = $source_node_definition;
        $this->target_node_definition = $target_node_definition;
        $this->link_type = $link_type;
        $this->weight = $weight;
    }

    /**
     * @return NodeDefinitionIF
     */
    public function getSourceNodeDefinition(): NodeDefinitionIF
    {
        return $this->source_node_definition;
    }

    /**
     * @param NodeDefinitionIF $source_node_definition
     */
    public function setSourceNodeDefinition(NodeDefinitionIF $source_node_definition)
    {
        $this->source_node_definition = $source_node_definition;
    }

    /**
     * @return NodeDefinitionIF
     */
    public function getTargetNodeDefinition(): NodeDefinitionIF
    {
        return $this->target_node_definition;
    }

    /**
     * @param NodeDefinitionIF $target_node_definition
     */
    public function setTargetNodeDefinition(NodeDefinitionIF $target_node_definition)
    {
        $this->target_node_definition = $target_node_definition;
    }

    /**
     * @return mixed
     */
    public function getLinkType()
    {
        return $this->link_type;
    }

    /**
     * @param mixed $link_type
     */
    public function setLinkType($link_type)
    {
        $this->link_type = $link_type;
    }

    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param mixed $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

}