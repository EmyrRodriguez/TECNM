<?php

/**
 * Description of Utils
 *
 * @author FroébelIván
 * @copyright (c) 2017, DIRAC
 */
class Utils {

    public $response;
    private static $instance;

    function __construct() {
        $this->response = array();
    }

    // Método singleton
    public static function utlsSngltn() {
        if (!isset(self::$instance)) {
            $Utils = __CLASS__;
            self::$instance = new $Utils;
        }
        return self::$instance;
    }

    // Evita que el objeto se pueda clonar
    public function __clone() {
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);
    }

    public function sumarDiasFecha($fecha, $dia) {
        $intervalo = 'P' . $dia . 'D';
        $nuevaFecha = new DateTime($fecha);
        $nuevaFecha->add(new DateInterval($intervalo));

        return $nuevaFecha->format('Y-m-d H:i:s');
    }

    //Método con str_shuffle() 
    public function generateRandomString($length = 6) {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        return substr(str_shuffle($characters), 0, $length);
    }

    public function sendMail($addr, $name, $subject, $msg) {
        require_once '../config/configMail.php';
        require_once '../config/properties.php';
        require_once 'class.phpmailer.php';
        require_once 'class.smtp.php';

//Crear una instancia de PHPMailer
        $mail = new PHPMailer();
//Definir que vamos a usar SMTP
        $mail->IsSMTP();
//Esto es para activar el modo depuración. En entorno de pruebas lo mejor es 2, en producción siempre 0
// 0 = off (producción)
// 1 = client messages
// 2 = client and server messages
        $mail->SMTPDebug = SMTP_DEBUG;
//Ahora definimos gmail como servidor que aloja nuestro SMTP
//$mail->Host       = 'smtp.gmail.com';
        $mail->Host = SMTP_HOST;
//El puerto será el 587 ya que usamos encriptación TLS
//$mail->Port       = 587;
        $mail->Port = SMTP_PORT;
//Definmos la seguridad como TLS
//$mail->SMTPSecure = 'tls';
//Tenemos que usar gmail autenticados, así que esto a TRUE
        $mail->SMTPAuth = true;
//Definimos la cuenta que vamos a usar. Dirección completa de la misma
        $mail->Username = SMTP_USER;
//Introducimos nuestra contraseña de gmail
        $mail->Password = SMTP_PASS;
//Definimos el remitente (dirección y, opcionalmente, nombre)
        $mail->SetFrom(SMTP_FROM, SMTP_FROM_ALIAS);
//Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)
//$mail->AddReplyTo('replyto@correoquesea.com','El de la réplica');
//Y, ahora sí, definimos el destinatario (dirección y, opcionalmente, nombre)
        $mail->AddAddress($addr, $name);
//        $mail->addBCC('sistemas@dirac.mx');
//        $mail->AddCC(SMTP_FROM, $name);//<-----------------------------------------------------------CON COPIA A ARJION
//Definimos el tema del email
        $mail->Subject = $subject;
//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
        $mail->MsgHTML($msg);
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
        $mail->AltBody = 'This is a plain-text message body';
//Enviamos el correo
        if (!$mail->Send()) {
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        } else {
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        }
        return $this->response;
    }
    
    public function sendMailQubi($addr, $name, $subject, $msg) {
        require_once '../config/configMail.php';
        require_once '../config/properties.php';
        require_once 'class.phpmailer.php';
        require_once 'class.smtp.php';

//Crear una instancia de PHPMailer
        $mail = new PHPMailer();
//Definir que vamos a usar SMTP
        $mail->IsSMTP();
//Esto es para activar el modo depuración. En entorno de pruebas lo mejor es 2, en producción siempre 0
// 0 = off (producción)
// 1 = client messages
// 2 = client and server messages
        $mail->SMTPDebug = SMTP_DEBUG;
//Ahora definimos gmail como servidor que aloja nuestro SMTP
//$mail->Host       = 'smtp.gmail.com';
        $mail->Host = SMTP_HOST;
//El puerto será el 587 ya que usamos encriptación TLS
//$mail->Port       = 587;
        $mail->Port = SMTP_PORT;
//Definmos la seguridad como TLS
//$mail->SMTPSecure = 'tls';
//Tenemos que usar gmail autenticados, así que esto a TRUE
        $mail->SMTPAuth = true;
//Definimos la cuenta que vamos a usar. Dirección completa de la misma
        $mail->Username = SMTP_USER;
//Introducimos nuestra contraseña de gmail
        $mail->Password = SMTP_PASS;
//Definimos el remitente (dirección y, opcionalmente, nombre)
        $mail->SetFrom(SMTP_FROM_QUBI, SMTP_FROM_ALIAS);
//Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)
//$mail->AddReplyTo('replyto@correoquesea.com','El de la réplica');
//Y, ahora sí, definimos el destinatario (dirección y, opcionalmente, nombre)
        $mail->AddAddress($addr, $name);
        $mail->addBCC('sistemas@dirac.mx');
//        $mail->AddCC(SMTP_FROM, $name);//<-----------------------------------------------------------CON COPIA A ARJION
//Definimos el tema del email
        $mail->Subject = $subject;
//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
        $mail->MsgHTML($msg);
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
        $mail->AltBody = 'This is a plain-text message body';
//Enviamos el correo
        if (!$mail->Send()) {
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        } else {
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        }
        return $this->response;
    }

    function getPageHTML($url, $inicio = '', $final) {
        $source = @file_get_contents($url) or trigger_error('Error al obtener url ' . $url, E_USER_ERROR);
        $posicion_inicio = strpos($source, $inicio) + strlen($inicio);
        $posicion_final = strpos($source, $final) - $posicion_inicio;
        $found_text = substr($source, $posicion_inicio, $posicion_final);
        return $inicio . $found_text . $final;
    }

    function printDateLetter($fecha) {
        $dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

//        return $dias[strftime('%w', strtotime($fecha))] . " " . strftime("%d", strtotime($fecha)) . " de " . $meses[intval(strftime('%m', strtotime($fecha)))] . " de " . strftime("%Y", strtotime($fecha)) . "";
        return strftime("%d", strtotime($fecha)) . " de " . $meses[intval(strftime('%m', strtotime($fecha)))] . " de " . strftime("%Y", strtotime($fecha)) . "";
    }
    
    function printDateLetter2($fecha) {
        $dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

//        return $dias[strftime('%w', strtotime($fecha))] . " " . strftime("%d", strtotime($fecha)) . " de " . $meses[intval(strftime('%m', strtotime($fecha)))] . " de " . strftime("%Y", strtotime($fecha)) . "";
        return strftime("%d", strtotime($fecha)) . " de " . $meses[intval(strftime('%m', strtotime($fecha)))] . " de " . strftime("%Y", strtotime($fecha)) . "";
    }

    function fechaDMA($fecha) {
        return date("d-m-Y", strtotime($fecha));
    }

    function dmaFecha($fecha) {
        return date("d/m/Y", strtotime($fecha));
    }

    function normaliza($cadena) {
        $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
        $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
        $cadena = utf8_decode($cadena);
        $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
//        $cadena = strtolower($cadena);
        return utf8_encode($cadena);
    }

    public function sendReportes($addr, $name, $subject, $msg, $path_adjunto, $adjunto) {
        require_once '../config/configMail.php';
        require_once '../config/properties.php';
        require_once 'class.phpmailer.php';
        require_once 'class.smtp.php';


//Crear una instancia de PHPMailer
        $mail = new PHPMailer();
//Definir que vamos a usar SMTP
        $mail->IsSMTP();
//Esto es para activar el modo depuración. En entorno de pruebas lo mejor es 2, en producción siempre 0
// 0 = off (producción)
// 1 = client messages
// 2 = client and server messages
        $mail->SMTPDebug = SMTP_DEBUG;
//Ahora definimos gmail como servidor que aloja nuestro SMTP
//$mail->Host       = 'smtp.gmail.com';
        $mail->Host = SMTP_HOST;
//El puerto será el 587 ya que usamos encriptación TLS
//$mail->Port       = 587;
        $mail->Port = SMTP_PORT;
//Definmos la seguridad como TLS
//$mail->SMTPSecure = 'tls';
//Tenemos que usar gmail autenticados, así que esto a TRUE
        $mail->SMTPAuth = true;
//Definimos la cuenta que vamos a usar. Dirección completa de la misma
        $mail->Username = SMTP_USER;
//Introducimos nuestra contraseña de gmail
        $mail->Password = SMTP_PASS;
//Definimos el remitente (dirección y, opcionalmente, nombre)
        $mail->SetFrom(SMTP_FROM, SMTP_FROM_ALIAS);
//Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)
//$mail->AddReplyTo('replyto@correoquesea.com','El de la réplica');
//Y, ahora sí, definimos el destinatario (dirección y, opcionalmente, nombre)
        $mail->AddAddress($addr, $name);
        $mail->addBCC('sistemas@dirac.mx');
//        $mail->AddCC(SMTP_FROM, $name);//<-----------------------------------------------------------CON COPIA A ARJION
//Definimos el tema del email
        $mail->Subject = $subject;
//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
        $mail->MsgHTML($msg);
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
        $mail->AltBody = 'This is a plain-text message body';
        //Adjuntamos archivo
        $mail->AddAttachment($path_adjunto, $adjunto);
//Enviamos el correo
        if (!$mail->Send()) {
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        } else {
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        }
        return $this->response;
    }

    public function sendMailAtach($addr, $name, $subject, $msg, $path_adjunto, $adjunto) {
        require_once '../config/configMail.php';
        require_once '../config/properties.php';
        require_once 'class.phpmailer.php';
        require_once 'class.smtp.php';

//Crear una instancia de PHPMailer
        $mail = new PHPMailer();
//Definir que vamos a usar SMTP
        $mail->IsSMTP();
//Esto es para activar el modo depuración. En entorno de pruebas lo mejor es 2, en producción siempre 0
// 0 = off (producción)
// 1 = client messages
// 2 = client and server messages
        $mail->SMTPDebug = SMTP_DEBUG;
//Ahora definimos gmail como servidor que aloja nuestro SMTP
//$mail->Host       = 'smtp.gmail.com';
        $mail->Host = SMTP_HOST;
//El puerto será el 587 ya que usamos encriptación TLS
//$mail->Port       = 587;
        $mail->Port = SMTP_PORT;
//Definmos la seguridad como TLS
//$mail->SMTPSecure = 'tls';
//Tenemos que usar gmail autenticados, así que esto a TRUE
        $mail->SMTPAuth = true;
//Definimos la cuenta que vamos a usar. Dirección completa de la misma
        $mail->Username = SMTP_USER;
//Introducimos nuestra contraseña de gmail
        $mail->Password = SMTP_PASS;
//Definimos el remitente (dirección y, opcionalmente, nombre)
        $mail->SetFrom(SMTP_FROM, SMTP_FROM_ALIAS);
//Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)
//$mail->AddReplyTo('replyto@correoquesea.com','El de la réplica');
//Y, ahora sí, definimos el destinatario (dirección y, opcionalmente, nombre)
        $mail->AddAddress($addr, $name);
        $mail->addBCC('sistemas@dirac.mx');
//        $mail->AddCC(SMTP_FROM, $name);//<-----------------------------------------------------------CON COPIA A ARJION
//Definimos el tema del email
        $mail->Subject = $subject;
//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
        $mail->MsgHTML($msg);
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
        $mail->AltBody = 'This is a plain-text message body';
        //Adjuntamos archivo
        echo $path_adjunto . "-" . $adjunto;
        $mail->AddAttachment($path_adjunto, $adjunto);
//Enviamos el correo
        if (!$mail->Send()) {
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        } else {
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        }
        return $this->response;
    }

    function getRealIP() {

        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];
    }

    function encriptar_AES($string, $key) {
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
        mcrypt_generic_init($td, $key, $iv);
        $encrypted_data_bin = mcrypt_generic($td, $string);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $encrypted_data_hex = bin2hex($iv) . bin2hex($encrypted_data_bin);
        return $encrypted_data_hex;
    }

    function desencriptar_AES($encrypted_data_hex, $key) {
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $iv_size_hex = mcrypt_enc_get_iv_size($td) * 2;
        $iv = pack("H*", substr($encrypted_data_hex, 0, $iv_size_hex));
        $encrypted_data_bin = pack("H*", substr($encrypted_data_hex, $iv_size_hex));
        mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $encrypted_data_bin);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $decrypted;
    }

}
