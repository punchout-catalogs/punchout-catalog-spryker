<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformParamsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\SplitCommand;

class SplitCommandTest extends Unit
{
    /**
     * @var SplitCommand
     */
    protected $command;

    public function testDefault()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setSep(','));
        $value = 'a,b,c';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('a', $result);
    }

    public function testAll()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setSep(',')->setIndex('all'));
        $value = 'a,b,c';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals(['a', 'b', 'c'], $result);
    }

    public function testLast()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setSep(',')->setIndex('last'));
        $value = 'a,b,c';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('c', $result);
    }
    
    public function setUp(): void
    {
        $this->command = new SplitCommand();
        parent::setUp();
    }
}
