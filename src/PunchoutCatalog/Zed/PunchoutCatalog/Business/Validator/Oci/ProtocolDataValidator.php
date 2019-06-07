<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\Oci;

use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\ProtocolDataValidatorInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Exception\AuthenticateException;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;

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
