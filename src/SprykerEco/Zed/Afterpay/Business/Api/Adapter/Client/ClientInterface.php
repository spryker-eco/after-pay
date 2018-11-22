<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client;

interface ClientInterface
{
    /**
     * @param string $endPointUrl
     * @param string|null $jsonBody
     *
     * @return string
     */
    public function sendPost($endPointUrl, $jsonBody = null);

    /**
     * @param string $endPointUrl
     *
     * @return string
     */
    public function sendGet($endPointUrl);

    /**
     * @param string $endPointUrl
     *
     * @return string
     */
    public function getStatus($endPointUrl);
}
