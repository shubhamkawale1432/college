<?php
declare(strict_types=1);

namespace Core;

final class Router
{
    private array $routes = [];
    /** @var callable[] */
    private array $globalMiddleware = [];

    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function any(string $path, array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
        $this->add('POST', $path, $handler, $middleware);
    }

    public function middleware(callable $mw): void
    {
        $this->globalMiddleware[] = $mw;
    }

    private function add(string $method, string $path, array $handler, array $middleware): void
    {
        $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', $path);
        $paramNames = [];
        preg_match_all('#\{([a-zA-Z_]+)\}#', $path, $m);
        $paramNames = $m[1];

        $this->routes[] = [
            'method' => $method,
            'pattern' => '#^' . $pattern . '$#',
            'params' => $paramNames,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request): void
    {
        foreach ($this->globalMiddleware as $mw) {
            $mw($request);
        }

        $uri = $request->uri();
        $method = $request->method();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);
                $args = array_combine($route['params'], $matches);

                foreach ($route['middleware'] as $mw) {
                    $mw($request);
                }

                [$class, $action] = $route['handler'];
                $controller = new $class();
                call_user_func_array([$controller, $action], $args);
                return;
            }
        }

        http_response_code(404);
        view('errors.404');
    }
}
