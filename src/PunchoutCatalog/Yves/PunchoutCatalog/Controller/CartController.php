<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Controller;

use Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestContext;

use SprykerShop\Yves\ShopApplication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogFactory getFactory()
 */
class CartController extends AbstractController
{
    protected const REDIRECT_URL = 'cart';
    protected const ERROR_MESSAGE_IS_NOT_PUNCHOUT = 'punchout-catalog.error.is_not_punchout';
    /**
     * Return transferred cart
     */
    public function transferAction()
    {
        if (!$this->isPunchout()) {
            return $this->addErrorMessage(static::ERROR_MESSAGE_IS_NOT_PUNCHOUT)
                ->redirectResponseInternal(static::REDIRECT_URL);
        }
        
        $quoteTransfer = $this->getFactory()->getQuoteClient()->getQuote();
        //$quoteTransfer = $this->getFakeQuoteTransfer();

        $punchoutCatalogCartRequestTransfer = $this->getFactory()
            ->getTransferCartMapper()
            ->mapQuoteTransferToPunchoutCatalogCartRequestTransfer(
                $quoteTransfer,
                new PunchoutCatalogCartRequestTransfer()
            );
        
        $punchoutCatalogCartRequestTransfer->setContext($this->getPunchoutCatalogCartRequestContext());
        
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
        if (!$this->isPunchout()) {
            return $this->addErrorMessage(static::ERROR_MESSAGE_IS_NOT_PUNCHOUT)
                ->redirectResponseInternal(static::REDIRECT_URL);
        }
        
        $punchoutCatalogCancelRequestTransfer = new PunchoutCatalogCancelRequestTransfer();
        $punchoutCatalogCancelRequestTransfer->setContext($this->getPunchoutCatalogCartRequestContext());
        
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
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        return $response;
    }
    
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $cartResponseTransfer
     *
     * @return Response
     */
    protected function handleSuccessResponse(PunchoutCatalogCartResponseTransfer $cartResponseTransfer): Response
    {
        //@todo: use $url in widget, not here as Widget has access to session object
        $url = 'https://dev.buyerquest.net/cs3/punchoutclient/transactions/cxmlresponse/conn_id/12/';
        //$url = 'https://dev.buyerquest.net/cs3/punchoutclient/transactions/ociresponse/conn_id/18/';
        //$url = 'https://demo.punchoutexpress.com/gateway/testconn/';
        //$url = 'http://www.de.suite-nonsplit.local/punchout-catalog/?punchout-poom=1';
        
        $response = $this->createResponse('text/html');
    
        $viewData = [
            'fields' => $cartResponseTransfer->getFields(),
            'submit_url' => $url,
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
     * @return PunchoutCatalogCartRequestContextTransfer
     */
    protected function getPunchoutCatalogCartRequestContext()
    {
        $impersonalDetails = $this->getFactory()->getCustomerClient()
            ->getCustomer()
            ->getPunchoutCatalogImpersonationDetails();
        
        $context = new PunchoutCatalogCartRequestContextTransfer();
        $context->fromArray([
            'locale' => 'en-US-Fake',
            'punchout_catalog_connection_id' => $impersonalDetails['punchout_catalog_connection_id'],
            'protocol_data' => $impersonalDetails['protocol_data'],
        ]);
        
        return $context;
    }
    
    /**
     * @return bool
     */
    protected function isPunchout()
    {
        return ($this->getPunchoutDetails() && $this->getPunchoutDetails()['is_punchout']);
    }
    
    /**
     * @return array|null
     */
    protected function getPunchoutDetails()
    {
        if ($this->getFactory()->getCustomerClient()->getCustomer()
            && $this->getFactory()->getCustomerClient()->getCustomer()->getPunchoutCatalogImpersonationDetails()
        ) {
            return $this->getFactory()->getCustomerClient()->getCustomer()->getPunchoutCatalogImpersonationDetails();
        }
        
        return null;
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
