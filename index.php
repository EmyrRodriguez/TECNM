<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'snippets/header.php';
?>
<!--<link href="http://148.243.10.117/sgi-dirac/dist/css/sweetalert2.min.css" rel="stylesheet" type="text/css" />
<link href="http://148.243.10.117/sgi-dirac/dist/css/animate.css" rel="stylesheet" type="text/css" />-->
<link href="http://201.149.54.149/sgi-dirac/dist/css/sweetalert2.min.css" rel="stylesheet" type="text/css" />
<link href="http://201.149.54.149/sgi-dirac/dist/css/animate.css" rel="stylesheet" type="text/css" />
<style>
    @import "bourbon";

    body {
        background: #eee !important;	
    }

    .wrapper {	
        margin-top: 80px;
        margin-bottom: 80px;
    }

    .form-signin {
        max-width: 380px;
        padding: 15px 35px 45px;
        margin: 0 auto;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,0.1);  

        .form-signin-heading,
        .checkbox {
            margin-bottom: 30px;
        }

        .checkbox {
            font-weight: normal;
        }

        .form-control {
            position: relative;
            font-size: 16px;
            height: auto;
            padding: 10px;
            @include box-sizing(border-box);

            &:focus {
                z-index: 2;
            }
        }

        input[type="text"] {
            margin-bottom: -1px;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        input[type="password"] {
            margin-bottom: 20px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    }
</style>
<body>
    <div class="wrapper">
        <h3 class="text-center">Este sería el login para el usuario interno</h3>
        <form class="form-signin" name="loginForm" id="loginForm" method="POST">       
            <img class="center-block" src="img/bandera-britanica-r.png" alt="TECNM" />
            <input type="text" class="form-control" name="usuario" id="usuario" placeholder="Usuario" required="true" autofocus="" />
            <br />
            <input type="password" class="form-control" name="contrasenia" id="contrasenia" placeholder="Contrase&ntilde;a" required="true"/>      
            <label class="checkbox">
                <input type="checkbox" name="remember" id="remember"> Recordar Usuario
            </label>
            <input type="hidden" name="evento" id="evento" value="5" />
            <br />
            <button class="btn btn-lg btn-primary btn-block" type="submit">Ingresar</button>   
            <div id="msg"></div>
        </form>
    </div>
    <?php
//    require_once 'snippets/footer.php';
//    require_once '../sgi-dirac/utils/datatables.php';
    ?>
    <!--<script type="text/javascript" src="js/functions.js"></script>-->
    <!--<script src="http://148.243.10.117/sgi-dirac/dist/js/sweetalert2.min.js" type="text/javascript"></script>--> 
    <script src="http://201.149.54.149/sgi-dirac/dist/js/sweetalert2.min.js" type="text/javascript"></script> 
    <script>
        $(document).ready(function () {
            readSession();
            $("#loginForm").submit(function (event) {
                event.preventDefault();
                $.ajax({
                    type: "POST",
                    url: 'controller/controllerTECNM.php',
                    data: $('#loginForm').serializeArray(),
                    dataType: 'json',
                    beforeSend: function () {
                        $("#msg").html('<div class="text-center"><i class="fa fa-spinner fa-spin" style="font-size:48px; color: #F49625"></i><p><b class="text-center"><b></p></div>');
                    },
                    success: function (response) {
                        if (response.errorCode === 0) {
                            console.log(response);
//                    $("#msg").html('<div class="col-md-8 col-md-offset-3"><div class="alert alert-dismissable alert-success"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <strong><i class="fa fa-success"> </i></strong>' + response.msg + '</div></div>');
                            if ($("#remember").prop("checked")) {
                                console.log("Remember me!");
                                //Si esta checkeado box de recordar usuario creamos las cookies
                                localStorage.setItem("usrArjion", $("#usuario").val());
                                localStorage.setItem("passArjion", $("#contrasenia").val());
                                localStorage.setItem("remArjion", 1);
                            } else {
                                console.log("No Remember me!");
                                localStorage.setItem("usrArjion", "");
                                localStorage.setItem("passArjion", "");
                                localStorage.setItem("remArjion", 0);
                            }
                            showAlert(response.msg, "Redireccionando..", "success", "bounce");
                            //Redireccionamos al dashboard
                            setTimeout(function () {
                                window.location.href = "catalogo_usuarios.php";
                            }, 1500);

                        } else {
                            showAlert("¡Error!", response.msg, "error", "swing");
                        }
                    },
                    error: function (a, b, c) {
                        console.log(a, b, c);
                    }
                });
            });
        });
        function  readSession() {
            $("#usuario").val(localStorage.getItem("usrArjion"));
            $("#contrasenia").val(localStorage.getItem("passArjion"));
            $("#remember").prop("checked", true);
        }
        function showAlert(title, text, type, animation) {
            console.log(title + " - " + text + " - " + type + " - " + animation);
            swal({
                title: '<p class="sweet-figg-title">' + title + '</p>',
                type: type,
                html: '<p class="sweet-figg-text">' + text + '</p>',
                timer: 1000,
                animation: false,
                customClass: 'animated ' + animation
            }).catch(swal.noop);
        }

        function showPass() {
            if ($("#show_pass").prop("checked")) {
                $("#contrasenia").prop("type", "text");
            } else {
                $("#contrasenia").prop("type", "password");
            }
        }

    </script>
</body>
</html>