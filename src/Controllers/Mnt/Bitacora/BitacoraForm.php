<?php

namespace Controllers\Mnt\Bitacora;

use Controllers\PublicController;
use Views\Renderer;
use Dao\Mnt\Bitacora as BitacoraDao;
use Utilities\Site;
use Utilities\Validators;

const BITACORA_FORM_VIEW = "mnt/bitacora/bitacoraForm";

class BitacoraForm extends PublicController
{
    private $viewData = [];
    private $mode = "DSP";
    private $modeDescriptions = [
        "DSP" => "Detalle de Bitacora %s",
        "INS" => "Nuevo registro de Bitacora",
        "UPD" => "Editar Bitacora %s",
        "DEL" => "Eliminar Bitacora %s"
    ];
    private $readonly = "";
    private $pkReadonly = "";
    private $showCommitBtn = true;
    private $registro = [
        "bitacoracod" => 0,
        "bitacorafch" => "",
        "bitprograma" => "",
        "bitdescripcion" => "",
        "bitobservacion" => "",
        "bitTipo" => "",
        "bitusuario" => ""
    ];

    public function run(): void
    {
        try {
            $this->getData();
            if ($this->isPostBack()) {
                if ($this->mode === "DEL" || $this->validateData()) {
                    $this->handlePostAction();
                }
            }
            $this->setViewData();
            Renderer::render(BITACORA_FORM_VIEW, $this->viewData);
        } catch (\Exception $ex) {
            Site::redirectToWithMsg(
                "index.php?page=Mnt_Bitacora_BitacoraList",
                $ex->getMessage()
            );
        }
    }

    private function getData()
    {
        $this->mode = $_GET["mode"] ?? "NOF";
        if (isset($this->modeDescriptions[$this->mode])) {
            $this->readonly = in_array($this->mode, ["DSP", "DEL"]) ? "readonly" : "";
            $this->pkReadonly = $this->mode === "INS" ? "" : "readonly";
            $this->showCommitBtn = $this->mode !== "DSP";
            if ($this->mode !== "INS") {
                $this->registro = BitacoraDao::getById(
                    intval($_GET["bitacoracod"] ?? 0)
                );
                if (!$this->registro) {
                    throw new \Exception("No se encontró el registro", 1);
                }
            }
        } else {
            throw new \Exception("Formulario cargado en modalidad invalida", 1);
        }
    }

    private function validateData()
    {
        $errors = [];
        $this->registro["bitacoracod"] = intval($_POST["bitacoracod"] ?? 0);
        $this->registro["bitacorafch"] = strval($_POST["bitacorafch"] ?? "");
        $this->registro["bitprograma"] = strval($_POST["bitprograma"] ?? "");
        $this->registro["bitdescripcion"] = strval($_POST["bitdescripcion"] ?? "");
        $this->registro["bitobservacion"] = strval($_POST["bitobservacion"] ?? "");
        $this->registro["bitTipo"] = strval($_POST["bitTipo"] ?? "");
        $this->registro["bitusuario"] = strval($_POST["bitusuario"] ?? "");

        if (!Validators::IsEmpty($this->registro["bitusuario"]) && !is_numeric($this->registro["bitusuario"])) {
            $errors["bitusuario_error"] = "El campo bitusuario debe ser numérico";
        }

        if (count($errors) > 0) {
            foreach ($errors as $key => $value) {
                $this->registro[$key] = $value;
            }
            return false;
        }
        return true;
    }

    private function valorCampo($campo)
    {
        return $this->registro[$campo] === "" ? null : $this->registro[$campo];
    }

    private function handlePostAction()
    {
        switch ($this->mode) {
            case "INS":
                $this->handleInsert();
                break;
            case "UPD":
                $this->handleUpdate();
                break;
            case "DEL":
                $this->handleDelete();
                break;
            default:
                throw new \Exception("Modo invalido", 1);
                break;
        }
    }

    private function handleInsert()
    {
        $result = BitacoraDao::insert(
                $this->valorCampo("bitacorafch"),
                $this->valorCampo("bitprograma"),
                $this->valorCampo("bitdescripcion"),
                $this->valorCampo("bitobservacion"),
                $this->valorCampo("bitTipo"),
                $this->valorCampo("bitusuario")
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Mnt_Bitacora_BitacoraList",
                "Registro creado exitosamente"
            );
        }
    }

    private function handleUpdate()
    {
        $result = BitacoraDao::update(
                $this->valorCampo("bitacoracod"),
                $this->valorCampo("bitacorafch"),
                $this->valorCampo("bitprograma"),
                $this->valorCampo("bitdescripcion"),
                $this->valorCampo("bitobservacion"),
                $this->valorCampo("bitTipo"),
                $this->valorCampo("bitusuario")
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Mnt_Bitacora_BitacoraList",
                "Registro actualizado exitosamente"
            );
        }
    }

    private function handleDelete()
    {
        $result = BitacoraDao::delete(
                $this->registro["bitacoracod"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Mnt_Bitacora_BitacoraList",
                "Registro eliminado exitosamente"
            );
        }
    }

    private function setViewData(): void
    {
        $this->viewData["mode"] = $this->mode;
        $this->viewData["FormTitle"] = sprintf(
            $this->modeDescriptions[$this->mode],
            $this->registro["bitacoracod"]
        );
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["readonly"] = $this->readonly;
        $this->viewData["pkReadonly"] = $this->pkReadonly;
        $this->viewData["registro"] = $this->registro;
    }
}
