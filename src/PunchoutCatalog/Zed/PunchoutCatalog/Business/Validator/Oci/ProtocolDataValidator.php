<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\Oci;

use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\ProtocolDataValidatorInterface;

class ProtocolDataValidator implements ProtocolDataValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer $protocolDataTransfer
     *
     * @return bool
     */
    public function validate(PunchoutCatalogProtocolDataTransfer $protocolDataTransfer): bool
    {
        $protocolDataTransfer
            ->requireCart()
            ->requireOciCredentials();

        $protocolDataTransfer
            ->getCart()
                ->requireUrl()
                ->requireOperation();
        
        $protocolDataTransfer
            ->getOciCredentials()
                ->requireUsername()
                ->requirePassword();
        
        return true;
    }
}
