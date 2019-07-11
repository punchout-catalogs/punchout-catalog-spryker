<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Client\PunchoutCatalog;

use Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;

interface PunchoutCatalogClientInterface
{
    /**
     * Specification:
     * -
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCartTransfer(PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer): PunchoutCatalogCartResponseTransfer;

    /**
     * Specification:
     * -
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer $punchoutCatalogCancelRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCartCancel(PunchoutCatalogCancelRequestTransfer $punchoutCatalogCancelRequestTransfer): PunchoutCatalogCartResponseTransfer;
}
