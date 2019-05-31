<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformParamsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\CutCommand;

class CutCommandTest extends Unit
{
    /**
     * @var CutCommand
     */
    protected $command;

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setLen(4));
        $value = 'some_value';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('some', $result);
    }

    public function testArray()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setLen(5));
        $value = [
            'some1_value1',
            'some2_value2'
        ];
        $result = $this->command->execute($transform, $value);
        $this->assertEquals([
            'some1',
            'some2',
        ], $result);
    }
    
    public function setUp(): void
    {
        $this->command = new CutCommand();
        parent::setUp();
    }
}
