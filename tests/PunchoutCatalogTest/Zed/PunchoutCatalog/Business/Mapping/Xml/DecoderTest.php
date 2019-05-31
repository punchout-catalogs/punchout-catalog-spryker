<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping\Xml;


use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\Decoder;

class DecoderTest extends Unit
{
    /**
     * @var Decoder
     */
    protected $decoder;

    public function testExecute()
    {
        $mapping = $this->createMapping();
        $source = simplexml_load_string($this->getSetupRequestCxml());
        $document = $this->decoder->execute($mapping, $source);

        $this->assertEquals($this->getSetupRequestData(), $document, 'Should be the same data');
    }

    /**
     * @return PunchoutCatalogMappingTransfer
     */
    protected function createMapping(): PunchoutCatalogMappingTransfer
    {
        return (new PunchoutCatalogMappingTransfer())
            ->addObject(
                (new PunchoutCatalogMappingObjectTransfer())
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('first_name')
                        ->setPath(['/cXML/Request[1]/PunchOutSetupRequest[1]/Extrinsic[@name=\'FirstName\']']))
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('last_name')
                        ->setPath(['/cXML/Request[1]/PunchOutSetupRequest[1]/Extrinsic[@name=\'LastName\']']))
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('email')
                        ->setPath(['/cXML/Request[1]/PunchOutSetupRequest[1]/Extrinsic[@name=\'UserEmail\']']))
                    ->setName('customer')
            )
            ->addObject(
                (new PunchoutCatalogMappingObjectTransfer())
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('internal_id')
                        ->setPath(['/cXML/Request[1]/PunchOutSetupRequest[1]/ItemOut/ItemID[1]/SupplierPartAuxiliaryID']))
                    ->setName('cart_item')
                    ->setIsMultiple(true)
            );
    }

    /**
     * @return string
     */
    protected function getSetupRequestCxml(): string
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
                <Identity>user_1</Identity>
                <SharedSecret>user_1_pass</SharedSecret>
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
     * @see
     * @return array
     */
    protected function getSetupRequestData()
    {
        return [
            'customer' => [
                'first_name' => 'cXML',
                'last_name' => 'Tester',
                'email' => 'cxml@punchoutcatalogs.net',
            ],
            'cart_item' => [
                [
                    'internal_id' => 'fakeInternalId',
                ],
            ],
        ];
    }

    /**
     *
     */
    public function setUp(): void
    {
        $this->decoder = new Decoder();
        parent::setUp();
    }
}
