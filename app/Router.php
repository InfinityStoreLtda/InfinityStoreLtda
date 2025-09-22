<?php
namespace App;

class Router {
  private array $routes = ['GET'=>[], 'POST'=>[]];
  private Bootstrap $app;
  public function __construct(private array $config){
    $this->app = new Bootstrap($config);
  }
  public function get(string $path, $handler){ $this->routes['GET'][$path] = $handler; }
  public function post(string $path, $handler){ $this->routes['POST'][$path] = $handler; }
  public function dispatch(){
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    [$handler, $params] = $this->match($method, $uri);
    if(!$handler){ http_response_code(404); echo '404'; return; }
    if(is_callable($handler)) return $handler($this->app);
    if(is_array($handler)){
      [$class, $func] = $handler; $ctrl = new $class($this->app);
      return $ctrl->$func(...$params);
    }
  }
  private function match($method, $uri){
    foreach(($this->routes[$method] ?? []) as $path=>$handler){
      $regex = preg_replace('#\{[^/]+\}#', '([^/]+)', $path);
      if(preg_match('#^'.$regex.'$#', $uri, $m)){
        array_shift($m); return [$handler, $m];
      }
    }
    return [null, []];
  }
}
