<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\Cxml;

use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\ProtocolDataValidatorInterface;

class ProtocolDataValidator implements ProtocolDataValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer $protocolDataTransfer
     * @param bool $validateSharedSecret
     *
     * @return bool
     */
    public function validate(PunchoutCatalogProtocolDataTransfer $protocolDataTransfer, bool $validateSharedSecret = true): bool
    {
        $protocolDataTransfer
            ->requireCart()
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
