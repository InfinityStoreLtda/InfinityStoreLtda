<?php
namespace App;

class Router {
  /** @var array<string, array<string, callable|array{0: class-string, 1: string}>> */
  private array $routes = ['GET' => [], 'POST' => []];
  private Bootstrap $app;

  public function __construct(private array $config) {
    $this->app = new Bootstrap($config);
  }

  public function get(string $path, $handler): void {
    $this->routes['GET'][$path] = $handler;
  }

  public function post(string $path, $handler): void {
    $this->routes['POST'][$path] = $handler;
  }

  public function dispatch(): void {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    [$handler, $params] = $this->match($method, $uri);

    if (!$handler) {
      http_response_code(404);
      echo '404';
      return;
    }

    if (is_callable($handler)) {
      $handler($this->app);
      return;
    }

    if (is_array($handler)) {
      [$class, $func] = $handler;
      $ctrl = new $class($this->app);
      $ctrl->$func(...$params);
    }
  }

  private function match(string $method, string $uri): array {
    foreach (($this->routes[$method] ?? []) as $path => $handler) {
      $regex = preg_replace('#\{[^/]+\}#', '([^/]+)', $path);
      if ($regex === null) {
        continue;
      }

      if (preg_match('#^' . $regex . '$#', $uri, $matches)) {
        array_shift($matches);
        return [$handler, $matches];
      }
    }

    return [null, []];
  }
}
