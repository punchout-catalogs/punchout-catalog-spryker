<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\NotCommand;

class NotCommandTest extends Unit
{
    /**
     * @var NotCommand
     */
    protected $command;

    public function testSingleValue()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $result = $this->command->execute($transform, true);
        $this->assertEquals(false, $result);
        $result = $this->command->execute($transform, 'true');
        $this->assertEquals(false, $result);
        $result = $this->command->execute($transform, false);
        $this->assertEquals(true, $result);
        $result = $this->command->execute($transform, 'yes');
        $this->assertEquals(false, $result);
    }

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = [
            'yes',
            'no',
            'true',
            'false',
            true,
            false,
            '1',
            '0',
        ];
        $result = $this->command->execute($transform, $value);
        $this->assertEquals([
            false,
            true,
            false,
            true,
            false,
            true,
            false,
            true,
        ], $result);
    }
    
    public function setUp(): void
    {
        $this->command = new NotCommand();
        parent::setUp();
    }
}
