<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client;

use Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;

class PunchoutCatalogToPunchoutCatalogClientBridge implements PunchoutCatalogToPunchoutCatalogClientInterface
{
    /**
     * @var \PunchoutCatalog\Client\PunchoutCatalog\PunchoutCatalogClientInterface
     */
    protected $punchoutCatalogClient;

    /**
     * @param \PunchoutCatalog\Client\PunchoutCatalog\PunchoutCatalogClientInterface $punchoutCatalogClient
     */
    public function __construct($punchoutCatalogClient)
    {
        $this->punchoutCatalogClient = $punchoutCatalogClient;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCartTransfer(PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer): PunchoutCatalogCartResponseTransfer
    {
        return $this->punchoutCatalogClient->processCartTransfer($punchoutCatalogCartRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer $punchoutCatalogCancelRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCartCancel(PunchoutCatalogCancelRequestTransfer $punchoutCatalogCancelRequestTransfer): PunchoutCatalogCartResponseTransfer
    {
        return $this->punchoutCatalogClient->processCartCancel($punchoutCatalogCancelRequestTransfer);
    }
}
