<?php

namespace App\Controllers;

require_once './autoload.php';

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
        // Code here
    }
}
