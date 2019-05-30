<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformParamsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\AppendCommand;

class AppendCommandTest extends Unit
{
    /**
     * @var AppendCommand
     */
    protected $command;

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setValue('_append'));
        $value = 'value';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('value_append', $result);
    }

    public function testArray()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setValue('_append'));
        $value = [
            'value1',
            'value2'
        ];
        $result = $this->command->execute($transform, $value);
        $this->assertEquals([
            'value1_append',
            'value2_append',
        ], $result);
    }

    public function setUp()
    {
        $this->command = new AppendCommand();
        parent::setUp();
    }

}
