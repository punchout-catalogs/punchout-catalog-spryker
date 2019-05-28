<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;


use Codeception\Test\Unit;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Converter;

class ConverterTest extends Unit
{
    /**
     * @var Converter
     */
    protected $converter;

    /**
     *
     */
    public function setUp()
    {
        $this->converter = new Converter();
        return parent::setUp();
    }

    public function testConvert()
    {
        $mapping = json_decode($this->getMappingJson(), JSON_OBJECT_AS_ARRAY);
        $resultMapping = $this->converter->convert($mapping);

        $foundCustomer = false;
        $foundCartItem = false;
        foreach ($resultMapping->getObjects() as $objectTransfer) {
            if ($objectTransfer->getName() === 'customer') {
                $foundCustomer = true;
            }
            if ($objectTransfer->getName() === 'cart_item') {
                $foundCartItem = true;
            }
        }
        $this->assertTrue($foundCustomer, 'Found customer field at result mapping');
        $this->assertTrue($foundCartItem, 'Found cart item field at result mapping');
        $this->assertSameSize($mapping, $resultMapping->getObjects());

//        codecept_debug($resultMapping);

    }

    protected function getMappingJson()
    {
        return <<<JSON
{  "customer": {
    "fields": {
      "first_name": {
        "path": "/cXML/Request[1]/PunchOutSetupRequest[1]/Extrinsic[@name='FirstName']"
      },
      "last_name": {
        "path": "/cXML/Request[1]/PunchOutSetupRequest[1]/Extrinsic[@name='LastName']"
      },
      "email": {
        "path": "/cXML/Request[1]/PunchOutSetupRequest[1]/Extrinsic[@name='UserEmail']"
      }
    }
  },
  "cart_item": {
    "fields": {
      "internal_id":{
        "path": "/cXML/Request[1]/PunchOutSetupRequest[1]/ItemOut/ItemID[1]/SupplierPartAuxiliaryID"
      }
    }
  }
}
JSON;
    }
}
