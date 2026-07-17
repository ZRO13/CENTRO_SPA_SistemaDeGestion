<?php


// CONTROLLER - Punto de entrada del panel administrativo. El listado y
// búsqueda de Clientes/Empleados vive en sus propios Controllers
// (ClienteController, EmpleadoController) para respetar una única
// responsabilidad por Controller; este solo sirve el Dashboard.

require_once "controllers/SesionHelper.php";

class AdminController
{
    public function __construct()
    {
        // El Dashboard es visible para Administrador y Colaborador.
        SesionHelper::requerirRol(['Administrador', 'Colaborador']);
    }

    public function dashboard()
    {
        $mostrarMenu = false;
        require_once "views/admin/admin.php";
    }
}
