<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Yves\PunchoutCatalogPage\Controller;

use Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use SprykerShop\Yves\ShopApplication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \PunchoutCatalogs\Yves\PunchoutCatalogPage\PunchoutCatalogPageFactory getFactory()
 */
class CartController extends AbstractController
{
    /**
     * @todo: fix: product description + product description in options
     *
     * Return transferred cart
     */
    public function transferAction()
    {
        $quoteTransfer = $this->getFactory()->getQuoteClient()->getQuote();

        //-------------------------------------------//
        //$quoteTransfer = $this->getFakeQuoteTransfer();
        //-------------------------------------------//

        $punchoutCatalogCartRequestTransfer = $this->getFactory()
            ->getTransferCartMapper()
            ->mapQuoteTransferToPunchoutCatalogCartRequestTransfer(
                $quoteTransfer,
                new PunchoutCatalogCartRequestTransfer()
            );

        /** @var \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $punchoutCatalogCartResponseTransfer */
        $punchoutCatalogCartResponseTransfer = $this->getFactory()
            ->getPunchoutCatalogClient()
            ->processCartTransfer($punchoutCatalogCartRequestTransfer);

        $response = $this->createResponse($punchoutCatalogCartResponseTransfer->getContentType());
        $viewData = [
            'content' => $punchoutCatalogCartResponseTransfer->getContent(),
        ];

        return $this->getApplication()->render('@PunchoutCatalogPage/views/cart/transfer.twig', $viewData, $response);
    }

    /**
     * Return empty transferred cart
     */
    public function cancelAction()
    {
        $punchoutCatalogCancelRequestTransfer = new PunchoutCatalogCancelRequestTransfer();

        /** @var \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $punchoutCatalogCartResponseTransfer */
        $punchoutCatalogCartResponseTransfer = $this->getFactory()
            ->getPunchoutCatalogClient()
            ->processCartCancel($punchoutCatalogCancelRequestTransfer);

        $response = $this->createResponse($punchoutCatalogCartResponseTransfer->getContentType());
        $viewData = [
            'content' => $punchoutCatalogCartResponseTransfer->getContent(),
        ];

        return $this->getApplication()->render('@PunchoutCatalogPage/views/cart/transfer.twig', $viewData, $response);
    }

    /**
     * @param string $contentType
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function createResponse(string $contentType): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', $contentType);

        return $response;
    }

    protected function getFakeQuoteTransfer()
    {
        $testFile = file_get_contents('/data/shop/development/current/data/DE/logs/quote2.json');
        $quoteTransferJson = json_decode($testFile, true);
        $quoteTransfer = new QuoteTransfer();
        return $quoteTransfer->fromArray($quoteTransferJson);
    }
}
