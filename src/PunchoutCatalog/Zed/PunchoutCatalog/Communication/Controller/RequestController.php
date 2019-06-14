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
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacadeInterface getFacade()
 */
class RequestController extends AbstractController
{
    /**
     * @var string
     */
    protected const BUSINESS_UNIT_PARAM = 'business-unit';
    
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request): Response
    {
        $punchoutCatalogRequestTransfer = $this->mapSymfonyRequestToSetupRequestTransfer($request);
        
        $processingResult = $this->getFacade()->processRequest($punchoutCatalogRequestTransfer);
        
        return (new Response())
            ->setContent($processingResult->getContent())
            ->setStatusCode(Response::HTTP_OK);
    }
    
    /**
     * @param Request $request
     *
     * @return PunchoutCatalogSetupRequestTransfer
     */
    protected function mapSymfonyRequestToSetupRequestTransfer(Request $request): PunchoutCatalogSetupRequestTransfer
    {
        $idBusinessUnit = $request->query->get(static::BUSINESS_UNIT_PARAM);
    
        $punchoutCatalogRequestTransfer = new PunchoutCatalogSetupRequestTransfer();
        $punchoutCatalogRequestTransfer->setIsSuccess(true);
        $punchoutCatalogRequestTransfer->setFkCompanyBusinessUnit((int)$idBusinessUnit);
        
        if (null === $request->getContentType() || 'form' === $request->getContentType()) {
            $punchoutCatalogRequestTransfer->setContentType(PunchoutConnectionConstsInterface::CONTENT_TYPE_FORM_MULTIPART);
        } elseif ('xml' === $request->getContentType()) {
            $punchoutCatalogRequestTransfer->setContentType(PunchoutConnectionConstsInterface::CONTENT_TYPE_TEXT_XML);
        } else {
            $punchoutCatalogRequestTransfer->setContentType($request->getContentType());
        }

        if ($request->getMethod() == Request::METHOD_GET) {
            $punchoutCatalogRequestTransfer->setContent($request->query->all());//ALL $_GET
        } elseif ($punchoutCatalogRequestTransfer->getContentType() === PunchoutConnectionConstsInterface::CONTENT_TYPE_FORM_MULTIPART) {
            $punchoutCatalogRequestTransfer->setContent($request->request->all());//ALL $_POST
        } else {
            $punchoutCatalogRequestTransfer->setContent($request->getContent());//RAW BODY
        }
    
        return $punchoutCatalogRequestTransfer;
    }

}
