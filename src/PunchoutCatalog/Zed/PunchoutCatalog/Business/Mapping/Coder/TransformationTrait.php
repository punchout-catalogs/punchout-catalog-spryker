<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder;

use Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer;
use InvalidArgumentException;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\AmountCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\AmountFormattedCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\AppendCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\CutCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\DateCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\DefaultCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\HtmlspecialCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\JoinCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\LowercaseCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\MapCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\NotCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\PrependCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\RoundCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\SplitCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\StripCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\UppercaseCommand;

trait TransformationTrait
{
    /**
     * @var array|\PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform[]
     */
    protected $transformations = [];

    public function __construct(?array $transformations = null)
    {
        if ($transformations !== null) {
            $this->transformations = $transformations;
        } else {
            $this->transformations = $this->getDefaultTransformations();
        }
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform[]
     */
    protected function getDefaultTransformations(): array
    {
        return [
            'default' => new DefaultCommand(),
            'join' => new JoinCommand(),
            'split' => new SplitCommand(),
            'cut' => new CutCommand(),
            'uppercase' => new UppercaseCommand(),
            'lowercase' => new LowercaseCommand(),
            'not' => new NotCommand(),
            'date' => new DateCommand(),
            'append' => new AppendCommand(),
            'prepend' => new PrependCommand(),
            'map' => new MapCommand(),
            'amount' => new AmountCommand(),
            'famount' => new AmountFormattedCommand(),
            'round' => new RoundCommand(),
            'strip' => new StripCommand(),
            'htmlspecial' => new HtmlspecialCommand(),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer $field
     * @param $value|null
     *
     * @return mixed
     */
    public function mapTransformations(PunchoutCatalogMappingObjectFieldTransfer $field, $value = null)
    {
        foreach ($field->getTransformations() as $transformation) {
            $value = $this->getTransformCommand($transformation->getName())->execute($transformation, $value);
        }
        return $value;
    }

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform
     */
    protected function getTransformCommand(string $name): ITransform
    {
        if (!isset($this->transformations[$name])) {
            throw new InvalidArgumentException('Could not handle transform: ' . $name);
        }

        if (!($this->transformations[$name] instanceof ITransform)) {
            throw new InvalidArgumentException('Undefined transform command: ' . $name);
        }

        return $this->transformations[$name];
    }
}
