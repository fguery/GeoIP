<?php

namespace GeoIP\Models;

use PDO;

/**
 * Class GeoIp
 */
class GeoIp
{
    /** @var PDO */
    protected $db;
    /** @var string */
    protected $tableName;

    /**
     * GeoIp constructor.
     * @param PDO $collection
     * @param string $tableName
     */
    public function __construct(PDO $collection, $tableName)
    {
        $this->db = $collection;
        $this->tableName = $tableName;
    }

    public function createDatabase()
    {
        $this->db->query(
            'drop table IF EXISTS ' . $this->tableName
        );
        $this->db->query(
            'drop index IF EXISTS ' . $this->tableName . '_ip_range'
        );
        $this->db->query('
            create table ' . $this->tableName . ' (
                range_start INET,
                range_end INET,
                is_v6 boolean,
                country varchar(2),
                region varchar(100),
                city varchar(100));
        ');
        $this->db->query('
            CREATE index ' . $this->tableName . '_ip_range on ' . $this->tableName . '(is_v6, range_start, range_end);
        ');
    }

    /**
     * Insert a batch of geoIP lines.
     * Each item must be as follow:
     * [
     *   "rangeStart" => ipv4 or ipv6
     *   "rangeEnd" => ipv4 or ipv6
     *   "isv6" => bool,
     *   "country" => alpha2,
     *   "state" => string
     *   "city" => string
     * ]
     *
     * @param array $documents
     */
    public function insertMany($documents)
    {
        $query = 'INSERT into geoIP
            (rangeStart, rangeEnd, isv6, country, region, city)
            VALUES (:rangeStart, :rangeEnd, :isv6, :country, :state, :city)'
        ;
        $stmt = $this->db->prepare($query);
        foreach ($documents as $doc) {
            $stmt->bindParam(':rangeStart', $doc['rangeStart']);
            $stmt->bindParam(':rangeEnd', $doc['rangeEnd']);
            $stmt->bindParam(':isv6', $doc['isv6']);
            $stmt->bindParam(':country', $doc['country']);
            $stmt->bindParam(':region', $doc['region']);
            $stmt->bindParam(':city', $doc['city']);
            $stmt->execute();
        }
    }
}
