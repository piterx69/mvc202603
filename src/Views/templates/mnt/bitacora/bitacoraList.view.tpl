<section class="WWList">
    <section class="depth-1 py-4 px-4">
        <h2>Trabajar con Bitacora</h2>
    </section>
    <table>
        <thead>
            <tr>
                <th>bitacoracod</th>
                <th>bitacorafch</th>
                <th>bitprograma</th>
                <th>bitdescripcion</th>
                <th>bitobservacion</th>
                <th>bitTipo</th>
                <th>bitusuario</th>
                <th>
                    <a href="index.php?page=Mnt-Bitacora-BitacoraForm&mode=INS">Nuevo</a>
                </th>
            </tr>
        </thead>
        <tbody>
            {{foreach bitacora}}
            <tr>
                <td>{{bitacoracod}}</td>
                <td>{{bitacorafch}}</td>
                <td>{{bitprograma}}</td>
                <td>{{bitdescripcion}}</td>
                <td>{{bitobservacion}}</td>
                <td>{{bitTipo}}</td>
                <td>{{bitusuario}}</td>
                <td class="center">
                    <a href="index.php?page=Mnt-Bitacora-BitacoraForm&mode=DSP&bitacoracod={{bitacoracod}}">Ver</a>
                    &nbsp;
                    <a href="index.php?page=Mnt-Bitacora-BitacoraForm&mode=UPD&bitacoracod={{bitacoracod}}">Editar</a>
                    &nbsp;
                    <a href="index.php?page=Mnt-Bitacora-BitacoraForm&mode=DEL&bitacoracod={{bitacoracod}}">Eliminar</a>
                </td>
            </tr>
            {{endfor bitacora}}
        </tbody>
    </table>
</section>
