<?php
class Router {
    private $routes = [];
    private $basePath = '';
    private $contentType = null;

    public function __construct($contentType, $basePath = '') {
        $this->basePath = $basePath;
        $this->contentType = $contentType;

        if ($this->contentType != null) {
            header('Content-Type: ' . $this->contentType);
        }
    }

    public function addRoute($method, $path, $callback, $permissionCallback = true, $loginCallback = true) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
            'permissionCallback' => $permissionCallback,
            'loginCallback' => $loginCallback
        ];
    }

    private function matchRoute($routePath, $requestUri) {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestUri, '/'));

        if (count($routeParts) !== count($requestParts)) {
            return false;
        }

        $params = [];
        for ($i = 0; $i < count($routeParts); $i++) {
            if (preg_match('/^{(.+)}$/', $routeParts[$i], $matches)) {
                $params[$matches[1]] = $requestParts[$i];
            } elseif ($routeParts[$i] !== $requestParts[$i]) {
                return false;
            }
        }

        return $params;
    }

    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (!empty($this->basePath)) {
            $requestUri = substr($requestUri, strlen($this->basePath));
        }

        foreach ($this->routes as $route) {
            $params = $this->matchRoute($route['path'], $requestUri);
            if ($route['method'] === $requestMethod && $params !== false) {
                if (isset($route['loginCallback']) && is_callable($route['loginCallback'])) {
                    if (!call_user_func($route['loginCallback'])) {
                        header("HTTP/1.0 401 Unauthorized");
                        if ($this->contentType == 'application/json') {
                            echo json_encode(['error' => '401 Unauthorized']);
                        } else {
                            echo '401 Unauthorized';
                        }
                        return;
                    }
                }
                if (isset($route['permissionCallback']) && is_callable($route['permissionCallback'])) {
                    if (!call_user_func($route['permissionCallback'])) {
                        header("HTTP/1.0 403 Forbidden");
                        if ($this->contentType == 'application/json') {
                            echo json_encode(['error' => '403 Forbidden']);
                        } else {
                            echo '403 Forbidden';
                        }
                        return;
                    }
                }
                
                if (is_callable($route['callback'])) {
                    call_user_func_array($route['callback'], $params);
                } else {
                    call_user_func_array($route['callback'], $params);
                }
                return;
            }
        }

        header("HTTP/1.0 404 Not Found");
        if ($this->contentType == 'application/json') {
            echo json_encode(['error' => '404 Not Found']);
        } else {
            echo '404 Not Found';
        }
    }
}