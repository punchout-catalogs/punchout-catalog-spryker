<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformParamsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\AmountCommand;

class AmountCommandTest extends Unit
{
    /**
     * @var AmountCommand
     */
    protected $command;

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = '21,444.19';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals(21444.19, $result);
    }

    public function testCustomFormat()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setThousandsSep(' '));
        $value = '22 444.55';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals(22444.55, $result);
    }

    public function setUp()
    {
        $this->command = new AmountCommand();
        parent::setUp();
    }

}
