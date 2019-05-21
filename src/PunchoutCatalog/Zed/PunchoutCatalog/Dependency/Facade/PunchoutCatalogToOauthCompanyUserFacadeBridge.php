<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\OauthResponseTransfer;
use Spryker\Zed\OauthCompanyUser\Business\OauthCompanyUserFacadeInterface;

class PunchoutCatalogToOauthCompanyUserFacadeBridge implements PunchoutCatalogToOauthCompanyUserFacadeInterface
{
    /**
     * @var OauthCompanyUserFacadeInterface
     */
    protected $oauthCompanyUserFacade;

    /**
     * @param OauthCompanyUserFacadeInterface $oAuthCustomerFacade
     */
    public function __construct($oAuthCustomerFacade)
    {
        $this->oauthCompanyUserFacade = $oAuthCustomerFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\OauthResponseTransfer
     */
    public function createCompanyUserAccessToken(CustomerTransfer $customerTransfer): OauthResponseTransfer
    {
        return $this->oauthCompanyUserFacade->createCompanyUserAccessToken($customerTransfer);
    }
}
