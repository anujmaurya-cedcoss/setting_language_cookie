<?php

namespace App\Middleware;

class Middleware
{
    public function boot()
    {
        if(isset($_GET['language'])) {
            $_SESSION['language'] = $_GET['language'];
        }
        header("/signup/index?language=$_SESSION[language]");
    }
}
