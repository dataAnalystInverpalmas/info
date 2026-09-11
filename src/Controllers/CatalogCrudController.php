<?php
namespace App\Controllers;

class CatalogCrudController {

    public function show(string $table, string $endpoint, string $title, string $selects = '', string $display = '', string $filters = '', string $bulkField = '') {
        extract([
            'table'      => $table,
            'endpoint'   => $endpoint,
            'title'      => $title,
            'selects'    => $selects,
            'display'    => $display,
            'filters'    => $filters,
            'bulkField'  => $bulkField,
        ]);
        require_once __DIR__ . '/../Views/Crud/catalog.php';
    }

    public function arrangement() {
        require_once __DIR__ . '/../Views/Crud/arrangement.php';
    }

    public function arrangements() {
        require_once __DIR__ . '/../Views/Crud/arrangements.php';
    }
}
