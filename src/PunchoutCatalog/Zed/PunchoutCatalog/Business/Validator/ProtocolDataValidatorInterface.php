<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator;

use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;

interface ProtocolDataValidatorInterface
{
    /**
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer $protocolDataTransfer*
     *
     * @return bool
     */
    public function validate(PunchoutCatalogProtocolDataTransfer $protocolDataTransfer): bool;
}
