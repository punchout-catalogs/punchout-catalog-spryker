<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\Oci;

use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\ProtocolDataValidatorInterface;

class ProtocolDataValidator implements ProtocolDataValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer $protocolDataTransfer
     * @param bool $validateSecrets
     *
     * @return bool
     */
    public function validate(PunchoutCatalogProtocolDataTransfer $protocolDataTransfer, bool $validateSecrets = true): bool
    {
        $protocolDataTransfer
            ->requireCart();

        $protocolDataTransfer
            ->getCart()
                ->requireUrl()
                ->requireOperation();

        if ($validateSecrets) {
            $protocolDataTransfer->requireOciCredentials();

            $protocolDataTransfer
                ->getOciCredentials()
                ->requireUsername()
                ->requirePassword();
        }

        return true;
    }
}
