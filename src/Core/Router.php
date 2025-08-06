<?php
namespace App\Core;

class Router{
    private $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];

    public function get($path, $handler){

        $this->routes["GET"][$path] = $handler;
    }

    public function post($path, $handler){
        $this->routes['POST'][$path] = $handler;
    }

    public function put($path, $handler){
        $this->routes['PUT'][$path] = $handler;
    }

    public function delete($path, $callback) {
        $this->routes['DELETE'][$path] = $callback;
    }

    public function dispatch($method, $uri) {
    $uri = explode('?', $uri)[0];

    if (!isset($this->routes[$method])) {
        http_response_code(405);
        echo json_encode(["error" => "Método não permitido"]);
        return;
    }

    foreach ($this->routes[$method] as $route => $handler) {
        // Substitui {param} por uma expressão regex
        $pattern = preg_replace('#\{[^}]+\}#', '([^/]+)', $route);
        $pattern = "#^" . $pattern . "$#";

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Remove o full match
            [$class, $methodName] = $handler;
            call_user_func_array([new $class, $methodName], $matches);
            return;
        }
    }

    http_response_code(404);
    echo json_encode(["error" => "Rota não encontrada"]);
}
}