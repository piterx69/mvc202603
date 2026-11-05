<?php

namespace Controllers\Mnt\Products;
use Controllers\PublicController;
use Dao\Store\Products as ProductsDao;
use Views\Renderer;


const PRODUCT_LIST_VIEW = "mnt/products/productList";
class ProductList extends PublicController {
    public function run(): void
    {
        $viewData = [
            "products" => ProductsDao::getAll()
        ];
        Renderer::render(
            PRODUCT_LIST_VIEW,
            $viewData
        );
    }
}
