<!-- INCLUCIONES GENERALES DE OPENCART -->
<?php echo $header; ?><?php echo $column_left; ?>


<div id="content">

    <!-- CREACION DE LA CABECERA DE CONFIGURACION -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <!-- SE AGREGA EL TOOLTIP DEL BOTON DE GUARDAR CONFIGURACION -->
                <button type="submit" form="form-paypoint" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <!-- SE AGREGA EL TOOLTIP DEL BOTON DE CANCELAR CONFIGURACION-->
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
            </div>

            <!--  SE AGREGA EL TITULO AL HEADER DE LA CONFIGRACION -->
            <h1><?php echo $heading_title; ?></h1>

            <!-- SE CREAN LOS BREADCRUMS -->
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">

        <!-- DISPLAY WARNINGS -->
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i><?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <div class="panel panel-default">

            <!-- CABECERA DE SECCION -->
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>

            <!-- CUERPO DEL FORMULARIO DE CONFIGURACION -->
            <div class="panel-body">
                <?php if($hook_error){ ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="alert alert-warning" role="alert">
                            <?php echo $hook_error_text; ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="row">
                    <div class="col-sm-12">

                        <ul id="myTabs" class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#home" aria-controls="home" role="tab" data-toggle="tab">
                                    <?php echo $tab_plugin_configurations; ?>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal" id="form-compropago">

                                <!-- TAB PLUGIN CONFIGURATIONS -->
                                <div role="tabpanel" class="tab-pane active" id="home">


                                    <!-- STATUS -->
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                                        <div class="col-sm-10">
                                            <select name="compropago_status" id="input-status" class="form-control">
                                                <?php if (!empty($compropago_status)) { ?>
                                                    <option value="1" <?php echo $compropago_status ? "selected" : ""; ?> ><?php echo $text_enabled; ?></option>
                                                    <option value="0" <?php echo !$compropago_status ? "selected" : ""; ?> ><?php echo $text_disabled; ?></option>
                                                <?php } else { ?>
                                                    <option value="1" ><?php echo $text_enabled; ?></option>
                                                    <option value="0" selected ><?php echo $text_disabled; ?></option>
                                                <?php } ?>
                                            </select>
                                            <input type="hidden" name="compropago_sort_order" value="1" placeholder="1" id="input-sort-order" class="form-control" />
                                        </div>
                                    </div>

                                    <!-- LLAVE PUBLICA -->
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="entry-public-key">
                                            <span data-toggle="tooltip" title="<?php echo $help_public_key; ?>"><?php echo $entry_public_key; ?></span>
                                        </label>
                                        <div class="col-sm-10">
                                            <input type="text" name="compropago_public_key" value="<?php echo $compropago_public_key; ?>" placeholder="<?php echo $entry_public_key; ?>" id="entry-public-key" class="form-control"/>
                                            <?php if ($error_public_key) { ?>
                                                <div class="text-danger"><?php echo $error_public_key; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <!--  LLAVE PRIVADA -->
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="entry-private-key"><span data-toggle="tooltip" title="<?php echo $help_private_key; ?>"><?php echo $entry_private_key; ?></span></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="compropago_private_key" value="<?php echo $compropago_private_key; ?>" placeholder="<?php echo $entry_private_key; ?>" class="form-control" id="entry-private-key" />
                                            <?php if ($error_private_key) { ?>
                                                <div class="text-danger"><?php echo $error_private_key; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <!-- SELECCION DE MODO DE PRUEVAS O ACTIVO -->
                                    <div class="form-group">
                                        <label for="entry-mode" class="col-sm-2 control-label">
                                            <span data-toggle="tooltip" title="<?php echo $help_mode; ?>">
                                                <?php echo $entry_mode; ?>
                                            </span>
                                        </label>

                                        <div class="col-sm-10">
                                            <select name="compropago_mode" id="" class="form-control">
                                                <?php if(!empty($compropago_mode)){ ?>
                                                    <option value="SI" <?php echo ($compropago_mode == "SI") ? "selected" : ""; ?> ><?php echo $entry_select_mode_true; ?></option>
                                                    <option value="NO" <?php echo ($compropago_mode == "NO") ? "selected" : ""; ?> ><?php echo $entry_select_mode_false; ?></option>
                                                <?php }else{ ?>
                                                    <option value="SI"><?php echo $entry_select_mode_true; ?></option>
                                                    <option value="NO" selected><?php echo $entry_select_mode_false; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- SELECCIONA SI QUIERE HABILITAR LA GEOLOCALIZACION -->
                                    <div class="form-group">
                                        <label for="entry-location" class="col-sm-2 control-label">
                                            <span data-toggle="tooltip" title="<?php echo $help_location; ?>">
                                                <?php echo $entry_location; ?>
                                            </span>
                                        </label>

                                        <div class="col-sm-10">
                                            <select name="compropago_location" id="" class="form-control">
                                                <?php if(!empty($compropago_location)){ ?>
                                                    <option value="SI" <?php echo ($compropago_location == "SI") ? "selected" : ""; ?> ><?php echo $entry_select_location_true; ?></option>
                                                    <option value="NO" <?php echo ($compropago_location == "NO") ? "selected" : ""; ?> ><?php echo $entry_select_location_false; ?></option>
                                                <?php }else{ ?>
                                                    <option value="SI"><?php echo $entry_select_location_true; ?></option>
                                                    <option value="NO" selected><?php echo $entry_select_location_false; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- SHOW LOGOS -->
                                    <div class="form-group">
                                        <label for="compropago_showlogo" class="col-sm-2 control-label"><?php echo $entry_showlogo; ?></label>
                                        <div class="col-sm-10">
                                            <select name="compropago_showlogo" id="compropago_showlogo" class="form-control">
                                                <?php if(!empty($compropago_showlogo)){ ?>
                                                    <option value="yes" <?php echo ($compropago_showlogo == "yes") ? "selected" : ""; ?> >Yes</option>
                                                    <option value="no" <?php echo ($compropago_showlogo == "no") ? "selected" : ""; ?> >No</option>
                                                <?php }else{ ?>
                                                    <option value="yes" selected>Yes</option>
                                                    <option value="no">No</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php
                                        if($_SERVER['HTTPS']){
                                            $http = "https://";
                                        }else{
                                            $http = "http://";
                                        }
                                        $uri = explode("admin/index.php",$_SERVER["REQUEST_URI"]);
                                        $uri = $uri[0];
                                    ?>
                                    <input type="hidden" name="compropago_webhook" id="webhook" value="<?php echo $http . $_SERVER['SERVER_NAME'].$uri."index.php?route=extension/payment/compropago/webhook";?>"> 
                                    </div>

                                    
                                </div> 
                                </div>

                            </form>

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<script>
    $('#myTabs a').click(function (e) {
        e.preventDefault();
        var link = e.target.getAttribute("href");

        cleanTabs();

        $(link).css("display","block");
    });

    function cleanTabs(){
        $("#home").css("display","none");
        $("#display").css("display", "none");
        $("#estatus").css("display", "none");
    }
</script>

<?php echo $footer; ?>