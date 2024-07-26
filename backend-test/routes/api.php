<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;

// User authentication route
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Item routes
Route::get('/items', [ItemController::class, 'index']); // Retrieve all items with search
Route::get('/items/{id}', [ItemController::class, 'show']); // Retrieve single item by ID
Route::post('/items', [ItemController::class, 'store']); // Create new item
Route::put('/items/{id}', [ItemController::class, 'update']); // Update existing item by ID
Route::delete('/items/{id}', [ItemController::class, 'destroy']); // Delete item by ID

// Category routes
Route::get('/categories', [CategoryController::class, 'index']); // Retrieve all categories with search
Route::get('/categories/{id}', [CategoryController::class, 'show']); // Retrieve single category by ID
Route::post('/categories', [CategoryController::class, 'store']); // Create new category
Route::put('/categories/{id}', [CategoryController::class, 'update']); // Update existing category by ID
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']); // Delete category by ID
