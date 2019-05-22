<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\OauthCompanyUser;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\OauthRequestTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\OauthCompanyUserExtension\Dependency\Plugin\CustomerOauthRequestMapperPluginInterface;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class ImpersonationDetailsCustomerOauthRequestMapperPlugin extends AbstractPlugin implements CustomerOauthRequestMapperPluginInterface
{
    /**
     * {@inheritdoc}
     * - Maps Punchout impersonation details from CustomerTransfer into access token request.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OauthRequestTransfer $oauthRequestTransfer
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\OauthRequestTransfer
     */
    public function map(OauthRequestTransfer $oauthRequestTransfer, CustomerTransfer $customerTransfer): OauthRequestTransfer
    {
        $oauthRequestTransfer->setPunchoutCatalogImpersonationDetails($customerTransfer->getPunchoutCatalogImpersonationDetails());

        return $oauthRequestTransfer;
    }
}
