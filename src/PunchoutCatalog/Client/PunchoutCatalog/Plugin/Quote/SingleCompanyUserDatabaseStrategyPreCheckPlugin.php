<?php

namespace PunchoutCatalog\Client\PunchoutCatalog\Plugin\Quote;

use Generated\Shared\Transfer\QuoteTransfer;
use PunchoutCatalog\Shared\PunchoutCatalog\PunchoutConstsInterface;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\QuoteExtension\Dependency\Plugin\DatabaseStrategyPreCheckPluginInterface;

/**
 * @method \PunchoutCatalog\Client\PunchoutCatalog\PunchoutCatalogFactory getFactory()
 * @method \PunchoutCatalog\Client\PunchoutCatalog\PunchoutCatalogClient getClient()
 */
class SingleCompanyUserDatabaseStrategyPreCheckPlugin extends AbstractPlugin implements DatabaseStrategyPreCheckPluginInterface
{
    /**
     * {@inheritdoc}
     * - Retrieves logged in customer from Session.
     * - Returns false to disable persistent carts when "single user" connection mode is selected for a punchout customer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function check(QuoteTransfer $quoteTransfer): bool
    {
        $customer = $this->getFactory()
            ->getCustomerClient()
            ->getCustomer();

        if ($customer) {
            $impersonationDetails = $customer->getPunchoutCatalogImpersonationDetails();

            if (!empty($impersonationDetails[PunchoutConstsInterface::IS_PUNCHOUT])
                && isset($impersonationDetails[PunchoutConstsInterface::PUNCHOUT_LOGIN_MODE])
                && ($impersonationDetails[PunchoutConstsInterface::PUNCHOUT_LOGIN_MODE] === PunchoutConstsInterface::CUSTOMER_LOGIN_MODE_SINGLE)
            ) {
                return false;
            }
        }

        return true;
    }
}
