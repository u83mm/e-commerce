<?php
declare(strict_types=1);

namespace Application\Core;

use Application\Database\Connection;
use Application\model\classes\CommonTasks;
use Application\model\classes\Query;
use Application\model\classes\Validate;
use Application\Repository\UserRepository;

final class Container
{
    public function __construct(
        private ?Connection $dbcon = null,
        private array $services     = [],
        private array $factories    = [],
        private array $middlewares  = []
    )
    {
        $this->dbcon = new Connection(include DB_CONFIG_FILE);
        $this->registerBaseServices();
        $this->registerControllerFactories();
        $this->registerMiddlewares();
    }

    /** We define middlewares */
    private function registerMiddlewares(): void
    {
        $this->middlewares['admin'] = fn() => new \Application\Middlewares\RoleMiddleware([
            'ROLE_ADMIN'
        ]);

        $this->middlewares['auth'] = fn() => new \Application\Middlewares\AuthMiddleware([
            'ROLE_USER'
        ]);
    }

    /** Get Middleware */
    public function getMiddleware(string $name): object
    {
        if(isset($this->middlewares[$name])) {
            return $this->middlewares[$name]();
        }
        
        throw new \Exception("Middleware $name not found", 1);
        
    }

    /** 
     * We define the base services (tools)
     */
    private function registerBaseServices(): void 
    {
        $this->services['query'] = function() {            
            return new Query($this->dbcon->getConnection());
        };

        $this->services['validate'] = function() {
            return new Validate;
        };
        
        $this->services['user_repository'] = function() {
            return new UserRepository($this->dbcon->getConnection());
        };

        $this->services['common_tasks'] = function() {
            return new CommonTasks;
        };
    }

    /** 
     * We define how to build every controller 
     */
    private function registerControllerFactories(): void
    {
        $this->factories["\Application\Controller\LoginController"] = fn() => new \Application\Controller\LoginController(
            $this->get('validate'),
            $this->get('query')
        );               

        $this->factories["\Application\Controller\RegisterController"] = fn() => new \Application\Controller\RegisterController(
            $this->get('validate'),
            $this->get('query'),
            $this->get('user_repository')
        );
        
        $this->factories["\Application\Controller\products\ProductsController"] = fn() => new \Application\Controller\products\ProductsController(
            $this->get('validate'),
            $this->get('query'),
            $this->get('common_tasks')
        );

        $this->factories["\Application\Controller\cart\CartController"] = fn() => new \Application\Controller\cart\CartController(
            $this->get('validate'),
            $this->get('query')
        );

        $this->factories["\Application\Controller\category\CategoryController"] = fn() => new \Application\Controller\category\CategoryController(
            $this->get('validate'),
            $this->get('query')
        );

        $this->factories["\Application\Controller\admin\DocumentController"] = fn() => new \Application\Controller\admin\DocumentController(
            $this->get('validate'),
            $this->get('query')
        );
    }

    /** We get a service or build a controller */
    public function get(string $id): object
    {
        // If it's a controller defined in our factories
        if(isset($this->factories[$id])) {
            return $this->factories[$id]();
        }

        // If it's a base service
        if(isset($this->services[$id])) {
            if(is_callable($this->services[$id])) {
                $this->services[$id] = $this->services[$id]();
            }

            return $this->services[$id];
        }

        if(class_exists($id)) {
            return new $id();
        }

        throw new \Exception("Service or Controller not found: $id", 1);        
    }
}