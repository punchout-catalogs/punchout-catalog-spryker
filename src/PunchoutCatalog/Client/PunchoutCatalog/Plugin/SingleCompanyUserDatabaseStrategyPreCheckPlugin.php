<?php

namespace PunchoutCatalog\Client\PunchoutCatalog\Communication\Plugin;

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
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @return bool
     */
    public function check(QuoteTransfer $quoteTransfer): bool
    {
        $impersonationDetails = $this->getFactory()->getCustomerClient()->getCustomer()->getPunchoutCatalogImpersonationDetails();

        if (isset($impersonationDetails['punchout_login_mode'])
            && $impersonationDetails['punchout_login_mode'] === 'single_user') {
            if (!empty($impersonationDetails['is_punchout'])) {
                return false;
            }
        }
        return true;
    }
}