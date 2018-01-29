<?php
/**
 * Slim FlashMiddleware Factory
 *
 * PHP version 5
 *
 * Copyright (c) Falvey Library 2017.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https:// Main Page
 */
namespace VuBib;

use Slim\Flash\Messages;

/**
 * Class Definition for Slim FlashMiddleware Factory.
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link https://
 */
class SlimFlashMiddlewareFactory
{
    /**
     * Start session
     *
     * @param ContainerInterface $container interface of a container that exposes methods to read its entries.
     *
     * @return HtmlResponse
     */
    public function __invoke($container)
    {
        return function ($request, $response, $next) {
            // Start the session whenever we use this!
            session_start();

            return $next(
               $request->withAttribute('flash', new Messages()),
              $response
            );
        };
    }
}
