<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestProcessor;

use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;

interface RequestProcessorInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     */
    public function processRequest(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupResponseTransfer;
}
