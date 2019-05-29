<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping\Xml;


use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartCustomerTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\Encoder;

class EncoderTest extends Unit
{
    /**
     * @var Encoder
     */
    protected $encoder;

    public function testExecute()
    {
        $mapping = $this->createMapping();

        $transferData = new PunchoutCatalogCartRequestTransfer();

        $customer = new PunchoutCatalogDocumentCartCustomerTransfer();
        $customer->setFirstName('cXML');
        $customer->setLastName('Tester');
        $customer->setEmail('cxml@punchoutcatalogs.net');
        $customer->setInternalId('InternalId');
        $transferData->setCustomer($customer);

        $cartItem = new PunchoutCatalogDocumentCartItemTransfer();
        $cartItem->setInternalId('fakeInternalId');
        $transferData->addCartItem($cartItem);

        $cartItem = new PunchoutCatalogDocumentCartItemTransfer();
        $cartItem->setInternalId('fakeInternalId2');
        $transferData->addCartItem($cartItem);

        $cart = new PunchoutCatalogDocumentCartTransfer();
        $cart->setCartNote('CartNote');
        $transferData->setCart($cart);

        $document = $this->encoder->execute($mapping, $transferData);

        $this->assertInstanceOf(\SimpleXMLElement::class, $document, 'Should be the xml document');
        $this->assertEquals($this->getXml()->__toString(), $document->asXML(), 'Should be the same xml data');
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
                        ->setName('cart_note')
                        ->setPath(['/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Comments[1]'])
                    )
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('grand_total')
                        ->setPath(['/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Total[1]/Money[1]'])
                    )
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('tax_total')
                        ->setPath(['/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Tax[1]/Money[1]'])
                    )
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('tax_description')
                        ->setPath(['/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Tax[1]/Description[1]'])
                    )
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('discount_total')
                        ->setPath(['/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Discount[1]/Money[1]'])
                    )
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('discount_description')
                        ->setPath(['/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Discount[1]/Description[1]'])
                    )
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('currency')
                        ->setIsAppend(true)
                        ->setPath(['/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Total[1]/Money[1]/@currency,/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Tax[1]/Money[1]/@currency,/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Discount[1]/Money[1]/@currency'])
                    )
                    ->setName('cart')
            )
            ->addObject(
                (new PunchoutCatalogMappingObjectTransfer())
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('internal_id')
                        ->setPath(['ItemID[1]/SupplierPartAuxiliaryID[1]'])
                    )
                    ->setName('cart_item')
                    ->setIsMultiple(true)
                    ->setPath(['/cXML/Message[1]/PunchOutOrderMessage[1]/ItemIn'])
            );
    }

    public function getXml()
    {
        $xml = new \Codeception\Util\XmlBuilder();
        $xml->getDom()->encoding = 'UTF-8';
        $xml
            ->cXML
            ->Request
            ->PunchOutSetupRequest
            ->Extrinsic
            ->attr('name', 'FirstName')
            ->val('cXML')
            ->parent()
            ->Extrinsic
            ->attr('name', 'LastName')
            ->val('Tester')
            ->parent()
            ->Extrinsic
            ->attr('name', 'UserEmail')
            ->val('cxml@punchoutcatalogs.net')
            ->parent()
            ->parent()
            ->parent()
            ->Message
            ->PunchOutOrderMessage
            ->PunchOutOrderMessageHeader
            ->Comments
            ->val('CartNote')
            ->parent()
            ->parent()
            ->ItemIn
            ->ItemID
            ->SupplierPartAuxiliaryID
            ->val('fakeInternalId')
            ->parent()
            ->parent()
            ->parent()
            ->ItemIn
            ->ItemID
            ->SupplierPartAuxiliaryID
            ->val('fakeInternalId2');
        return $xml;
    }

    /**
     *
     */
    public function setUp()
    {
        $this->encoder = new Encoder();
        return parent::setUp();
    }
}
