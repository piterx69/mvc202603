<section>
    <h1>Bienvenido a nuestra tienda</h1>
    <p>Encuentra los mejores libros de Negocios Web al mejor precio.</p>
</section>
<section>
    <h2>Ofertas del Día</h2>
    <div class="product-list">
        {{foreach productsOnSale}}
        <div class="product">
            <img src="{{productImgUrl}}" alt="{{productName}}" />
            <span class="price">L. {{productPrice}}</span>
            <p>{{productDescription}}</p>
            <button class="add-to-cart">Agregar al Carrito</button>
        </div>
        {{endfor productsOnSale}}
    </div>
</section>
<section>
    <h2>Destacados</h2>
    <div class="product-list">
        {{foreach productsHighlighted}}
        <div class="product">
            <img src="{{productImgUrl}}" alt="{{productName}}" />
            <span class="price">L. {{productPrice}}</span>
            <p>{{productDescription}}</p>
            <button class="add-to-cart">Agregar al Carrito</button>
        </div>
        {{endfor productsHighlighted}}
    </div>
</section>
<section>
    <h2>Novedades</h2>
    <div class="product-list">
        {{foreach productsNew}}
        <div class="product">
            <img src="{{productImgUrl}}" alt="{{productName}}" />
            <span class="price">L. {{productPrice}}</span>
            <p>{{productDescription}}</p>
            <button class="add-to-cart">Agregar al Carrito</button>
        </div>
        {{endfor productsNew}}
    </div>
</section>
