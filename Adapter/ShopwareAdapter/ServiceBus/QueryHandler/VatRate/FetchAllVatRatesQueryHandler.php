<?php

namespace ShopwareAdapter\ServiceBus\QueryHandler\VatRate;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use PlentyConnector\Connector\ServiceBus\Query\QueryInterface;
use PlentyConnector\Connector\ServiceBus\Query\VatRate\FetchAllVatRatesQuery;
use PlentyConnector\Connector\ServiceBus\QueryHandler\QueryHandlerInterface;
use Shopware\Models\Tax\Tax;
use ShopwareAdapter\ResponseParser\VatRate\VatRateResponseParserInterface;
use ShopwareAdapter\ShopwareAdapter;

/**
 * Class FetchAllVatRatesQueryHandler
 */
class FetchAllVatRatesQueryHandler implements QueryHandlerInterface
{
    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var VatRateResponseParserInterface
     */
    private $responseParser;

    /**
     * FetchAllVatRatesQueryHandler constructor.
     *
     * @param EntityManagerInterface         $entityManager
     * @param VatRateResponseParserInterface $responseParser
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        VatRateResponseParserInterface $responseParser
    ) {
        $this->repository = $entityManager->getRepository(Tax::class);
        $this->responseParser = $responseParser;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(QueryInterface $query)
    {
        return $query instanceof FetchAllVatRatesQuery &&
            $query->getAdapterName() === ShopwareAdapter::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(QueryInterface $query)
    {
        $elements = $this->createTaxQuery()->getArrayResult();

        foreach ($elements as $element) {
            $result = $this->responseParser->parse($element);

            if (null === $result) {
                continue;
            }

            yield $result;
        }
    }

    /**
     * @return Query
     */
    private function createTaxQuery()
    {
        $queryBuilder = $this->repository->createQueryBuilder('taxes');
        $queryBuilder->select([
            'taxes.id as id',
            'taxes.name as name',
            'taxes.tax as tax',
        ]);

        $objectQuery = $queryBuilder->getQuery();
        $objectQuery->execute();

        return $objectQuery;
    }
}
