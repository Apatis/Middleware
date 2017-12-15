<?php
/**
 * MIT License
 *
 * Copyright (c) 2017 Pentagonal Development
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Apatis\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface MiddlewareInterface
 * @package Apatis\Middleware
 */
interface MiddlewareInterface
{
    /**
     * Add Middleware for queue
     *
     * @param callable $callable callback to call middleware
     *
     * @return MiddlewareInterface
     *
     * @throws MiddlewareLockedException if middleware locked or in stack queue process
     */
    public function addMiddleware(callable $callable) : MiddlewareInterface;

    /**
     * Call middleware stack
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function callMiddlewareStack(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) : ResponseInterface;

    /**
     * Get Middleware Storage Lists
     *
     * @return MiddlewareStorageInterface[]
     */
    public function getMiddleware() : array;

    /**
     * Validate if middleware in process
     *
     * @return bool
     */
    public function isMiddlewareLocked() : bool;
}
