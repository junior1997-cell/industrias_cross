<?php
//Activamos el almacenamiento en el buffer
ob_start();
date_default_timezone_set('America/Lima'); require "../config/funcion_general.php";
session_start();
if (!isset($_SESSION["user_nombre"])) {
  header("Location: index.php?file=" . basename($_SERVER['PHP_SELF']));
} else {

?>
  <!DOCTYPE html>
  <html lang="es" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="icon-overlay-close">

  <head>
    <?php $title_page = "Tipo de Tributo";
    include("template/head.php"); ?>
  </head>

  <body id="body-tipo-de-tribujos">
    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>

      <!-- Start::app-content -->
      <div class="main-content app-content">
        <div class="container-fluid">

          <!-- Start::page-header -->
          <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
              <div class="d-md-flex d-block align-items-center ">
                <button type="button" class="btn-modal-effect btn btn-primary label-btn m-r-10px" data-bs-toggle="modal" data-bs-target="#modal-tp-tributo" onclick="limpiar_form();"><i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                <div>
                  <p class="fw-semibold fs-18 mb-0">Tipo de Tributos</p>
                  <span class="fs-semibold text-muted">Administra los gastos del trabajador.</span>
                </div>
              </div>
            </div>
            <div class="btn-list mt-md-0 mt-2">
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Tipo de Tributos</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Administracion</li>
                </ol>
              </nav>
            </div>
          </div>
          <!-- End::page-header -->

          <!-- Start::row-1 -->
          <div class="row">
            <div class="col-xxl-12 col-xl-12">
              <div>
                <div class="card custom-card">
                  <div class="card-body">
                    <!-- Tabla Tipo de Tributos -->
                    <div class="table-responsive" id="div-tabla">
                      <table class="table table-bordered w-100" style="width: 100%;" id="tabla-tp-tributos">
                        <thead>
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Acciones</th>
                            <th>Código SUNAT</th>
                            <th>Nombre</th>
                            <th>Código UNECE</th>
                            <th>Estado</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Acciones</th>
                            <th>Código SUNAT</th>
                            <th>Nombre</th>
                            <th>Código UNECE</th>
                            <th>Estado</th>
                          </tr>
                        </tfoot>

                      </table>

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End::row-1 -->
      
        </div>
      </div>
      <!-- End::app-content -->

      <!-- Start::Modal-registrar-tipo-tributo -->
      <div class="modal fade modal-effect" id="modal-tp-tributo" role="dialog" tabindex="-1" aria-labelledby="modal-tp-tributoLabel">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h6 class="modal-title" id="modal-tp-tributoLabel1"></h6>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form name="formulario-tp-tributo" id="formulario-tp-tributo" method="POST" class="row needs-validation" novalidate>
                <div class="row gy-2" id="cargando-1-fomulario">
                  <input type="hidden" name="idsunat_tipo_tributo" id="idsunat_tipo_tributo">

                  <div class="col-md-4">
                    <div class="form-label">
                      <label for="codg_sunat" class="form-label">Codigo SUNAT(*)</label>
                      <input type="number" class="form-control" name="codg_sunat" id="codg_sunat" />
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-label">
                      <label for="nombre" class="form-label">Nombre(*)</label>
                      <input type="text" class="form-control" name="nombre" id="nombre" onkeyup="mayus(this);"/>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="codg" class="form-label">Código(*)</label>
                      <input type="text" class="form-control" name="codg" id="codg" onkeyup="mayus(this);"/>
                    </div>
                  </div>
                </div>
                <div class="row" id="cargando-2-fomulario" style="display: none;">
                  <div class="col-lg-12 text-center">
                    <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                    <h4 class="bx-flashing">Cargando...</h4>
                  </div>
                </div>
                <button type="submit" style="display: none;" id="submit-form-tp-tributo">Submit</button>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form();"><i class="las la-times fs-lg"></i> Close</button>
              <button type="button" class="btn btn-primary btn-guardar" id="guardar_registro_tp_tributo"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
            </div>
          </div>
        </div>
      </div> 
      <!-- End::Modal-registrar-tipo-tributo -->

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

    <script src="scripts/tipo_de_tributos.js?version_jdl=1.38"></script>
    <script>
      $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
      });
    </script>


  </body>



  </html>
<?php
}
ob_end_flush();
?>