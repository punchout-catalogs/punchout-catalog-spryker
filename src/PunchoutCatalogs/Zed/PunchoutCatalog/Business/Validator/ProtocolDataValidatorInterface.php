<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business\Validator;

use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;

interface ProtocolDataValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer $protocolDataTransfer
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException
     *
     * @return bool
     */
    public function validate(PunchoutCatalogProtocolDataTransfer $protocolDataTransfer): bool;
}
