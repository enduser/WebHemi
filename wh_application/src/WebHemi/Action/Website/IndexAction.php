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

namespace WebHemi\Action\Website;

use WebHemi\Action\AbstractAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Expressive\ZendView\ZendViewRenderer;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Config;
use WebHemi\User\Entity as UserEntity;

/**
 * Class IndexAction
 * @package WebHemi\Action\Website
 */
class IndexAction extends AbstractAction
{
    /** @var Router\RouterInterface  */
    protected $router;
    /** @var AuthenticationService  */
    protected $auth;
    /** @var  Config */
    protected $config;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return HtmlResponse|JsonResponse
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $params = $request->getQueryParams();

        if (isset($params['e404'])) {
            throw new \Exception('Not found', 404);
        }

        if (isset($params['e500'])) {
            throw new \Exception('Internal server error', 500);
        }

        $data = [];

//        if ($this->auth && !$this->auth->hasIdentity()) {
//            $this->auth->getAdapter()->setIdentity('admin');
//            $this->auth->getAdapter()->setCredential('admin');
//            /** @var Result $result */
//            $result = $this->auth->authenticate();
//
//            if ($result->getCode() != Result::SUCCESS) {
//                throw new \Exception(implode('; ', $result->getMessages()), 403);
//            }
//        }

        if ($this->auth) {
            if ($this->auth->hasIdentity()) {
                /** @var UserEntity $userEntity */
                $userEntity = $this->auth->getIdentity();

                $data = [
                    'user' => $userEntity,
                    'meta' => $userEntity->getMetaList(),
                    'role' => $userEntity->getCurrentUserRole(),
                ];
            }
        }

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

        $data['action'] = 'website/index';

        return new HtmlResponse($this->template->render('test::x', $data));
    }
}
