<?php
// app/core/Controller.php

class Controller {
    public function model($model) {
        require_once 'app/models/' . $model . '.php';
        return new $model();
    }
    
    public function view($view, $data = []) {
        if(file_exists('app/views/' . $view . '.php')) {
            require_once 'app/views/' . $view . '.php';
        } else {
            die("Görünüm bulunamadı: " . $view);
        }
    }
    
    public function redirect($url) {
        // Tam URL oluştur
        $redirectUrl = BASE_URL . $url;
        header("Location: " . $redirectUrl);
        exit;
    }
}