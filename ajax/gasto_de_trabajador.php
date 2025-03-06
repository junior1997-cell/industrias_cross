<?php
ob_start();
if (strlen(session_id()) < 1) { session_start(); }//Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['gastos_trabajador'] == 1) {

    require_once "../modelos/Gasto_de_trabajador.php";
    $gasto_trab = new Gasto_de_trabajador();

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idgasto_de_trabajador  = isset($_POST["idgasto_de_trabajador"]) ? limpiarCadena($_POST["idgasto_de_trabajador"]) : "";

    $idtrabajador      = isset($_POST["idtrabajador"]) ? limpiarCadena($_POST["idtrabajador"]) : "";
    $descr_gastos      = isset($_POST["descr_gastos"]) ? limpiarCadena($_POST["descr_gastos"]) : "";
    $tipo_comprobante  = isset($_POST["tipo_comprobante"]) ? limpiarCadena($_POST["tipo_comprobante"]) : "";
    $serie_comprobante = isset($_POST["serie_comprobante"]) ? limpiarCadena($_POST["serie_comprobante"]) : "";
    $fecha             = isset($_POST["fecha"]) ? limpiarCadena($_POST["fecha"]) : "";
    $idproveedor       = isset($_POST["idproveedor"]) ? limpiarCadena($_POST["idproveedor"]) : "";
    $precio_sin_igv    = isset($_POST["precio_sin_igv"]) ? limpiarCadena($_POST["precio_sin_igv"]) : "";
    $igv               = isset($_POST["igv"]) ? limpiarCadena($_POST["igv"]) : "";
    $val_igv           = isset($_POST["val_igv"]) ? limpiarCadena($_POST["val_igv"]) : "";
    $precio_con_igv    = isset($_POST["precio_con_igv"]) ? limpiarCadena($_POST["precio_con_igv"]) : "";
    $descr_comprobante = isset($_POST["descr_comprobante"]) ? limpiarCadena($_POST["descr_comprobante"]) : "";
    $img_comprob       = isset($_POST["doc_old_1"]) ? limpiarCadena($_POST["doc_old_1"]) : "";


    switch ($_GET["op"]){

      case 'guardar_editar':
        //guardar img_comprob fondo
        if ( !file_exists($_FILES['doc1']['tmp_name']) || !is_uploaded_file($_FILES['doc1']['tmp_name']) ) {
          $img_comprob = $_POST["doc_old_1"];
          $flat_img = false; 
        } else {          
          $ext = explode(".", $_FILES["doc1"]["name"]);
          $flat_img = true;
          $img_comprob = $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext);
          move_uploaded_file($_FILES["doc1"]["tmp_name"], "../assets/modulo/gasto_de_trabajador/" . $img_comprob);          
        }

        if ( empty($idgasto_de_trabajador) ) { #Creamos el registro

          $rspta = $gasto_trab->insertar($idtrabajador, $descr_gastos, $tipo_comprobante, $serie_comprobante, 
          $fecha, $idproveedor, $precio_sin_igv, $igv, $val_igv, $precio_con_igv, $descr_comprobante, $img_comprob);
          echo json_encode($rspta, true);

        } else { # Editamos el registro

          $rspta = $gasto_trab->editar($idgasto_de_trabajador, $idtrabajador, $descr_gastos, $tipo_comprobante, $serie_comprobante, 
          $fecha, $idproveedor, $precio_sin_igv, $igv, $val_igv, $precio_con_igv, $descr_comprobante, $img_comprob);
          echo json_encode($rspta, true);
        }


      break;

      case 'listar_tabla':
        $rspta = $gasto_trab->listar_tabla();
        $data = []; $count = 1;

        if($rspta['status'] == true){

          // foreach($rspta['data'] as $key => $value){
          while ($reg = $rspta['data']->fetch_object()) {

            // -------------- CONDICIONES --------------      
            $img = empty($reg->foto_perfil_trabajador) ? 'no-perfil.jpg'  : $reg->foto_perfil_trabajador;
            $data[] = [
              "0" => $count++,
              "1" =>  '<div class="hstack gap-2 fs-15">' .
                '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_editar_gdt('.($reg->idgasto_de_trabajador).')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                '<button  class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="eliminar_gasto('.$reg->idgasto_de_trabajador.', \''.$reg->trabajador.'\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'.
                '<button class="btn btn-icon btn-sm btn-info-light" onclick="mostrar_detalles_gasto('.($reg->idgasto_de_trabajador).')" data-bs-toggle="tooltip" title="Ver"><i class="ri-eye-line"></i></button>'.
              '</div>',
              "2" => ($reg->fecha_ingreso),
              "3" => '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">
                  <span class="avatar"> <img src="../assets/modulo/persona/perfil/'.$img.'" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml($reg->trabajador ) . '\')"> </span>
                </div>
                <div>
                  <span class="d-block fw-semibold text-primary">'.$reg->trabajador.'</span>
                  <span class="text-muted">'.$reg->tipo_documento_nombre .' '. $reg->numero_documento.'</span>
                </div>
              </div>',
              "4" => $reg->tipo_comprobante .': '. $reg->serie_comprobante,
              "5" => $reg->precio_con_igv,
              "6" => '<textarea class="textarea_datatable bg-light"  readonly>' .($reg->descripcion_comprobante). '</textarea>',
              "7" => !empty($reg->comprobante) ? '<div class="d-flex justify-content-center"><button class="btn btn-icon btn-sm btn-info-light" onclick="mostrar_comprobante('.($reg->idgasto_de_trabajador).');" data-bs-toggle="tooltip" title="Ver"><i class="ti ti-file-dollar fs-lg"></i></button></div>' : 
                '<div class="d-flex justify-content-center"><button class="btn btn-icon btn-sm btn-danger-light" data-bs-toggle="tooltip" title="no encontrado"><i class="ti ti-file-alert fs-lg"></i></button></div>',
              
              "8" => $reg->trabajador,
              "9" => $reg->tipo_documento_nombre,
              "10" => $reg->numero_documento,
              "11" => $reg->proveedor,
              "12" => $reg->name_day,
              "13" => $reg->name_month,
              "14" => $reg->precio_sin_igv,
              "15" => $reg->precio_igv,
              "16" => $reg->descripcion_gasto,
              "17" => $reg->descripcion_comprobante,
            ];
          }
          $results =[
            'status'=> true,
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
          ];
          echo json_encode($results, true);

        } else { echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data']; }
      break;

      case 'desactivar':
        $rspta = $gasto_trab->desactivar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $gasto_trab->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'listar_trabajador':
        $rspta = $gasto_trab->listar_trabajador(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idpersona_trabajador']  . '>' . $value['nombre_razonsocial'] . ' '. $value['apellidos_nombrecomercial'] . ' - ' . $value['numero_documento']. '</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break;

      case 'listar_proveedor':
        $rspta = $gasto_trab->listar_proveedor(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idpersona']  . '>' . $value['tipo_persona'] . ': ' . $value['nombre'] . ' '. $value['apellido'] . ' - '. $value['numero_documento'] . '</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => '<option  value="2" >PROVEEDOR VARIOS</option>'.$data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break; 

      case 'mostrar_editar_gdt':
        $rspta = $gasto_trab->mostrar_editar_gdt($idgasto_de_trabajador);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_detalle_gasto':
        $rspta = $gasto_trab->mostrar_detalle_gasto($idgasto_de_trabajador);
        $img_t = empty($rspta['data']['foto_perfil_trabajador']) ? 'no-perfil.jpg'  : $rspta['data']['foto_perfil_trabajador'];
        $img_p = empty($rspta['data']['foto_perfil_proveedor']) ? 'no-perfil.jpg'  : $rspta['data']['foto_perfil_proveedor'];
        $nombre_doc = $rspta['data']['tipo_comprobante'] .' ' .$rspta['data']['serie_comprobante'];
        $html_table = '
        <div class="my-3" ><span class="h6"> Datos del Trabajador </span></div>
        <table class="table text-nowrap table-bordered">        
          <tbody>
            <tr>
              <th scope="col">Trabajador</th>
              <th scope="row">
                <div class="d-flex align-items-center">
                  <span class="avatar avatar-xs me-2 online avatar-rounded"> <img src="../assets/modulo/persona/perfil/'.$img_t.'" alt="img"> </span>
                  '.$rspta['data']['trabajador'].'
                </div>
              </th>            
            </tr>              
            <tr>
              <th scope="col">'.$rspta['data']['tipo_documento_nombre_t'].'</th>
              <th scope="row">'.$rspta['data']['numero_documento_t'].'</th>
            </tr> 
            <tr>
              <th scope="col">Descripción</th>
              <th scope="row">'.$rspta['data']['descripcion_gasto'].'</th>
            </tr>                  
          </tbody>
        </table>
        <div class="my-3" ><span class="h6"> Datos del comprobante </span></div>
        <table class="table text-nowrap table-bordered">        
          <tbody>
            <tr>
              <th scope="col">Proveedor</th>
              <th scope="row">
                <div class="d-flex align-items-center">
                  <span class="avatar avatar-xs me-2 online avatar-rounded"> <img src="../assets/modulo/persona/perfil/'.$img_p.'" alt="img"> </span>
                  '.$rspta['data']['proveedor'].'
                </div>
              </th>            
            </tr>    
            <tr>
              <th scope="col">'.$rspta['data']['tipo_documento_nombre_p'].'</th>
              <th scope="row">'.$rspta['data']['numero_documento_p'].'</th>
            </tr> 
            <tr>
              <th scope="col">'.$rspta['data']['tipo_comprobante'].'</th>
              <th scope="row">'.$rspta['data']['serie_comprobante'].'</th>
            </tr>  
            <tr>
              <th scope="col">Fecha</th>
              <th scope="row">'.$rspta['data']['fecha_ingreso_f'].' | '.$rspta['data']['name_day'].' | '.$rspta['data']['name_month'].'</th>
            </tr>    
            <tr>
              <th scope="col">Subtotal</th>
              <th scope="row">'. number_format($rspta['data']['precio_sin_igv'], 2, '.', ',') .'</th>
            </tr> 
            <tr>
              <th scope="col">IGV</th>
              <th scope="row">'.number_format($rspta['data']['precio_igv'], 2, '.', ',') .'</th>
            </tr>  
            <tr>
              <th scope="col">Total</th>
              <th scope="row">'.number_format($rspta['data']['precio_con_igv'], 2, '.', ',') .'</th>
            </tr>
            <tr>
              <th scope="col">Descripción</th>
              <th scope="row">'.$rspta['data']['descripcion_comprobante'].'</th>
            </tr>                 
          </tbody>
        </table> 
        <div class="my-3" ><span class="h6"> Comprobante </span></div>';
        $rspta = ['status' => true, 'message' => 'Todo bien', 'data' => $html_table, 'comprobante' => $rspta['data']['comprobante'], 'nombre_doc'=> $nombre_doc];
        echo json_encode($rspta, true);
      break;

      default:
        $rspta = ['status' => 'error_code', 'message' => 'Te has confundido en escribir en el <b>swich.</b>', 'data' => []];
        echo json_encode($rspta, true);
      break;

      // case "select2TipoTrabajador":

      //   $rspta = $ajax_general->select2_tipo_trabajador(); $cont = 1; $data = "";

      //   if ($rspta['status'] == true) {

      //     foreach ($rspta['data'] as $key => $value) {

      //       $data .= '<option  value=' . $value['idtipo_trabajador']  . '>' . $value['nombre'] . '</option>';
      //     }

      //     $retorno = array(
      //       'status' => true, 
      //       'message' => 'Salió todo ok', 
      //       'data' => '<option  value="1" >NINGUNO</option>'.$data, 
      //     );

      //     echo json_encode($retorno, true);

      //   } else {

      //     echo json_encode($rspta, true); 
      //   }        
      // break;

    }

  } else {
    $retorno = ['status'=>'nopermiso', 'message'=>'No tienes acceso a este modulo, pide acceso a tu administrador', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }  
}
ob_end_flush();