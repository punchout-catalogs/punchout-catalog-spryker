<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Controller;

use Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;

use SprykerShop\Yves\ShopApplication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogFactory getFactory()
 */
class CartController extends AbstractController
{
    protected const REDIRECT_URL = 'cart';
    
    /**
     * @todo: fix: product description + product description in options
     *
     * Return transferred cart
     */
    public function transferAction()
    {
        $quoteTransfer = $this->getFactory()->getQuoteClient()->getQuote();
        //-------------------------------------------//
        $quoteTransfer = $this->getFakeQuoteTransfer();
        //-------------------------------------------//

        $punchoutCatalogCartRequestTransfer = $this->getFactory()
            ->getTransferCartMapper()
            ->mapQuoteTransferToPunchoutCatalogCartRequestTransfer(
                $quoteTransfer,
                new PunchoutCatalogCartRequestTransfer()
            );

        /** @var \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $cartResponseTransfer */
        $cartResponseTransfer = $this->getFactory()
            ->getPunchoutCatalogClient()
            ->processCartTransfer($punchoutCatalogCartRequestTransfer);

        if ($cartResponseTransfer->getIsSuccess()) {
            return $this->handleSuccessResponse($cartResponseTransfer);
        } else {
            return $this->handleErrorResponse($cartResponseTransfer);
        }
    }

    /**
     * Return empty transferred cart
     */
    public function cancelAction()
    {
        $punchoutCatalogCancelRequestTransfer = new PunchoutCatalogCancelRequestTransfer();
    
        /** @var \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $cartResponseTransfer */
        $cartResponseTransfer = $this->getFactory()
            ->getPunchoutCatalogClient()
            ->processCartCancel($punchoutCatalogCancelRequestTransfer);
    
        if ($cartResponseTransfer->getIsSuccess()) {
            return $this->handleSuccessResponse($cartResponseTransfer);
        } else {
            return $this->handleErrorResponse($cartResponseTransfer);
        }
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
    
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $cartResponseTransfer
     *
     * @return Response
     */
    protected function handleSuccessResponse(PunchoutCatalogCartResponseTransfer $cartResponseTransfer): Response
    {
        $response = $this->createResponse('text/html');
    
        $viewData = [
            'fields' => $cartResponseTransfer->getFields(),
            'submit_url' => $this->getFormSubmitUrl(),
        ];
    
        return $this->getApplication()->render('@PunchoutCatalog/views/cart/transfer.twig', $viewData, $response);
    }
    
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $cartResponseTransfer
     *
     * @return Response
     */
    protected function handleErrorResponse(PunchoutCatalogCartResponseTransfer $cartResponseTransfer): Response
    {
        $messages = [];
        
        if ($cartResponseTransfer->getMessages()) {
            foreach ($cartResponseTransfer->getMessages() as $message) {
                $messages[] = $message->getValue();
            }
        }
        
        return $this->addErrorMessage(implode("\n", $messages))
            ->redirectResponseInternal(static::REDIRECT_URL);
    }
    
    /**
     * @todo: fix it
     *
     * @return string
     */
    protected function getFormSubmitUrl(): string
    {
        return 'https://demo.punchoutexpress.com/gateway/testconn/';
    }
    
    /**
     * @todo: fix it
     *
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return 'http://www.de.suite-nonsplit.local/cart';
    }
    
    /**
     * @todo: remove it
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function getFakeQuoteTransfer()
    {
        $testFile = file_get_contents('/data/shop/development/current/data/DE/logs/quote.json');
        $quoteTransferJson = json_decode($testFile, true);
        $quoteTransfer = new \Generated\Shared\Transfer\QuoteTransfer();
        return $quoteTransfer->fromArray($quoteTransferJson);
    }
}
