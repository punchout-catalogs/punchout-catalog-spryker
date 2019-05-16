<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Controller;

use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;
use Symfony\Component\HttpFoundation\Request;
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
        //@todo: remove it
        if (null === $punchoutCatalogCartRequestTransfer) {
            $punchoutCatalogCartRequestTransfer = $this->getFakeCartTransfer();
        }
        
        return  $this->filterCartResponseContext(
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
        //@todo: remove it
        if (null === $punchoutCatalogCancelRequestTransfer) {
            $punchoutCatalogCancelRequestTransfer = new PunchoutCatalogCancelRequestTransfer();
            $punchoutCatalogCancelRequestTransfer->fromArray($this->getFakeCartTransfer()->toArray(), true);
        }
        
        return $this->filterCartResponseContext(
            $this->getFacade()->processCancel($punchoutCatalogCancelRequestTransfer)
        );
    }
    
    /**
     * @param PunchoutCatalogCartResponseTransfer $response
     * @return PunchoutCatalogCartResponseTransfer
     */
    protected function filterCartResponseContext(PunchoutCatalogCartResponseTransfer $response)
    {
        return $response->setContext(null);
    }

    protected function getFakeCartTransfer()
    {
        $testFile = file_get_contents('/data/shop/development/current/data/DE/logs/cart.json');
        $transferJson = json_decode($testFile, true);
        $documentCartTransfer = new PunchoutCatalogCartRequestTransfer();
        return $documentCartTransfer->fromArray($transferJson, true);
    }
}
