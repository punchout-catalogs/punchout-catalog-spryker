<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\ContentProcessor;

use Generated\Shared\Transfer\PunchoutCatalogProtocolDataCartTransfer;
use Generated\Shared\Transfer\PunchoutCatalogProtocolDataOciCredentialsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;

class OciContentProcessor implements OciContentProcessorInterface
{
    /**
     * @param array $content
     *
     * @return PunchoutCatalogProtocolDataTransfer
     */
    public function fetchHeader(array $content): PunchoutCatalogProtocolDataTransfer
    {
        $username = $hookUrl = null;
        
        $usernameKeys = ['username', 'login', 'cid'];
        $hookKeys = ['HOOK_URL', 'hook_url', 'Hook_Url'];
        
        foreach ($usernameKeys as $usernameKey) {
            if (!empty($content[$usernameKey])) {
                $username = $content[$usernameKey];
                break;
            }
        }
    
        foreach ($hookKeys as $hookKey) {
            if (!empty($content[$hookKey])) {
                $hookUrl = $content[$hookKey];
                break;
            }
        }
        
        $password = $content['password'] ?? null;

        return (new PunchoutCatalogProtocolDataTransfer())
            ->setOciCredentials(
                (new PunchoutCatalogProtocolDataOciCredentialsTransfer())
                    ->setUsername($username)
                    ->setPassword($password)
            )
            ->setCart(
                (new PunchoutCatalogProtocolDataCartTransfer())
                    ->setUrl($hookUrl)
                    ->setTarget($content['returntarget'] ?? null)
                    ->setOperation('create') // always create
            );
    }

    /**
     * @param array $content
     *
     * @return string
     */
    public function fetchOperation(array $content): string
    {
        return 'request/punchoutsetuprequest';
    }

    /**
     * @param array $content
     *
     * @return bool
     */
    public  function isOciContent(array $content): bool
    {
        return isset($content['HOOK_URL'])
            || isset($content['hook_url'])
            || isset($content['Hook_Url'])
            || isset($content['OCI_VERSION'])
            || isset($content['oci_version'])
            || isset($content['Oci_Version'])
        ;
    }
}
