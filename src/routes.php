<?php
use App\ErrorHandler;
use App\Controllers\EmployeesController;
use App\Controllers\DepartmentController;
use App\Controllers\LocationController;
use Pecee\SimpleRouter\SimpleRouter;

SimpleRouter::group(['exceptionHandler' => ErrorHandler::class], function() {
    SimpleRouter::get('/employees', [EmployeesController::class, 'index']);
    SimpleRouter::put('/employees/{id}', [EmployeesController::class, 'update']);
    SimpleRouter::post('/employees', [EmployeesController::class, 'create']);
    SimpleRouter::delete('/employees/{id}', [EmployeesController::class, 'delete']);

    SimpleRouter::get('/departments', [DepartmentController::class, 'index']);
    SimpleRouter::put('/departments/{id}', [DepartmentController::class, 'update']);
    SimpleRouter::post('/departments', [DepartmentController::class, 'create']);
    SimpleRouter::delete('/departments/{id}', [DepartmentController::class, 'delete']);

    SimpleRouter::get('/locations', [LocationController::class, 'index']);
    SimpleRouter::put('/locations/{id}', [LocationController::class, 'update']);
    SimpleRouter::post('/locations', [LocationController::class, 'create']);
    SimpleRouter::delete('/locations/{id}', [LocationController::class, 'delete']);

    SimpleRouter::get('/forms', function() {
        echo file_get_contents("../public/forms.html");
    });
    SimpleRouter::get('/', function() {
        echo file_get_contents("../public/index.html");
    });
    SimpleRouter::get('/error', function() {
        throw new Exception("error");
    });

});




