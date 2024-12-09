<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::with('category')->get();
        return response()->json($product);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'bail|required|string|max:255',
            'slug' => 'bail|required|string|max:255|unique:products',
            'image' => 'bail|required|file|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'bail|required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Handle the file upload
        $file = $request->file('image');
        $filePath = $file->store('images', 'public'); // Save to 'storage/app/public/images'

        // Save product with the image path
        $validated = $validator->validated();
        $product = Product::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'category_id' => $validated['category_id'],
            'image_path' => $filePath,
        ]);

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category');
        return response()->json($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            // Delete the old image (optional)
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }

            // Save the new image
            $file = $request->file('image');
            $filePath = $file->store('images', 'public');
            $product->image_path = $filePath;
        }

        // Update other fields
        $product->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'category_id' => $validated['category_id'],
            'image_path' => $product->image_path,
        ]);

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
