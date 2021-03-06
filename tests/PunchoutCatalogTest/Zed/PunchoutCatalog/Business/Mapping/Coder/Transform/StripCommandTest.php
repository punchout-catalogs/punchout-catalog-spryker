<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\StripCommand;

class StripCommandTest extends Unit
{
    /**
     * @var StripCommand
     */
    protected $command;


    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = '<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('Test paragraph. Other text', $result);
    }
    
    public function setUp(): void
    {
        $this->command = new StripCommand();
        parent::setUp();
    }
}
