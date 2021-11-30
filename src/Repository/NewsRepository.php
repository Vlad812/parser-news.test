<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class NewsRepository
 * @package App\Repository
 */
class NewsRepository extends EntityRepository
{
    /**
     * @param int $resourceId
     * @return array
     */
    public function getNewsListByResourceId(int $resourceId): array
    {
        $sql = "SELECT id,
                       title,
                       SUBSTRING(News.article, 1, 200 ) AS articleDescribe
                FROM News
                WHERE resourceId = {$resourceId}";

        $conn = $this->getEntityManager()->getConnection();
        return $conn->fetchAll("$sql");
    }
}
