<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client;

use Psr\Http\Message\StreamInterface;

interface ClientInterface
{
    /**
     * @param string $endPointUrl
     * @param string|null $jsonBody
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function sendPost(string $endPointUrl, ?string $jsonBody = null): StreamInterface;

    /**
     * @param string $endPointUrl
     *
     * @return string
     */
    public function sendGet(string $endPointUrl): string;

    /**
     * @param string $endPointUrl
     *
     * @return int
     */
    public function getStatus(string $endPointUrl): int;
}
