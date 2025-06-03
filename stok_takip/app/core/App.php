<?php
// app/core/App.php

class App {
    protected $controller = 'DashboardController';
    protected $method = 'index';
    protected $params = [];
    
    public function __construct() {
        $url = $this->parseUrl();
        
        // Controller'ı belirle
        if(isset($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            
            if(file_exists('app/controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
            } else {
                // Controller bulunamadı - varsayılan olarak DashboardController kullan
                // Ancak oturum yoksa, AuthController'a yönlendir
                if(!isset($_SESSION['user_id'])) {
                    $this->controller = 'AuthController';
                    $this->method = 'login';
                }
            }
        } else {
            // URL boş - oturum yoksa login sayfasına yönlendir
            if(!isset($_SESSION['user_id'])) {
                $this->controller = 'AuthController';
                $this->method = 'login';
            }
        }
        
        // Controller'ı yükle
        require_once 'app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;
        
        // Methodu belirle
        if(isset($url[1])) {
            if(method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        
        // Parametreleri belirle
        $this->params = $url ? array_values($url) : [];
        
        // Controller methodu çağır
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
    
    protected function parseUrl() {
        if(isset($_GET['url'])) {
            // URL'i parçala
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        
        return [];
    }
}