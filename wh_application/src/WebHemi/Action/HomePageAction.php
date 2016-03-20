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

    public function __construct(Router\RouterInterface $router, Template\TemplateRendererInterface $template = null, Adapter $dbAdapter = null)
    {
        $this->router   = $router;
        $this->template = $template;
        $this->dbAdapter = $dbAdapter;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        echo 'Action<br>';

        $userTable = new User($this->dbAdapter);

        var_dump($userTable->getUserById(1));
        
        $data = [];

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
