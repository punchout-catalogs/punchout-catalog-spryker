<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin;

use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;

interface PunchoutCatalogProtocolStrategyPluginInterface
{
    /**
     * Specification:
     * - Decides if the current protocol is applicable for the provided request based on content and content-type.
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
     * - Sets protocol and protocol credentials based on strategy.
     * - Optionally sets protocol operation if it is already determinable.
     * - Adds error message and sets "isSuccess=false" in case of error.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer
     */
    public function setRequestProtocol(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupRequestTransfer;

    /**
     * Specification:
     * - Identifies connection based on current protocol and protocol credentials.
     * - Adds error message and sets "isSuccess=false" if no connection was found.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer
     */
    public function setPunchoutCatalogConnection(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupRequestTransfer;
}
