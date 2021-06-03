<?php

/*
 * DiracMx.php
 * @author FIGG - DIRAC
 * @copyright (c) 2017, DIRAC.
 * @description Clase para procesos en BD's.
 */

require_once realpath(dirname(__FILE__) . '/../config/ConnectionDB.php');
require_once realpath(dirname(__FILE__) . '/../config/properties.php');

class TECNM implements JsonSerializable {

    public $db;
    public $response;
    private static $instance;

    function __construct() {
        $this->db = ConnectionDB::connectSngtn()->DBConnect();
        $this->response = array();
//        $this->table = "cat_encuestas";
    }

    // Método singleton
    public static function TECNMSngltn() {
        if (!isset(self::$instance)) {
            $TECNM = __CLASS__;
            self::$instance = new $TECNM();
        }
        return self::$instance;
    }

    // Evita que el objeto se pueda clonar
    public function __clone() {
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);
    }

    /*     * ************************************************************* */

    function addComplaint($complaint) {
        $this->db->beginTransaction();

        require_once '../utils/Utils.php';
        $utils = Utils::utlsSngltn();
        $ip = $utils->getRealIP();


        try {
            $query = "INSERT INTO denuncias (asunto, fecha_suceso, descripcion, comentarios, nombre, correo, telefono, ip, fecha, archivo) VALUES(?,?,?,?,?,?,?,?,?,?) ";
            $oComp = $this->db->prepare($query);
            $oComp->execute(array(
                $complaint["asunto"],
                $complaint["fecha_suceso"],
                $complaint["descripcion"],
                $complaint["comentarios"],
                $complaint["nombre"],
                $complaint["correo"],
                $complaint["telefono"],
                $ip,
                date('Y-m-d H:i:s'),
                $complaint["archivo"]
            ));
            $id = $this->db->lastInsertId();
            $this->db->Commit();

            $this->response["data"] = $id;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    /*     * ******************************************************************************** */

    function addBuildingInspection($inspection) {
        @session_start();

        $revisores = array(9, 17, 108);
        shuffle($revisores);
        $usrR = $revisores[0];


        $this->db->beginTransaction();
        try {
            $query = "INSERT INTO dcmx_inspeccion_edificios (id_cliente, id_supervisor, fecha_inspeccion, resultado, estado_h, nombre_edificio, fecha, tipo, actividades, resultados, otros, comentarios, reviso, no_pisos, sotano, no_sotanos, solucion_estructural) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
            $oComp = $this->db->prepare($query);
            $oComp->execute(array(
                $inspection["id_cliente"],
                $_SESSION["sgi_id_usr"],
                $inspection["fecha_inspeccion"],
                $inspection["resultado"],
                $inspection["estado_h"],
                $inspection["nombre_edificio"],
                date('Y-m-d H:i:s'),
                $inspection["tipo"],
                json_encode($inspection["actividades"]),
                json_encode($inspection["resultados"]),
                (isset($inspection["otros"]) ? $inspection["otros"] : ""),
                $inspection["comentarios"],
                $usrR,
                $inspection["pisos"],
                (isset($inspection["sotano"]) ? $inspection["sotano"] : 0),
                (isset($inspection["numero_sotano"]) ? $inspection["numero_sotano"] : 0),
                $inspection["solucion"]
            ));
            $id = $this->db->lastInsertId();
            $this->db->Commit();

            //Agregamos direccion de edificio
            $address = $this->addBuildingAddress($inspection, $id);

            //Enviar correo a revisor
            /* $usuario = $this->getUsrById($usrR);
              require_once '../utils/Utils.php';
              $utils = Utils::utlsSngltn();

              $subject = 'Asignacion de Revision de Inspeccion';

              $url = SYSTEM_PATH . 'utils/templates/asignacion_inspeccion.php?nombre=' . urlencode($usuario["data"]["nombre"]) .
              '&supervisor=' . urlencode($_SESSION["sgi_nombre"]) .
              '&edificio=' . urlencode($inspection["nombre_edificio"]);
              $msg = $utils->getPageHTML($url, '<html>', '</html>');
              $sendMail = $utils->sendMail($usuario["data"]["correo"], $usuario["data"]["nombre"], $subject, $msg);
             */
            $this->response["data"] = $id;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $e) {
            echo "DataBase Error: The user could not be added.<br>" . $e->getMessage();
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function addBuildingAddress($inspection, $id) {
        $this->db->beginTransaction();
        try {
            $query = "INSERT INTO dcmx_direccion_edificio (id_edificio, id_colonia, calle, numero, lat, lon, referencias, estado, municipio, colonia, cp) VALUES(?,?,?,?,?,?,?,?,?,?,?) ";
            $oComp = $this->db->prepare($query);
            $oComp->execute(array(
                $id,
                1,
                $inspection["calle"],
                $inspection["numero"],
                $inspection["latitud"],
                $inspection["longitud"],
                $inspection["referencias"],
                $inspection["estados"],
                $inspection["ciudades"],
                $inspection["colonias"],
                $inspection["cp"]
            ));
            $id_dir = $this->db->lastInsertId();
            $this->db->Commit();

            $this->response["data"] = $id_dir;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $e) {
            echo "DataBase Error: The user could not be added.<br>" . $e->getMessage();
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function addClientInspection($client) {
        $this->db->beginTransaction();
        try {
            $query = "INSERT INTO dcmx_clientes (nombre, correo, telefono, fecha_registro, sexo) VALUES(?,?,?,?,?) ";
            $oComp = $this->db->prepare($query);
            $oComp->execute(array(
                $client["nombre_cliente"],
                $client["correo"],
                $client["telefono"],
                date('Y-m-d H:i:s'),
                $client["sexo"]
            ));
            $id = $this->db->lastInsertId();
            $this->db->Commit();

            $this->response["data"] = $id;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function addFileBuildingInspection($registro, $descripcion, $nombre) {
        $this->db->beginTransaction();
        try {
            $query = "INSERT INTO dcmx_inspeccion_archivos (id_inspeccion, descripcion, archivo, fecha) VALUES(?,?,?,?) ";
            $oComp = $this->db->prepare($query);
            $oComp->execute(array(
                $registro,
                $descripcion,
                $nombre,
                date('Y-m-d H:i:s')
            ));
            $id = $this->db->lastInsertId();
            $this->db->Commit();

            $this->response["data"] = $id;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $e) {
            echo "DataBase Error: The user could not be added.<br>" . $e->getMessage();
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function getBuildingRecords() {
        try {
            $query = "SELECT
                                E.*, C.nombre, C.sexo, CONCAT(U.nombre,' ', U.apellidos) AS supervisor,
                                (SELECT CONCAT(nombre,' ', apellidos) FROM usuarios_dirac WHERE id_usuario = E.reviso ) AS revisor
                        FROM
                                dcmx_inspeccion_edificios AS E
                        INNER JOIN dcmx_clientes AS C ON E.id_cliente = C.id
                        INNER JOIN usuarios_dirac AS U ON E.id_supervisor = U.id_usuario GROUP BY C.id";
            $oCat = $this->db->prepare($query);
            $oCat->execute();
            $data = array();
            foreach ($oCat as $key => $cat) {
                $catt = array();
                $catt["id"] = $cat["id"];
                $catt["nombre"] = $cat["nombre"];
                $catt["fecha_inspeccion"] = $cat["fecha_inspeccion"];
                $catt["resultado"] = utf8_encode($cat["resultado"]);
                $catt["estado_h"] = utf8_encode($cat["estado_h"]);
                $catt["nombre_edificio"] = $cat["nombre_edificio"];
                $catt["comentarios"] = $cat["comentarios"];
                $catt["fecha"] = $cat["fecha"];
                $catt["estatus"] = $cat["estatus"];
                $catt["tipo"] = $cat["tipo"];
                $catt["sexo"] = $cat["sexo"];
                $catt["revisado"] = $cat["revisado"];
                $catt["reviso"] = $cat["reviso"];
                $catt["revisor"] = $cat["revisor"];
                $catt["id_supervisor"] = $cat["id_supervisor"];
                $catt["supervisor"] = $cat["supervisor"];
                $data[] = $cat;
            }
//            var_dump($data);
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function getBuildingRecordsToday() {
        try {
            $query = "SELECT
                                E.*, C.nombre, C.sexo, CONCAT(U.nombre,' ', U.apellidos) AS supervisor,
                                (SELECT CONCAT(nombre,' ', apellidos) FROM usuarios_dirac WHERE id_usuario = E.reviso ) AS revisor
                        FROM
                                dcmx_inspeccion_edificios AS E
                        INNER JOIN dcmx_clientes AS C ON E.id_cliente = C.id
                        INNER JOIN usuarios_dirac AS U ON E.id_supervisor = U.id_usuario
                        WHERE LEFT(E.fecha, 10) = CURDATE() GROUP BY C.id";
            $oCat = $this->db->prepare($query);
            $oCat->execute();
            $data = array();
            foreach ($oCat as $key => $cat) {
                $catt = array();
                $catt["id"] = $cat["id"];
                $catt["nombre"] = $cat["nombre"];
                $catt["fecha_inspeccion"] = $cat["fecha_inspeccion"];
                $catt["resultado"] = utf8_encode($cat["resultado"]);
                $catt["estado_h"] = utf8_encode($cat["estado_h"]);
                $catt["nombre_edificio"] = $cat["nombre_edificio"];
                $catt["comentarios"] = $cat["comentarios"];
                $catt["fecha"] = $cat["fecha"];
                $catt["estatus"] = $cat["estatus"];
                $catt["tipo"] = $cat["tipo"];
                $catt["sexo"] = $cat["sexo"];
                $catt["revisado"] = $cat["revisado"];
                $catt["reviso"] = $cat["reviso"];
                $catt["revisor"] = $cat["revisor"];
                $catt["supervisor"] = $cat["supervisor"];
                $data[] = $cat;
            }
//            var_dump($data);
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function getAllBuildingRecords() {
        try {
            $query = "SELECT
                                E.*, C.nombre, C.sexo, CONCAT(U.nombre,' ', U.apellidos) AS supervisor,
                                (SELECT CONCAT(nombre,' ', apellidos) FROM usuarios_dirac WHERE id_usuario = E.reviso ) AS revisor
                        FROM
                                dcmx_inspeccion_edificios AS E
                        INNER JOIN dcmx_clientes AS C ON E.id_cliente = C.id
                        INNER JOIN usuarios_dirac AS U ON E.id_supervisor = U.id_usuario
                        GROUP BY C.id";
            $oCat = $this->db->prepare($query);
            $oCat->execute();
            $data = array();
            foreach ($oCat as $key => $cat) {
                $catt = array();
                $catt["id"] = $cat["id"];
                $catt["nombre"] = $cat["nombre"];
                $catt["fecha_inspeccion"] = $cat["fecha_inspeccion"];
                $catt["resultado"] = utf8_encode($cat["resultado"]);
                $catt["estado_h"] = utf8_encode($cat["estado_h"]);
                $catt["nombre_edificio"] = $cat["nombre_edificio"];
                $catt["comentarios"] = $cat["comentarios"];
                $catt["fecha"] = $cat["fecha"];
                $catt["estatus"] = $cat["estatus"];
                $catt["tipo"] = $cat["tipo"];
                $catt["sexo"] = $cat["sexo"];
                $catt["revisado"] = $cat["revisado"];
                $catt["reviso"] = $cat["reviso"];
                $catt["revisor"] = $cat["revisor"];
                $catt["supervisor"] = $cat["supervisor"];
                $data[] = $cat;
            }
//            var_dump($data);
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function getRecordFiles($id) {
        try {
            $query = "SELECT * FROM dcmx_inspeccion_archivos WHERE id_inspeccion = ?;";
            $oRF = $this->db->prepare($query);
            $oRF->execute(array($id));
            $data = array();
            foreach ($oRF as $key => $cat) {
                $catt = array();
                $catt["id"] = $cat["id"];
                $catt["id_inspeccion"] = $cat["id_inspeccion"];
                $catt["descripcion"] = $cat["descripcion"];
                $catt["archivo"] = utf8_encode($cat["archivo"]);
                $catt["fecha"] = utf8_encode($cat["fecha"]);
                $data[] = $cat;
            }
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function getInfoRecord($id) {
        try {
            /* $query = "SELECT
              E.*, C.id AS id_cliente,
              C.nombre,
              C.correo,
              C.telefono,
              C.sexo,
              D.id AS id_direccion,
              D.calle,
              D.id_colonia,
              D.lat,
              D.lon,
              D.numero,
              D.referencias,
              D.estado,
              D.municipio,
              D.colonia,
              CONCAT(U.nombre, ' ', U.apellidos) AS supervisor,
              (SELECT CONCAT(nombre,' ', apellidos) FROM usuarios_dirac WHERE id_usuario = E.reviso ) AS revisor,
              VD.*
              FROM
              dcmx_inspeccion_edificios AS E
              INNER JOIN dcmx_clientes AS C ON E.id_cliente = C.id
              INNER JOIN dcmx_direccion_edificio AS D ON E.id = D.id_edificio
              INNER JOIN usuarios_dirac AS U ON E.id_supervisor = U.id_usuario
              INNER JOIN v_direcciones AS VD ON D.id_colonia = VD.id_colonia
              WHERE E.id = ?;"; */
            $query = "SELECT
                                E.*, C.id AS id_cliente,
                                C.nombre,
                                C.correo,
                                C.telefono,
                                C.sexo,
                                D.id AS id_direccion,
                                D.calle,
                                D.id_colonia,
                                D.lat,
                                D.lon,
                                D.numero,
                                D.referencias,
                                D.estado,
                                D.municipio,
                                D.colonia,
                                D.cp,
                                CONCAT(U.nombre, ' ', U.apellidos) AS supervisor,
                                (
                                        SELECT
                                                CONCAT(nombre, ' ', apellidos)
                                        FROM
                                                usuarios_dirac
                                        WHERE
                                                id_usuario = E.reviso
                                ) AS revisor
                        FROM
                                dcmx_inspeccion_edificios AS E
                        INNER JOIN dcmx_clientes AS C ON E.id_cliente = C.id
                        INNER JOIN dcmx_direccion_edificio AS D ON E.id = D.id_edificio
                                                INNER JOIN usuarios_dirac AS U ON E.id_supervisor = U.id_usuario

                                                    WHERE E.id = ?";
            $oIR = $this->db->prepare($query);
            $oIR->execute(array($id));
            $data = array();

            if ($oIR->rowCount() === 1) {
                $record = $oIR->fetch(PDO::FETCH_OBJ);
                $data["id"] = $record->id;
                $data["fecha_inspeccion"] = $record->fecha_inspeccion;
                $data["resultado"] = $record->resultado;
                $data["estado_h"] = $record->estado_h;
                $data["nombre_edificio"] = $record->nombre_edificio;
                $data["comentarios"] = $record->comentarios;
                $data["fecha"] = $record->fecha;
                $data["estatus"] = $record->estatus;
                $data["tipo"] = $record->tipo;
                $data["actividades"] = json_decode($record->actividades);
                $data["resultados"] = json_decode($record->resultados);
                $data["revisado"] = $record->revisado;
                $data["id_cliente"] = $record->id_cliente;
                $data["nombre"] = $record->nombre;
                $data["correo"] = $record->correo;
                $data["telefono"] = $record->telefono;
                $data["calle"] = $record->calle;
                $data["id_colonia"] = $record->id_colonia;
                $data["numero"] = $record->numero;
                $data["latitud"] = $record->lat;
                $data["longitud"] = $record->lon;
                $data["referencias"] = $record->referencias;
                $data["estado"] = $record->estado;
                $data["municipio"] = $record->municipio;
                $data["colonia"] = $record->colonia;
                $data["cp"] = $record->cp;
                $data["id_supervisor"] = $record->id_supervisor;
                $data["supervisor"] = $record->supervisor;
                $data["sexo"] = $record->sexo;
                $data["otros"] = $record->otros;
//                $data["id_estado"] = $record->id_estado;
//                $data["id_ciudad"] = $record->id_ciudad;
//                $data["id_cp"] = $record->id_cp;
//                $data["valor"] = $record->valor;
                $data["id_direccion"] = $record->id_direccion;
                $data["reviso"] = $record->reviso;
                $data["revisor"] = $record->revisor;
                $data["no_pisos"] = $record->no_pisos;
                $data["sotano"] = $record->sotano;
                $data["no_sotanos"] = $record->no_sotanos;
                $data["solucion_estructural"] = $record->solucion_estructural;
                $data["dro"] = $record->dro;
            }

//            var_dump($data);
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function getActivitiesByKey($clave) {
        try {
            $query = "SELECT * FROM dcmx_cat_actividades_realizar WHERE clave = ?;";
            $oRF = $this->db->prepare($query);
            $oRF->execute(array($clave));
            $data = array();
            foreach ($oRF as $key => $cat) {
                $catt = array();
                $catt["id"] = $cat["id"];
                $catt["clave"] = $cat["clave"];
                $catt["descripcion"] = $cat["descripcion"];
                $catt["estatus"] = utf8_encode($cat["estatus"]);
                $data[] = $cat;
            }
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function getResultsByKey($clave) {
        try {
            $query = "SELECT * FROM dcmx_cat_resultados_inspeccion WHERE clave = ?;";
            $oRF = $this->db->prepare($query);
            $oRF->execute(array($clave));
            $data = array();
            foreach ($oRF as $key => $cat) {
                $catt = array();
                $catt["id"] = $cat["id"];
                $catt["clave"] = $cat["clave"];
                $catt["descripcion"] = $cat["descripcion"];
                $catt["estatus"] = utf8_encode($cat["estatus"]);
                $data[] = $cat;
            }
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function updateStatus($id, $estatus) {
        $this->db->beginTransaction();
        try {
            $query = "UPDATE dcmx_inspeccion_edificios SET revisado = ? WHERE id =? ";
            $oRec = $this->db->prepare($query);
            $oRec->execute(
                    array($estatus, $id)
            );
            $this->db->Commit();

            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
            $this->response["info"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function updateDRO($id, $dro) {
        $this->db->beginTransaction();
        try {
            $query = "UPDATE dcmx_inspeccion_edificios SET dro = ? WHERE id =? ";
            $oRec = $this->db->prepare($query);
            $oRec->execute(
                    array($dro, $id)
            );
            $this->db->Commit();

            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
            $this->response["info"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function deleteFile($id) {
        $this->db->beginTransaction();
        try {
            $query = "DELETE FROM dcmx_inspeccion_archivos WHERE id =? ";
            $oRec = $this->db->prepare($query);
            $oRec->execute(array($id));
            $this->db->Commit();

            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
            $this->response["info"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function updateClient($data) {
        $this->db->beginTransaction();
        try {
            $query = "UPDATE dcmx_clientes SET nombre = ?, correo = ?, telefono = ?, sexo = ? WHERE id =? ";
            $oRec = $this->db->prepare($query);
            $oRec->execute(array($data["nombre_cliente"],
                $data["correo"],
                $data["telefono"],
                $data["sexo"],
                $data["id_cliente"]
            ));
            $this->db->Commit();

            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
            $this->response["info"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function updateInspection($inspection) {
        $this->db->beginTransaction();
        try {
            $query = "UPDATE dcmx_inspeccion_edificios SET fecha_inspeccion = ?, resultado = ?, estado_h = ?, nombre_edificio = ?, tipo = ?, actividades = ?, resultados = ?, otros = ?, comentarios = ?, no_pisos = ?, sotano = ?, no_sotanos = ?, solucion_estructural = ? WHERE id = ? ";
            $oRec = $this->db->prepare($query);
            $oRec->execute(array(
                $inspection["fecha_inspeccion"],
                $inspection["resultado"],
                $inspection["estado_h"],
                $inspection["nombre_edificio"],
                $inspection["tipo"],
                json_encode($inspection["actividades"]),
                json_encode($inspection["resultados"]),
                (isset($inspection["otros"]) ? $inspection["otros"] : ""),
                $inspection["comentarios"],
                $inspection["pisos"],
                (isset($inspection["sotano"]) ? $inspection["sotano"] : 0),
                (isset($inspection["numero_sotano"]) ? $inspection["numero_sotano"] : 0),
                $inspection["solucion"],
                $inspection["id_inspeccion"]
            ));
            $this->db->Commit();

            $updateAddress = $this->updateAddress($inspection);

            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $e) {
            echo "DataBase Error: The user could not be added.<br>" . $e->getMessage();
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
            $this->response["info"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function updateAddress($inspection) {
        $this->db->beginTransaction();
        try {
            $query = "UPDATE dcmx_direccion_edificio SET id_colonia = ?, calle = ?, numero = ?, lat = ?, lon = ?, referencias = ?, estado = ?, municipio = ?, colonia = ?, cp = ? WHERE id = ? ";
            $oRec = $this->db->prepare($query);
            $oRec->execute(array(
                $inspection["colonias"],
                $inspection["calle"],
                $inspection["numero"],
                $inspection["latitud"],
                $inspection["longitud"],
                $inspection["referencias"],
                $inspection["estados"],
                $inspection["ciudades"],
                $inspection["colonias"],
                $inspection["cp"],
                $inspection["id_direccion"]
            ));
            $this->db->Commit();

            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $e) {
            echo "DataBase Error: The user could not be added.<br>" . $e->getMessage();
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
            $this->response["info"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function addInspectionSignature($id, $supervisor, $firma_supervisor, $revisor, $firma_revisor) {
        $this->db->beginTransaction();
        try {
            $query = "INSERT INTO dcmx_firmas_reporte_inspeccion(id_inspeccion, id_supervisor, firma_supervisor, id_revisor, firma_revisor, fecha) VALUES(?,?,?,?,?,?) ";
            $oComp = $this->db->prepare($query);
            $oComp->execute(array(
                $id,
                $supervisor,
                $firma_supervisor,
                $revisor,
                $firma_revisor,
                date('Y-m-d H:i:s')
            ));
            $id_signature = $this->db->lastInsertId();
            $this->db->Commit();

            $this->response["data"] = $id_signature;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $e) {
            echo "DataBase Error: The user could not be added.<br>" . $e->getMessage();
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    /*     * **************************************************************** */

    function getAllActivities() {
        try {
            $query = "SELECT * FROM dcmx_cat_actividades_realizar";
            $oRF = $this->db->prepare($query);
            $oRF->execute();
            $data = array();
            foreach ($oRF as $key => $cat) {
                $catt = array();
                $catt["id"] = $cat["id"];
                $catt["clave"] = $cat["clave"];
                $catt["descripcion"] = $cat["descripcion"];
                $catt["estatus"] = utf8_encode($cat["estatus"]);
                $data[] = $cat;
            }
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function getAllResults() {
        try {
            $query = "SELECT * FROM dcmx_cat_resultados_inspeccion";
            $oRF = $this->db->prepare($query);
            $oRF->execute();
            $data = array();
            foreach ($oRF as $key => $cat) {
                $catt = array();
                $catt["id"] = $cat["id"];
                $catt["clave"] = $cat["clave"];
                $catt["descripcion"] = $cat["descripcion"];
                $catt["estatus"] = utf8_encode($cat["estatus"]);
                $data[] = $cat;
            }
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function updtImgDesc($id, $descripcion) {
        $this->db->beginTransaction();
        try {
            $query = "UPDATE dcmx_inspeccion_archivos SET descripcion = ? WHERE id =? ";
            $oRec = $this->db->prepare($query);
            $oRec->execute(
                    array($descripcion, $id)
            );
            $this->db->Commit();

            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
            $this->response["info"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    /*     * ************************************************************* */

    function getAddressByCol($colony) {
        try {
            $query = "SELECT * FROM v_direcciones WHERE id_colonia= ?";
            $oAddress = $this->db->prepare($query);
            $oAddress->execute(array($colony));

            $data = array();

            foreach ($oAddress as $key => $addr) {
                $address = array();

                $address["id_pais"] = $addr["id_pais"];
                $address["nombre_pais"] = $addr["nombre_pais"];
                $address["id_estado"] = $addr["id_estado"];
                $address["nombre_estado"] = $addr["nombre_estado"];
                $address["id_ciudad"] = $addr["id_ciudad"];
                $address["nombre_ciudad"] = $addr["nombre_ciudad"];
                $address["id_colonia"] = $addr["id_colonia"];
                $address["nombre_colonia"] = $addr["nombre_colonia"];
                $address["id_cp"] = $addr["id_cp"];
                $address["valor"] = $addr["valor"];


                $data[] = $address;
            }
            $this->response["numElems"] = $oAddress->rowCount();
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function getUsrById($id_usuario) {
        try {
            $query = "SELECT * FROM usuarios_dirac WHERE id_usuario = ? AND status = 1";
            $oUsr = $this->db->prepare($query);
            $oUsr->execute(array($id_usuario));
            //Si el usuario existe asignamos valores al objeto
            $usuario = array();

            if ($oUsr->rowCount() == 1) {
                $usr = $oUsr->fetch(PDO::FETCH_OBJ);

                $usuario["id_usuario"] = $usr->id_usuario;
                $usuario["usuario"] = $usr->usuario;
                $usuario["nombre"] = $usr->nombre;
                $usuario["apellidos"] = $usr->apellidos;
                $usuario["correo"] = $usr->correo;
                $usuario["no_control"] = $usr->no_control;
                $usuario["telefono"] = $usr->telefono;
                $usuario["estatus"] = $usr->status;
                $usuario["imagen"] = $usr->imagen;
                $usuario["genero"] = $usr->genero;
                $usuario["id_area"] = $usr->id_area;
                $usuario["id_director"] = $usr->id_director_area;
                $usuario["nivel"] = $usr->nivel;

                $this->response["data"] = $usuario;
                $this->response["errorCode"] = SUCCESS_CODE;
                $this->response["msg"] = SUCCESS;
            } else {
                $this->response["errorCode"] = ERROR_CODE_L1;
                $this->response["msg"] = ERROR_L1;
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function saveClientMovil($data) {
        $this->db->beginTransaction();
        try {
            //Guardamos Cliente
            $query = "INSERT INTO dcmx_clientes (nombre, correo, telefono, fecha_registro, sexo) VALUES(?,?,?,?,?) ";
            $oClient = $this->db->prepare($query);
            $oClient->execute(array(
                $data["nombre_cliente"],
                $data["correo"],
                $data["telefono"],
                $data["fecha_registro"],
                $data["sexo"]
            ));
            $id = $this->db->lastInsertId();
            $this->db->Commit();
            $building = $this->saveBuildingMovil($data, $id);

            $this->response["idInspection"] = $building["data"];
            $this->response["data"] = $id;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function saveBuildingMovil($inspection, $id) {
        $revisores = array(9, 17, 108);
        shuffle($revisores);
        $usrR = $revisores[0];
        $this->db->beginTransaction();
        try {
            $query = "INSERT INTO dcmx_inspeccion_edificios (id_cliente, id_supervisor, fecha_inspeccion, resultado, estado_h, nombre_edificio, fecha, tipo, actividades, resultados, otros, comentarios, reviso, no_pisos, sotano, no_sotanos, solucion_estructural) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
            $oComp = $this->db->prepare($query);
            $oComp->execute(array(
                $id,
                $inspection["id_supervisor"],
                $inspection["fecha_inspeccion"],
                $inspection["resultado"],
                $inspection["estado_h"],
                $inspection["nombre_edificio"],
                $inspection["fecha"],
                $inspection["tipo"],
                $inspection["actividades"],
                $inspection["resultados"],
                (isset($inspection["otros"]) ? $inspection["otros"] : ""),
                $inspection["comentarios"],
                $usrR,
                $inspection["no_pisos"],
                $inspection["sotano"],
                $inspection["no_sotanos"],
                $inspection["solucion_estructural"]
            ));
            $id_inspection = $this->db->lastInsertId();
            $this->db->Commit();
            //Agregamos direccion de edificio
            $address = $this->addBuildingAddress($inspection, $id_inspection);

            //Enviar correo a revisor
            /* $usuario = $this->getUsrById($usrR);
              require_once '../utils/Utils.php';
              $utils = Utils::utlsSngltn();

              $subject = 'Asignacion de Revision de Inspeccion';

              $url = SYSTEM_PATH . 'utils/templates/asignacion_inspeccion.php?nombre=' . urlencode($usuario["data"]["nombre"]) .
              '&supervisor=' . urlencode($_SESSION["sgi_nombre"]) .
              '&edificio=' . urlencode($inspection["nombre_edificio"]);
              $msg = $utils->getPageHTML($url, '<html>', '</html>');
              $sendMail = $utils->sendMail($usuario["data"]["correo"], $usuario["data"]["nombre"], $subject, $msg);
             */
            $this->response["data"] = $id_inspection;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $e) {
            echo "DataBase Error: The user could not be added.<br>" . $e->getMessage();
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function addVisitor($visitor) {
        $this->db->beginTransaction();
        try {
            $query = "INSERT INTO dcmx_check_in_out_v (correo, fecha_entrada, nombre, apellidos, id_usuario, motivo, gafete, identificacion, fecha_salida, foto, empresa, fecha_registro, estatus, genero, temperatura) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
            $oComp = $this->db->prepare($query);
            $oComp->execute(array(
                $visitor["correo"],
                date('Y-m-d H:i:s'),
                $visitor["nombre"],
                $visitor["apellidos"],
                $visitor["id_usuario"],
                $visitor["motivo"],
                $visitor["gafete"],
                $visitor["identificacion"],
                "",
                "",
                $visitor["empresa"],
                date("Y-m-d"),
                1,
                $visitor["genero"],
                $visitor["temperatura"]
            ));
            $id = $this->db->lastInsertId();
            $this->db->Commit();

            $this->response["data"] = $id;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            $this->db->rollback();
            $this->response["info"] = $exc->getMessage();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function getInfoUsrsView($q = "1=1") {
        try {
            $query = "SELECT * FROM v_usuarios_dirac WHERE $q AND id_empresa = 1 AND status = 1;";
            $oUsr = $this->db->prepare($query);
            $oUsr->execute();
            $data = array();
//            echo $query;

            foreach ($oUsr as $key => $infoUsr) {
                $allInfo = array();
                $allInfo["id_usuario"] = $infoUsr["id_usuario"];
                $allInfo["usuario"] = $infoUsr["usuario"];
                $allInfo["no_control"] = $infoUsr["no_control"];
                $allInfo["nombre"] = $infoUsr["nombre"];
                $allInfo["apellidos"] = $infoUsr["apellidos"];
                $allInfo["genero"] = $infoUsr["genero"];
                $allInfo["correo"] = $infoUsr["correo"];
                $allInfo["imagen"] = $infoUsr["imagen"];
                $allInfo["telefono"] = $infoUsr["telefono"];
                $allInfo["id_direccion"] = $infoUsr["id_direccion"];
                $allInfo["status"] = $infoUsr["status"];
                $allInfo["id_director_area"] = $infoUsr["id_director_area"];
                $allInfo["nivel"] = $infoUsr["nivel"];
                $allInfo["eliminado"] = $infoUsr["eliminado"];
                $allInfo["fecha_ingreso"] = $infoUsr["fecha_ingreso"];
                $allInfo["id_empresa"] = $infoUsr["id_empresa"];
                $allInfo["jefe_inmediato"] = $infoUsr["jefe_inmediato"];

                /*                 * **************************************************** */
                $allInfo["fecha_nacimiento"] = $infoUsr["fecha_nacimiento"];
                $allInfo["rfc"] = $infoUsr["rfc"];
                $allInfo["curp"] = $infoUsr["curp"];
                $allInfo["fecha_registro"] = $infoUsr["fecha_registro"];
                /*                 * **************************************************** */

                $allInfo["direccion"] = $infoUsr["direccion"];
                $allInfo["id_area"] = $infoUsr["id_area"];
                $allInfo["area"] = $infoUsr["area"];
                $allInfo["id_colonia"] = $infoUsr["id_colonia"];
                $allInfo["calle"] = $infoUsr["calle"];
                $allInfo["numero"] = $infoUsr["numero"];
                $allInfo["nombre_contacto"] = $infoUsr["nombre_contacto"];
                $allInfo["tel_contacto"] = $infoUsr["tel_contacto"];
                $allInfo["tel_empresa"] = $infoUsr["tel_empresa"];
                $allInfo["tel_celular"] = $infoUsr["tel_celular"];
                $allInfo["tipo_sangre"] = $infoUsr["tipo_sangre"];
                $allInfo["alergias"] = $infoUsr["alergias"];
                $allInfo["foto"] = $infoUsr["foto"];
                $allInfo["lat"] = $infoUsr["lat"];
                $allInfo["lon"] = $infoUsr["lon"];
                $allInfo["id_escolaridad"] = $infoUsr["id_escolaridad"];
                $allInfo["escolaridad"] = $infoUsr["escolaridad"];
                $allInfo["titulado"] = $infoUsr["titulado"];
                $allInfo["id_pais"] = $infoUsr["id_pais"];
                $allInfo["pais"] = $infoUsr["pais"];
                $allInfo["id_estado"] = $infoUsr["id_estado"];
                $allInfo["estado"] = $infoUsr["estado"];
                $allInfo["id_ciudad"] = $infoUsr["id_ciudad"];
                $allInfo["ciudad"] = $infoUsr["ciudad"];
                $allInfo["nombre_colonia"] = $infoUsr["nombre_colonia"];
                $allInfo["id_cp"] = $infoUsr["id_cp"];
                $allInfo["valor"] = $infoUsr["valor"];
                $allInfo["id_proyecto"] = $infoUsr["id_proyecto"];
                $allInfo["proyecto"] = $infoUsr["proyecto"];
                $allInfo["id_perfil"] = $infoUsr["id_perfil"];
                $allInfo["puesto"] = $infoUsr["puesto"];
                $allInfo["evaluacion"] = $infoUsr["evaluacion"];
                $allInfo["id_perfil_sgi"] = $infoUsr["id_perfil_sgi"];
                $allInfo["perfil_sgi"] = $infoUsr["perfil_sgi"];
                $allInfo["id_piso"] = $infoUsr["id_piso"];

                $data[] = $allInfo;
            }
            $this->response["numElems"] = $oUsr->rowCount();
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getMessage();
        }
        return $this->response;
    }

    function getVisitors($q = "1=1") {
        try {
            $query = "SELECT * FROM dcmx_check_in_out_v WHERE " . $q;
            $oUsr = $this->db->prepare($query);
            $oUsr->execute();
            $data = array();

            $path = $_SERVER["DOCUMENT_ROOT"] . "/diracmx/photos_v/";

            foreach ($oUsr as $key => $vis) {
                $visitors = array();

                $visitors["id"] = $vis["id"];
                $visitors["fecha_entrada"] = $vis["fecha_entrada"];
                $visitors["nombre"] = $vis["nombre"];
                $visitors["apellidos"] = $vis["apellidos"];
                $visitors["id_usuario"] = $vis["id_usuario"];
                $visitors["motivo"] = $vis["motivo"];
                $visitors["gafete"] = $vis["gafete"];
                $visitors["identificacion"] = $vis["identificacion"];
                $visitors["fecha_salida"] = $vis["fecha_salida"];
                $visitors["foto"] = $vis["foto"];
                $visitors["foto1"] = $path . $vis["foto"];
                $visitors["fecha_registro"] = $vis["fecha_registro"];
                $visitors["estatus"] = $vis["estatus"];
                $visitors["genero"] = $vis["genero"];
                $visitors["empresa"] = $vis["empresa"];
                $visitors["temperatura"] = $vis["temperatura"];

                $anfitrion = $this->getInfoUsrsView("id_usuario = " . $vis["id_usuario"]);
                if (empty($anfitrion["data"])) {
                    $visitors["anfitrion"] = "";
                } else {
                    $visitors["anfitrion"] = $anfitrion["data"][0]["nombre"] . " " . $anfitrion["data"][0]["apellidos"];
                }

                $data[] = $visitors;
            }
            $this->response["numElems"] = $oUsr->rowCount();
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function uploadPhoto($id, $foto) {
        $this->db->beginTransaction();
        try {
            $query = "UPDATE dcmx_check_in_out_v SET foto = ? WHERE id =? ";
            $oRec = $this->db->prepare($query);
            $oRec->execute(
                    array($foto, $id)
            );
            $this->db->Commit();

            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
            $this->response["info"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function CheckOut($id) {
        $this->db->beginTransaction();
        try {
            $query = "UPDATE dcmx_check_in_out_v SET estatus = 2, fecha_salida = ? WHERE id =? ";
            $oRec = $this->db->prepare($query);
            $oRec->execute(array(date("Y-m-d H:i:s"), $id));
            $this->db->Commit();

            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
            $this->response["info"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function getOutputs($q = "1=1") {
        try {
            $query = "SELECT * FROM daf_salidas_equipo WHERE " . $q;
//            echo $query;
            $oOutputs = $this->db->prepare($query);
            $oOutputs->execute();
            $data = array();

            foreach ($oOutputs as $key => $outputs) {
                $output = array();
                $output["id"] = $outputs["id"];
                $output["id_solicitante"] = $outputs["id_solicitante"];
                $output["id_usuario"] = $outputs["id_usuario"];
                $output["descripcion"] = $outputs["descripcion"];
                $output["fecha_salida"] = $outputs["fecha_salida"];
                $output["fecha"] = $outputs["fecha"];
                $output["destino"] = $outputs["destino"];
                $output["comentarios"] = $outputs["comentarios"];
                $output["archivo"] = $outputs["archivo"];
                $output["otro_usuario"] = $outputs["otro_usuario"];
                $output["id_destinatario"] = $outputs["destinatario"];
                $output["estatus_envio"] = $outputs["estatus_envio"];
                $output["estatus"] = $outputs["estatus"];
                $output["hora_salida"] = $outputs["hora_salida"];
                $output["hora_regreso"] = $outputs["hora_regreso"];
                $output["hora_recepcion"] = $outputs["hora_recepcion"];
                $output["vigencia"] = $outputs["vigencia"];
                $output["persona_salida"] = $outputs["persona_salida"];
                $output["ccp"] = $outputs["ccp"];

                if (is_null($outputs["persona_salida"])) {
                    $output["persona_salida_nombre"] = "";
                } else {
                    $persona_nombre = $this->getUsrT("no_control = " . $outputs["persona_salida"]);
                    $output["persona_salida_nombre"] = $persona_nombre["data"][0]["nombre"];
                }

                if (intval($output["id_solicitante"]) === 0) {
                    $output["solicitante"] = $output["otro_usuario"];
                } else {
                    $solicitante = $this->getUsrByIdGral($output["id_solicitante"]);
                    $output["solicitante"] = $solicitante["data"]->nombre . " " . $solicitante["data"]->apellidos;
                }

                $usuario = $this->getUsrByIdGral($output["id_usuario"]);
                $output["nombre"] = $usuario["data"]->nombre . " " . $usuario["data"]->apellidos;

                $destinatario = $this->getUsrByIdGral($output["id_destinatario"]);
                $output["destinatario"] = $destinatario["data"]->nombre . " " . $destinatario["data"]->apellidos;

                $data[] = $output;
            }

            $this->response["numElems"] = $oOutputs->rowCount();
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getMessage();
        }
        return $this->response;
    }

    function getUsrByIdGral($id_usuario) {
        try {
            $query = "SELECT * FROM usuarios_dirac WHERE id_usuario = ?";
            $oUsr = $this->db->prepare($query);
            $oUsr->execute(array($id_usuario));
            //Si el usuario existe asignamos valores al objeto

            if ($oUsr->rowCount() == 1) {
                $usr = $oUsr->fetch(PDO::FETCH_OBJ);

                $this->response["data"] = $usr;
                $this->response["errorCode"] = SUCCESS_CODE;
                $this->response["msg"] = SUCCESS;
            } else {
                $this->response["errorCode"] = ERROR_CODE;
                $this->response["msg"] = ERROR;
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function getUsrT($q = "1=1") {
        try {
            $query = "SELECT * FROM usuarios_dirac WHERE " . $q;
            $oUsr = $this->db->prepare($query);
            $oUsr->execute();
            $data = array();
//            echo $query;

            foreach ($oUsr as $key => $infoUsr) {
                $allInfo = array();
                $allInfo["id_usuario"] = $infoUsr["id_usuario"];
                $allInfo["usuario"] = $infoUsr["usuario"];
                $allInfo["no_control"] = $infoUsr["no_control"];
                $allInfo["nombre"] = $infoUsr["nombre"];
                $allInfo["apellidos"] = $infoUsr["apellidos"];
                $allInfo["genero"] = $infoUsr["genero"];
                $allInfo["correo"] = $infoUsr["correo"];
                $allInfo["imagen"] = $infoUsr["imagen"];
                $allInfo["telefono"] = $infoUsr["telefono"];
                $allInfo["id_area"] = $infoUsr["id_area"];
                $allInfo["status"] = $infoUsr["status"];
                $allInfo["id_director_area"] = $infoUsr["id_director_area"];
                $allInfo["nivel"] = $infoUsr["nivel"];
                $allInfo["eliminado"] = $infoUsr["eliminado"];
                $allInfo["fecha_ingreso"] = $infoUsr["fecha_ingreso"];
                $allInfo["fecha_nacimiento"] = $infoUsr["fecha_nacimiento"];
                $allInfo["curp"] = $infoUsr["curp"];
                $allInfo["rfc"] = $infoUsr["rfc"];
                $allInfo["fecha_registro"] = $infoUsr["fecha_registro"];
                $allInfo["id_empresa"] = $infoUsr["id_empresa"];
                $allInfo["jefe_inmediato"] = $infoUsr["jefe_inmediato"];
                $allInfo["rfid"] = $infoUsr["rfid"];

                $data[] = $allInfo;
            }

            $this->response["numElems"] = $oUsr->rowCount();
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getMessage();
        }
        return $this->response;
    }

    function checkOutS($id, $no_control) {
        $usr = $this->getUsrT("no_control = " . $no_control);
        if (intval($usr["numElems"]) === 1) {
            $this->db->beginTransaction();
            try {
                $query = "UPDATE daf_salidas_equipo SET hora_salida = ?, persona_salida = ? WHERE id = ? ";
                $oRec = $this->db->prepare($query);
                $oRec->execute(
                        array(
                            date('Y-m-d H_i:s'),
                            $no_control,
                            $id
                        )
                );
                $this->db->Commit();

                $this->response["errorCode"] = SUCCESS_CODE;
                $this->response["msg"] = SUCCESS;
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->db->rollback();
                $this->response["errorCode"] = ERROR_CODE;
                $this->response["msg"] = ERROR;
                $this->response["info"] = $exc->getMessage();
            }
        } else {
            $this->response["errorCode"] = 1;
            $this->response["msg"] = "El numero de control introducido no coincide, intente nuevamente.";
        }
        return $this->response;
    }

    function checkOutS2($id, $no_control) {
        $usr = $this->getUsrT("no_control = " . $no_control);
        if (intval($usr["numElems"]) === 1) {
            $this->db->beginTransaction();
            try {
                $query = "UPDATE daf_salidas_equipo SET hora_regreso = ?, persona_regreso = ? WHERE id = ? ";
                $oRec = $this->db->prepare($query);
                $oRec->execute(
                        array(
                            date('Y-m-d H_i:s'),
                            $no_control,
                            $id
                        )
                );
                $this->db->Commit();

                $this->response["errorCode"] = SUCCESS_CODE;
                $this->response["msg"] = SUCCESS;
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->db->rollback();
                $this->response["errorCode"] = ERROR_CODE;
                $this->response["msg"] = ERROR;
                $this->response["info"] = $exc->getMessage();
            }
        } else {
            $this->response["errorCode"] = 1;
            $this->response["msg"] = "El numero de control introducido no coincide, intente nuevamente.";
        }
        return $this->response;
    }

    /*     * *******************************************************************************
     * FIGG - DIRAC
     * Fecha: 13.Agosto.2019
     * Descripción: Se agregan funciones para modulo de salida de personal   
     * ******************************************************************************* */

    function getOutputsPersonnel($q = "1=1") {
        try {
            $query = "SELECT * FROM daf_salidas_personal WHERE " . $q;
            $oOutputs = $this->db->prepare($query);
            $oOutputs->execute();
            $data = array();

            foreach ($oOutputs as $key => $outputs) {
                $output = array();
                $output["id"] = $outputs["id"];
                $output["id_solicitante"] = $outputs["id_solicitante"];
                $output["id_usuario"] = $outputs["id_usuario"];
                $output["fecha_salida"] = $outputs["fecha_salida"];
                $output["fecha"] = $outputs["fecha"];
                $output["comentarios"] = $outputs["comentarios"];
                $output["estatus"] = $outputs["estatus"];
                $output["hora_salida"] = $outputs["hora_salida"];
                $output["hora_regreso"] = $outputs["hora_regreso"];
                $output["destino"] = $outputs["destino"];

                $solicitante = $this->getUsrByIdGral($output["id_solicitante"]);
                $output["solicitante"] = $solicitante["data"]->nombre . " " . $solicitante["data"]->apellidos;

                $usuario = $this->getUsrByIdGral($output["id_usuario"]);
                $output["nombre"] = $usuario["data"]->nombre . " " . $usuario["data"]->apellidos;

                $data[] = $output;
            }

            $this->response["numElems"] = $oOutputs->rowCount();
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getMessage();
        }
        return $this->response;
    }

    function checkPersonnel($id, $tipo) {
        $this->db->beginTransaction();
        try {
            $query = NULL;
            if (intval($tipo) === 1) {
                $query = "UPDATE daf_salidas_personal SET hora_salida = ? WHERE id =? ";
            } else {
                $query = "UPDATE daf_salidas_personal SET hora_regreso = ? WHERE id =? ";
            }
            $oRec = $this->db->prepare($query);
            $oRec->execute(array(date("Y-m-d H:i:s"), $id));
            $this->db->Commit();

            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
            $this->response["info"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    /*     * *******************************************************************************
     * FIGG - DIRAC
     * Fecha: 04.Noviembre.2019
     * Descripción: Se agregan funciones para modulo de registro de proveedores.
     * ******************************************************************************* */

    function checkSuppliers($data) {
        @session_start();
        $this->db->beginTransaction();
        try {
            $query = "INSERT INTO daf_visita_proveedores (id_usuario, fecha, fecha_ingreso, nombre_proveedor, empresa, comentarios, estatus) VALUES(?,?,?,?,?,?,?) ";
            $oResp = $this->db->prepare($query);
            $oResp->execute(array(
                $_SESSION["sgi_id_usr"],
                date("Y-m-d H:i:s"),
                $data["fecha_visita"],
                $data["proveedor"],
                $data["empresa"],
                $data["comentarios"],
                1
            ));
            $id = $this->db->lastInsertId();
            $this->db->Commit();
            $this->response["data"] = $id;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getMessage();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getMessage();
        }
        return $this->response;
    }

    function getSuppliers($q = "1=1") {
        try {
            $query = "SELECT * FROM daf_visita_proveedores WHERE " . $q;
            $oOutputs = $this->db->prepare($query);
            $oOutputs->execute();
            $data = array();

            foreach ($oOutputs as $key => $outputs) {
                $output = array();
                $output["id"] = $outputs["id"];
                $output["id_usuario"] = $outputs["id_usuario"];
                $output["fecha"] = $outputs["fecha"];
                $output["fecha_ingreso"] = $outputs["fecha_ingreso"];
                $output["nombre_proveedor"] = $outputs["nombre_proveedor"];
                $output["empresa"] = $outputs["empresa"];
                $output["comentarios"] = $outputs["comentarios"];
                $output["hora_entrada"] = $outputs["hora_entrada"];
                $output["hora_salida"] = $outputs["hora_salida"];
                $output["estatus"] = $outputs["estatus"];
                $output["personal_apoyo"] = $outputs["personal_apoyo"];
                $output["notificacion"] = $outputs["notificacion"];


                $usuario = $this->getUsrByIdGral($output["id_usuario"]);
                $output["nombre_usuario"] = $usuario["data"]->nombre . " " . $usuario["data"]->apellidos;

                $data[] = $output;
            }

            $this->response["numElems"] = $oOutputs->rowCount();
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getMessage();
        }
        return $this->response;
    }

    function checkingSuppliers($id, $tipo) {
        $this->db->beginTransaction();
        try {
            $query = NULL;
            if (intval($tipo) === 1) {
                $query = "UPDATE daf_visita_proveedores SET hora_entrada = ? WHERE id =? ";
            } else {
                $query = "UPDATE daf_visita_proveedores SET hora_salida = ? WHERE id =? ";
            }
            $oRec = $this->db->prepare($query);
            $oRec->execute(array(date("Y-m-d H:i:s"), $id));
            $this->db->Commit();

            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
            $this->response["info"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    /*     * *******************************************************************************
     * FIGG - DIRAC
     * Fecha: 04.Noviembre.2019
     * Descripción: Se agregan funciones para modulo de registro de proveedores.
     * ******************************************************************************* */

    function getInterviews($q = "1=1") {
        try {
            $query = "SELECT * FROM gfh_registro_entrevista_candidatos WHERE " . $q;
            $oOutputs = $this->db->prepare($query);
            $oOutputs->execute();
            $data = array();

            foreach ($oOutputs as $key => $outputs) {
                $output = array();
                $output["id"] = $outputs["id"];
                $output["id_usuario"] = $outputs["id_usuario"];
                $output["id_candidato"] = $outputs["id_candidato"];
                $output["fecha"] = $outputs["fecha"];
                $output["comentarios"] = $outputs["comentarios"];
                $output["fecha_registro"] = $outputs["fecha_registro"];
                $output["hora_ingreso"] = $outputs["hora_ingreso"];
                $output["hora_salida"] = $outputs["hora_salida"];

                $registro = $this->getUsrByIdGral($output["id_usuario"]);
                $output["registro"] = $registro["data"]->nombre . " " . $registro["data"]->apellidos;

                $candidato = $this->getInfoAPP("id =" . $output["id_candidato"]);
                $output["candidato"] = $candidato["data"][0]["nombre"] . " " . $candidato["data"][0]["apellidos"];

                $data[] = $output;
            }

            $this->response["numElems"] = $oOutputs->rowCount();
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getMessage();
        }
        return $this->response;
    }

    function checkingapps($id, $tipo) {
        $this->db->beginTransaction();
        try {
            $query = NULL;
            if (intval($tipo) === 1) {
                $query = "UPDATE gfh_registro_entrevista_candidatos SET hora_ingreso = ? WHERE id =? ";
            } else {
                $query = "UPDATE gfh_registro_entrevista_candidatos SET hora_salida = ? WHERE id =? ";
            }
            $oRec = $this->db->prepare($query);
            $oRec->execute(array(date("Y-m-d H:i:s"), $id));
            $this->db->Commit();

            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
            $this->response["info"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function getInfoAPP($q = "1=1") {
        try {
            $query = "SELECT * FROM gfh_info_candidato WHERE " . $q;
            $oInfoApp = $this->db->prepare($query);
            $oInfoApp->execute();
            $data = array();
            foreach ($oInfoApp as $key => $info) {
                $infoApp = array();
                $infoApp["id"] = $info["id"];
                $infoApp["nombre"] = $info["nombre"];
                $infoApp["apellidos"] = $info["apellidos"];
                $infoApp["genero"] = $info["genero"];
                $infoApp["correo"] = $info["correo"];
                $infoApp["telefono"] = $info["telefono"];
                $infoApp["curp"] = $info["curp"];
                $infoApp["rfc"] = $info["rfc"];
                $infoApp["id_colonia"] = $info["id_colonia"];
                $infoApp["calle"] = $info["calle"];
                $infoApp["numero"] = $info["numero"];
                $infoApp["clave"] = $info["clave"];
                $infoApp["id_solicitud"] = $info["id_solicitud"];
                $infoApp["fecha"] = $info["fecha"];
                $infoApp["estatus"] = $info["estatus"];
                $infoApp["social_media"] = $info["social_media"];


//                $infoApp["llave"] = $this->getKeys("clave = '" . $info["clave"] . "' AND id_solicitud = " . $info["id_solicitud"]);
//                $infoApp["solicitud"] = $this->getKeys("clave = '" . $info["clave"] . "' AND id_solicitud = " . $info["id_solicitud"]);

                $data[] = $infoApp;
            }
//            var_dump($data);
            $this->response["numElems"] = $oInfoApp->rowCount();
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function getProjects($q = "1=1") {
        try {
            $query = "SELECT * FROM proyectos_sgi WHERE " . $q;

            $oUsr = $this->db->prepare($query);
            $oUsr->execute();
            $data = array();
            foreach ($oUsr as $key => $infoUsr) {
                $allInfo = array();
                $allInfo["id"] = $infoUsr["id"];
                $allInfo["clave"] = $infoUsr["clave"];
                $allInfo["nombre"] = $infoUsr["nombre"];
                $allInfo["descripcion"] = $infoUsr["descripcion"];
                $allInfo["estatus"] = $infoUsr["estatus"];
                $allInfo["responsable"] = $infoUsr["responsable"];
                $allInfo["orden"] = $infoUsr["orden"];

                $data[] = $allInfo;
            }

            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            echo $exc->getMessage();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getMessage();
        }
        return $this->response;
    }

    public function jsonSerialize() {
        $var = get_object_vars($this);
        foreach ($var as &$value) {
            if (is_object($value) && method_exists($value, 'getJsonData')) {
                $value = $value->getJsonData();
            }
        }
        return $var;
    }

    function saveTemp($data) {
        $this->db->beginTransaction();
        try {
            $query = "INSERT INTO dcmx_registro_temperatura (id_usuario, temperatura, fecha, modulo) VALUES(?,?,?,?) ";
            $oClient = $this->db->prepare($query);
            $oClient->execute(array(
                $data["id_usuario"],
                $data["temperatura"],
                date("Y-m-d H:i:s"),
                $data["modulo"]
            ));
//            $query2 = "TRUNCATE TABLE dcmx_temperatura_temp";
//            $oClient2 = $this->db->prepare($query2);
//            $oClient2->execute();

            //}$id = $this->db->lastInsertId();
            $this->db->Commit();

            //$this->response["data"] = $id;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function getRecordsTemp($q = "1=1") {
        try {
            $query = "SELECT * FROM dcmx_registro_temperatura WHERE " . $q;

            $oUsr = $this->db->prepare($query);
            $oUsr->execute();
            $data = array();
            foreach ($oUsr as $key => $infoUsr) {
                $allInfo = array();
                $allInfo["id"] = $infoUsr["id"];
                $allInfo["id_usuario"] = $infoUsr["id_usuario"];
                $allInfo["temperatura"] = $infoUsr["temperatura"];
                $allInfo["fecha"] = $infoUsr["fecha"];
                $allInfo["modulo"] = $infoUsr["modulo"];

                $usuario = $this->getInfoUsrsView("id_usuario = " . $infoUsr["id_usuario"]);
                if ($usuario["numElems"] > 0) {
                    $allInfo["nombre"] = $usuario["data"][0]["nombre"] . " " . $usuario["data"][0]["apellidos"];
                } else {
                    $allInfo["nombre"] = "";
                }

                $data[] = $allInfo;
            }

            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            echo $exc->getMessage();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getMessage();
        }
        return $this->response;
    }

    function getRecordsTempTemp($q = "1=1") {
        try {
            $query = "SELECT * FROM dcmx_temperatura_temp WHERE " . $q;

            $oUsr = $this->db->prepare($query);
            $oUsr->execute();
            $data = array();
            foreach ($oUsr as $key => $infoUsr) {
                $allInfo = array();
                $allInfo["id"] = $infoUsr["id"];
                $allInfo["id_usuario"] = $infoUsr["id_usuario"];
                $allInfo["temperatura"] = $infoUsr["temperatura"];
                $allInfo["fecha"] = $infoUsr["fecha"];

//                $usuario = $this->getInfoUsrsView("id_usuario = " . $infoUsr["id_usuario"]);
//                $allInfo["nombre"] = $usuario["data"][0]["nombre"] . " " . $usuario["data"][0]["apellidos"];

                $data[] = $allInfo;
            }

            $this->response["numElems"] = $oUsr->rowCount();
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            echo $exc->getMessage();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getMessage();
        }
        return $this->response;
    }

    function replaceTempTemp($data) {
        $this->db->beginTransaction();
        try {
            $query = "REPLACE INTO dcmx_temperatura_temp (id, temperatura, fecha)VALUES(?,?,?)";
            $oClient = $this->db->prepare($query);
            $oClient->execute(array(
                1,
                $data["temperatura"],
                date("Y-m-d H:i:s")
            ));
            $id = $this->db->lastInsertId();
            $this->db->Commit();

            $this->response["data"] = $id;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function replaceRFID($data) {
        $this->db->beginTransaction();
        try {
            $query = "REPLACE INTO dcmx_rfid_temp (id, rfid, fecha)VALUES(?,?,?)";
            $oClient = $this->db->prepare($query);
            $oClient->execute(array(
                1,
                $data["rfid"],
                date("Y-m-d H:i:s")
            ));
            $id = $this->db->lastInsertId();
            $this->db->Commit();

            $this->response["data"] = $id;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function getInfoUsrsView2($q = "1=1") {
        try {
            $query = "SELECT * FROM v_usuarios_dirac WHERE " . $q;
            $oUsr = $this->db->prepare($query);
            $oUsr->execute();
            $data = array();
//            echo $query;

            foreach ($oUsr as $key => $infoUsr) {
                $allInfo = array();
                $allInfo["id_usuario"] = $infoUsr["id_usuario"];
                $allInfo["usuario"] = $infoUsr["usuario"];
                $allInfo["no_control"] = $infoUsr["no_control"];
                $allInfo["nombre"] = $infoUsr["nombre"];
                $allInfo["apellidos"] = $infoUsr["apellidos"];
                $allInfo["genero"] = $infoUsr["genero"];
                $allInfo["correo"] = $infoUsr["correo"];
                $allInfo["imagen"] = $infoUsr["imagen"];
                $allInfo["telefono"] = $infoUsr["telefono"];
                $allInfo["id_direccion"] = $infoUsr["id_direccion"];
                $allInfo["status"] = $infoUsr["status"];
                $allInfo["id_director_area"] = $infoUsr["id_director_area"];
                $allInfo["nivel"] = $infoUsr["nivel"];
                $allInfo["eliminado"] = $infoUsr["eliminado"];
                $allInfo["fecha_ingreso"] = $infoUsr["fecha_ingreso"];
                $allInfo["id_empresa"] = $infoUsr["id_empresa"];
                $allInfo["jefe_inmediato"] = $infoUsr["jefe_inmediato"];

                /*                 * **************************************************** */
                $allInfo["fecha_nacimiento"] = $infoUsr["fecha_nacimiento"];
                $allInfo["rfc"] = $infoUsr["rfc"];
                $allInfo["curp"] = $infoUsr["curp"];
                $allInfo["fecha_registro"] = $infoUsr["fecha_registro"];
                /*                 * **************************************************** */

                $allInfo["direccion"] = $infoUsr["direccion"];
                $allInfo["id_area"] = $infoUsr["id_area"];
                $allInfo["area"] = $infoUsr["area"];
                $allInfo["id_colonia"] = $infoUsr["id_colonia"];
                $allInfo["calle"] = $infoUsr["calle"];
                $allInfo["numero"] = $infoUsr["numero"];
                $allInfo["nombre_contacto"] = $infoUsr["nombre_contacto"];
                $allInfo["tel_contacto"] = $infoUsr["tel_contacto"];
                $allInfo["tel_empresa"] = $infoUsr["tel_empresa"];
                $allInfo["tel_celular"] = $infoUsr["tel_celular"];
                $allInfo["tipo_sangre"] = $infoUsr["tipo_sangre"];
                $allInfo["alergias"] = $infoUsr["alergias"];
                $allInfo["foto"] = $infoUsr["foto"];
                $allInfo["lat"] = $infoUsr["lat"];
                $allInfo["lon"] = $infoUsr["lon"];
                $allInfo["id_escolaridad"] = $infoUsr["id_escolaridad"];
                $allInfo["escolaridad"] = $infoUsr["escolaridad"];
                $allInfo["titulado"] = $infoUsr["titulado"];
                $allInfo["id_pais"] = $infoUsr["id_pais"];
                $allInfo["pais"] = $infoUsr["pais"];
                $allInfo["id_estado"] = $infoUsr["id_estado"];
                $allInfo["estado"] = $infoUsr["estado"];
                $allInfo["id_ciudad"] = $infoUsr["id_ciudad"];
                $allInfo["ciudad"] = $infoUsr["ciudad"];
                $allInfo["nombre_colonia"] = $infoUsr["nombre_colonia"];
                $allInfo["id_cp"] = $infoUsr["id_cp"];
                $allInfo["valor"] = $infoUsr["valor"];
                $allInfo["id_proyecto"] = $infoUsr["id_proyecto"];
                $allInfo["proyecto"] = $infoUsr["proyecto"];
                $allInfo["id_perfil"] = $infoUsr["id_perfil"];
                $allInfo["puesto"] = $infoUsr["puesto"];
                $allInfo["evaluacion"] = $infoUsr["evaluacion"];
                $allInfo["id_perfil_sgi"] = $infoUsr["id_perfil_sgi"];
                $allInfo["perfil_sgi"] = $infoUsr["perfil_sgi"];
                $allInfo["id_piso"] = $infoUsr["id_piso"];

                $data[] = $allInfo;
            }
            $this->response["numElems"] = $oUsr->rowCount();
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getMessage();
        }
        return $this->response;
    }

}
