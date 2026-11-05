<?php

namespace Controllers\Mnt\Products;

use Controllers\PublicController;
use Views\Renderer;

const PRODUCT_FORM_VIEW = "mnt/products/productForm";
const PRODUCT_LIST_VIEW = "mnt/products/productList";

class ProductForm extends PublicController
{
    private $viewData = [];
    public function run(): void
    {
        Renderer::render(
            PRODUCT_FORM_VIEW,
            $this->viewData
        );
    }
}
