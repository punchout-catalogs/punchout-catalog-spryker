<?php

namespace PunchoutCatalog\Client\PunchoutCatalog\Plugin;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Client\BusinessOnBehalfExtension\Dependency\Plugin\CompanyUserChangeAllowedCheckPluginInterface;
use Spryker\Client\Kernel\AbstractPlugin;

/**
 * @method \PunchoutCatalog\Client\PunchoutCatalog\PunchoutCatalogFactory getFactory()
 * @method \PunchoutCatalog\Client\PunchoutCatalog\PunchoutCatalogClient getClient()
 */
class BusinessOnBehalfSwitchPreCheckPlugin extends AbstractPlugin implements CompanyUserChangeAllowedCheckPluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     * @return bool
     */
    public function check(CustomerTransfer $customerTransfer): bool
    {
        $impersonationDetails = $customerTransfer->getPunchoutCatalogImpersonationDetails();

        return empty($impersonationDetails['is_punchout']);
    }

}
