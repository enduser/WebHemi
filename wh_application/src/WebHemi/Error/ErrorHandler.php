<?php

/**
 *
 * WebHemi
 *
 * PHP version 5.6
 *
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webhemi.gixx-web.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@gixx-web.com so we can send you a copy immediately.
 *
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 *
 */

namespace WebHemi\Error;

use Zend\Expressive\Template;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Zend\Stratigility\Http\Response as StratigilityResponse;
use Zend\Stratigility\Utils;

/**
 * Class ErrorHandler
 * @package WebHemi\Error
 */
class ErrorHandler
{
    private $renderer;

    /**
     * ErrorHandler constructor.
     * @param TemplateRendererInterface|null $renderer
     */
    public function __construct(Template\TemplateRendererInterface $renderer = null)
    {
        $this->renderer = $renderer;
    }

    /**
     * Final handler for an application.
     *
     * @param Request $request
     * @param Response $response
     * @param null|mixed $err
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $err = null)
    {
        if (! $err) {
            return $this->handlePotentialSuccess($request, $response);
        }

        return $this->handleErrorResponse($err, $request, $response);
    }
}
