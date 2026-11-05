<section class="depth-1 py-4 px-4">
    <h2>{{modeDsc}}</h2>
</section>
<section class="depth-2 grid">
    <form class="col-12 col-m-8 offset-m-2 col-l-6 offset-l-3"
        action="index.php?page=Mnt-Products-ProductForm&mode={{mode}}" method="post">
        <!-- productId
        productName
        productDescription
        productPrice
        productImgUrl
        productStock
        productStatus
        -->
        <div class="row my-3">
            <label class="col-12 col-m-5 col-l-4" for="productName">Nombre</label>
            <input class="col-12 col-m-7 col-l-8" type="text" name="productName" value="{{productName}}"
                id="productName" />
        </div>
        <div class="row my-3">
            <label class="col-12 col-m-5 col-l-4" for="productDescription">Descripción</label>
            <input class="col-12 col-m-7 col-l-8" type="text" name="productDescription" value="{{productDescription}}"
                id="productDescription" />
        </div>
        <div class="row my-3">
            <label class="col-12 col-m-5 col-l-4" for="productPrice">Precio</label>
            <input class="col-12 col-m-7 col-l-8" type="number" name="productPrice" value="{{productPrice}}"
                id="productPrice" />
        </div>
        <div class="row my-3">
            <label class="col-12 col-m-5 col-l-4" for="productImgUrl">Imágen (url)</label>
            <input class="col-12 col-m-7 col-l-8" type="url" name="productImgUrl" value="{{productImgUrl}}"
                id="productImgUrl" />
        </div>
        <div class="row my-3">
            <label class="col-12 col-m-5 col-l-4" for="productStock">Inventario</label>
            <input class="col-12 col-m-7 col-l-8" type="number" name="productStock" value="{{productStock}}"
                id="productStock" />
        </div>
        <div class="row my-3">
            <label class="col-12 col-m-5 col-l-4" for="productStatus">Estado</label>
            <input class="col-12 col-m-7 col-l-8" type="text" name="productStatus" value="{{productStatus}}"
                id="productStatus" />
        </div>

        <div class="row my-3 flex-end">
                <button class="col-12 col-m-2 primary" type="submit">Guardar</button &nbsp;>
                <button class="col-12 col-m-2">Cancelar</button>      
        </div>
                
    </form>

</section>