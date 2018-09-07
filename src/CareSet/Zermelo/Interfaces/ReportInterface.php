<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 4/13/18
 * Time: 12:14 PM
 */

namespace CareSet\Zermelo\Interfaces;


interface ReportInterface
{
    /**
     * MapRow
     * When displaying to the tabular view,
     * Model can chose to modify the content of each row cell.
     * NOTE: Header name CAN be changed, but columns cannot be added or removed
     *
     * @param array $row
     * @param int $row_number
     * @return void
     */
    public function MapRow( array $row, int $row_number );

    /**
     * OverrideHeader
     * Override a default column format or add additional column tag to be sent back to the front end
     * This returns the value as a reference parameter
     *
     * @param array &$format
     * @param array &$tags
     * @return void
     */
    public function OverrideHeader( array &$format, array &$tags ): void;
}
