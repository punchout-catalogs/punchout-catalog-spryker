<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\Cxml;

use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\ProtocolDataValidatorInterface;

class ProtocolDataValidator implements ProtocolDataValidatorInterface
{
    /**
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer $protocolDataTransfer
     * @param bool $validateSharedSecret
     *
     * @return bool
     */
    public function validate(PunchoutCatalogProtocolDataTransfer $protocolDataTransfer, bool $validateSharedSecret = true): bool
    {
        $protocolDataTransfer
            ->requireCart()
            ->requireCxmlFromCredentials()
            ->requireCxmlSenderCredentials()
            ->requireCxmlToCredentials();

        $protocolDataTransfer->getCart()
            ->requireUrl()
            ->requireOperation()
            ->requireBuyerCookie()
            ->requireDeploymentMode();

        $protocolDataTransfer->getCxmlSenderCredentials()
            ->requireIdentity()
            ->requireDomain();

        if ($validateSharedSecret) {
            $protocolDataTransfer->getCxmlSenderCredentials()->requireSharedSecret();
        }

        $protocolDataTransfer->getCxmlToCredentials()
            ->requireIdentity()
            ->requireDomain();

        return true;
    }
}
