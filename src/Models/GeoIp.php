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

    public function prepareCollection()
    {
        $this->db->query(
            'drop database geoIP;'
    );
        $this->db->query(
            'create database geoIP;'
        );
        $this->db->query('
            create table geoIP (
                min INET,
                max INET,
                country varchar(3),
                state varchar(100),
                city varchar(100));
        ');
        $this->db->query('
            CREATE index maxIp on geoIP(max);
        ');
    }

    /**
     * Insert a batch of geoIP lines.
     * Each item must be as follow:
     * [
     *   "min" => ipv4 or ipv6
     *   "max" => ipv4 or ipv6
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
            (min, max, country, state, city)
            VALUES (:min, :max, :country, :state, :city)'
        ;
        $stmt = $this->db->prepare($query);
        foreach ($documents as $doc) {
            $stmt->bindParam(':min', $doc['min']);
            $stmt->bindParam(':max', $doc['max']);
            $stmt->bindParam(':country', $doc['country']);
            $stmt->bindParam(':state', $doc['state']);
            $stmt->bindParam(':city', $doc['city']);
            $stmt->execute();
        }
    }
}
