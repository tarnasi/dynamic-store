<?php


namespace  App\Repositories;

use App\Models\Category;


class CategoryRepository
{

    function __construct()
    {
        // Do Something
    }


    public function all() {
        return Category::all();
    }

    public function single(int $id) {
        return Category::where('id', $id)->get();
    }
}
