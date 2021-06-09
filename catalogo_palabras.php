<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'snippets/header.php';
?>
<meta http-equiv="refresh" content="300" />
<body>

    <div class="container">        
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><img src="img/bandera-britanica-r.png" alt="Dirac Ingenieros Consultores">
                    </a>
                </div>
                <div id="navbar1" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a data-toggle="tab" href="#home"><b>Catalogo de usuarios</b></a></li>
                        <li><a data-toggle="tab" href="#menu1" id="personnel"><b>Tab2</b></a></li>
                        <li><a data-toggle="tab" href="#menu2" id="suppliers"><b>Tab 3</b></a></li>
                        <li><a data-toggle="tab" href="#menu3" id="apps"><b>Tab 4</b></a></li>
                        <li><a data-toggle="tab" href="#menu4" id="returnn"><b>Etc</b></a></li>
                        <!--                        <li class="dropdown">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li><a href="#">Action</a></li>
                                                        <li><a href="#">Another action</a></li>
                                                        <li><a href="#">Something else here</a></li>
                                                        <li class="divider"></li>
                                                        <li class="dropdown-header">Nav header</li>
                                                        <li><a href="#">Separated link</a></li>
                                                        <li><a href="#">One more separated link</a></li>
                                                    </ul>
                                                </li>-->
                    </ul>
                </div>
                <!--/.nav-collapse -->
            </div>
            <!--/.container-fluid -->
        </nav>
        <div class="row">
            <div class="col-lg-12">
                <div class="col-lg-6">
                    <img src="img/bandera-britanica-r.png" width="157" height="81" />
                </div>
                <div class="col-lg-offset-1 col-lg-5">
                    <h3>Catalogo de usuarios [<small>CRUD</small>] </h3>
                </div>
            </div>
        </div>
        <hr/>
        <div id="msg2"></div>
        <div class="row">
            <div class="tab-content">
                <div id="home" class="tab-pane fade in active">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-lg-offset-9 col-lg-3">
                                <a href="#" title="Agregar usuario" class="btn btn-block btn-primary text-info" onclick="showModal(0, 1);"><i class="fa fa-plus"></i> Agregar usuario</a>
                            </div>
                        </div>
                    </div>
                    <table id="dirac_BI" class="table table-bordered dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Usuario</th>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>Correo</th>
                                <th>Departamento</th>
                                <th>Estatus</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody id="cat_usuarios">
                            <?php
                            require_once './model/TECNM.php';
                            require_once './model/Catalogo.php';
                            $tecnm = TECNM::TECNMSngltn();
                            $cat = Catalogo::catSngltn("cat_departamentos");

                            $usuarios = $tecnm->obtenerUsuarios();

                            foreach ($usuarios["data"] as $key => $u) {
                                $estatus = "Sin estatus";
                                switch (intval($u["estatus"])) {
                                    case 1:
                                        $estatus = "<b class='text-success'>Activo</b>";
                                        break;
                                    case 2:
                                        $estatus = "<b class='text-danger'>Inactivo</b>";
                                        break;
                                    default:
                                        break;
                                }
                                $cat->setId($u["id_departamento"]);
                                $depto = $cat->getCatById($cat);
                                ?>
                                <tr>
                                    <td><?php echo $u["id"]; ?></td>
                                    <td><?php echo $u["usuario"]; ?></td>
                                    <td><?php echo $u["nombre"]; ?></td>
                                    <td><?php echo $u["apellidos"]; ?></td>
                                    <td><?php echo $u["correo"]; ?></td>
                                    <td><?php echo $depto["data"]["nombre"]; ?></td>
                                    <td><?php echo $estatus; ?></td>
                                    <td>
                                        <?php
                                        echo '<a href="#" title="Editar usuario" class="text-info" onclick="showModal(' . $u["id"] . ', 2);"><i class="fa fa-user"></i> Editar usuario</a><br />';
                                        echo '<a href="#" title="Eliminar usuario" class="text-danger" onclick="eliminarUsr(' . $u["id"] . ');"><i class="fa fa-times"></i> Eliminar usuario</a>';
                                        ?>

                                    </td>
                                </tr>
                                <?php
                            }
                            ?>

                        </tbody>
                    </table>
                </div>   
                <div id="menu1" class="tab-pane fade">
                    <h3>Tab 2</h3>
                </div>   
                <div id="menu2" class="tab-pane fade">
                    <h3>Tab 3</h3>
                </div>   
                <div id="menu3" class="tab-pane fade">
                    <h3>Tab 4</h3>
                </div>   
                <div id="menu4" class="tab-pane fade">
                    <h3>Tab 5</h3>
                </div>
            </div>
        </div>
        <div id="msg"></div>
        <br />        
    </div>
    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">Registro/ Edici&oacute;n de usuarios</h4>
                </div>
                <div class="modal-body">
                    <form role="form" name="add_user" id="add_user">                        
                        <div class="row">
                            <div class="col-lg-12">
                                <hr />
                                <div class="form-group">
                                    Usuario:
                                    <input type="text" name="usuario" id="usuario" class="form-control" />
                                </div>
                                <div class="form-group">
                                    Contrase&ntilde;a:
                                    <input type="password" name="contrasenia" id="contrasenia" class="form-control" />
                                </div>
                                <div class="form-group">
                                    Nombre:
                                    <input type="text" name="nombre" id="nombre" class="form-control" />
                                </div>
                                <div class="form-group">
                                    Apellidos:
                                    <input type="text" name="apellidos" id="apellidos" class="form-control" />
                                </div>
                                <div class="form-group">
                                    Correo:
                                    <input type="mail" name="correo" id="correo" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <?php
                                    $departamentos = $cat->getCat();
                                    ?>
                                    Departamentos 
                                    <select name="id_departamento" id="id_departamento" class="form-control slctF">
                                        <option value="0">Seleccione departamento</option>
                                        <?php
                                        foreach ($departamentos["data"] as $key => $d) {
                                            echo '<option value="' . $d["id"] . '">' . $d["nombre"] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>    
                                <div class="form-group">
                                    Estatus
                                    <select name="estatus" id="estatus" class="form-control slctF">
                                        <option value="0">Seleccione estatus</option>
                                        <option value="1">Activo</option>
                                        <option value="2">Inactivo</option>                                            
                                    </select>
                                </div>   

                                <div class="form-group">
                                    <input type="hidden" name="evento" id="evento" value="0" />
                                    <input type="hidden" name="id_usuario" id="id_usuario" value="0" />
                                    <input type="submit" name="enviar" id="enviar" value="Guardar informaci&oacute;n" class="btn btn-block btn-primary"/>

                                </div>

                            </div>

                            <div id="msgForm">

                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
            <div id="msgS"></div>
        </div>
    </div>



    <?php
    require_once 'snippets/footer.php';
    require_once '../sgi-dirac/utils/datatables.php';
    ?>
    <script type="text/javascript" src="js/functions.js?v=<?php echo $fecha_scripts->getTimestamp(); ?>"></script>
<!--    <script type="text/javascript" src="js/angular.js"></script>
    <script type="text/javascript" src="js/app.js"></script>-->

    <!-- ---------------------------------------------------------------- -->
    <script type="text/javascript">
                                    $(document).ready(function () {

                                        $("#add_user").submit(function (event) {
                                            event.preventDefault();
                                            var data = $("#add_user").serializeArray();
                                            //                                                        data.push({name: 'nombre_proyecto', value: $("#id_proyecto option:selected").text()});
                                            $.ajax({
                                                type: "POST",
                                                url: 'controller/controllerTECNM.php',
                                                data: data,
                                                dataType: 'json',
                                                beforeSend: function () {
                                                    $("#msgForm").html('<div class="text-center"><i class="fa fa-spinner fa-spin" style="font-size:48px; color: aqua"></i><br /><b class="text-center">Procesando informaci&oacute;n...<b></div>');
                                                },
                                                success: function (response) {
                                                    if (response.errorCode === 0) {
                                                        $("#msgForm").html('<div class="text-center"><b class="text-center text-success">Informaci&oacute;n guardada correctamente<b></div>');
                                                        setTimeout(function () {
                                                            window.location.reload();
                                                        }, 1500);
                                                    } else {
                                                        $("#msgForm").html("¡Error!<br />" + response.msg);
                                                    }
                                                },
                                                error: function (a, b, c) {
                                                    console.log(a, b, c);
                                                }
                                            });
                                        });


                                        $("#dirac_BI").dataTable({
//                                "order": [[10, "asc"]],
                                            "dom": 'Bfrtip',
                                            "buttons": [
                                                'colvis', 'csv', 'excel', 'pdf', 'print'
//                            'excel', 'pdf', 'print'
                                            ],
                                            "bPaginate": true,
                                            "bLengthChange": true,
                                            "bFilter": true,
//                                "bSort": true,
                                            "bInfo": true,
                                            "bAutoWidth": false,
                                            "oLanguage": {
                                                "sProcessing": "Procesando...",
                                                "sLengthMenu": "Mostrar _MENU_ registros",
                                                "sZeroRecords": "No se encontraron resultados",
                                                "sEmptyTable": "Ningún dato disponible en esta tabla",
                                                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                                                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                                                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                                                "sInfoPostFix": "",
                                                "sSearch": "Buscar:",
                                                "sUrl": "",
                                                "sInfoThousands": ",",
                                                "sLoadingRecords": "Cargando...",
                                                "sButtonText": "Imprimir",
                                                "oPaginate": {
                                                    "sFirst": "Primero",
                                                    "sLast": "Último",
                                                    "sNext": "Siguiente",
                                                    "sPrevious": "Anterior"
                                                },
                                                "buttons": {
                                                    "print": "Imprimir",
                                                    "colvis": "Columnas mostradas"
                                                }
                                            }
                                        });

                                    });

                                    function obtenerUsuario(id) {
                                        $.ajax({
                                            type: "POST",
                                            url: 'controller/controllerTECNM.php',
                                            data: {evento: 1, id_usuario: id, },
                                            dataType: 'json',
                                            beforeSend: function () {
//                                    $("#msgS").html('<div class="text-center"><i class="fa fa-spinner fa-spin" style="font-size:48px; color: #F49625"></i><p><b class="text-center"><b></p></div>');
                                            },
                                            success: function (response) {
                                                if (response.errorCode === 0) {
                                                    $("#usuario").val(response.data[0]["usuario"]);
                                                    $("#nombre").val(response.data[0]["nombre"]);
                                                    $("#apellidos").val(response.data[0]["apellidos"]);
                                                    $("#correo").val(response.data[0]["correo"]);
                                                    $("#id_departamento").val(response.data[0]["id_departamento"]);
                                                    $("#estatus").val(response.data[0]["estatus"]);
                                                    $("#id_usuario").val(id);
                                                    $("#evento").val(2);
                                                } else {
                                                    $("#msgS").html('<div class="text-center" style="color: #F49625"><h3><b class="text-center">' + response.msg + '<b></h3></div>');
                                                }
                                            },
                                            error: function (a, b, c) {
                                                console.log(a, b, c);
                                                $("#msgS").html('<div class="text-center" style="color: #F49625"><h3><b class="text-center">Favor de verificar que el numero de control sea correcto<b></h3></div>');
                                            }
                                        });


                                    }

                                    function eliminarUsr(id) {
                                        $.ajax({
                                            type: "POST",
                                            url: 'controller/controllerTECNM.php',
                                            data: {evento: 4, id_usuario: id},
                                            dataType: 'json',
                                            beforeSend: function () {
                                                $("#msg").html('<div class="text-center"><i class="fa fa-spinner fa-spin" style="font-size:48px; color: #F49625"></i><p><b class="text-center"><b></p></div>');
                                            },
                                            success: function (response) {
                                                if (response.errorCode === 0) {
                                                    $("#msg").html('<div class="text-center"><i class="fa fa-check-circle" style="font-size:48px; color: #00539B"></i></div>');
                                                    setTimeout(function () {
                                                        location.reload();
                                                    }, 1500);
                                                } else {
                                                    $("#msg").html('<div class="text-center" style="color: #F49625"><h3><b class="text-center">' + response.msg + '<b></h3></div>');
                                                }
                                            },
                                            error: function (a, b, c) {
                                                console.log(a, b, c);
                                                $("#msgS2").html('<div class="text-center" style="color: #F49625"><h3><b class="text-center">Favor de verificar que el numero de control sea correcto<b></h3></div>');
                                            }
                                        });
                                        setTimeout(function () {
                                            $("#msgS2").html('');
                                        }, 2000);

                                    }

                                    function showModal(id, opcion) {
                                        $("#id").val(id);
                                        $("#myModal").modal("show");
                                        if (parseInt(opcion) === 2) {
                                            obtenerUsuario(id);
                                        } else {
                                            $("#evento").val(3);
                                            $("#add_user").trigger("reset");
                                        }
                                        $("#opcion").val(opcion);

                                    }



                                    /***********************************************************************************************************************************************************************************************/

                                    function search_personnel() {
                                        $("#dirac_Personnel").dataTable().fnDestroy();
                                        $("#dirac_outputs_personnel").html("");
                                        var q = "1=1";
                                        $.post("controller/controllerMx.php",
                                                {evento: 28, tipo: 1},
                                                function (response) {
                                                    if (response.errorCode === 0) {
                                                        var records = '';
                                                        $.each(response.data, function (index, value) {
                                                            records += '<tr>';
                                                            records += '<td>' + value.id + '</td>';
                                                            records += '<td>' + value.solicitante + '</td>';
                                                            records += '<td>' + value.nombre + '</td>';
                                                            records += '<td>' + value.destino + '</td>';
                                                            records += '<td>' + value.fecha_salida + '</td>';
                                                            records += '<td>' + value.comentarios + '</td>';
                                                            var hora_salida = "";
                                                            var hora_regreso = "";

                                                            if (value.hora_salida !== null) {
                                                                hora_salida = value.hora_salida;
                                                            } else {
                                                                hora_salida = '<button title="Registrar Salida" class="btn btn-secondary" onclick="check_personnel(' + value.id + ', 1);"><i class="fa fa-chevron-circle-right"></i> Registrar salida</button>';
                                                            }

                                                            if (value.hora_regreso !== null) {
                                                                hora_regreso = value.hora_regreso;
                                                            } else {
                                                                hora_regreso = '<button title="Registrar Regreso" class="btn btn-secondary" onclick="check_personnel(' + value.id + ', 2);" > <i class="fa fa-chevron-circle-right"> </i> Registrar regreso</button >';
                                                            }
                                                            records += '<td>' + hora_salida + '</td>';
                                                            records += '<td>' + hora_regreso + '</td>';
                                                            records += '</tr>';
                                                        });

                                                        $("#dirac_outputs_personnel").append(records);
                                                        $("#dirac_Personnel").dataTable({
                                                            "order": [[4, "asc"]],
                                                            "dom": 'Bfrtip',
                                                            "buttons": [
                                                                'colvis', 'csv', 'excel', 'pdf', 'print'
//                            'excel', 'pdf', 'print'
                                                            ],
                                                            "bPaginate": true,
                                                            "bLengthChange": true,
                                                            "bFilter": true,
                                                            "bSort": true,
                                                            "bInfo": true,
                                                            "bAutoWidth": false,
                                                            "oLanguage": {
                                                                "sProcessing": "Procesando...",
                                                                "sLengthMenu": "Mostrar _MENU_ registros",
                                                                "sZeroRecords": "No se encontraron resultados",
                                                                "sEmptyTable": "Ningún dato disponible en esta tabla",
                                                                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                                                                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                                                                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                                                                "sInfoPostFix": "",
                                                                "sSearch": "Buscar:",
                                                                "sUrl": "",
                                                                "sInfoThousands": ",",
                                                                "sLoadingRecords": "Cargando...",
                                                                "sButtonText": "Imprimir",
                                                                "oPaginate": {
                                                                    "sFirst": "Primero",
                                                                    "sLast": "Último",
                                                                    "sNext": "Siguiente",
                                                                    "sPrevious": "Anterior"
                                                                },
                                                                "buttons": {
                                                                    "print": "Imprimir",
                                                                    "colvis": "Columnas mostradas"
                                                                }
                                                            }
                                                        });


                                                    } else {
                                                        $("#msg").html("Ha ocurrido un error, por favor intente m&aacute;s tarde.");
                                                    }
                                                }, 'json');
                                    }

                                    /************************************************************ 
                                     * FIGG - DIRAC
                                     * 04.Noviembre.2019
                                     * Se agrega opcion para registrar entrada y salida de proveedores
                                     /************************************************************ */
                                    function search_suppliers() {
                                        $("#dirac_suppliers").dataTable().fnDestroy();
                                        $("#dirac_body_suppliers").html("");
                                        var q = "1=1";
                                        $.post("controller/controllerMx.php",
                                                {evento: 30, tipo: 1},
                                                function (response) {
                                                    if (response.errorCode === 0) {
                                                        var records = '';
                                                        $.each(response.data, function (index, value) {
                                                            records += '<tr>';
                                                            records += '<td>' + value.id + '</td>';
                                                            records += '<td>' + value.nombre_usuario + '</td>';
                                                            records += '<td>' + value.nombre_proveedor + '</td>';
                                                            records += '<td>' + value.empresa + '</td>';

                                                            var comments = "";
                                                            if (parseInt(value.notificacion) === 1) {
                                                                comments += value.comentarios + ".<br /> Visita autorizada por DAF.";
                                                            } else {
                                                                comments += value.comentarios;
                                                            }

                                                            records += '<td>' + comments + '</td>';
                                                            records += '<td>' + value.personal_apoyo + '</td>';
                                                            records += '<td>' + value.fecha_ingreso + '</td>';
                                                            var hora_entrada = "";
                                                            var hora_salida = "";

                                                            if (value.hora_entrada !== null) {
                                                                hora_entrada = value.hora_entrada;
                                                            } else {
                                                                hora_entrada = '<button title="Registrar Salida" class="btn btn-secondary" onclick="check_supplier(' + value.id + ', 1);"><i class="fa fa-chevron-circle-right"></i> Registrar salida</button>';
                                                            }

                                                            if (value.hora_salida !== null) {
                                                                hora_salida = value.hora_salida;
                                                            } else {
                                                                hora_salida = '<button title="Registrar Regreso" class="btn btn-secondary" onclick="check_supplier(' + value.id + ', 2);" > <i class="fa fa-chevron-circle-right"> </i> Registrar regreso</button >';
                                                            }
                                                            records += '<td>' + hora_entrada + '</td>';
                                                            records += '<td>' + hora_salida + '</td>';
                                                            records += '</tr>';
                                                        });

                                                        $("#dirac_body_suppliers").append(records);
                                                        $("#dirac_suppliers").dataTable({
                                                            "order": [[5, "asc"]],
                                                            "dom": 'Bfrtip',
                                                            "buttons": [
                                                                'colvis', 'csv', 'excel', 'pdf', 'print'
//                            'excel', 'pdf', 'print'
                                                            ],
                                                            "bPaginate": true,
                                                            "bLengthChange": true,
                                                            "bFilter": true,
                                                            "bSort": true,
                                                            "bInfo": true,
                                                            "bAutoWidth": false,
                                                            "oLanguage": {
                                                                "sProcessing": "Procesando...",
                                                                "sLengthMenu": "Mostrar _MENU_ registros",
                                                                "sZeroRecords": "No se encontraron resultados",
                                                                "sEmptyTable": "Ningún dato disponible en esta tabla",
                                                                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                                                                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                                                                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                                                                "sInfoPostFix": "",
                                                                "sSearch": "Buscar:",
                                                                "sUrl": "",
                                                                "sInfoThousands": ",",
                                                                "sLoadingRecords": "Cargando...",
                                                                "sButtonText": "Imprimir",
                                                                "oPaginate": {
                                                                    "sFirst": "Primero",
                                                                    "sLast": "Último",
                                                                    "sNext": "Siguiente",
                                                                    "sPrevious": "Anterior"
                                                                },
                                                                "buttons": {
                                                                    "print": "Imprimir",
                                                                    "colvis": "Columnas mostradas"
                                                                }
                                                            }
                                                        });


                                                    } else {
                                                        $("#msg").html("Ha ocurrido un error, por favor intente m&aacute;s tarde.");
                                                    }
                                                }, 'json');
                                    }

                                    /************************************************************ 
                                     * FIGG - DIRAC
                                     * 12.Noviembre.2019
                                     * Se agrega opcion para registrar entrada y salida de proveedores
                                     /************************************************************ */
                                    function search_apps() {
                                        $("#dirac_applicant").dataTable().fnDestroy();
                                        $("#dirac_body_applicants").html("");
                                        var q = "1=1";
                                        $.post("controller/controllerMx.php",
                                                {evento: 32, tipo: 1},
                                                function (response) {
                                                    if (response.errorCode === 0) {
                                                        var records = '';
                                                        $.each(response.data, function (index, value) {
                                                            records += '<tr>';
                                                            records += '<td>' + value.id + '</td>';
                                                            records += '<td>' + value.registro + '</td>';
                                                            records += '<td>' + value.candidato + '</td>';
                                                            records += '<td>' + value.comentarios + '</td>';
                                                            records += '<td>' + value.fecha + '</td>';

                                                            var hora_entrada = "";
                                                            var hora_salida = "";

                                                            if (value.hora_ingreso !== null) {
                                                                hora_entrada = value.hora_ingreso;
                                                            } else {
                                                                hora_entrada = '<button title="Registrar Ingreso" class="btn btn-secondary" onclick="check_app(' + value.id + ', 1);"><i class="fa fa-chevron-circle-right"></i> Registrar ingreso</button>';
                                                            }

                                                            if (value.hora_salida !== null) {
                                                                hora_salida = value.hora_salida;
                                                            } else {
                                                                hora_salida = '<button title="Registrar Salida" class="btn btn-secondary" onclick="check_app(' + value.id + ', 2);" > <i class="fa fa-chevron-circle-right"> </i> Registrar salida</button >';
                                                            }
                                                            records += '<td>' + hora_entrada + '</td>';
                                                            records += '<td>' + hora_salida + '</td>';
                                                            records += '</tr>';
                                                        });

                                                        $("#dirac_body_applicants").append(records);
                                                        $("#dirac_applicant").dataTable({
                                                            "order": [[5, "asc"]],
                                                            "dom": 'Bfrtip',
                                                            "buttons": [
                                                                'colvis', 'csv', 'excel', 'pdf', 'print'
//                            'excel', 'pdf', 'print'
                                                            ],
                                                            "bPaginate": true,
                                                            "bLengthChange": true,
                                                            "bFilter": true,
                                                            "bSort": true,
                                                            "bInfo": true,
                                                            "bAutoWidth": false,
                                                            "oLanguage": {
                                                                "sProcessing": "Procesando...",
                                                                "sLengthMenu": "Mostrar _MENU_ registros",
                                                                "sZeroRecords": "No se encontraron resultados",
                                                                "sEmptyTable": "Ningún dato disponible en esta tabla",
                                                                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                                                                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                                                                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                                                                "sInfoPostFix": "",
                                                                "sSearch": "Buscar:",
                                                                "sUrl": "",
                                                                "sInfoThousands": ",",
                                                                "sLoadingRecords": "Cargando...",
                                                                "sButtonText": "Imprimir",
                                                                "oPaginate": {
                                                                    "sFirst": "Primero",
                                                                    "sLast": "Último",
                                                                    "sNext": "Siguiente",
                                                                    "sPrevious": "Anterior"
                                                                },
                                                                "buttons": {
                                                                    "print": "Imprimir",
                                                                    "colvis": "Columnas mostradas"
                                                                }
                                                            }
                                                        });


                                                    } else {
                                                        $("#msg").html("Ha ocurrido un error, por favor intente m&aacute;s tarde.");
                                                    }
                                                }, 'json');
                                    }


                                    function search_outs_stat() {
                                        $("#dirac_BI_2").dataTable().fnDestroy();
                                        $("#dirac_outputs_return").html("");
                                        var q = "1=1";
                                        $.post("controller/controllerMx.php",
                                                {evento: 34, fecha_inicio: $("#fecha_inicio").val(), fecha_fin: $("#fecha_fin").val()},
                                                function (response) {
                                                    if (response.errorCode === 0) {
                                                        console.log(response.data);
                                                        var records = '';
                                                        $.each(response.data, function (index, value) {
                                                            records += '<tr>';
                                                            records += '<td>' + value.id + '</td>';
                                                            var solicitante = "";
                                                            if (parseInt(value.id_solicitante) === 0) {
                                                                solicitante = value.otro_usuario;
                                                            } else {
                                                                solicitante = value.solicitante;
                                                            }
//                                                                    records += '<td>' + value.solicitante + '</td>';
                                                            records += '<td>' + solicitante + '</td>';
                                                            records += '<td>' + value.nombre + '</td>';
                                                            records += '<td>' + value.descripcion + '</td>';
                                                            records += '<td>' + value.destino + '</td>';
                                                            records += '<td>' + value.fecha_salida + '</td>';
                                                            records += '<td>' + value.destinatario + '</td>';
                                                            records += '<td>' + value.vigencia + '</td>';

                                                            var clase = "text-primary";
                                                            if (value.hora_salida === "NULL") {
                                                                clase = "text-danger";
                                                            }
                                                            records += '<td class="text-danger ' + clase + '">' + value.hora_salida + '</td>';
                                                            records += '<td>' + value.persona_salida_nombre + '</td>';
                                                            records += '<td><a href="#" title="Registrar regreso" class="text-info" onclick="showModal2(' + value.id + ');"><i class="fa fa-check-circle-o"></i>Registrar regreso</a></td>';
//                                                                    records += '<td>' + value.vigencia + ' d&iacute;as</td>';
//                                                                    records += '<td><a href="../documentos_salida/' + value.archivo + '" target="_blank">' + value.archivo + '</a></td>';

                                                            records += '</tr>';
                                                        });
                                                        $("#dirac_outputs_return").append(records);
                                                        $("#dirac_BI_2").dataTable({
                                                            "dom": 'Bfrtip',
                                                            "buttons": [
                                                                'colvis', 'csv', 'excel', 'pdf', 'print'
//                            'excel', 'pdf', 'print'
                                                            ],
                                                            "bPaginate": true,
                                                            "bLengthChange": true,
                                                            "bFilter": true,
                                                            "bSort": true,
                                                            "bInfo": true,
                                                            "bAutoWidth": false,
                                                            "oLanguage": {
                                                                "sProcessing": "Procesando...",
                                                                "sLengthMenu": "Mostrar _MENU_ registros",
                                                                "sZeroRecords": "No se encontraron resultados",
                                                                "sEmptyTable": "Ningún dato disponible en esta tabla",
                                                                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                                                                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                                                                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                                                                "sInfoPostFix": "",
                                                                "sSearch": "Buscar:",
                                                                "sUrl": "",
                                                                "sInfoThousands": ",",
                                                                "sLoadingRecords": "Cargando...",
                                                                "sButtonText": "Imprimir",
                                                                "oPaginate": {
                                                                    "sFirst": "Primero",
                                                                    "sLast": "Último",
                                                                    "sNext": "Siguiente",
                                                                    "sPrevious": "Anterior"
                                                                },
                                                                "buttons": {
                                                                    "print": "Imprimir",
                                                                    "colvis": "Columnas mostradas"
                                                                }
                                                            }
                                                        });
                                                    } else {
                                                        $("#msg").html("Ha ocurrido un error, por favor intente m&aacute;s tarde.");
                                                    }
                                                }, 'json');
                                    }
    </script>

</body>
</html>