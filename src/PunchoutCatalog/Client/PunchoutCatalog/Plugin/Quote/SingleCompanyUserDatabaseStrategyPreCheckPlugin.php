<?php

namespace PunchoutCatalog\Client\PunchoutCatalog\Plugin\Quote;

use Generated\Shared\Transfer\QuoteTransfer;
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

            // @todo Recommendation: use shared constant to ensure key matching
            if (!empty($impersonationDetails['is_punchout'])
                && isset($impersonationDetails['punchout_login_mode'])
                && ($impersonationDetails['punchout_login_mode'] === 'single_user')
            ) {
                return false;
            }
        }

        return true;
    }
}
