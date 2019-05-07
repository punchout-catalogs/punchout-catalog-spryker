<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use PunchoutCatalogs\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;

/**
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
abstract class AbstractCartProcessorStrategyPlugin extends AbstractPlugin
{
    protected const HTML_FORM_TPL = '<form id="cartReturnBody" method="POST" action="%s" style="height: 0; width: 0; border: 0;">%s</form>';
    protected const HTML_FORM_FIELD_TPL = '<input type="hidden" name="%s" value="%s" />';

    /**
     * @param array $fields
     * @param string $url
     *
     * @return string
     */
    protected function renderHtmlForm(array $fields, string $url): string
    {
        $fieldsHtml = '';
        foreach ($fields as $key => $val) {
            $fieldsHtml .= sprintf(static::HTML_FORM_FIELD_TPL, $key, $this->prepareHtmlFormFieldValue($val));
        }

        return sprintf('<h3>%s</h3>', PunchoutConnectionConstsInterface::MESSAGE_CART_RETURN)
            . sprintf(static::HTML_FORM_TPL, $url, $fieldsHtml)
            . '<script type="text/javascript">document.getElementById("cartReturnBody").submit();</script>';
    }

    /**
     * @param $val
     *
     * @return mixed
     */
    protected function prepareHtmlFormFieldValue($val)
    {
        return $val;
    }
}
