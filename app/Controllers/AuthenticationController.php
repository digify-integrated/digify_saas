<?php

namespace App\Controllers;

use App\Models\Authentication; 

class AuthenticationController {
    private $authentication;

    public function __construct() {
        $this->authentication = new Authentication();
    }

    public function index() {
        require_once './app/Views/Pages/Authentication/login.php';
    }

    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $deviceInfo = filter_input(INPUT_POST, 'device_info', FILTER_SANITIZE_STRING);

            $loginCredentials = $this->authentication->checkLoginCredentialsExist(null, $username);

            echo json_encode(['title' => $password, "message" => $deviceInfo, 'messageType' => 'success']);
        }
        else {
            header("HTTP/1.1 405 Method Not Allowed");
            exit;
        }
    }
}
