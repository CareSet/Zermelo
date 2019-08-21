<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 8/20/19
 * Time: 10:26 AM
 */

namespace CareSet\Zermelo\Reports\Graph;


interface NodeDefinitionIF
{
    /**
     * @return string
     */
    public function getNodeSizeFormula();

    /**
     * @param string $node_size_formula
     */
    public function setNodeSizeFormula(string $node_size_formula);

    /**
     * @return mixed
     */
    public function getNodeId();

    /**
     * @param mixed $node_id
     */
    public function setNodeId($node_id);

    /**
     * @return mixed
     */
    public function getNodeName();

    /**
     * @param mixed $node_name
     */
    public function setNodeName($node_name);

    /**
     * @return mixed
     */
    public function getNodeSize();

    /**
     * @param mixed $node_size
     */
    public function setNodeSize($node_size);

    /**
     * @return mixed
     */
    public function getNodeType();

    /**
     * @param mixed $node_type
     */
    public function setNodeType($node_type);

    /**
     * @return mixed
     */
    public function getNodeGroup();

    /**
     * @param mixed $node_group
     */
    public function setNodeGroup($node_group);

    /**
     * @return mixed
     */
    public function getNodeLatitude();

    /**
     * @param mixed $node_latitude
     */
    public function setNodeLatitude($node_latitude);

    /**
     * @return mixed
     */
    public function getNodeLongitude();

    /**
     * @param mixed $node_longitude
     */
    public function setNodeLongitude($node_longitude);

    /**
     * @return mixed
     */
    public function getNodeImg();

    /**
     * @param mixed $node_img
     */
    public function setNodeImg($node_img);

}