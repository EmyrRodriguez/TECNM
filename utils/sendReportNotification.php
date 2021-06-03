<?php

require_once '../model/DiracMx.php';
require_once 'Utils.php';
require_once '../../sgi-dirac/model/BitacoraCorreo.php';

error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();

$diracMX = DiracMx::DiracMxSngltn();
$utils = Utils::utlsSngltn();
//Obtenermos todos las inspecciones
//$getInfoReports = $diracMX->getBuildingRecordsToday();
$getInfoReports = $diracMX->getAllBuildingRecords();

//Recorremos resultado de la consulta 
foreach ($getInfoReports["data"] as $key => $value) {
    $usuario = $diracMX->getUsrById($value["reviso"]);

    $subject = "Asignacion de Revision de Inspeccion";
    $url = SYSTEM_PATH . 'utils/templates/asignacion_inspeccion.php?nombre=' . urlencode($usuario["data"]["nombre"]) .
            '&supervisor=' . urlencode($value["supervisor"]) .
            '&edificio=' . urlencode($value["nombre_edificio"]);
    $msg = $utils->getPageHTML($url, '<html>', '</html>');
    
//    echo $usuario["data"]["correo"];
    
    $sendMail = $utils->sendMail($usuario["data"]["correo"], $usuario["data"]["nombre"], $subject, $msg);

    $bitacora = BitacoraCorreo::BitMlSngltn();
    if (isset($_SESSION["sgi_id_usr"])) {
        $bitacora->setRemitente($_SESSION["sgi_id_usr"]);
    } else {
        $bitacora->setRemitente('QUBI-arjion@dirac.mx');
    }
    $bitacora->setCorreo_destino($usuario["data"]["correo"]);
    $bitacora->setFecha(date('Y-m-d H:i:s'));
    $bitacora->setEstatus(1);

    $bitacora->setDescripcion('QUBI - Asignacion de Revision de Inspeccion');
    $addBinn = $bitacora->addRecBin($bitacora);
}

