<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalogExtension\Dependency\Plugin;

use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;

interface PunchoutCatalogProtocolStrategyPluginInterface
{
    /**
     * Specification:
     * - Decides if the current protocol is applicable for the provided request based on content and content-type.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return bool
     */
    public function isApplicable(PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer): bool;

    /**
     * Specification:
     * - Sets protocol and protocol credentials based on strategy.
     * - Optionally sets protocol operation if it is already determinable.
     * - Adds error message and sets "isSuccess=false" in case of error.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer
     */
    public function setRequestProtocol(PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogRequestTransfer;

    /**
     * Specification:
     * - Identifies connection based on current protocol and protocol credentials.
     * - Adds error message and sets "isSuccess=false" if no connection was found.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer
     */
    public function setPunchoutCatalogConnection(PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogRequestTransfer;
}
