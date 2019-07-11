<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;

interface PunchoutCatalogRequestProcessorStrategyPluginInterface
{
    /**
     * Specification:
     * - Decides if the current processor is able to process the request.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return bool
     */
    public function isApplicable(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): bool;

    /**
     * Specification:
     * - Processes request.
     * - Returns with prepared content and content type.
     * - Adds error message and sets "isSuccess=false" in case of error.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     */
    public function processRequest(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupResponseTransfer;

    /**
     * Specification:
     * - Prepares error response.
     * - Returns with prepared content and content type.
     * - Adds error message and sets "isSuccess=false".
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MessageTransfer $messageTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     */
    public function processError(MessageTransfer $messageTransfer): PunchoutCatalogSetupResponseTransfer;
}
