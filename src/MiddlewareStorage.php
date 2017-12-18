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
 * Class MiddlewareStorage
 * @package Apatis\Middleware
 */
class MiddlewareStorage implements MiddlewareStorageInterface
{
    /**
     * @var \SplFixedArray
     */
    private $middleware;

    /**
     * @var int
     */
    private $totalInvoked = 0;

    /**
     * {@inheritdoc}
     *
     * Target Callable by default is null depending to save resource
     * Behavior to use the callable for end of execution
     */
    public function __construct(callable $middleware, callable $targetCallable = null)
    {
        /**
         * @uses \SplFixedArray to reduce memory usage
         * @link http://php.net/manual/en/class.splfixedarray.php
         */
        $this->middleware = new \SplFixedArray(2);
        $this->middleware[0] = $middleware;
        $this->middleware[1] = $targetCallable;
    }

    /**
     * {@inheritdoc}
     */
    public function getCallableMiddleware() : callable
    {
        return $this->middleware[0];
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetMiddleware() : callable
    {
        return $this->middleware[1];
    }

    /**
     * Get total invoked
     *
     * @return int
     */
    public function getTotalInvoked() : int
    {
        return $this->totalInvoked;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        // call middleware
        $response = call_user_func(
            $this->getCallableMiddleware(),
            $request,
            $response,
            $this->getTargetMiddleware()
        );

        // increment total invoke
        $this->totalInvoked++;
        if (!$response instanceof ResponseInterface) {
            throw new \UnexpectedValueException(
                sprintf(
                    'Middleware response must be instance of %s, result is %s',
                    ResponseInterface::class,
                    is_object($response) ?
                        sprintf(
                            'instance of %s',
                            get_class($response)
                        )
                        : gettype($response)
                )
            );
        }

        return $response;
    }
}
