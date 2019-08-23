<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 8/20/19
 * Time: 10:26 AM
 */

namespace CareSet\Zermelo\Reports\Graph;


interface LinkDefinitionIF
{
    /**
     * @return NodeDefinitionIF
     */
    public function getSourceNodeDefinition(): NodeDefinitionIF;

    /**
     * @param NodeDefinitionIF $source_node_definition
     */
    public function setSourceNodeDefinition(NodeDefinitionIF $source_node_definition);

    /**
     * @return NodeDefinitionIF
     */
    public function getTargetNodeDefinition(): NodeDefinitionIF;

    /**
     * @param NodeDefinitionIF $target_node_definition
     */
    public function setTargetNodeDefinition(NodeDefinitionIF $target_node_definition);

    /**
     * @return mixed
     */
    public function getLinkType();

    /**
     * @param mixed $link_type
     */
    public function setLinkType($link_type);

    /**
     * @return mixed
     */
    public function getWeight();

    /**
     * @param mixed $weight
     */
    public function setWeight($weight);
}