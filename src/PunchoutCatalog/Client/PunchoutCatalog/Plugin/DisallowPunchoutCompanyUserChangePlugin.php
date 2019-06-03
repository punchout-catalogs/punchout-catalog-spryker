<?php

namespace PunchoutCatalog\Client\PunchoutCatalog\Plugin;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Client\BusinessOnBehalfExtension\Dependency\Plugin\CompanyUserChangeAllowedCheckPluginInterface;
use Spryker\Client\Kernel\AbstractPlugin;

/**
 * @method \PunchoutCatalog\Client\PunchoutCatalog\PunchoutCatalogFactory getFactory()
 * @method \PunchoutCatalog\Client\PunchoutCatalog\PunchoutCatalogClient getClient()
 */
class DisallowPunchoutCompanyUserChangePlugin extends AbstractPlugin implements CompanyUserChangeAllowedCheckPluginInterface
{
    /**
     * {@inheritdoc}
     * - Returns true and disables company user change when provided customer is logged in through punchout.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return bool
     */
    public function check(CustomerTransfer $customerTransfer): bool
    {
        $impersonationDetails = $customerTransfer->getPunchoutCatalogImpersonationDetails();

        // @todo Recommendation: use shared constant to ensure key matching
        return empty($impersonationDetails['is_punchout']);
    }
}
