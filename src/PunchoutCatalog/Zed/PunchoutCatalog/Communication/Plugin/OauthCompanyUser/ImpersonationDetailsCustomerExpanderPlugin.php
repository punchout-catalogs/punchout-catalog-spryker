<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\OauthCompanyUser;

use Generated\Shared\Transfer\CompanyUserIdentifierTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\OauthCompanyUserExtension\Dependency\Plugin\CustomerExpanderPluginInterface;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class ImpersonationDetailsCustomerExpanderPlugin extends AbstractPlugin implements CustomerExpanderPluginInterface
{
    /**
     * {@inheritdoc}
     * - Expands CustomerTransfer with Punchout impersonation details.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     * @param \Generated\Shared\Transfer\CompanyUserIdentifierTransfer $companyUserIdentifierTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function expand(
        CustomerTransfer $customerTransfer,
        CompanyUserIdentifierTransfer $companyUserIdentifierTransfer
    ): CustomerTransfer
    {
        $customerTransfer->setPunchoutCatalogImpersonationDetails($companyUserIdentifierTransfer->getPunchoutCatalogImpersonationDetails());

        return $customerTransfer;
    }
}
