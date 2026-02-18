<?php

use Illuminate\Support\Facades\Route;

//Controllers
use App\Http\Controllers\DataController;

Route::post("import", [DataController::class, "import"]);
