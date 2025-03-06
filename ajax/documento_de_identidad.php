<?php
ob_start();
if (strlen(session_id()) < 1) { session_start(); }
require_once "../modelos/Documento_de_identidad.php";
$doc_identidad = new Documento_de_identidad();

date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
$imagen_error = "this.src='../dist/svg/404-v2.svg'";
$toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

$idsunat_c06_doc_identidad  = isset($_POST["idsunat_c06_doc_identidad"]) ? limpiarCadena($_POST["idsunat_c06_doc_identidad"]) : "";

$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$abreviatura   = isset($_POST["abrt"]) ? limpiarCadena($_POST["abrt"]) : "";
$codigo   = isset($_POST["codg"]) ? limpiarCadena($_POST["codg"]) : "";


switch ($_GET["op"]){

  case 'listar_tabla':
    $rspta = $doc_identidad->listar_tabla();
    $data = []; $count = 1;
    if($rspta['status'] == true){
      foreach($rspta['data'] as $key => $value){
        $data[]=[
          "0" => $count++,
          "1" =>  '<div class="hstack gap-2 fs-15">' .
                    '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_doc_didentidad('.($value['idsunat_c06_doc_identidad']).')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                    '<button  class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="eliminar_papelera_doc_didentidad('.$value['idsunat_c06_doc_identidad'].'.,\''.$value['nombre'].'\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'.
                  '</div>',
          "2" => ($value['nombre']),
          "3" => ($value['abreviatura']),
          "4" => ($value['code_sunat']),
          "5" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'
        ];
      }
      $results =[
        'status'=> true,
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
      ];
      echo json_encode($results);

    } else { echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data']; }
  break;

  case 'guardar_editar':
    $validar = $doc_identidad->validar($nombre);
      
    if (empty($idsunat_c06_doc_identidad )) {

      if(empty($validar["data"])){

        $rspta = $doc_identidad->insertar($nombre, $abreviatura,  $codigo);
        echo json_encode(['status' => 'registrado', 'data' => $rspta]);

      }else{
        $info_repetida = '';

        foreach ($validar["data"] as $key => $value) {
          $info_repetida .= '
          <div class="row">
            <div class="col-md-12 text-left">
              <span class="font-size-15px text-danger"><b>Doc. de Identidad: </b>' . $value['nombre'] .  '</span>
              ' . ($value['estado'] == 1 ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Inhabilitado').'</span><br>
            </div>
            <div class="col-md-12 text-left">
              <b>Papelera: </b>' . ($value['estado'] == 0 ? '<i class="fas fa-check text-success"></i> SI' : '<i class="fas fa-times text-danger"></i> NO') . ' <b>|</b>
              <b>Eliminado: </b>' . ($value['estado_delete'] == 0 ? '<i class="fas fa-check text-success"></i> SI' : '<i class="fas fa-times text-danger"></i> NO') . '<br>
              
            </div>
          </div>';
        }
        echo json_encode(['status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>' . $info_repetida . '</ul>', 'id_tabla' => '']);
      }

    } else {
      $rspta = $doc_identidad->editar($idsunat_c06_doc_identidad, $nombre, $abreviatura,  $codigo);
      echo json_encode(['status' => 'modificado', 'data' => $rspta]);
    }
    
  break;

  case 'mostrar_doc_didentidad':
    $rspta = $doc_identidad->mostrar($idsunat_c06_doc_identidad );
    echo json_encode($rspta, true);
  break;

  case 'eliminar':
    $rspta = $doc_identidad->eliminar($_GET["id_tabla"]);
    echo json_encode($rspta, true);
  break;

  case 'desactivar':
    $rspta = $doc_identidad->desactivar($_GET["id_tabla"]);
    echo json_encode($rspta, true);
  break;

  default: 
    $rspta = ['status'=>'error_code', 'message'=>'Te has confundido en escribir en el <b>swich.</b>', 'data'=>[]]; echo json_encode($rspta, true); 
  break;
}

ob_end_flush();