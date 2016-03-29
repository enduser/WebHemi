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

namespace WebHemi\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Expressive\ZendView\ZendViewRenderer;
use WebHemi\Auth\AuthenticationService;
use WebHemi\Application\DependencyInjectionInterface;

/**
 * Class HomePageAction
 * @package WebHemi\Action
 */
class HomePageAction implements DependencyInjectionInterface
{
    /** @var Router\RouterInterface  */
    protected $router;
    /** @var null|Template\TemplateRendererInterface  */
    protected $template;
    /** @var AuthenticationService  */
    protected $auth;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return HtmlResponse|JsonResponse
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        echo 'Action<br>';
        $params = $request->getQueryParams();

        if (isset($params['e404'])) {
            throw new \Exception('Not found', 404);
        }

        if (isset($params['e500'])) {
            throw new \Exception('Internal server error', 500);
        }

        $data = [];

        if ($this->auth) {
            if ($this->auth->hasIdentity()) {
                $data = [
                    'user' => $this->auth->getIdentity()
                ];
            }
        }

//        if (Application::$APPLICATION_MODULE == Application::APPLICATION_MODULE_ADMIN) {
//            $data['layout'] = 'layout::admin';
//        }

        if ($this->router instanceof Router\ZendRouter) {
            $data['routerName'] = 'Zend Router';
            $data['routerDocs'] = 'http://framework.zend.com/manual/current/en/modules/zend.mvc.routing.html';
        }

        if ($this->template instanceof ZendViewRenderer) {
            $data['templateName'] = 'Zend View';
            $data['templateDocs'] = 'http://framework.zend.com/manual/current/en/modules/zend.view.quick-start.html';
        }

        if (!$this->template) {
            return new JsonResponse([
                'welcome' => 'Congratulations! You have installed the zend-expressive skeleton application.',
                'docsUrl' => 'zend-expressive.readthedocs.org',
            ]);
        }

        return new HtmlResponse($this->template->render('web-hemi::home-page', $data));
    }

    /**
     * Injects a service into the class
     *
     * @param string $property
     * @param object $service
     * @return void
     */
    public function injectDependency($property, $service)
    {
        $this->{$property} = $service;
    }
}
