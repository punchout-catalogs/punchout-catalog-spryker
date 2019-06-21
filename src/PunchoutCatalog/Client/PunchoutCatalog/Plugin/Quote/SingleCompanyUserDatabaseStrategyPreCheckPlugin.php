<?php

namespace PunchoutCatalog\Client\PunchoutCatalog\Plugin\Quote;

use Generated\Shared\Transfer\QuoteTransfer;
use PunchoutCatalog\Shared\PunchoutCatalog\PunchoutCatalogConstsInterface;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\QuoteExtension\Dependency\Plugin\DatabaseStrategyPreCheckPluginInterface;

/**
 * @method \PunchoutCatalog\Client\PunchoutCatalog\PunchoutCatalogFactory getFactory()
 * @method \PunchoutCatalog\Client\PunchoutCatalog\PunchoutCatalogClient getClient()
 */
class SingleCompanyUserDatabaseStrategyPreCheckPlugin extends AbstractPlugin implements DatabaseStrategyPreCheckPluginInterface
{
    /**
     * @see \PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\AbstractSetupRequestProcessorStrategyPlugin::CUSTOMER_LOGIN_MODE_SINGLE
     */
    protected const CUSTOMER_LOGIN_MODE_SINGLE = 'single_user';

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

            if (!empty($impersonationDetails[PunchoutCatalogConstsInterface::IS_PUNCHOUT])
                && isset($impersonationDetails[PunchoutCatalogConstsInterface::PUNCHOUT_LOGIN_MODE])
                && ($impersonationDetails[PunchoutCatalogConstsInterface::PUNCHOUT_LOGIN_MODE] === self::CUSTOMER_LOGIN_MODE_SINGLE)
            ) {
                return false;
            }
        }

        return true;
    }
}
