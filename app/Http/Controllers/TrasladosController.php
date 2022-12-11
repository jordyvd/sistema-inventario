<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\traslados;
use App\sucursal;
use App\almacen;
use App\detalle_traslado;
use App\Movimientos;
use Illuminate\Support\Facades\DB;

class TrasladosController extends Controller
{
    public function sucursales_list($sucursal){
       return sucursal::where('nombre','!=',$sucursal)
       ->get();
    }
    public function listarrequerimientos($sucursal,$fecha){
      if($fecha === "1"){
        $traslados = traslados::where('sucursal',$sucursal)
        ->where('nro','>',0)
        ->where('estado',0)
        ->orderBy('id','DESC')
        ->paginate(10);
      }else{
        $traslados = traslados::where('sucursal',$sucursal)
        ->where('nro','>',0)
        ->where('estado',0)
        ->where('fecha',$fecha)
        ->orderBy('id','DESC')
        ->paginate(6);
      }
        return [
          'paginate' => [
              'total'        => $traslados->total(),
              'current_page' => $traslados->currentPage(),
              'per_page'     => $traslados->perPage(),
              'last_page'    => $traslados->lastPage(),
              'from'         => $traslados->firstItem(),
              'to'           => $traslados->lastPage(),
          ],
          'traslados' => $traslados
      ];
    }
    public function escanear($barra,$sucursal){
       return almacen::select('products.nompro','products.barra','products.marca','almacen.stock_almacen')
       ->join('products','almacen.barra_almacen','products.barra')
       ->where('almacen.sucursal',$sucursal)
       ->where('products.barra',$barra)
       ->orWhere('products.codigo',$barra)
       ->first();
    }
    public function create(Request $request)
    {
        $traslados = new traslados;
        // $traslados->
    }
    public function gennro($sucursal)
    {
      return traslados::where('sucursal',$sucursal)
      ->max('nro');
    }
    public function agregar_numero(Request $request){
      $traslados = new traslados;
      $traslados->nro = $request->nro;
      $traslados->cod_sucursal = $request->cod_sucursal;
      $traslados->sucursal = $request->de;
      $traslados->estado = '0';
      $traslados->para = $request->para;
      $traslados->fecha = date('Y-m-d');
      $traslados->save();
      return back();
    }
    public function agregarRequer(Request $request){
      $detalle = new detalle_traslado;
      $detalle->nro_tras = $request->nro_tras;
      $detalle->barra_tras = $request->barra_tras;
      $detalle->cantidad = $request->cantidad;
      $detalle->de = $request->de;
      $detalle->para = $request->para;
      $detalle->save();
      return back();
    }
    public function detallerequer($nro){
      // return detalle_traslado::select('detalle_traslado.id','detalle_traslado.nro_tras','detalle_traslado.de','products.barra','products.nompro','products.marca','products.codigo','detalle_traslado.cantidad','detalle_traslado.para','almacen.precio_venta')
      // ->join('products','detalle_traslado.barra_tras','products.barra')
      // ->join('almacen','detalle_traslado.de','almacen.sucursal')
      // ->join('almacen','detalle_traslado.barra_tras','almacen.barra_almacen')
      // ->where('detalle_traslado.nro_tras',$nro)
      // ->orderBy('id','DESC')
      // ->get();
      $sql = "select dt.*,al.*,ps.*, dt.id from detalle_traslado dt join products ps on dt.barra_tras = ps.barra
      join almacen al on dt.de = al.sucursal and dt.barra_tras = al.barra_almacen where dt.nro_tras = ?
      order by dt.id desc";
      $parameter = [
        $nro
      ];
      return DB::select($sql,$parameter);
    }
    public function editrequer(Request $request, $id){
      $detalle = detalle_traslado::find($id);
      $detalle->cantidad = $request->cantidad;
      $detalle->save();
      return back();
    }
    public function deleterequer($id){
      $detalle = detalle_traslado::find($id);
      $detalle->delete();
    }
    public function deleterequerimiento($id,$cod){
      $traslado = traslados::find($id);
      $traslado->delete();
      $detalle = detalle_traslado::where('nro_tras',$cod)->delete();
    }
    public function listarpendientes(Request $request,$sucursal,$fecha){
      if($fecha==="1"){
        $pendientes = traslados::where('sucursal',$sucursal)
      ->where('nro','>',0)
      ->orderBy('id','DESC')
      ->paginate(10); 
      }
      else{
        $pendientes = traslados::where('sucursal',$sucursal)
        ->where('fecha',$fecha)
        ->where('nro','>',0)
        ->orderBy('id','DESC')
        ->paginate(8);
      }
      return [
        'paginate' => [
            'total'        => $pendientes->total(),
            'current_page' => $pendientes->currentPage(),
            'per_page'     => $pendientes->perPage(),
            'last_page'    => $pendientes->lastPage(),
            'from'         => $pendientes->firstItem(),
            'to'           => $pendientes->lastPage(),
        ],
        'pendientes' => $pendientes
    ];
    }
    public function buscar_pendientes($sucursal,$search){
      $pendientes = traslados::where('sucursal',$sucursal)
      ->where('nro','like','%'.$search.'%')
      ->paginate(8);
      return [
        'paginate' => [
            'total'        => $pendientes->total(),
            // 'current_page' => $pendientes->currentPage(),
            'per_page'     => $pendientes->perPage(),
            'last_page'    => $pendientes->lastPage(),
            'from'         => $pendientes->firstItem(),
            'to'           => $pendientes->lastPage(),
        ],
        'pendientes' => $pendientes
    ];
    }
    public function estado_pendiente($id){
      $traslado = traslados::find($id);
      $traslado->estado = "1";
      $traslado->save();
      return back();
    }
    public function detalles_pen($nro,$de,$para){
      return detalle_traslado::select('products.barra','products.nompro','detalle_traslado.cantidad','detalle_traslado.para','products.marca','detalle_traslado.id','products.codigo','detalle_traslado.nro_tras')
      ->join('products','detalle_traslado.barra_tras','products.barra')
      ->where('detalle_traslado.nro_tras',$nro)
      ->where('detalle_traslado.de',$de)
      ->where('detalle_traslado.para',$para)
      ->orderBy('id','DESC')
      ->get();
    }
    public function ingresos_requer($sucursal,$fecha){
      if($fecha === "1"){
        $traslado = traslados::where('para',$sucursal)
        ->where('estado',1)
        ->orderBy('id','DESC')
        ->paginate(10);
      }else{
        $traslado = traslados::where('para',$sucursal)
        ->where('estado',1)
        ->where('fecha',$fecha)
        ->orderBy('id','DESC')
        ->paginate(10);
      }
      return [
        'paginate' => [
            'total'        => $traslado->total(),
            'current_page' => $traslado->currentPage(),
            'per_page'     => $traslado->perPage(),
            'last_page'    => $traslado->lastPage(),
            'from'         => $traslado->firstItem(),
            'to'           => $traslado->lastPage(),
        ],
        'ingresos' => $traslado
    ];
    }
    public function buscar_numero_tras($sucursal,$search,$estado){
      $traslado = traslados::where('nro','like','%'.$search.'%')
      ->where('sucursal',$sucursal)
      ->where('estado','=',$estado)
      ->paginate(8);
      return [
        'paginate' => [
            'total'        => $traslado->total(),
            // 'current_page' => $traslado->currentPage(),
            'per_page'     => $traslado->perPage(),
            'last_page'    => $traslado->lastPage(),
            'from'         => $traslado->firstItem(),
            'to'           => $traslado->lastPage(),
        ],
        'ingresos' => $traslado
    ]; 
    }
    public function buscar_numero_ingresos($sucursal,$search,$estado){
      $traslado = traslados::where('nro','like','%'.$search.'%')
      ->where('para',$sucursal)
      ->where('estado','=',$estado)
      ->paginate(8);
      return [
        'paginate' => [
            'total'        => $traslado->total(),
            // 'current_page' => $traslado->currentPage(),
            'per_page'     => $traslado->perPage(),
            'last_page'    => $traslado->lastPage(),
            'from'         => $traslado->firstItem(),
            'to'           => $traslado->lastPage(),
        ],
        'ingresos' => $traslado
    ]; 
    }
    public function stock_pendiente(Request $request,$sucursal){
      DB::beginTransaction();
      if(count($request['ArrayDate']) == 0){
        return response()->json(["message" => "algo salio mal"], 500);
      }
      foreach($request['ArrayDate'] as $value){
          $stock_almacen = almacen::where('barra_almacen',$value['barra'])
          ->where('sucursal',$sucursal)
          ->first();
          $almacen = almacen::find($stock_almacen->id);
          $almacen->stock_almacen = intval($stock_almacen->stock_almacen) - intval($value['cantidad']);
          $almacen->save();
          //movimiento
          $movimiento = new Movimientos;
          $movimiento->nro_documento = $value['nro_tras'];
          $movimiento->barra_mov = $value['barra'];
          $movimiento->precio = $almacen->precio_venta;
          $movimiento->condicion = "salida";
          $movimiento->fecha = date('Y-m-d');
          $movimiento->detalle = "enviado para ".$value['para'];
          $movimiento->tipo = "traslado";
          $movimiento->cantidad = $value['cantidad'];
          $movimiento->sucursal = $sucursal;
          $movimiento->save();
      }
      $this->estado_pendiente($request->traslado_id);
      DB::commit();
      return back();
    }
    public function stock_ingresos_tras(Request $request,$sucursal){
      DB::beginTransaction();
      if(count($request['ArrayDate']) == 0){
        return response()->json(["message" => "algo salio mal"], 500);
      }
      foreach($request['ArrayDate'] as $value){
          $stock_almacen = almacen::where('barra_almacen',$value['barra'])
          ->where('sucursal',$sucursal)
          ->first();
          if(empty($stock_almacen)){
            $stock_recibido = almacen::where('barra_almacen',$value['barra'])
            ->where('sucursal',$value['de'])
            ->first();
            $almacen = new almacen;
            $almacen->barra_almacen = $value['barra'];
            $almacen->stock_almacen = $value['cantidad'];
            $almacen->precio_venta =  $stock_recibido->precio_venta;
            $almacen->precio_mayor = $stock_recibido->precio_mayor;
            $almacen->sucursal = $sucursal;    
            $almacen->save();
            $precio = $stock_recibido->precio_venta;
          }else{
            $almacen = almacen::find($stock_almacen->id);
            $almacen->stock_almacen = intval($stock_almacen->stock_almacen) + intval($value['cantidad']);
            $almacen->save();
            $precio = $almacen->precio_venta;
          }
          // ******
          $movimiento = new Movimientos;
          $movimiento->nro_documento = $value['nro_tras'];
          $movimiento->barra_mov = $value['barra'];
          $movimiento->precio = $precio;
          $movimiento->condicion = "ingreso";
          $movimiento->fecha = date('Y-m-d');
          $movimiento->detalle = "recibido de ".$value['de'];
          $movimiento->tipo = "traslado";
          $movimiento->cantidad = $value['cantidad'];
          $movimiento->sucursal = $sucursal;
          $movimiento->save();
      }
      $this->estado_ingresos($request->traslado_id);
      DB::commit();
      return back();
    }
    public function estado_ingresos($id){
      $traslado = traslados::find($id);
      $traslado->recibido = "1";
      $traslado->save();
    }
    public function guardarDescripcion(Request $request)
    {
      $statement = 'update traslados set descripcion = ? where id = ?';
      $parameter = [
        $request->descripcion,
        $request->id
      ];
      DB::statement($statement, $parameter);
    }
}
