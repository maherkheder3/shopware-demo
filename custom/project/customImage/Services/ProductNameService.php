<?php

namespace customImage\Services;

use Doctrine\DBAL\Connection;

class ProductNameService
{
    /** @var Connection */
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getProductionName(){
        $qb = $this->connection->createQueryBuilder();

        return $qb->select(['name'])
            ->from('s_articles')
            ->setMaxResults(20)
            ->execute()
            ->fetchAll(\PDO::FETCH_COLUMN);


    }
}
