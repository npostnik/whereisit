<?php
namespace Npostnik\Whereisit\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

class ContentRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
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

    public function listAllContentTypes()
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $result = $queryBuilder
            ->select('CType')
            ->from('tt_content')
            ->groupBy('CType')
            ->where(
                $queryBuilder->expr()->neq('CType', $queryBuilder->createNamedParameter('', Connection::PARAM_STR)),
                $queryBuilder->expr()->neq('CType', $queryBuilder->createNamedParameter('list', Connection::PARAM_STR))
            )
            ->executeQuery();
        return $result->fetchAllAssociative();
    }

    public function listAllPluginTypes()
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $result = $queryBuilder
            ->select('list_type')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('list', Connection::PARAM_STR))
            )
            ->groupBy('list_type')
            ->executeQuery();
        return $result->fetchAllAssociative();
    }


    public function findByCType($cType)
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $result = $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter($cType, Connection::PARAM_STR))
            )
            ->executeQuery();
        $contentElements = $result->fetchAllAssociative();
        return $contentElements;
    }

    public function findByListType($listType)
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $result = $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('list', Connection::PARAM_STR)),
                $queryBuilder->expr()->eq('list_type', $queryBuilder->createNamedParameter($listType, Connection::PARAM_STR))
            )
            ->executeQuery();
        $contentElements = $result->fetchAllAssociative();
        return $contentElements;
    }


}
