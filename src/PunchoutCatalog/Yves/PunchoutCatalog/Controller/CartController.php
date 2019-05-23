<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Controller;

use Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestContext;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use SprykerShop\Yves\ShopApplication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogFactory getFactory()
 */
class CartController extends AbstractController
{
    protected const REDIRECT_URL = 'cart';
    protected const ERROR_MESSAGE_IS_NOT_PUNCHOUT = 'punchout-catalog.error.is-not-punchout';

    /**
     * Return transferred cart
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | \Symfony\Component\HttpFoundation\Response
     */
    public function transferAction(Request $request)
    {
        if (!$this->isPunchout()) {
            return $this->addErrorMessage(static::ERROR_MESSAGE_IS_NOT_PUNCHOUT)
                ->redirectResponseInternal(static::REDIRECT_URL);
        }

        $quoteTransfer = $this->getFactory()->getQuoteClient()->getQuote();

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
            return $this->handleSuccessResponse($cartResponseTransfer, $request);
        } else {
            return $this->handleErrorResponse($cartResponseTransfer);
        }
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
     * @return PunchoutCatalogCartRequestContextTransfer
     */
    protected function getPunchoutCatalogCartRequestContext()
    {
        $impersonalDetails = $this->getFactory()->getCustomerClient()
            ->getCustomer()
            ->getPunchoutCatalogImpersonationDetails();

        $context = new PunchoutCatalogCartRequestContextTransfer();
        $context->fromArray([
            'locale' => $this->getCurrentLocale(),
            'punchout_catalog_connection_id' => $impersonalDetails['punchout_catalog_connection_id'],
            'protocol_data' => $impersonalDetails['protocol_data'],
        ]);

        return $context;
    }

    /**
     * @return string
     */
    protected function getCurrentLocale()
    {
        return $this->getFactory()->getStore()->getCurrentLocale();
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $cartResponseTransfer
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleSuccessResponse(PunchoutCatalogCartResponseTransfer $cartResponseTransfer, Request $request): Response
    {
        $response = $this->createResponse('text/html');

        $viewData = [
            'fields' => $cartResponseTransfer->getFields(),
            'submit_url' => $this->getPunchoutDetails()['protocol_data']['cart']['url'],
            'submit_target' => $this->getPunchoutDetails()['protocol_data']['cart']['target'] ?? null,
        ];

        //Should go after the last data getting from customer session
        $this->clearQuote()->logoutCustomer();
        $request->getSession()->invalidate();
        $this->getFactory()->getTokenStorage()->setToken(null);

        return $this->getApplication()->render('@PunchoutCatalog/views/cart/transfer.twig', $viewData, $response);
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
     * Clear existing customer session
     *
     * @return $this
     */
    protected function logoutCustomer()
    {
        $this->getFactory()->getCustomerClient()->logout();
        return $this;
    }

    /**
     * Clear existing quote
     *
     * @return $this
     */
    protected function clearQuote()
    {
        $this->getFactory()->getQuoteClient()->clearQuote();
        return $this;
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
     * Return empty transferred cart
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | \Symfony\Component\HttpFoundation\Response
     */
    public function cancelAction(Request $request)
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
            return $this->handleSuccessResponse($cartResponseTransfer, $request);
        } else {
            return $this->handleErrorResponse($cartResponseTransfer);
        }
    }
}
