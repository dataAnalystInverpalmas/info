<?php
namespace App\Controllers;

class PowerBIController {

    public function show(string $title, string $url) {
        $pbTitle = $title;
        $pbUrl   = $url;
        require_once __DIR__ . '/../Views/PowerBI/index.php';
    }
}
