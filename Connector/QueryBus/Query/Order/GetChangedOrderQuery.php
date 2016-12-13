<?php

namespace PlentyConnector\Connector\QueryBus\Query\Order;

use PlentyConnector\Connector\QueryBus\Query\QueryInterface;

/**
 * Class GetChangedOrderQuery
 */
class GetChangedOrderQuery implements QueryInterface
{
    /**
     * @var string
     */
    private $adapterName;

    /**
     * FetchChangedManufacturersQuery constructor.
     *
     * @param string $adapterName
     */
    public function __construct($adapterName)
    {
        $this->adapterName = $adapterName;
    }

    /**
     * @return string
     */
    public function getAdapterName()
    {
        return $this->adapterName;
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return [
            'adapterName' => $this->adapterName,
        ];
    }

    /**
     * @param array $payload
     */
    public function setPayload(array $payload = [])
    {
        $this->adapterName = $payload['adapterName'];
    }
}