<?php

function nombreEntidad($tabla)
{
    return str_replace(" ", "", ucwords(str_replace("_", " ", strtolower($tabla))));
}

function tituloTabla($tabla)
{
    return ucwords(str_replace("_", " ", $tabla));
}

function esNumerico($tipo)
{
    return preg_match("/^(tinyint|smallint|mediumint|int|bigint|decimal|float|double)/i", $tipo) === 1;
}

function esEntero($tipo)
{
    return preg_match("/^(tinyint|smallint|mediumint|int|bigint)/i", $tipo) === 1;
}

function esTexto($tipo)
{
    return preg_match("/^(tinytext|text|mediumtext|longtext)/i", $tipo) === 1;
}

function esAutoIncremento($campo)
{
    return stripos($campo["Extra"], "auto_increment") !== false;
}

function obtenerLlaves($campos)
{
    $llaves = [];
    foreach ($campos as $campo) {
        if ($campo["Key"] === "PRI") {
            $llaves[] = $campo;
        }
    }
    if (count($llaves) === 0) {
        $llaves[] = $campos[0];
    }
    return $llaves;
}

function esLlave($campo, $llaves)
{
    foreach ($llaves as $llave) {
        if ($llave["Field"] === $campo["Field"]) {
            return true;
        }
    }
    return false;
}

function listaCampos($campos, $formato, $separador)
{
    $partes = [];
    foreach ($campos as $campo) {
        $partes[] = str_replace("%c", $campo["Field"], $formato);
    }
    return implode($separador, $partes);
}

function generarDao($tabla, $campos)
{
    $entidad = nombreEntidad($tabla);
    $llaves = obtenerLlaves($campos);
    $insertables = array_values(array_filter($campos, function ($c) {
        return !esAutoIncremento($c);
    }));
    $actualizables = array_values(array_filter($campos, function ($c) use ($llaves) {
        return !esLlave($c, $llaves);
    }));
    if (count($actualizables) === 0) {
        $actualizables = $llaves;
    }
    $todos = listaCampos($campos, "`%c`", ", ");
    $whereLlaves = listaCampos($llaves, "`%c` = :%c", " AND ");
    $paramsLlaves = listaCampos($llaves, "\$%c", ", ");
    $arrLlaves = listaCampos($llaves, "\"%c\" => \$%c", ", ");

    $insCampos = listaCampos($insertables, "`%c`", ", ");
    $insValores = listaCampos($insertables, ":%c", ", ");
    $insParams = listaCampos($insertables, "\$%c", ", ");
    $insArr = listaCampos($insertables, "            \"%c\" => \$%c", ",\n");

    $updSet = listaCampos($actualizables, "`%c` = :%c", ", ");
    $updCampos = array_merge($llaves, array_values(array_filter($actualizables, function ($c) use ($llaves) {
        return !esLlave($c, $llaves);
    })));
    $updParams = listaCampos($updCampos, "\$%c", ", ");
    $updArr = listaCampos($updCampos, "            \"%c\" => \$%c", ",\n");

    return <<<EOT
<?php

namespace Dao\Mnt;

use Dao\Table;

class {$entidad} extends Table
{
    public static function getAll(): array
    {
        \$sqlstr = "SELECT {$todos} FROM `{$tabla}`;";
        return self::obtenerRegistros(\$sqlstr, []);
    }

    public static function getById({$paramsLlaves})
    {
        \$sqlstr = "SELECT {$todos} FROM `{$tabla}` WHERE {$whereLlaves};";
        \$params = [{$arrLlaves}];
        return self::obtenerUnRegistro(\$sqlstr, \$params);
    }

    public static function insert({$insParams})
    {
        \$sqlstr = "INSERT INTO `{$tabla}` ({$insCampos}) VALUES ({$insValores});";
        \$params = [
{$insArr}
        ];
        return self::executeNonQuery(\$sqlstr, \$params);
    }

    public static function update({$updParams})
    {
        \$sqlstr = "UPDATE `{$tabla}` SET {$updSet} WHERE {$whereLlaves};";
        \$params = [
{$updArr}
        ];
        return self::executeNonQuery(\$sqlstr, \$params);
    }

    public static function delete({$paramsLlaves})
    {
        \$sqlstr = "DELETE FROM `{$tabla}` WHERE {$whereLlaves};";
        \$params = [{$arrLlaves}];
        return self::executeNonQuery(\$sqlstr, \$params);
    }
}

EOT;
}

function generarControladorLista($tabla)
{
    $entidad = nombreEntidad($tabla);
    $constante = strtoupper($entidad) . "_LIST_VIEW";
    $vista = "mnt/" . strtolower($tabla) . "/" . lcfirst($entidad) . "List";
    $variable = lcfirst($entidad);
    return <<<EOT
<?php

namespace Controllers\Mnt\\{$entidad};

use Controllers\PublicController;
use Dao\Mnt\\{$entidad} as {$entidad}Dao;
use Views\Renderer;

const {$constante} = "{$vista}";

class {$entidad}List extends PublicController
{
    public function run(): void
    {
        \$viewData = [
            "{$variable}" => {$entidad}Dao::getAll()
        ];
        Renderer::render(
            {$constante},
            \$viewData
        );
    }
}

EOT;
}

function generarControladorFormulario($tabla, $campos)
{
    $entidad = nombreEntidad($tabla);
    $titulo = tituloTabla($tabla);
    $constante = strtoupper($entidad) . "_FORM_VIEW";
    $vista = "mnt/" . strtolower($tabla) . "/" . lcfirst($entidad) . "Form";
    $pagina = "Mnt_" . $entidad . "_" . $entidad . "List";
    $llaves = obtenerLlaves($campos);
    $insertables = array_values(array_filter($campos, function ($c) {
        return !esAutoIncremento($c);
    }));
    $actualizables = array_values(array_filter($campos, function ($c) use ($llaves) {
        return !esLlave($c, $llaves);
    }));
    $updCampos = array_merge($llaves, $actualizables);

    $estructura = [];
    foreach ($campos as $campo) {
        $valor = esAutoIncremento($campo) ? "0" : "\"\"";
        $estructura[] = "        \"{$campo["Field"]}\" => {$valor}";
    }
    $estructura = implode(",\n", $estructura);

    $getLlaves = [];
    foreach ($llaves as $llave) {
        if (esEntero($llave["Type"])) {
            $getLlaves[] = "                    intval(\$_GET[\"{$llave["Field"]}\"] ?? 0)";
        } else {
            $getLlaves[] = "                    strval(\$_GET[\"{$llave["Field"]}\"] ?? \"\")";
        }
    }
    $getLlaves = implode(",\n", $getLlaves);

    $lecturas = [];
    $validaciones = [];
    foreach ($campos as $campo) {
        $c = $campo["Field"];
        if (esAutoIncremento($campo)) {
            $lecturas[] = "        \$this->registro[\"{$c}\"] = intval(\$_POST[\"{$c}\"] ?? 0);";
            continue;
        }
        $lecturas[] = "        \$this->registro[\"{$c}\"] = strval(\$_POST[\"{$c}\"] ?? \"\");";
        if ($campo["Null"] === "NO") {
            $validaciones[] = <<<EOT
        if (Validators::IsEmpty(\$this->registro["{$c}"])) {
            \$errors["{$c}_error"] = "El campo {$c} es requerido";
        }

EOT;
        }
        if (esNumerico($campo["Type"])) {
            $validaciones[] = <<<EOT
        if (!Validators::IsEmpty(\$this->registro["{$c}"]) && !is_numeric(\$this->registro["{$c}"])) {
            \$errors["{$c}_error"] = "El campo {$c} debe ser numérico";
        }

EOT;
        }
    }
    $lecturas = implode("\n", $lecturas);
    $validaciones = implode("\n", $validaciones);

    $argsInsert = listaCampos($insertables, "                \$this->valorCampo(\"%c\")", ",\n");
    $argsUpdate = listaCampos($updCampos, "                \$this->valorCampo(\"%c\")", ",\n");
    $argsDelete = listaCampos($llaves, "                \$this->registro[\"%c\"]", ",\n");
    $tituloLlaves = listaCampos($llaves, "\$this->registro[\"%c\"]", " . \" - \" . ");

    return <<<EOT
<?php

namespace Controllers\Mnt\\{$entidad};

use Controllers\PublicController;
use Views\Renderer;
use Dao\Mnt\\{$entidad} as {$entidad}Dao;
use Utilities\Site;
use Utilities\Validators;

const {$constante} = "{$vista}";

class {$entidad}Form extends PublicController
{
    private \$viewData = [];
    private \$mode = "DSP";
    private \$modeDescriptions = [
        "DSP" => "Detalle de {$titulo} %s",
        "INS" => "Nuevo registro de {$titulo}",
        "UPD" => "Editar {$titulo} %s",
        "DEL" => "Eliminar {$titulo} %s"
    ];
    private \$readonly = "";
    private \$pkReadonly = "";
    private \$showCommitBtn = true;
    private \$registro = [
{$estructura}
    ];

    public function run(): void
    {
        try {
            \$this->getData();
            if (\$this->isPostBack()) {
                if (\$this->mode === "DEL" || \$this->validateData()) {
                    \$this->handlePostAction();
                }
            }
            \$this->setViewData();
            Renderer::render({$constante}, \$this->viewData);
        } catch (\Exception \$ex) {
            Site::redirectToWithMsg(
                "index.php?page={$pagina}",
                \$ex->getMessage()
            );
        }
    }

    private function getData()
    {
        \$this->mode = \$_GET["mode"] ?? "NOF";
        if (isset(\$this->modeDescriptions[\$this->mode])) {
            \$this->readonly = in_array(\$this->mode, ["DSP", "DEL"]) ? "readonly" : "";
            \$this->pkReadonly = \$this->mode === "INS" ? "" : "readonly";
            \$this->showCommitBtn = \$this->mode !== "DSP";
            if (\$this->mode !== "INS") {
                \$this->registro = {$entidad}Dao::getById(
{$getLlaves}
                );
                if (!\$this->registro) {
                    throw new \Exception("No se encontró el registro", 1);
                }
            }
        } else {
            throw new \Exception("Formulario cargado en modalidad invalida", 1);
        }
    }

    private function validateData()
    {
        \$errors = [];
{$lecturas}

{$validaciones}
        if (count(\$errors) > 0) {
            foreach (\$errors as \$key => \$value) {
                \$this->registro[\$key] = \$value;
            }
            return false;
        }
        return true;
    }

    private function valorCampo(\$campo)
    {
        return \$this->registro[\$campo] === "" ? null : \$this->registro[\$campo];
    }

    private function handlePostAction()
    {
        switch (\$this->mode) {
            case "INS":
                \$this->handleInsert();
                break;
            case "UPD":
                \$this->handleUpdate();
                break;
            case "DEL":
                \$this->handleDelete();
                break;
            default:
                throw new \Exception("Modo invalido", 1);
                break;
        }
    }

    private function handleInsert()
    {
        \$result = {$entidad}Dao::insert(
{$argsInsert}
        );
        if (\$result > 0) {
            Site::redirectToWithMsg(
                "index.php?page={$pagina}",
                "Registro creado exitosamente"
            );
        }
    }

    private function handleUpdate()
    {
        \$result = {$entidad}Dao::update(
{$argsUpdate}
        );
        if (\$result > 0) {
            Site::redirectToWithMsg(
                "index.php?page={$pagina}",
                "Registro actualizado exitosamente"
            );
        }
    }

    private function handleDelete()
    {
        \$result = {$entidad}Dao::delete(
{$argsDelete}
        );
        if (\$result > 0) {
            Site::redirectToWithMsg(
                "index.php?page={$pagina}",
                "Registro eliminado exitosamente"
            );
        }
    }

    private function setViewData(): void
    {
        \$this->viewData["mode"] = \$this->mode;
        \$this->viewData["FormTitle"] = sprintf(
            \$this->modeDescriptions[\$this->mode],
            {$tituloLlaves}
        );
        \$this->viewData["showCommitBtn"] = \$this->showCommitBtn;
        \$this->viewData["readonly"] = \$this->readonly;
        \$this->viewData["pkReadonly"] = \$this->pkReadonly;
        \$this->viewData["registro"] = \$this->registro;
    }
}

EOT;
}

function parametrosLlaves($llaves)
{
    return listaCampos($llaves, "&%c={{%c}}", "");
}

function generarVistaLista($tabla, $campos)
{
    $entidad = nombreEntidad($tabla);
    $titulo = tituloTabla($tabla);
    $variable = lcfirst($entidad);
    $formulario = "Mnt-" . $entidad . "-" . $entidad . "Form";
    $llaves = parametrosLlaves(obtenerLlaves($campos));
    $encabezados = listaCampos($campos, "                <th>%c</th>", "\n");
    $celdas = listaCampos($campos, "                <td>{{%c}}</td>", "\n");
    return <<<EOT
<section class="WWList">
    <section class="depth-1 py-4 px-4">
        <h2>Trabajar con {$titulo}</h2>
    </section>
    <table>
        <thead>
            <tr>
{$encabezados}
                <th>
                    <a href="index.php?page={$formulario}&mode=INS">Nuevo</a>
                </th>
            </tr>
        </thead>
        <tbody>
            {{foreach {$variable}}}
            <tr>
{$celdas}
                <td class="center">
                    <a href="index.php?page={$formulario}&mode=DSP{$llaves}">Ver</a>
                    &nbsp;
                    <a href="index.php?page={$formulario}&mode=UPD{$llaves}">Editar</a>
                    &nbsp;
                    <a href="index.php?page={$formulario}&mode=DEL{$llaves}">Eliminar</a>
                </td>
            </tr>
            {{endfor {$variable}}}
        </tbody>
    </table>
</section>

EOT;
}

function generarCampoFormulario($campo, $llaves)
{
    $c = $campo["Field"];
    if (esAutoIncremento($campo)) {
        return <<<EOT
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="{$c}D">{$c}</label>
            <input class="col-12 col-m-9" readonly disabled type="text" name="{$c}D" id="{$c}D" value="{{{$c}}}" />
            <input type="hidden" name="{$c}" value="{{{$c}}}" />
        </div>
EOT;
    }
    $bloqueo = esLlave($campo, $llaves) ? "{{~pkReadonly}}" : "{{~readonly}}";
    if (esTexto($campo["Type"])) {
        $control = "<textarea class=\"col-12 col-m-9\" {$bloqueo} name=\"{$c}\" id=\"{$c}\">{{{$c}}}</textarea>";
    } else {
        $tipo = "text";
        $extra = "";
        if (esNumerico($campo["Type"])) {
            $tipo = "number";
            $extra = esEntero($campo["Type"]) ? "" : " step=\"any\"";
        } elseif (preg_match("/^date$/i", $campo["Type"])) {
            $tipo = "date";
        }
        $control = "<input class=\"col-12 col-m-9\" {$bloqueo} type=\"{$tipo}\"{$extra} name=\"{$c}\" id=\"{$c}\" value=\"{{{$c}}}\" />";
    }
    return <<<EOT
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="{$c}">{$c}</label>
            {$control}
            {{if {$c}_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{{$c}_error}}
            </div>
            {{endif {$c}_error}}
        </div>
EOT;
}

function generarVistaFormulario($tabla, $campos)
{
    $entidad = nombreEntidad($tabla);
    $lista = "Mnt_" . $entidad . "_" . $entidad . "List";
    $formulario = "Mnt_" . $entidad . "_" . $entidad . "Form";
    $llavesObj = obtenerLlaves($campos);
    $llaves = parametrosLlaves($llavesObj);
    $bloques = [];
    foreach ($campos as $campo) {
        $bloques[] = generarCampoFormulario($campo, $llavesObj);
    }
    $bloques = implode("\n", $bloques);
    return <<<EOT
<section class="container-m row px-4 py-4">
    <h1>{{FormTitle}}</h1>
</section>
<section class="container-m row px-4 py-4">
    {{with registro}}
    <form action="index.php?page={$formulario}&mode={{~mode}}{$llaves}" method="POST" class="col-12 col-m-8 offset-m-2">
        <input type="hidden" name="mode" value="{{~mode}}" />
{$bloques}
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
            window.location.assign("index.php?page={$lista}");
        });
    });
</script>

EOT;
}

function guardarArchivo($ruta, $contenido, &$mensajes)
{
    if (file_exists($ruta)) {
        $mensajes[] = "Ya existe, no se sobrescribió: " . $ruta;
        return;
    }
    $carpeta = dirname($ruta);
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }
    file_put_contents($ruta, $contenido);
    $mensajes[] = "Creado: " . $ruta;
}

function generarCrud($tabla, $campos, $raiz)
{
    $mensajes = [];
    $entidad = nombreEntidad($tabla);
    $carpetaVistas = $raiz . "/src/Views/templates/mnt/" . strtolower($tabla);
    $carpetaControladores = $raiz . "/src/Controllers/Mnt/" . $entidad;
    guardarArchivo($raiz . "/src/Dao/Mnt/" . $entidad . ".php", generarDao($tabla, $campos), $mensajes);
    guardarArchivo($carpetaControladores . "/" . $entidad . "List.php", generarControladorLista($tabla), $mensajes);
    guardarArchivo($carpetaControladores . "/" . $entidad . "Form.php", generarControladorFormulario($tabla, $campos), $mensajes);
    guardarArchivo($carpetaVistas . "/" . lcfirst($entidad) . "List.view.tpl", generarVistaLista($tabla, $campos), $mensajes);
    guardarArchivo($carpetaVistas . "/" . lcfirst($entidad) . "Form.view.tpl", generarVistaFormulario($tabla, $campos), $mensajes);
    return $mensajes;
}

if (PHP_SAPI === "cli") {
    return;
}

require __DIR__ . "/vendor/autoload.php";

\Utilities\Site::configure();
$conn = \Dao\Dao::getConn();
$tablas = $conn->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
$tabla = $_GET["tabla"] ?? "";
$mensajes = [];
$campos = [];
$enlace = "";
if ($tabla !== "") {
    if (!in_array($tabla, $tablas)) {
        $mensajes[] = "La tabla " . $tabla . " no existe en la base de datos";
    } else {
        $campos = $conn->query("DESC `" . $tabla . "`")->fetchAll(\PDO::FETCH_ASSOC);
        $mensajes = generarCrud($tabla, $campos, __DIR__);
        $enlace = "index.php?page=Mnt-" . nombreEntidad($tabla) . "-" . nombreEntidad($tabla) . "List";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generador de CRUD</title>
    <link rel="stylesheet" href="public/css/appstyle.css" />
</head>
<body>
    <main>
        <h1>Generador de CRUD (Work With)</h1>
        <form action="generador.php" method="get" class="row align-center">
            <label for="tabla">Tabla&nbsp;</label>
            <select name="tabla" id="tabla">
                <?php foreach ($tablas as $t) { ?>
                    <option value="<?= $t ?>" <?= $t === $tabla ? "selected" : "" ?>><?= $t ?></option>
                <?php } ?>
            </select>
            &nbsp;
            <button class="primary" type="submit">Generar</button>
        </form>
        <?php if (count($campos) > 0) { ?>
            <h2>Campos de <?= $tabla ?> (desc <?= $tabla ?>;)</h2>
            <section class="WWList">
                <table>
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Type</th>
                            <th>Null</th>
                            <th>Key</th>
                            <th>Default</th>
                            <th>Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($campos as $campo) { ?>
                            <tr>
                                <td><?= $campo["Field"] ?></td>
                                <td><?= $campo["Type"] ?></td>
                                <td><?= $campo["Null"] ?></td>
                                <td><?= $campo["Key"] ?></td>
                                <td><?= $campo["Default"] ?></td>
                                <td><?= $campo["Extra"] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </section>
        <?php } ?>
        <?php if (count($mensajes) > 0) { ?>
            <h2>Resultado</h2>
            <ul>
                <?php foreach ($mensajes as $mensaje) { ?>
                    <li><?= $mensaje ?></li>
                <?php } ?>
            </ul>
        <?php } ?>
        <?php if ($enlace !== "") { ?>
            <p><a class="link" href="<?= $enlace ?>">Abrir el WW generado</a></p>
        <?php } ?>
    </main>
</body>
</html>
