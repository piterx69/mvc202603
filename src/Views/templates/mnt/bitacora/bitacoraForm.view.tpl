<section class="container-m row px-4 py-4">
    <h1>{{FormTitle}}</h1>
</section>
<section class="container-m row px-4 py-4">
    {{with registro}}
    <form action="index.php?page=Mnt_Bitacora_BitacoraForm&mode={{~mode}}&bitacoracod={{bitacoracod}}" method="POST" class="col-12 col-m-8 offset-m-2">
        <input type="hidden" name="mode" value="{{~mode}}" />
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="bitacoracodD">bitacoracod</label>
            <input class="col-12 col-m-9" readonly disabled type="text" name="bitacoracodD" id="bitacoracodD" value="{{bitacoracod}}" />
            <input type="hidden" name="bitacoracod" value="{{bitacoracod}}" />
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="bitacorafch">bitacorafch</label>
            <input class="col-12 col-m-9" {{~readonly}} type="text" name="bitacorafch" id="bitacorafch" value="{{bitacorafch}}" />
            {{if bitacorafch_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{bitacorafch_error}}
            </div>
            {{endif bitacorafch_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="bitprograma">bitprograma</label>
            <input class="col-12 col-m-9" {{~readonly}} type="text" name="bitprograma" id="bitprograma" value="{{bitprograma}}" />
            {{if bitprograma_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{bitprograma_error}}
            </div>
            {{endif bitprograma_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="bitdescripcion">bitdescripcion</label>
            <input class="col-12 col-m-9" {{~readonly}} type="text" name="bitdescripcion" id="bitdescripcion" value="{{bitdescripcion}}" />
            {{if bitdescripcion_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{bitdescripcion_error}}
            </div>
            {{endif bitdescripcion_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="bitobservacion">bitobservacion</label>
            <textarea class="col-12 col-m-9" {{~readonly}} name="bitobservacion" id="bitobservacion">{{bitobservacion}}</textarea>
            {{if bitobservacion_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{bitobservacion_error}}
            </div>
            {{endif bitobservacion_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="bitTipo">bitTipo</label>
            <input class="col-12 col-m-9" {{~readonly}} type="text" name="bitTipo" id="bitTipo" value="{{bitTipo}}" />
            {{if bitTipo_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{bitTipo_error}}
            </div>
            {{endif bitTipo_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="bitusuario">bitusuario</label>
            <input class="col-12 col-m-9" {{~readonly}} type="number" name="bitusuario" id="bitusuario" value="{{bitusuario}}" />
            {{if bitusuario_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{bitusuario_error}}
            </div>
            {{endif bitusuario_error}}
        </div>
    {{endwith registro}}
        <div class="row my-4 align-center flex-end">
            {{if showCommitBtn}}
            <button class="primary col-12 col-m-2" type="submit" name="btnConfirmar">Confirmar</button>
            &nbsp;
            {{endif showCommitBtn}}
            <button class="col-12 col-m-2" type="button" id="btnCancelar">
                {{if showCommitBtn}}
                Cancelar
                {{endif showCommitBtn}}
                {{ifnot showCommitBtn}}
                Regresar
                {{endifnot showCommitBtn}}
            </button>
        </div>
    </form>
</section>

<script>
    document.addEventListener("DOMContentLoaded", ()=>{
        const btnCancelar = document.getElementById("btnCancelar");
        btnCancelar.addEventListener("click", (e)=>{
            e.preventDefault();
            e.stopPropagation();
            window.location.assign("index.php?page=Mnt_Bitacora_BitacoraList");
        });
    });
</script>
