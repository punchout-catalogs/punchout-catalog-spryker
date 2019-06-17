<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder;

use Spryker\Shared\Kernel\Transfer\TransferInterface;

/**
 * @todo There is no reason to have this as a trait. This is a regular library model class, that could be injected through constructor to anyone who needs it.
 *
 * Trait TransferDataTrait
 *
 * @package PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder
 */
trait TransferDataTrait
{
    /**
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $transfer
     *
     * @return array
     */
    public function toAssociativeArray(TransferInterface $transfer): array
    {
        return $this->_toAssociativeArray($transfer->toArray());
    }

    /**
     * @param array $transferData
     *
     * @return array
     */
    protected function _toAssociativeArray(array $transferData): array
    {
        foreach ($transferData as $key => &$val) {
            if (($key === 'custom_attributes' || $key === 'options')) {
                $val = $this->_fixCustomAttributes($val);
            } elseif (is_array($val)) {
                $val = $this->_toAssociativeArray($val);
            } elseif (is_object($val)) {
                $val = [];//hot fix for empty ArrayObject in transfer objects
            }
        }
        return $transferData;
    }

    /**
     * @param $transferData
     *
     * @return array
     */
    protected function _fixCustomAttributes($transferData): array
    {
        if (!is_array($transferData)) {
            return [];
        }

        $newTransferData = [];
        foreach ($transferData as $key => $val) {
            $idx = $val['code'] ?? '';
            $newTransferData[$idx] = $val;
        }

        return $newTransferData;
    }
}
