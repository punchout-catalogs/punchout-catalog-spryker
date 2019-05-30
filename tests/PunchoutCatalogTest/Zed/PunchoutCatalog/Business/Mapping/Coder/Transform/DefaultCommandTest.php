<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformParamsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\DefaultCommand;

class DefaultCommandTest extends Unit
{
    /**
     * @var DefaultCommand
     */
    protected $command;

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setValue('default_value'));
        $value = 'some_value';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('some_value', $result);
        $nullValue = null;
        $defaultResult = $this->command->execute($transform, $nullValue);
        $this->assertEquals('default_value', $defaultResult);
    }

    public function testArray()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setValue('default_value'));
        $value = [
            'value',
            false,
            null,
            '',
        ];
        $result = $this->command->execute($transform, $value);
        $this->assertEquals([
            'value',
            'default_value',
            'default_value',
            'default_value',
        ], $result);
    }

    public function setUp()
    {
        $this->command = new DefaultCommand();
        parent::setUp();
    }

}
