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

    /**
     * GeoIp constructor.
     * @param PDO $collection
     */
    public function __construct(PDO $collection)
    {
        $this->db = $collection;
    }

    public function createDatabase()
    {
        $this->db->query(
            'drop database geoIP;'
    );
        $this->db->query(
            'create database geoIP;'
        );
        $this->db->query('
            create table geoIP (
                rangeStart INET,
                rangeEnd INET,
                isv6 boolean,
                country varchar(2),
                region varchar(100),
                city varchar(100));
        ');
        $this->db->query('
            CREATE index rangeEnd on geoIP(isv6, rangeEnd);
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
