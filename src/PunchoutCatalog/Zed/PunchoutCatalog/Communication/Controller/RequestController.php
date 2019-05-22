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
    
        //@todo: remove it
        //$punchoutCatalogRequestTransfer->setContentType('text/xml');
        //$punchoutCatalogRequestTransfer->setContent($this->getFakeSetupRequestCxml());
    
        $punchoutCatalogRequestTransfer->setContentType('multipart/form-data');
        $punchoutCatalogRequestTransfer->setContent($this->getFakeSetupRequestOci());

        return $punchoutCatalogRequestTransfer;
    }
    
    /**
     * @todo: remove it
     * @return string
     */
    protected function getFakeSetupRequestCxml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.1.007/cXML.dtd">
<cXML version="1.1.007" xml:lang="en-US" payloadID="1553787174.8310848132.4653@demo.punchoutexpress.com" timestamp="2012-04-30T08:09:11-06:00">
    <Header>
        <From>
            <Credential domain="NetworkId">
                <Identity>Company1</Identity>
            </Credential>
        </From>
        <To>
            <Credential domain="DUNS">
                <Identity>Demo</Identity>
            </Credential>
        </To>
        <Sender>
            <Credential domain="NetworkId">
                <Identity>user_2</Identity>
                <SharedSecret>user_2_pass</SharedSecret>
            </Credential>
            <UserAgent>Gateway Sandbox</UserAgent>
        </Sender>
    </Header>
    <Request deploymentMode="production">
        <PunchOutSetupRequest operation="create">
            <BuyerCookie>abrakadabra</BuyerCookie>
            <!-- Customer data here -->
            <Extrinsic name="FirstName">cXML</Extrinsic>
            <Extrinsic name="LastName">Tester</Extrinsic>
            <Extrinsic name="UserEmail">cxml@punchoutcatalogs.net</Extrinsic>
            
            <Extrinsic >cxml@punchoutcatalogs.net2</Extrinsic>
            <Extrinsic name="UserEmail2" />

            <BrowserFormPost>
                <URL>https://demo.punchoutexpress.com/gateway/testconn/</URL>
            </BrowserFormPost>

            <!-- or Customer data here -->
            <Contact>
                <Name xml:lang="en-US">cXML Tester</Name>
                <Email>cxml@punchout&amp;catalogs.net</Email>
            </Contact>

            <SupplierSetup>
                <URL>https://demo.punchoutexpress.com/gateway/</URL>
            </SupplierSetup>
            <ShipTo>
                <Address>
                    <PostalAddress>
                        <DeliverTo>cXML Tester</DeliverTo>
                        <Street>Great Ocean ave, bd. 145, ap. 44</Street>
                        <City>Eureka</City>
                        <State>CA</State>
                        <PostalCode>95501</PostalCode>
                        <Country isoCountryCode="US">United States</Country>
                    </PostalAddress>
                </Address>
            </ShipTo>

            <ItemOut>
                <ItemID>
                    <SupplierPartAuxiliaryID>fakeInternalId</SupplierPartAuxiliaryID>
                </ItemID>
            </ItemOut>
            <ItemOut>
                <ItemID>
                    <SupplierPartAuxiliaryID>fakeInternalId2</SupplierPartAuxiliaryID>
                </ItemID>
            </ItemOut>
                        <ItemOut>
                <ItemID>
                    <SupplierPartAuxiliaryID>fakeInternalId3</SupplierPartAuxiliaryID>
                </ItemID>
            </ItemOut>
        </PunchOutSetupRequest>
    </Request>
</cXML>';
    }

    /**
     * @todo: remove it
     * @return array
     */
    protected function getFakeSetupRequestOci(): array
    {
        return [
            'username' => 'user_1',
            'password' => 'user_1_pass',
            'HOOK_URL' => 'http://www.test.com/cart.php2',
            'OCI_VERSION' => '4.0',
            'first_name' => 'ftest1',
            'last_name' => 'ltest2',
            'email' => 'teste@example.com',
            'returntarget' => '_top',
        ];
    }
}
