<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Client\PunchoutCatalog\Plugin\BusinessOnBehalf;

use Generated\Shared\Transfer\CustomerTransfer;
use PunchoutCatalog\Shared\PunchoutCatalog\PunchoutCatalogConstsInterface;
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

        return empty($impersonationDetails[PunchoutCatalogConstsInterface::IS_PUNCHOUT]);
    }
}
