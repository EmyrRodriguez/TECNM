<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

error_reporting(E_ALL);
ini_set("display_errors", 1);
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
        default:
            break;
    }
}

function catchPOST() {
    return $data = filter_input_array(INPUT_POST);
}
