<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

require_once __DIR__ . '/../vendor/autoload.php';
require_once "../src/Core/Database.php";
require_once "../src/Core/Router.php";
require_once "../src/Controllers/TrainingController.php";
require_once "../src/Models/Training.php";

use App\Core\Router;
use App\Controllers\TrainingController;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$router = new Router;

$router->get("/workouts", [TrainingController::class, "index"]);
$router->post("/workouts", [TrainingController::class, "store"]);
$router->put("/workouts/{id}", [TrainingController::class, "update"]);
$router->delete("/workouts/{id}",  [TrainingController::class, "destroy"]);

$router->dispatch($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);