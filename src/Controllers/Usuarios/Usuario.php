<?php

namespace Controllers\Usuarios;

use Controllers\PublicController;
use Views\Renderer;
use Dao\Usuarios\Usuarios as UsuariosDao;
use Utilities\Site;
use Utilities\Validators;

class Usuario extends PublicController
{
    private $viewData = [];
    private $mode = "DSP";
    private $modeDescriptions = [
        "DSP" => "Detalle de Usuario %s %s",
        "INS" => "Nuevo Usuario",
        "UPD" => "Editar Usuario %s %s",
        "DEL" => "Eliminar Usuario %s %s"
    ];
    private $readonly = "";
    private $showCommitBtn = true;
    private $usuario = [
        "usercod" => 0,
        "useremail" => "",
        "username" => "",
        "userest" => "ACT",
        "usertipo" => "PBL"
    ];
    private $usuario_xss_token = "";

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
            Renderer::render("usuarios/usuario", $this->viewData);
        } catch (\Exception $ex) {
            Site::redirectToWithMsg(
                "index.php?page=Usuarios_Usuarios",
                $ex->getMessage()
            );
        }
    }

    private function getData()
    {
        $this->mode = $_GET["mode"] ?? "NOF";
        if (isset($this->modeDescriptions[$this->mode])) {
            $this->readonly = $this->mode === "DEL" ? "readonly" : "";
            $this->showCommitBtn = $this->mode !== "DSP";
            if ($this->mode !== "INS") {
                $this->usuario = UsuariosDao::getUsuarioById(intval($_GET["usercod"] ?? 0));
                if (!$this->usuario) {
                    throw new \Exception("No se encontró el Usuario", 1);
                }
            }
        } else {
            throw new \Exception("Formulario cargado en modalidad invalida", 1);
        }
    }

    private function validateData()
    {
        $errors = [];
        $this->usuario_xss_token = $_POST["usuario_xss_token"] ?? "";
        $this->usuario["usercod"] = intval($_POST["usercod"] ?? "");
        $this->usuario["useremail"] = strval($_POST["useremail"] ?? "");
        $this->usuario["username"] = strval($_POST["username"] ?? "");
        $this->usuario["userest"] = strval($_POST["userest"] ?? "");
        $this->usuario["usertipo"] = strval($_POST["usertipo"] ?? "");

        if (Validators::IsEmpty($this->usuario["useremail"])) {
            $errors["useremail_error"] = "El correo del usuario es requerido";
        } elseif (!Validators::IsValidEmail($this->usuario["useremail"])) {
            $errors["useremail_error"] = "El correo del usuario no es válido";
        }

        if (Validators::IsEmpty($this->usuario["username"])) {
            $errors["username_error"] = "El nombre del usuario es requerido";
        }

        if (!in_array($this->usuario["userest"], ["ACT", "INA", "BLQ", "SUS"])) {
            $errors["userest_error"] = "El estado del usuario es invalido";
        }

        if (!in_array($this->usuario["usertipo"], ["PBL", "ADM", "AUD"])) {
            $errors["usertipo_error"] = "El tipo de usuario es invalido";
        }

        if (count($errors) > 0) {
            foreach ($errors as $key => $value) {
                $this->usuario[$key] = $value;
            }
            return false;
        }
        return true;
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
        $result = UsuariosDao::insertUsuario(
            $this->usuario["useremail"],
            $this->usuario["username"],
            $this->usuario["userest"],
            $this->usuario["usertipo"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Usuarios_Usuarios",
                "Usuario creado exitosamente"
            );
        }
    }

    private function handleUpdate()
    {
        $result = UsuariosDao::updateUsuario(
            $this->usuario["usercod"],
            $this->usuario["useremail"],
            $this->usuario["username"],
            $this->usuario["userest"],
            $this->usuario["usertipo"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Usuarios_Usuarios",
                "Usuario actualizado exitosamente"
            );
        }
    }

    private function handleDelete()
    {
        $result = UsuariosDao::deleteUsuario($this->usuario["usercod"]);
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Usuarios_Usuarios",
                "Usuario eliminado exitosamente"
            );
        }
    }

    private function setViewData(): void
    {
        $this->viewData["mode"] = $this->mode;
        $this->viewData["usuario_xss_token"] = $this->usuario_xss_token;
        $this->viewData["FormTitle"] = sprintf(
            $this->modeDescriptions[$this->mode],
            $this->usuario["usercod"],
            $this->usuario["username"]
        );
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["readonly"] = $this->readonly;

        $userestKey = "userest_" . strtolower($this->usuario["userest"]);
        $this->usuario[$userestKey] = "selected";
        $usertipoKey = "usertipo_" . strtolower($this->usuario["usertipo"]);
        $this->usuario[$usertipoKey] = "selected";

        $this->viewData["usuario"] = $this->usuario;
    }
}
?>
