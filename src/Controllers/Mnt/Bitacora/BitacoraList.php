<?php

namespace Controllers\Mnt\Bitacora;

use Controllers\PublicController;
use Dao\Mnt\Bitacora as BitacoraDao;
use Views\Renderer;

const BITACORA_LIST_VIEW = "mnt/bitacora/bitacoraList";

class BitacoraList extends PublicController
{
    public function run(): void
    {
        $viewData = [
            "bitacora" => BitacoraDao::getAll()
        ];
        Renderer::render(
            BITACORA_LIST_VIEW,
            $viewData
        );
    }
}
