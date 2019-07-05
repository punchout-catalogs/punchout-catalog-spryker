<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping\Oci;


use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Oci\Decoder;

class DecoderTest extends Unit
{
    /**
     * @var Decoder
     */
    protected $decoder;

    public function testExecute()
    {
        $mapping = $this->createMapping();
        $source = $this->getSetupRequestOci();
        $document = $this->decoder->execute($mapping, $source);

        $this->assertNull($document['customer']['middle_name'], 'Empty node should be null');
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
                        ->setPath(['first_name']))
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('last_name')
                        ->setPath(['last_name']))
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('middle_name')
                        ->setPath(['middle_name']))
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('email')
                        ->setPath(['email']))
                    ->setName('customer')
            );
    }

    /**
     * @see
     * @return array
     */
    protected function getSetupRequestOci()
    {
        return [
            'first_name' => 'oci',
            'last_name' => 'Tester',
            'email' => 'oci@punchoutcatalogs.net',
        ];
    }

    /**
     * @return string
     */
    public function getSetupRequestData()
    {
        return [
            'customer' => [
                'first_name' => 'oci',
                'last_name' => 'Tester',
                'email' => 'oci@punchoutcatalogs.net',
                'middle_name' => null,
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
