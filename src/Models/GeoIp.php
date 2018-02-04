<?php

namespace GeoIP\Models;

use MongoDB\Collection;


/**
 * Class GeoIp
 */
class GeoIp
{
    /** @var Collection */
    protected $collection;

    /**
     * GeoIp constructor.
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function prepareCollection()
    {
        $this->collection->createIndexes(
            [
                ['min' => 1],
                ['max' => 1]
            ]
        );
    }

    public function insertMany($documents)
    {
        $this->collection->insertMany($documents);
    }
}
