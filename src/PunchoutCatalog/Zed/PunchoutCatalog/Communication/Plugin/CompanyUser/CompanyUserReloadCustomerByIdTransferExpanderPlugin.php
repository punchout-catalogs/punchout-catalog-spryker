<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\CompanyUser;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Zed\Customer\Dependency\Plugin\CustomerTransferExpanderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\CompanyUser\Business\CompanyUserFacadeInterface getFacade()
 * @method \Spryker\Zed\CompanyUser\CompanyUserConfig getConfig()
 */
class CompanyUserReloadCustomerByIdTransferExpanderPlugin extends AbstractPlugin implements CustomerTransferExpanderPluginInterface
{
    /**
     * {@inheritDoc}
     * - Reloads company user if it is already set in Customer transfer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function expandTransfer(CustomerTransfer $customerTransfer): CustomerTransfer
    {
        if (!$customerTransfer->getCompanyUserTransfer()
            || !$customerTransfer->getCompanyUserTransfer()->getUuid()
            || $customerTransfer->getCompanyUserTransfer()->getIdCompanyUser()
            || !$this->isValidEntityId($customerTransfer->getCompanyUserTransfer()->getUuid())
        ) {
            return $customerTransfer;
        }

        $companyUserTransfer = $this->getFacade()->findCompanyUserById(
            $customerTransfer->getCompanyUserTransfer()->getUuid()
        );

        if (!$companyUserTransfer) {
            return $customerTransfer;
        }

        return $customerTransfer->setCompanyUserTransfer($companyUserTransfer);
    }

    /**
     * @param string|int $str
     *
     * @return bool
     */
    protected function isValidEntityId($id)
    {
        return is_numeric($id);
    }
}
