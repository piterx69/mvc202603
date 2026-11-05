<section class="WWTable">
    <section class="depth-1">
        <h2>Trabajar con Productos</h2>
    </section>
    <table>
        <thead>
            <tr>
                <th>productId</th>
                <th>productName</th>
                <th>productDescription</th>
                <th>productPrice</th>
                <th>productImgUrl</th>
                <th>productStock</th>
                <th>productStatus</th>
                <th>
                    <a href="index.php?page=Mnt-Products-ProductForm&mode=INS">Nuevo</a>
                </th>
            </tr>
        </thead>
        <tbody>
            {{foreach products}}
            <tr>
                <td>{{productId}}</td>
                <td>{{productName}}</td>
                <td>{{productDescription}}</td>
                <td>{{productPrice}}</td>
                <td>{{productImgUrl}}</td>
                <td>{{productStock}}</td>
                <td>{{productStatus}}</td>
                <td>
                    <a href="index.php?page=Mnt-Products-ProductForm&mode=DSP&productId={{productId}}">Ver</a><br/>
                    <a href="index.php?page=Mnt-Products-ProductForm&mode=UPD&productId={{productId}}">Editar</a><br/>
                    <a href="index.php?page=Mnt-Products-ProductForm&mode=DEL&productId={{productId}}">Eliminar</a><br/>
                </td>
            </tr>
            {{endfor products}}
        </tbody>
    </table>
</section>
