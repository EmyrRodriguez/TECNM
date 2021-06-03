<?php
require_once '../../config/properties.php';

$nombre = utf8_encode($_REQUEST['nombre']);
$supervisor = $_REQUEST['supervisor'];
$edificio = utf8_encode($_REQUEST['edificio']);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>DIRAC | Ingenieros Consultores</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <style>
            @font-face {
                font-family: 'Terminator Real NFI';
                font-style: normal;
                font-weight: normal;
                src: local('Terminator Real NFI'), url('terminator real nfi.woff') format('woff');
            }


            @font-face {
                font-family: 'Terminator Real NFI';
                font-style: normal;
                font-weight: normal;
                src: local('Terminator Real NFI'), url('terminator real nfi.woff') format('woff');
            }
        </style>
    </head>
    <body style="margin: 0; padding: 0;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">	
            <tr>
                <td style="padding: 10px 0 30px 0;">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border: 1px solid #cccccc; border-collapse: collapse;">
                        <tr>
                            <td align="center" bgcolor="#FEFEFE" style="padding: 40px 0 30px 0; color: #153643; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;">
                                <!--<img src="<?php // echo SYSTEM_PATH; ?>img/Qubi.png" alt="Creating Email Magic" width="200" height="130" style="display: block;" />-->
                                <img src="../img/Qubi.png" alt="Creating Email Magic" width="200" height="130" style="display: block;" />
                                <!--<h1 style="font-family:'Terminator Real NFI';font-weight:normal;font-size:42px"><b style="color: #F89E44">A</b><b style="color: #003365">rjion</b></h1>-->
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="color: #153643; font-family: Arial, sans-serif; font-size: 16px;">
                                            <b style="color: #F89E44"><?php echo $nombre; ?> :</b><br /><br /><b style="color: #003365"></b>
                                            <p align="justify">Se ha registrado un reporte de inspecci&oacute;n del edificio <b style="color: #F89E44"><?php echo $edificio; ?></b>, por parte del supervisor <b style="color: #F89E44"><?php echo $supervisor; ?>.</p></b> <br />                                           
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 20px 0 30px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                            <p align="justify"> 
                                                Para consultar y revisar el reporte entra a Arjion en la secci&oacute;n de <strong>Inspecci&oacute;n</strong>.
                                            </p>
                                            <br />
                                            <p>&iexcl;Excelente d&iacute;a!</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>                                            
                                            &nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color: #0072b1; font-family: Arial, sans-serif; font-size: 12px;">                                            
                                            <!--<p> * Para el correcto uso de la aplicaci&oacute;n se recomienda utilizar Mozilla Firefox o Google Chrome.</p>-->
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="#003365" style="padding: 30px 30px 30px 30px;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="color: #ffffff; font-family: Arial, sans-serif; font-size: 14px;" width="75%">
                                            &reg; DIRAC | Ingenieros Consultores<br/>
                                            <!--<a href="#" style="color: #ffffff;"><font color="#ffffff">Unsubscribe</font></a> to this newsletter instantly-->
                                        </td>
                                        <td align="right" width="25%">
                                            <table border="0" cellpadding="0" cellspacing="0">
                                                <tr>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>