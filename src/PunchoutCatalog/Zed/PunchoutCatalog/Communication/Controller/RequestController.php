<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Controller;

use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use PunchoutCatalog\Shared\PunchoutCatalog\PunchoutCatalogConstsInterface;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacadeInterface getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Communication\PunchoutCatalogCommunicationFactory getFactory()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface getRepository()
 */
class RequestController extends AbstractController
{
    public const PARAM_BUSINESS_UNIT = 'business-unit';
    public const PARAM_STORE = 'store';
    
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer
     */
    protected function mapSymfonyRequestToSetupRequestTransfer(Request $request): PunchoutCatalogSetupRequestTransfer
    {
        $idBusinessUnit = $request->query->get(static::PARAM_BUSINESS_UNIT);

        $punchoutCatalogRequestTransfer = new PunchoutCatalogSetupRequestTransfer();
        $punchoutCatalogRequestTransfer->setIsSuccess(true);
        $punchoutCatalogRequestTransfer->setFkCompanyBusinessUnit((int)$idBusinessUnit);

        // @todo Plugin stack recommendation
        if ($request->getContentType() === null || $request->getContentType() === 'form') {
            $punchoutCatalogRequestTransfer->setContentType(PunchoutCatalogConstsInterface::CONTENT_TYPE_FORM_MULTIPART);
        } elseif ($request->getContentType() === 'xml') {
            $punchoutCatalogRequestTransfer->setContentType(PunchoutCatalogConstsInterface::CONTENT_TYPE_TEXT_XML);
        } else {
            $punchoutCatalogRequestTransfer->setContentType($request->getContentType());
        }

        if ($request->getMethod() == Request::METHOD_GET) {
            $punchoutCatalogRequestTransfer->setContent($request->query->all());//ALL $_GET
        } elseif ($punchoutCatalogRequestTransfer->getContentType() === PunchoutCatalogConstsInterface::CONTENT_TYPE_FORM_MULTIPART) {
            $punchoutCatalogRequestTransfer->setContent($request->request->all());//ALL $_POST
        } else {
            $punchoutCatalogRequestTransfer->setContent($request->getContent());//RAW BODY
        }


        return $punchoutCatalogRequestTransfer;
    }
}
