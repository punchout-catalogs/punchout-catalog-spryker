<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformParamsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\MapCommand;

class MapCommandTest extends Unit
{
    /**
     * @var MapCommand
     */
    protected $command;

    public function testSingleValue()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = 'Some_Value';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('Some_Value', $result);
    }

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setValue('value')->setResult(1));
        $value = [
            'some',
            'value',
            'text',
            '',
            'value',
        ];
        $result = $this->command->execute($transform, $value);
        $this->assertEquals([
            'some',
            1,
            'text',
            '',
            1
        ], $result);
    }
    
    public function setUp(): void
    {
        $this->command = new MapCommand();
        parent::setUp();
    }
}
