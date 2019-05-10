<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Controller;

use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogResponseTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer|null $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogResponseTransfer
     */
    public function processRequestAction(?PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer = null): PunchoutCatalogResponseTransfer
    {
        //@todo: remove it - it is fake data to test connection auth using ZED route directly in browser
        //---------------------------------------------------------------------//
        if ($punchoutCatalogRequestTransfer === null) {
            $punchoutCatalogRequestTransfer = new PunchoutCatalogRequestTransfer();
            $punchoutCatalogRequestTransfer->setCompanyUuid('729758f4-59c9-5d6a-97cf-806af1eb7e82');
            $punchoutCatalogRequestTransfer->setContentType('text/xml');
            $punchoutCatalogRequestTransfer->setContent($this->getFakeSetupRequestCxml());

            //$punchoutCatalogRequestTransfer->setContentType('multipart/form-data');
            //$punchoutCatalogRequestTransfer->setContent($this->getFakeSetupRequestOci());
        }
        //---------------------------------------------------------------------//
        return $this->getFacade()->processRequest($punchoutCatalogRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer|null $punchoutCatalogCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCartTransferAction(?PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer = null): PunchoutCatalogCartResponseTransfer
    {
        //@todo: remove it - it is fake data to test connection auth using ZED route directly in browser
        //---------------------------------------------------------------------//
        //if (null === $punchoutCatalogCartRequestTransfer) {
            $punchoutCatalogCartRequestTransfer = $this->getFakeCartTransfer();
        //}
        //---------------------------------------------------------------------//
        return $this->getFacade()->processCart($punchoutCatalogCartRequestTransfer);
    }

    /**
     * @todo: LANG need to move to PunchoutCatalogCartRequestTransfer
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCartCancelAction(): PunchoutCatalogCartResponseTransfer
    {
//        return (new PunchoutCatalogCartResponseTransfer())
//            ->setIsSuccess(true)
//            ->setContentType('text/html')
//            ->setContent('SAMPLE CANCEL');
        $punchoutCatalogCartRequestTransfer = new PunchoutCatalogCartRequestTransfer();
        return $this->getFacade()->processCart($punchoutCatalogCartRequestTransfer);
    }

    /**
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
     * @return array
     */
    protected function getFakeSetupRequestOci(): array
    {
        return [
            'username' => 'user_1',
            'password' => 'user_1_pass',
            'HOOK_URL' => 'http://www.test.com/cart.php',
            'first_name' => 'ftest1',
            'last_name' => 'ltest2',
            'email' => 'teste@example.com',
        ];
    }

    protected function getFakeCartTransfer()
    {
        $testFile = file_get_contents('/data/shop/development/current/data/DE/logs/cart2.json');
        $transferJson = json_decode($testFile, true);
        $cartTransfer = new PunchoutCatalogCartRequestTransfer();
        return $cartTransfer->fromArray($transferJson);
    }
}
