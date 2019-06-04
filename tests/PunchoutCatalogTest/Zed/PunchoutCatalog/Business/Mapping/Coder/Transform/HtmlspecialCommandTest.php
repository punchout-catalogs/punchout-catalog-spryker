<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformParamsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\HtmlspecialCommand;

class HtmlspecialCommandTest extends Unit
{
    /**
     * @var HtmlspecialCommand
     */
    protected $command;

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = '<a href=\'test\'>Test</a>';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals("&lt;a href='test'&gt;Test&lt;/a&gt;", $result);
    }

    public function testArray()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = [
            '<p>',
            'text',
        ];
        $result = $this->command->execute($transform, $value);
        $this->assertEquals([
            '&lt;p&gt;',
            'text',
        ], $result);
    }
    
    public function setUp(): void
    {
        $this->command = new HtmlspecialCommand();
        parent::setUp();
    }
}
