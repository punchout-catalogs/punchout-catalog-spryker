<?php

namespace PunchoutCatalog\Client\PunchoutCatalog\Dependency\Client;

interface PunchoutCatalogToCustomerClientInterface
{
    /**
     * @return \Generated\Shared\Transfer\CustomerTransfer|null
     */
    public function getCustomer();
}
