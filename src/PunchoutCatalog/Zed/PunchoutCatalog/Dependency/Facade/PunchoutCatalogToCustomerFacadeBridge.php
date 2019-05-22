<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade;

use Spryker\Zed\Customer\Business\CustomerFacadeInterface;
use Generated\Shared\Transfer\CustomerTransfer;

class PunchoutCatalogToCustomerFacadeBridge implements PunchoutCatalogToCustomerFacadeInterface
{
    /**
     * @var \Spryker\Zed\Customer\Business\CustomerFacadeInterface
     */
    protected $customerFacade;

    /**
     * @param \Spryker\Zed\CompanyBusinessUnit\Business\CustomerFacadeInterface $customerFacade
     */
    public function __construct(CustomerFacadeInterface $customerFacade)
    {
        $this->customerFacade = $customerFacade;
    }
    
    /**
     * @param int $idCustomer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer|null
     */
    public function findCustomerById(int $idCustomer): ?CustomerTransfer
    {
        $customerTransfer = (new CustomerTransfer())
            ->setIdCustomer($idCustomer);
        return $this->customerFacade->findCustomerById($customerTransfer);
    }
}
