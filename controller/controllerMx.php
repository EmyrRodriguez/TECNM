<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../model/DiracMx.php';

$params = json_decode(file_get_contents('php://input'), true);

$evento = 0;
$data = "";
if (is_null($params)) {
    $evento = filter_input(INPUT_POST, "evento");
    $data = catchPOST();
} else {
    $evento = $params["evento"];
    $data = $params;
}

//var_dump($_REQUEST);
handler($evento, $data);

function handler($evento, $data) {
    $diracMX = DiracMx::DiracMxSngltn();
    switch ($evento) {
        case 1://Insertar denuncia
            $addC = $diracMX->addComplaint($data);

            if ($addC["errorCode"] === 0) {
                require_once '../utils/Utils.php';
                $utils = Utils::utlsSngltn();

                $subject = 'Se ha generado una denuncia';

                $url = SYSTEM_PATH . 'utils/templates/correo_denuncias.php?asunto=' . urlencode($data["asunto"]) .
                        '&fecha_suceso=' . urlencode($data["fecha_suceso"]) .
                        '&descripcion=' . urlencode($data["descripcion"]) .
                        '&comentarios=' . urlencode($data["comentarios"]) .
                        '&nombre=' . urlencode($data["nombre"]) .
                        '&correo=' . urlencode($data["correo"]) .
                        '&archivo=' . urlencode($data["archivo"]) .
                        '&telefono=' . urlencode($data["telefono"]);
                $msg = $utils->getPageHTML($url, '<html>', '</html>');
                $sendMail = $utils->sendMail("msalazar@dirac.mx", "Mario Salazar", $subject, $msg);
                $sendMail = $utils->sendMail("sistemas@dirac.mx", "Sistemas", $subject, $msg);
                $sendMail = $utils->sendMail("ivan@dirac.mx", "Admin", $subject, $msg);
//                var_dump($sendMail);
            }
            echo json_encode($addC);
            break;
        case 2:
            $addBI = $diracMX->addBuildingInspection($data);
            echo json_encode($addBI);
            break;
        case 3:
            $getBR = $diracMX->getBuildingRecords();
            echo json_encode($getBR);
            break;
        case 4:
            $getRF = $diracMX->getRecordFiles($data["id"]);
            echo json_encode($getRF);
            break;
        case 5:
            $getIR = $diracMX->getInfoRecord($data["id"]);
            echo json_encode($getIR);
            break;
        case 6:
            $addClient = $diracMX->addClientInspection($data);
            echo json_encode($addClient);
            break;
        case 7:
            $results = $diracMX->getResultsByKey($data["clave"]);
            echo json_encode($results);
            break;
        case 8:
            $activities = $diracMX->getActivitiesByKey($data["clave"]);
            echo json_encode($activities);
            break;
        case 9:
            $addressbyCol = $diracMX->getAddressByCol($data["id_colonia"]);
            echo json_encode($addressbyCol);
            break;
        case 10:
            $delFileInspection = $diracMX->deleteFile($data["id_archivo"]);
            echo json_encode($delFileInspection);
            break;
        case 11:
            $updateClient = $diracMX->updateClient($data);
            echo json_encode($updateClient);
            break;
        case 12:
            $updateInspection = $diracMX->updateInspection($data);
            echo json_encode($updateInspection);
            break;
        case 13:
            $allResults = $diracMX->getAllResults();
            echo json_encode($allResults);
            break;
        case 14:
            $allActivities = $diracMX->getAllActivities();
            echo json_encode($allActivities);
            break;
        case 15://Subir registros QUBI Movil
            $saveRecords = $diracMX->saveClientMovil($data);
            echo json_encode($saveRecords);
            break;
        case 16:// Subir registros de imagenes QUBI Movil
            $saveRecordImages = $diracMX->addFileBuildingInspection($data["id_inspeccion"], $data["descripcion"], $data["archivo"]);
            echo json_encode($saveRecordImages);
            break;
        case 17://Enviar notificaciones por correo
            require_once '../utils/Utils.php';
            $utils = Utils::utlsSngltn();
            $infoRecord = $diracMX->getInfoRecord($data["id_inspeccionO"]);

            $response = array();

            switch (intval($data["tipo"])) {
                case 1://Correcciones de revisor a inspector                    
                    $subject = 'Observaciones en inspeccion ' . $infoRecord["data"]["nombre_edificio"] . ".";
                    $msg = 'El revisor ' . $infoRecord["data"]["revisor"] . " le ha enviado observaciones en la inspecci&oacute;n del edificio " . $infoRecord["data"]["nombre_edificio"] . " <br /><br />" . $data["correcciones_revisor"];

                    $nombre = $infoRecord["data"]["supervisor"];

                    $url = SYSTEM_PATH . 'utils/templates/correo_generico.php?nombre=' . urlencode($nombre) .
                            '&mensaje=' . urlencode($msg);
                    $msg2 = $utils->getPageHTML($url, '<html>', '</html>');

                    $inspector = $diracMX->getUsrById($infoRecord["data"]["id_supervisor"]);
                    $sendMail = $utils->sendMail($inspector["data"]["correo"], $inspector["data"]["nombre"], $subject, $msg2);

                    $response["errorCode"] = 0;
                    $response["msg"] = "Â¡Operaci&oacute;n exitosa!";

                    break;
                case 2://Edicion de inspeccion por parte del inspector
                    $subject = 'Modificaciones en inspeccion ' . $infoRecord["data"]["nombre_edificio"];
                    $msg = 'El inspector ' . $infoRecord["data"]["supervisor"] . " le ha enviado un comentario con respecto a la inspecci&oacute;n del edificio " . $infoRecord["data"]["nombre_edificio"] . " <br /><br />" . $data["correcciones_revisor"];
                    $nombre = $infoRecord["data"]["revisor"];

                    $url = SYSTEM_PATH . 'utils/templates/correo_generico.php?nombre=' . urlencode($nombre) .
                            '&mensaje=' . urlencode($msg);
                    $msg2 = $utils->getPageHTML($url, '<html>', '</html>');

                    $revisor = $diracMX->getUsrById($infoRecord["data"]["reviso"]);
                    $sendMail = $utils->sendMail($revisor["data"]["correo"], $revisor["data"]["nombre"], $subject, $msg2);

                    $response["errorCode"] = 0;
                    $response["msg"] = "Â¡Operaci&oacute;n exitosa!";
                    break;

                default:
                    break;
            }
            echo json_encode($response);

            break;

        case 18:// Editar pie de imagen
//            var_dump($_REQUEST);
            $editImgDescript = $diracMX->updtImgDesc($data["id_archivo"], $data["descripcion"]);
            echo json_encode($editImgDescript);
            break;

        case 19:// Agregar visitante
            $add_visitor = $diracMX->addVisitor($data);

            if (intval($data["id_usuario"]) !== 1 || intval($data["id_usuario"]) !== 2) {
                //Enviamos correo a persona que visitan
                require_once '../utils/Utils.php';
                $utils = Utils::utlsSngltn();
                $subject = 'Notificacion de visita';
                $colaborador = $diracMX->getUsrById($data["id_usuario"]);
                $msg = 'Tienes un visitante de nombre <b>' . $data["nombre"] . '</b>, de la empresa ' . $data["empresa"] . ', el cual se encuentra en recepci&oacute;n de Planta Baja y viene a tratar un asunto relacionado a <b>' . $data["motivo"] . "</b>";

                $nombre = $colaborador["data"]["nombre"];

                $url = SYSTEM_PATH . 'utils/templates/correo_registro_visitantes.php?nombre=' . urlencode($nombre) .
                        '&mensaje=' . urlencode($msg);
                $msg2 = $utils->getPageHTML($url, '<html>', '</html>');

                $sendMail = $utils->sendMail($colaborador["data"]["correo"], $colaborador["data"]["nombre"], $subject, $msg2);
            }
            /*             * ********************************************************************************************************** */
            echo json_encode($add_visitor);
            break;
        case 20:// Obtener usuarios de Oficinas Centrales
            $q = "eliminado = 1";
            $getUsrsOC = $diracMX->getInfoUsrsView($q);
            echo json_encode($getUsrsOC);
            break;
        case 21:// Obtener usuarios registrados
            $q = "estatus = 1 ";
            $getVisitorsR = $diracMX->getVisitors($q);
            echo json_encode($getVisitorsR);
            break;
        case 22:// Registrar salida           
            $checkOut = $diracMX->CheckOut($data["id"]);
            echo json_encode($checkOut);
            break;
        case 23:// Buscar usuarios que tengan registros anteriores
            $q = "correo = '" . $data["correo"] . "' AND estatus = 2 GROUP BY correo LIMIT 1 ";
            $getPVisitors = $diracMX->getVisitors($q);
            echo json_encode($getPVisitors);
            break;
        case 24:// Obtener usuarios registrados
//            $q = "1=1";
            $getVisitorsR = $diracMX->getVisitors($data["q"]);
            echo json_encode($getVisitorsR);
            break;
        case 25:// Obtener usuarios registrados
            $getVisitorsR = $diracMX->getVisitors($data["q"]);
            echo json_encode($getVisitorsR);
            break;
        case 26://Confirmar recepcion de equipo.
            $checkOut = $diracMX->checkOutS($data["id"], $data["no_control"]);
            echo json_encode($checkOut);
            break;
        case 27:// Registrar salida           
            $checkPersonnel = $diracMX->checkPersonnel($data["id"], $data["tipo"]);
            echo json_encode($checkPersonnel);
            break;
        case 28://Consulta de salidas por creacion o destinatario
            @session_start();
            $query = "1=1";
            $tipo = intval($data["tipo"]);

            switch ($tipo) {
                case 1:
                    $query = "fecha_salida = '" . date("Y-m-d") . "'";
                    break;
                default:
                    break;
            }
            $myOutputs = $diracMX->getOutputsPersonnel($query);
            echo json_encode($myOutputs);
            break;
        case 29:// Registrar salida           
            $checkPersonnel = $diracMX->checkingSuppliers($data["id"], $data["tipo"]);
            echo json_encode($checkPersonnel);
            break;
        case 30://Consulta de salidas por creacion o destinatario
            @session_start();
            $query = "1=1";
            $tipo = intval($data["tipo"]);

            switch ($tipo) {
                case 1:
                    $query = "fecha_ingreso = '" . date("Y-m-d") . "' AND estatus = 2";
                    break;
                default:
                    break;
            }
            $myOutputs = $diracMX->getSuppliers($query);
            echo json_encode($myOutputs);
            break;

        case 31:// Registrar candidatos           
            $checkApps = $diracMX->checkingapps($data["id"], $data["tipo"]);
            echo json_encode($checkApps);
            break;
        case 32://Consulta de candidatos
            @session_start();
            $query = "1=1";
            $tipo = intval($data["tipo"]);
            switch ($tipo) {
                case 1:
                    $query = "fecha >= '" . date("Y-m-d") . " 00:00:00' AND fecha <= '" . date("Y-m-d") . " 23:59:59 '";
                    break;
                case 2:
                    $query = "fecha >= '" . $data["fecha_inicio"] . " 00:00:00' AND fecha <= '" . $data["fecha_fin"] . " 23:59:59 '";
                    break;
                default:
                    break;
            }
            $myOutputs = $diracMX->getInterviews($query);
            echo json_encode($myOutputs);
            break;
        case 33:
            $saveTemp = $diracMX->saveTemp($data);
            echo json_encode($saveTemp);
            break;
        case 34:// Obtener usuarios
            $getUsrs = $diracMX->getInfoUsrsView($data["q"]);
            echo json_encode($getUsrs);
            break;
        case 35:// Obtener registros de usuarios
            $getRecords = $diracMX->getRecordsTemp($data["q"]);
            echo json_encode($getRecords);
            break;
        default:
            break;
    }
}

function catchPOST() {
    return $data = filter_input_array(INPUT_POST);
}
