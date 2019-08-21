<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 8/20/19
 * Time: 10:28 AM
 */

namespace CareSet\Zermelo\Reports\Graph;


class NodeDefinition implements NodeDefinitionIF
{
    protected $node_size_formula = "";

    protected $node_id;
    protected $node_name;
    protected $node_size;
    protected $node_type;
    protected $node_group;
    protected $node_latitude;
    protected $node_longitude;
    protected $node_img;


    /**
     * NodeDefinition constructor.
     * @param $node_id
     * @param $node_size
     * @param $node_type
     * @param $node_group
     * @param $node_latitude
     * @param $node_longitude
     * @param $node_img
     */
    public function __construct($node_id = '', $node_name = '', $node_size = '', $node_type = '', $node_group = '', $node_latitude = '', $node_longitude = '', $node_img = '')
    {
        $this->node_id = $node_id;
        $this->node_name = $node_name;
        $this->node_size = $node_size;
        $this->node_type = $node_type;
        $this->node_group = $node_group;
        $this->node_latitude = $node_latitude;
        $this->node_longitude = $node_longitude;
        $this->node_img = $node_img;
    }

    /**
     * @return string
     */
    public function getNodeSizeFormula(): string
    {
        return "IF(MAX(`{$this->getNodeSize()}`) > 0, MAX(`{$this->getNodeSize()}`), 50)";
    }

    /**
     * @param string $node_size_formula
     */
    public function setNodeSizeFormula(string $node_size_formula)
    {
        $this->node_size_formula = $node_size_formula;
    }


    /**
     * @return mixed
     */
    public function getNodeId()
    {
        return $this->node_id;
    }

    /**
     * @param mixed $node_id
     */
    public function setNodeId($node_id)
    {
        $this->node_id = $node_id;
    }

    /**
     * @return mixed
     */
    public function getNodeName()
    {
        return $this->node_name;
    }

    /**
     * @param mixed $node_name
     */
    public function setNodeName($node_name)
    {
        $this->node_name = $node_name;
    }

    /**
     * @return mixed
     */
    public function getNodeSize()
    {
        return $this->node_size;
    }

    /**
     * @param mixed $node_size
     */
    public function setNodeSize($node_size)
    {
        $this->node_size = $node_size;
    }

    /**
     * @return mixed
     */
    public function getNodeType()
    {
        return $this->node_type;
    }

    /**
     * @param mixed $node_type
     */
    public function setNodeType($node_type)
    {
        $this->node_type = $node_type;
    }

    /**
     * @return mixed
     */
    public function getNodeGroup()
    {
        return $this->node_group;
    }

    /**
     * @param mixed $node_group
     */
    public function setNodeGroup($node_group)
    {
        $this->node_group = $node_group;
    }

    /**
     * @return mixed
     */
    public function getNodeLatitude()
    {
        return $this->node_latitude;
    }

    /**
     * @param mixed $node_latitude
     */
    public function setNodeLatitude($node_latitude)
    {
        $this->node_latitude = $node_latitude;
    }

    /**
     * @return mixed
     */
    public function getNodeLongitude()
    {
        return $this->node_longitude;
    }

    /**
     * @param mixed $node_longitude
     */
    public function setNodeLongitude($node_longitude)
    {
        $this->node_longitude = $node_longitude;
    }

    /**
     * @return mixed
     */
    public function getNodeImg()
    {
        return $this->node_img;
    }

    /**
     * @param mixed $node_img
     */
    public function setNodeImg($node_img)
    {
        $this->node_img = $node_img;
    }


}