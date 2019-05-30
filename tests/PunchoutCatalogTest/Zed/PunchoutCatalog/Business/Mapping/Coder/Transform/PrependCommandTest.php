<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformParamsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\PrependCommand;

class PrependCommandTest extends Unit
{
    /**
     * @var PrependCommand
     */
    protected $command;

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setValue('prepend_'));
        $value = 'value';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('prepend_value', $result);
    }

    public function testArray()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setValue('prepend_'));
        $value = [
            'value1',
            'value2'
        ];
        $result = $this->command->execute($transform, $value);
        $this->assertEquals([
            'prepend_value1',
            'prepend_value2',
        ], $result);
    }

    public function setUp()
    {
        $this->command = new PrependCommand();
        parent::setUp();
    }

}
