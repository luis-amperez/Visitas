<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VisitaController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth:sanctum');
  }

    public function visitas(Request $request){

      $response["status"] = 1;
      $response["msg"] = '';



     if (Auth::User()->can('listarVisitas')){
      
      $visitas = Visita::query();
      $visitas->select("visitas.id", "visitas.correo_cliente", "cl.nombre as nombre_cliente", "us.name as nombre_tecnico", "visitas.fecha_inicio", "visitas.fecha_final");
      $visitas->join('users as us','us.id','visitas.id_tecnico');
      $visitas->join('clientes as cl','cl.id','visitas.id_cliente');
      


      if(Auth::User()->hasRole('Tecnico')){

        $visitas->where('us.id',Auth::User()->id);
      }
          $visitas = $visitas->get();
          $response["status"] = 200;
          $response["msg"] = 'Exito';
          $response['data'] = $visitas;
          return response()->json($response, 200);

     }else{
          $response["msg"] = 'Permiso denegado, comuniquese con su adminstrador';
          return response()->json($response, 401);
     }
      //$users = Hash::make('Admin1234');


      return response()->json($visitas, 200);
      //return response(["mensaje"=>'Ok'], $users,Response::HTTP_OK);
  }

  public function crearVisita(Request $request){

    $response["status"] = 1;
    $response["msg"] = '';
    $correo_cliente = $request->correo_cliente;
    $id_cliente = $request->id_cliente;
    $id_tecnico = $request->id_tecnico;
    $fecha_inicio = $request->fecha_inicio;
    $fecha_final = $request->fecha_final;

if(Auth::User()->can('listarVisitas')){
    DB::connection('mysql')->beginTransaction();
    try {
       Visita::create(['correo_cliente' => $correo_cliente, 'id_cliente' => $id_cliente, 'id_tecnico' => $id_tecnico, 'fecha_inicio' => $fecha_inicio, 'fecha_final' => $fecha_final]);
        DB::connection('mysql')->commit();
        $response["status"]  = 200;
        $response["msg"] = 'Visita creada con exito';
         $visitas = Visita::all();
         $response['data'] = $visitas;
        return response()->json($response, 200);
    } catch (\Throwable $th) {
        DB::connection('mysql')->rollBack();
        $response["status"]  = 404;
        $response["msg"] = $th->getMessage();
        return response()->json($response, 404);
    }
  
}else{
  $response["status"]  = 404;
  $response["msg"] = "permiso denegado";
  return response()->json($response, 404);
}

}

public function eliminarVisitas($id){
  //$response = new StdClass();
  $response["status"] = 1;
  $response["msg"] = '';
  $visitas = visita::where('id',$id)->first();

if(Auth::User()->can('listarVisitas')){
  DB::connection('mysql')->beginTransaction();
  try {
      if (empty($visitas)){

          $response["status"]  = 200;
          $response["msg"] = 'Visita no existe';
           $visitas = visita::all();
           $response['data'] = $visitas;
          return response()->json($response, 200);
      }else{
          $visitas->delete();
          DB::connection('mysql')->commit();
          $response["status"]  = 200;
          $response["msg"] = 'Visita eliminada exitosamente';
           $visitas = visita::all();
           $response['data'] = $visitas;
          return response()->json($response, 200);
      }
  } catch (\Throwable $th) {
      DB::connection('mysql')->rollBack();
      $response["status"]  = 404;
      $response["msg"] = $th->getMessage();
      return response()->json($response, 404);
  }

}else{
$response["status"]  = 404;
$response["msg"] = "permiso denegado";
return response()->json($response, 404);
}
return response()->json($response); 
}


}