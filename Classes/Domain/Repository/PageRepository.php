<?php
namespace Npostnik\Whereisit\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\Repository;

class PageRepository extends Repository
{
    /**
     * @var ConnectionPool
     */
    protected $connectionPool;

    /**
     * @param ConnectionPool $connectionPool
     */
    public function __construct(ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;
    }

    public function findAll()
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        $result = $queryBuilder
            ->select('*')
            ->from('pages')
            ->executeQuery();

        return $result->fetchAllAssociative();
    }

    /**
     * Findet eine Seite anhand ihrer UID
     *
     * @param int $uid
     * @return array|null
     */
    public function findByUid($uid): ?array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        $result = $queryBuilder
            ->select('*')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
            )
            ->executeQuery();

        return $result->fetchAssociative() ?: null;
    }

    /**
     * Holt den Seitentitel für eine gegebene UID
     *
     * @param int $uid
     * @return string
     */
    public function getPageTitle(int $uid): string
    {
        $page = $this->findByUid($uid);
        return $page['title'] ?? '';
    }
}
