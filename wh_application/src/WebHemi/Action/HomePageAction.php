<?php

namespace WebHemi\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Expressive\ZendView\ZendViewRenderer;
use Zend\Db\Adapter\Adapter;
use WebHemi\Table\User;

class HomePageAction
{
    private $router;

    private $template;

    private $dbAdapter;

    /**
     * HomePageAction constructor.
     * @param Router\RouterInterface $router
     * @param Template\TemplateRendererInterface|null $template
     * @param Adapter|null $dbAdapter
     */
    public function __construct(Router\RouterInterface $router, Template\TemplateRendererInterface $template = null, Adapter $dbAdapter = null)
    {
        $this->router   = $router;
        $this->template = $template;
        $this->dbAdapter = $dbAdapter;
    }

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

        $userTable = new User($this->dbAdapter);
        $data = [
//            'user' => $userTable->getUserById(1)
        ];

//        if (Application::$APPLICATION_MODULE == Application::APPLICATION_MODULE_ADMIN) {
//            $data['layout'] = 'layout::admin';
//        }
//
//        if (Application::$APPLICATION_MODULE == 'AdminWiki') {
//            $data['layout'] = 'layout::login';
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
}
