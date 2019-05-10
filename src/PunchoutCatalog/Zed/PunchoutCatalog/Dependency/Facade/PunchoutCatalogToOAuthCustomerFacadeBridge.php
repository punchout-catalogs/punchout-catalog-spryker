<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\OauthResponseTransfer;

class PunchoutCatalogToOAuthCustomerFacadeBridge implements PunchoutCatalogToOAuthCustomerFacadeInterface
{
    // TODO: hack to simulate OAuth access token generation behavior.
    protected $oAuthCustomerFacade;

    public function __construct()
    {
    }

    /**
     * Specification
     * - Expects Customer.customerReference
     * - Expects Customer.companyUserTransfer.idCompanyUser
     * - Expects Customer.punchoutCatalogImpersonationDetails
     * - Optionally expects Customer.companyUserTransfer.companyBusinessUnit.idCompanyBusinessUnit
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\OauthResponseTransfer
     */
    public function getAccessToken(CustomerTransfer $customerTransfer): OauthResponseTransfer
    {
        return (new OauthResponseTransfer())
            ->setAccessToken('GEkpZfphbIGe7HEAJIsLGEkpZfphbIGe7HEAJIsLGEkpZfphbIGe7HEAJIsL') // ~ 874 characters long
            ->setIsValid(true);
    }
}
