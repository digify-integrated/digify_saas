<?php

namespace App\Controllers;

use App\Models\Authentication; 

class AuthenticationController {
    private $authModel;

    public function __construct() {
        $this->authModel = new Authentication();
    }

    public function index() {
        require_once './app/Views/Pages/Authentication/login.php';
    }

    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo json_encode(["message" => "Form submitted successfully!"]);
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
            exit;
        }
    }
}
