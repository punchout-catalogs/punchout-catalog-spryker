<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Controller;

use Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer|null $punchoutCatalogCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCartTransferAction(?PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer = null): PunchoutCatalogCartResponseTransfer
    {
        return $this->filterCartResponseContext(
            $this->getFacade()->processCart($punchoutCatalogCartRequestTransfer)
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer|null $punchoutCatalogCancelRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCartCancelAction(?PunchoutCatalogCancelRequestTransfer $punchoutCatalogCancelRequestTransfer = null): PunchoutCatalogCartResponseTransfer
    {
        return $this->filterCartResponseContext(
            $this->getFacade()->processCancel($punchoutCatalogCancelRequestTransfer)
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $response
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    protected function filterCartResponseContext(PunchoutCatalogCartResponseTransfer $response)
    {
        return $response->setContext(null);
    }
}
