<?php
/**
 * Login Page Factory
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
namespace VuBib\Action;

//use VuBib\Entity\LoginUser;
use VuBib\Repository\UserAuthenticationInterface;
use Interop\Config\ConfigurationTrait;
use Interop\Config\RequiresConfigId;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Db\Adapter\Adapter;

/**
 * Class Definition for LoginPageFactory.
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link https://
 */
class LoginPageFactory implements RequiresConfigId
{
    use ConfigurationTrait;

    /**
     * Returns dimensions.
     *
     * @return Array $vubib
     */
    public function dimensions()
    {
        return ['vubib'];
    }
    
    /**
     * Invokes required template
     *
     * @param ContainerInterface $container interface of a container that exposes methods to read its entries.
     *
     * @return HtmlResponse
     */
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $userRepository = $container->get(UserAuthenticationInterface::class);
        $adapter = $container->get(Adapter::class);
        //$userEntity = new LoginUser();

        $authenticationOptions = $this->options($container->get('config'), 'authentication');

        return new LoginPageAction(
            $router,
            $template,
            $userRepository,
            //$userEntity,
            $authenticationOptions['default_redirect_to'], $adapter,
            $container->get(\Zend\Session\Container::class)
        );
    }
}
