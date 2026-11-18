<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    Yajra\DataTables\DataTablesServiceProvider::class,
    Barryvdh\DomPDF\ServiceProvider::class,
    Spatie\Permission\PermissionServiceProvider::class,
];
