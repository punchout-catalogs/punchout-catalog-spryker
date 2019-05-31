<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping\Oci;


use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartCustomerTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Oci\Encoder;

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
        $customer->setFirstName('oci');
        $customer->setLastName('Tester');
        $customer->setEmail('oci@punchoutcatalogs.net');
        $customer->setInternalId('InternalId');
        $transferData->setCustomer($customer);

        $cartItem = new PunchoutCatalogDocumentCartItemTransfer();
        $cartItem->setInternalId('fakeInternalId');
        $cartItem->setLineNumber(1);
        $transferData->addCartItem($cartItem);

        $cartItem = new PunchoutCatalogDocumentCartItemTransfer();
        $cartItem->setInternalId('fakeInternalId2');
        $cartItem->setLineNumber(2);
        $transferData->addCartItem($cartItem);

        $document = $this->encoder->execute($mapping, $transferData);

        $this->assertIsArray($document, 'Should be array');
        $this->assertEquals($this->getTestData(), $document, 'Should be the same xml data');
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
                        ->setPath(['first_name']))
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('last_name')
                        ->setPath(['last_name']))
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('email')
                        ->setPath(['email']))
                    ->setName('customer')
            )
            ->addObject(
                (new PunchoutCatalogMappingObjectTransfer())
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('internal_id')
                        ->setPath(['NEW_ITEM-EXT_PRODUCT_ID[%line_number%]'])
                    )
                    ->setName('cart_item')
                    ->setIsMultiple(true)
            );
    }

    public function getTestData()
    {
        return [
            'first_name' => 'oci',
            'last_name' => 'Tester',
            'email' => 'oci@punchoutcatalogs.net',
            'NEW_ITEM-EXT_PRODUCT_ID[1]' => 'fakeInternalId',
            'NEW_ITEM-EXT_PRODUCT_ID[2]' => 'fakeInternalId2',
        ];
    }

    /**
     *
     */
    public function setUp(): void
    {
        $this->encoder = new Encoder();
        parent::setUp();
    }
}
