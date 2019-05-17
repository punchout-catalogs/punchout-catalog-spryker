<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Service\UtilOci;

use Spryker\Service\Kernel\AbstractService;

/**
 * @method \PunchoutCatalog\Service\UtilOci\UtilOciFactory getFactory()
 */
class UtilOciService extends AbstractService implements UtilOciServiceInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param array $content
     *
     * @return array
     */
    public function fetchHeaderAsArray(array $content): array
    {
        $username = $hookUrl = null;
        
        $usernameKeys = ['username', 'login', 'cid'];
        $hookKeys = ['HOOK_URL', 'hook_url', 'Hook_Url'];
        
        foreach ($usernameKeys as $usernameKey) {
            if (!empty($content[$usernameKey])) {
                $username = $content[$usernameKey];
            }
        }
    
        foreach ($hookKeys as $hookKey) {
            if (!empty($content[$hookKey])) {
                $hookUrl = $content[$hookKey];
            }
        }
        
        return [
            'oci_credentials' => [
                'username' => $username,
                'password' => $content['password'] ?? null,
            ],
            'cart' => [
                'url' => $hookUrl,
                'operation' => 'create',//always create
            ]
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param array $content
     *
     * @return string|null
     */
    public function getOperation(array $content): string
    {
        return $this->isOci($content) ? 'request/punchoutsetuprequest' : null;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param array $content
     *
     * @return bool
     */
    public function isOci(array $content): bool
    {
        return isset($content['HOOK_URL']) || isset($content['hook_url']) || isset($content['Hook_Url']);
    }
}
