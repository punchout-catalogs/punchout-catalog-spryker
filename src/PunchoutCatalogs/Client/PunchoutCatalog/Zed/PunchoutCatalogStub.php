<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Client\PunchoutCatalog\Zed;

use Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use PunchoutCatalogs\Client\PunchoutCatalog\Dependency\Client\PunchoutCatalogToZedRequestClientInterface;

class PunchoutCatalogStub implements PunchoutCatalogStubInterface
{
    /**
     * @var \PunchoutCatalogs\Client\PunchoutCatalog\Dependency\Client\PunchoutCatalogToZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * @param \PunchoutCatalogs\Client\PunchoutCatalog\Dependency\Client\PunchoutCatalogToZedRequestClientInterface $zedRequestClient
     */
    public function __construct(PunchoutCatalogToZedRequestClientInterface $zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCartTransfer(PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer): PunchoutCatalogCartResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $response */
        $response = $this->zedRequestClient->call('/punchout-catalog/gateway/process-cart-transfer', $punchoutCatalogCartRequestTransfer);

        return $response;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer $punchoutCatalogCancelRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCartCancel(PunchoutCatalogCancelRequestTransfer $punchoutCatalogCancelRequestTransfer): PunchoutCatalogCartResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $response */
        $response = $this->zedRequestClient->call('/punchout-catalog/gateway/process-cart-cancel', $punchoutCatalogCancelRequestTransfer);

        return $response;
    }
}
