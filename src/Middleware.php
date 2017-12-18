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
 * Class Middleware
 * @package Apatis\Middleware
 */
class Middleware implements MiddlewareInterface
{
    /**
     * @var MiddlewareStorageInterface[]
     */
    protected $middleware = [];

    /**
     * @var bool
     */
    protected $middlewareLocked = false;

    /**
     * Initialize middleware stack
     * this recommended to intended for middleware empty
     * @access protected
     */
    protected function currentStackMiddleware() : MiddlewareStorage
    {
        // add current object as first middleware middleware
        if (count($this->middleware) === 0) {
            $middleware = method_exists($this, '__invoke')
                ? $this
                : new FakeMiddlewareInvokable();
            $this->middleware[] = new MiddlewareStorage($middleware);
        }

        $middleware = end($this->middleware);
        return $middleware;
    }

    /**
     * {@inheritdoc}
     */
    public function addMiddleware(callable $callable) : MiddlewareInterface
    {
        if ($this->isMiddlewareLocked()) {
            throw new MiddlewareLockedException(
                'Can not add middleware while middleware is locked or in stack queue'
            );
        }

        $this->middleware[] = new MiddlewareStorage(
            $callable,
            $this->currentStackMiddleware()
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function callMiddlewareStack(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($this->isMiddlewareLocked()) {
            throw new MiddlewareLockedException(
                'Can not call middleware while middleware is locked or in stack queue'
            );
        }

        $this->middlewareLocked = true;
        $response               = $this->currentStackMiddleware()->__invoke($request, $response);
        $this->middlewareLocked = false;
        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function isMiddlewareLocked() : bool
    {
        return $this->middlewareLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function getMiddleware() : array
    {
        return $this->middleware;
    }
}
