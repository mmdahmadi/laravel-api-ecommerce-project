<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::paginate(5);
        return $this->successResponse([
            'category' => CategoryResource::collection($category),
            'links' => CategoryResource::collection($category)->response()->getData()->links,
            'meta' => CategoryResource::collection($category)->response()->getData()->meta,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'required|integer',
            'name' => 'required|string'
        ]);
        if ($validator->fails()){
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();

        $category = Category::create([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'description' => $request->description
        ]);

        DB::commit();

        return $this->successResponse(new CategoryResource($category), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $this->successResponse(new CategoryResource($category), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'required|integer',
            'name' => 'required|string'
        ]);
        if ($validator->fails()){
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();

        $category->update([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'description' => $request->description
        ]);

        DB::commit();

        return $this->successResponse(new CategoryResource($category), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        DB::beginTransaction();
        $category->delete();
        DB::commit();
        return $this->successResponse(new CategoryResource($category), 200);
    }

    public function children(Category $category)
    {
        return $this->successResponse(new CategoryResource($category->load('children')), 200);
    }
    public function parent(Category $category)
    {
        return $this->successResponse(new CategoryResource($category->load('parent')), 200);
    }

    public function products(Category $category)
    {
        return $this->successResponse(new CategoryResource($category->load('products')), 200);
    }
}
