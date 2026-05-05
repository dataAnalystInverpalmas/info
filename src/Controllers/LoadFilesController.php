<?php
namespace App\Controllers;

class LoadFilesController {

    public function index() {
        $dirPath = realpath(__DIR__ . '/../../archivos');
        $files = [];
        if ($dirPath !== false) {
            $found = glob($dirPath . DIRECTORY_SEPARATOR . '*.*');
            if ($found !== false) {
                $files = $found;
                usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
            }
        }
        extract(['files' => $files]);
        require_once __DIR__ . '/../Views/LoadFiles/index.php';
    }
}
