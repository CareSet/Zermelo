<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 8/20/19
 * Time: 10:28 AM
 */

namespace CareSet\Zermelo\Reports\Graph;


class LinkDefinition
{
    protected $link_type;
    protected $weight;

    /**
     * LinkDefinition constructor.
     * @param $link_type
     * @param $weight
     */
    public function __construct($link_type, $weight)
    {
        $this->link_type = $link_type;
        $this->weight = $weight;
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