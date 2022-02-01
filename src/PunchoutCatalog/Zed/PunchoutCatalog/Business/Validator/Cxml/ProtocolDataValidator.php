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
     * @param bool $validateSecrets
     *
     * @return bool
     */
    public function validate(PunchoutCatalogProtocolDataTransfer $protocolDataTransfer, bool $validateSecrets = true): bool
    {
        $protocolDataTransfer
            ->requireCart()
            ->requireCxmlToCredentials();

        $protocolDataTransfer->getCart()
            ->requireUrl()
            ->requireOperation()
            ->requireBuyerCookie()
            ->requireDeploymentMode();

        if ($validateSecrets) {
            $protocolDataTransfer
                ->requireCxmlFromCredentials()
                ->requireCxmlSenderCredentials();

            $protocolDataTransfer->getCxmlSenderCredentials()
                ->requireIdentity()
                ->requireDomain();

            $protocolDataTransfer->getCxmlSenderCredentials()->requireSharedSecret();
        }

        $protocolDataTransfer->getCxmlToCredentials()
            ->requireIdentity()
            ->requireDomain();

        return true;
    }
}
