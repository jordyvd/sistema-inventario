<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Proveedor;
use App\detalle_notas;
use App\almacen;
use App\Notas;
use App\product;
use App\Movimientos;
use Illuminate\Support\Facades\DB;

class NotasController extends Controller
{
   public function crearempresa(Request $request){
      $proveedor = new proveedor;
      $proveedor->ruc = $request->ruc;
      $proveedor->nombre = $request->nombre;
      $proveedor->save();
      return back();
   }
   public function listarproveedor(){
      $proveedor = proveedor::orderBy('id','DESC')->paginate(8);
      return [
         'paginate' => [
              'total'        => $proveedor->total(),
              'current_page' => $proveedor->currentPage(),
              'per_page'     => $proveedor->perPage(),
              'last_page'    => $proveedor->lastPage(),
              'from'         => $proveedor->firstItem(),
              'to'           => $proveedor->lastPage(),
         ],
         'proveedor' => $proveedor
      ];
   }
   public function deleteproveedor($id){
      $proveedor = proveedor::find($id);
      $proveedor->delete();
   }
   public function buscarproveedor($ruc){
      return proveedor::where('ruc',$ruc)->first();
   }
   public function agregar_nota(Request $request){
      $nota = new Notas;
      $nota->codigo_nota = $request->cod_nota;
      $nota->nro = $request->nro;
      $nota->sucursal = $request->sucursal;
      $nota->total = $request->total;
      $nota->ruc_provee = $request->ruc;
      $nota->empresa = $request->empresa;
      $nota->fecha = date('Y-m-d');
      $nota->estado = '0';
      $nota->anulado = '0';
      $nota->save();
      return back();
   }
   public function agregar_detalle_nota(Request $request){
      foreach($request['ArrayDate'] as $value){
         $detalle = new detalle_notas;
         $detalle->barra_nota = $value['barra'];
         $detalle->precio_com = $value['precio'];
         $detalle->descuento = $value['descuento'];
         $detalle->cantidad = $value['cantidad'];
         $detalle->cod_nota = $value['cod_nota'];
         $detalle->save();
      }
      return back();
   }
   public function codigo_generado($sucursal){
      return Notas::where('sucursal',$sucursal)->max('nro');
   }
   public function listar_pedidos($sucursal,$fecha){
      if($fecha === "1"){
         $notas = Notas::where('sucursal',$sucursal)
         ->where('nro','>',0)
         ->where('anulado',0)
         ->orderBy('id','DESC')->paginate(7);
      }
      else{
         $notas = Notas::where('sucursal',$sucursal)
         ->where('fecha',$fecha)
         ->where('nro','>',0)
         ->where('anulado',0)
         ->orderBy('id','DESC')->paginate(7);
      }
      return [
         'paginate' => [
              'total'        => $notas->total(),
              'current_page' => $notas->currentPage(),
              'per_page'     => $notas->perPage(),
              'last_page'    => $notas->lastPage(),
              'from'         => $notas->firstItem(),
              'to'           => $notas->lastPage(),
         ],
         'notas' => $notas
      ];
   }
   public function buscar_num_pedido($search,$sucursal){
      $notas = Notas::where('codigo_nota','like','%'.$search.'%')
       ->where('sucursal',$sucursal)
       ->orderBy('id','DESC')->paginate(8);
       return [
         'paginate' => [
              'total'        => $notas->total(),
            //   'current_page' => $notas->currentPage(),
              'per_page'     => $notas->perPage(),
              'last_page'    => $notas->lastPage(),
              'from'         => $notas->firstItem(),
              'to'           => $notas->lastPage(),
         ],
         'notas' => $notas
      ];
   }
   public function detallesnotas_pedido($codigo)
   {
      return detalle_notas::select('products.barra','products.codigo','products.nompro','products.marca','detalle_notas.precio_com','detalle_notas.cantidad','detalle_notas.descuento','detalle_notas.cod_nota')
      ->join('products','detalle_notas.barra_nota','products.barra')
      ->where('detalle_notas.cod_nota',$codigo)
      ->get();
   }
   public function cambiarestado_nota(Request $request,$id)
   {
      $notas = Notas::find($id);
      $notas->estado = "1";
      $notas->save();
   }
   public function subirstock_nota(Request $request,$sucursal)
   {
      foreach($request['ArrayDate'] as $value){
         $almacen = almacen::where('barra_almacen',$value['barra'])
         ->where('sucursal',$sucursal)->first();
         // **** PRODUCTO GENERAL ****
         $product = product::where('barra',$value['barra'])->first();
          // **** PRODUCTO GENERAL ****
         $stock = almacen::find($almacen->id);
         $stock->stock_almacen = intval($almacen->stock_almacen) + intval($value['cantidad']);
         $stock->save(); 
         $precio_comp = product::find($product->id);
         $precio_comp->precio = $value['precio_com'];
         $precio_comp->save();
         //movimiento
         $movimiento = new Movimientos;
         $movimiento->nro_documento = $value['cod_nota'];
         $movimiento->barra_mov = $value['barra'];
         $movimiento->precio = $value['precio_com'];
         $movimiento->condicion = "entrada";
         $movimiento->fecha = date('Y-m-d');
         $movimiento->detalle = "comprado";
         $movimiento->tipo = "compra";
         $movimiento->cantidad = $value['cantidad'];
         $movimiento->sucursal = $sucursal;
         $movimiento->save();
      }
      return back();
   }
   public function eliminarnota($codigo)
   {
      $notas = Notas::where('codigo_nota',$codigo)->delete();
      $detalle = detalle_notas::where('cod_nota',$codigo)->delete();
   }
   public function DarBajanota($codigo,$id)
   {
      $notas = Notas::find($id);
      $notas->anulado = "1";
      $notas->total = "0";
      $notas->save();
      $detalle = detalle_notas::where('cod_nota',$codigo)->delete();
   }
   public function bajarstock_nota(Request $request,$sucursal)
   {
      foreach($request['ArrayDate'] as $value){
         $almacen = almacen::where('barra_almacen',$value['barra'])
         ->where('sucursal',$sucursal)->first();
         $stock = almacen::find($almacen->id);
         $stock->stock_almacen = intval($almacen->stock_almacen) - intval($value['cantidad']);
         $stock->save(); 
         $movimiento = new Movimientos;
         $movimiento->nro_documento = $request->cod_nota;
         $movimiento->barra_mov = $value['barra'];
         $movimiento->precio = $value['precio_com'];
         $movimiento->condicion = "salida";
         $movimiento->fecha = date('Y-m-d');
         $movimiento->detalle = "anulado";
         $movimiento->tipo = "compra";
         $movimiento->cantidad = $value['cantidad'];
         $movimiento->sucursal = $sucursal;
         $movimiento->save();
      }
      return back();
   }
   public function llenar_listaN($nrof){
      return detalle_notas::select(DB::raw('detalle_notas.id,products.barra,products.url_imagen,products.nompro,products.marca,detalle_notas.precio_com as precio,detalle_notas.descuento,detalle_notas.cantidad,detalle_notas.cod_nota,almacen.stock_almacen as stock'))
      ->join('products','detalle_notas.barra_nota','products.barra')
      ->join('almacen','detalle_notas.barra_nota','almacen.barra_almacen')
      ->where('detalle_notas.cod_nota',trim($nrof))
      ->get();
  }

  public function codigoTraslado($sucursal)
  {
      return DB::select("select nro from traslados where sucursal = ? order by id desc limit 1", [$sucursal])[0]->nro;
  }

  public function generarTraslado(Request $request)
  {
      $productos = DB::select("select * from detalle_notas where cod_nota = ?",[$request['codigo']]);
      $sucursal = DB::select("select id from sucursal where nombre = ?", [$request['sucursal']])[0]->id;
      $numero = $this->formatNumber($this->codigoTraslado($request['sucursal']) + 1);
      $codigo = 'G00'. $sucursal . '-' . $numero;
      DB::statement("insert into traslados(nro,sucursal,cod_sucursal,estado,recibido,para,fecha,created_at,updated_at) 
      values(?,?,?,?,?,?,?,?,?)", 
         [$numero, $request['sucursal'], $codigo, 0, 0, $request['para'], date('Y-m-d'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]
      );
      foreach($productos as $value)
      {
         DB::statement("insert into detalle_traslado(nro_tras, barra_tras, cantidad, de, para, created_at, updated_at) values(?,?,?,?,?,?,?)",
           [$codigo, $value->barra_nota, $value->cantidad, $request['sucursal'], $request['para'], date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]
         );
      }
  }

  public function formatNumber($number)
  {
      $length = 9;
      $char = 0;
      $type = 'd';
      $format = "%{$char}{$length}{$type}"; // or "$010d";
      //store to a variable
      $newFormat = sprintf($format, $number);
      return $newFormat;
  }
}
