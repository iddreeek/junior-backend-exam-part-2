<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use MongoDB\Laravel\Eloquent\Model;

class ItemController extends Controller
{
    // Retrieve all items with dynamic search
    public function index(Request $request)
    {
        try {
            $query = Item::query();

            // Dynamic search by name and description
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            return response()->json($query->get(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving items', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Retrieve a single item by ID
    public function show($id)
    {
        try {
            $item = Item::findOrFail($id);
            return response()->json($item, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Item not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving item', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Create a new item
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'quantity' => 'required|integer',
                'category_id' => 'required|exists:Category,_id', // Ensure category exists
            ]);

            $item = Item::create($request->all());
            return response()->json($item, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error', 'errors' => $e->validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating item', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Update an existing item by ID
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric',
                'quantity' => 'sometimes|required|integer',
                'category_id' => 'sometimes|required|exists:categories,_id',
            ]);

            $item = Item::findOrFail($id);
            $item->update($request->all());
            return response()->json($item, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Item not found'], Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error', 'errors' => $e->validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating item', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Delete an item by ID
    public function destroy($id)
    {
        try {
            $item = Item::findOrFail($id);
            $item->delete();
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Item not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting item', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Retrieve items with category details using $lookup
    public function itemsWithCategory()
    {
        try {
            $items = Item::raw(function ($collection) {
                return $collection->aggregate([
                    [
                        '$lookup' => [
                            'from' => 'categories', // The collection to join
                            'localField' => 'category_id', // The field from the items collection
                            'foreignField' => '_id', // The field from the categories collection
                            'as' => 'category' // The name of the new array field to add
                        ]
                    ],
                    [
                        '$unwind' => [
                            'path' => '$category',
                            'preserveNullAndEmptyArrays' => true
                        ]
                    ]
                ]);
            });

            return response()->json($items, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving items with category details', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
