<?php


namespace App\Controllers;


class AcceuilController {

    public function __construct() {
    }

    public function showHomePage() {
        include 'app/views/AcceuilView.php';
    }
}
?>
