<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ventas;
use App\almacen;
use App\detalle_venta;
use App\cliente;
use Illuminate\Support\Facades\Auth;
use App\Movimientos;
use Illuminate\Support\Facades\DB;

class VentasController extends Controller
{
    public function nuevaventa($sucursal){
        return ventas::where('sucursal',$sucursal)->max('nrof');
    }
    public function listcreditos(Request $request, $sucursal,$fecha,$desde){
        if($fecha === "1" && $desde === "1"){
           $fecha = date('Y-m-d');
           $desde = date('Y-m-d');
        }
        $ventas = ventas::where('sucursal',$sucursal)
        ->whereBetween('fecha',[$desde,$fecha])
        ->where('nrof','>',0)
        ->where('estado','0')
        ->where('anulado','0')
        ->orderBy('id','DESC')
        ->paginate(12);
        return [
            'paginate' => [
                 'total'        => $ventas->total(),
                 'current_page' => $ventas->currentPage(),
                 'per_page'     => $ventas->perPage(),
                 'last_page'    => $ventas->lastPage(),
                 'from'         => $ventas->firstItem(),
                 'to'           => $ventas->lastPage(),
            ],
            'ventas' => $ventas
         ];
    }
    public function listventas(Request $request, $sucursal,$fecha,$desde,$estado){
        if($fecha === "1" && $desde === "1"){
           $fecha = date('Y-m-d');
           $desde = date('Y-m-d');
        }
        $ventas = ventas::where('sucursal',$sucursal)
        ->whereBetween('fecha',[$desde,$fecha])
        ->where('nrof','>',0)
        ->where('estado','!=','0')
        ->where('anulado','0')
        ->orderBy('id','DESC')
        ->paginate(12);
        return [
            'paginate' => [
                 'total'        => $ventas->total(),
                 'current_page' => $ventas->currentPage(),
                 'per_page'     => $ventas->perPage(),
                 'last_page'    => $ventas->lastPage(),
                 'from'         => $ventas->firstItem(),
                 'to'           => $ventas->lastPage(),
            ],
            'ventas' => $ventas
         ];
    }
    public function listdetalles($nrof,$sucursal){
        return detalle_venta::select('detalle_venta.id','products.barra','products.codigo','products.nompro','detalle_venta.precio','detalle_venta.descuento','detalle_venta.cantidad','detalle_venta.nrof','detalle_venta.sucursal')
        ->join('products','detalle_venta.barra_detalles','products.barra')
        ->where('detalle_venta.nrof',$nrof)
        ->get();
        
    }
    public function search_ventas($search,$sucursal,$estado){
        if($estado === "1"){
            $ventas = ventas::where('sucursal',$sucursal)
            ->where('cod_sucursal','like','%'.$search.'%')
            ->where('estado','!=','0')
            ->where('anulado','0')
            ->orderBy('id','DESC')
            ->paginate(12);
        }else{
            $ventas = ventas::where('sucursal',$sucursal)
            ->where('cod_sucursal','like','%'.$search.'%')
            ->where('estado',$estado)
            ->where('anulado','0')
            ->orderBy('id','DESC')
            ->paginate(12);
        }
        return [
            'paginate' => [
                 'total'        => $ventas->total(),
                //  'current_page' => $ventas->currentPage(),
                 'per_page'     => $ventas->perPage(),
                 'last_page'    => $ventas->lastPage(),
                 'from'         => $ventas->firstItem(),
                 'to'           => $ventas->lastPage(),
            ],
            'ventas' => $ventas
         ];
    }
    // ******************* POR MAYOR ****************
    public function listventasmayor(Request $request, $sucursal,$fecha,$desde,$estado){
        if($fecha === "1" && $desde === "1"){
           $fecha = date('Y-m-d');
           $desde = date('Y-m-d');
        }
        $ventas = ventas::where('sucursal',$sucursal)
        ->whereBetween('fecha',[$desde,$fecha])
        ->where('nrof','>',0)
        ->where('estado','!=','0')
        ->where('anulado','0')
        ->where('xmayor',1)
        ->orderBy('id','DESC')
        ->paginate(12);
        return [
            'paginate' => [
                 'total'        => $ventas->total(),
                 'current_page' => $ventas->currentPage(),
                 'per_page'     => $ventas->perPage(),
                 'last_page'    => $ventas->lastPage(),
                 'from'         => $ventas->firstItem(),
                 'to'           => $ventas->lastPage(),
            ],
            'ventas' => $ventas
         ];
    }
    public function search_ventasmayor($search,$sucursal,$estado){
        if($estado === "1"){
            $ventas = ventas::where('sucursal',$sucursal)
            ->where('cod_sucursal','like','%'.$search.'%')
            ->where('estado','!=','0')
            ->where('anulado','0')
            ->where('xmayor',1)
            ->orderBy('id','DESC')
            ->paginate(12);
        }else{
            $ventas = ventas::where('sucursal',$sucursal)
            ->where('cod_sucursal','like','%'.$search.'%')
            ->where('estado',$estado)
            ->where('anulado','0')
            ->where('xmayor',1)
            ->orderBy('id','DESC')
            ->paginate(12);
        }
        return [
            'paginate' => [
                 'total'        => $ventas->total(),
                //  'current_page' => $ventas->currentPage(),
                 'per_page'     => $ventas->perPage(),
                 'last_page'    => $ventas->lastPage(),
                 'from'         => $ventas->firstItem(),
                 'to'           => $ventas->lastPage(),
            ],
            'ventas' => $ventas
         ];
    }
    // ******************** FIN POR MAYOR ****************
    public function modificaracumulado(Request $request,$id){
        $venta = ventas::find($id);
        $venta->estado_pago = $request->acumulado;
        $venta->save();
    }
    public function generar_venta(Request $request){
        $venta = new ventas;
        $venta->nrof = $request->nrof;
        $venta->estado = $request->estado;
        $venta->estado_pago = '0'; 
        $venta->tipo_v = $request->tipo_v;
        $venta->total_v = $request->total_v;
        $venta->total_ganancia = $request->total_ganancia;
        $venta->ruc_dni_v = $request->ruc_dni;
        $venta->nombre_cliente = $request->nom_cliente;
        $venta->sucursal = $request->sucursal;
        $venta->cod_sucursal = $request->cod_sucursal;
        $venta->usuario = $request->usuario;
        $venta->anulado = "0";
        $venta->fecha = date('Y-m-d');
        $venta->fecha_t = date("d-m-Y h:i:s a");
        $venta->xmayor = $request->xmayor;
        $venta->save();
        return back();
    }
    public function detalle_venta(Request $request){
        foreach($request['arrayDate'] as $value){
        //guardar detalles
            $detalle = new detalle_venta;
            $detalle->barra_detalles = $value['barra'];
            $detalle->cantidad = $value['cantidad'];
            $detalle->precio = $value['precio'];
            $detalle->descuento = $value['descuento'];
            $detalle->nrof = $request['nrof'];
            $detalle->sucursal = $value['sucursal'];
            $detalle->save();
            //bajar stock
            $almacen = almacen::where('barra_almacen',$value['barra'])
            ->where('sucursal',$value['sucursal'])->first();
            $stock = almacen::find($almacen->id);
            $stock->stock_almacen = intval($almacen->stock_almacen) - intval($value['cantidad']);
            $stock->save(); 
            // //movimiento
            $movimiento = new Movimientos;
            $movimiento->nro_documento = $request['nrof'];
            $movimiento->barra_mov = $value['barra'];
            $movimiento->precio = $value['precio'];
            $movimiento->condicion = "salida";
            $movimiento->fecha = date('Y-m-d');
            $movimiento->detalle = "vendido";
            $movimiento->tipo = "venta";
            $movimiento->cantidad = $value['cantidad'];
            $movimiento->sucursal = $value['sucursal'];
            $movimiento->save();
        }
        return back();
    }
    public function anularfactura($id,$nrof,$sucursal){
        $venta = ventas::find($id);
        $venta->anulado = "1";
        $venta->total_v = "0";
        $venta->save();
        $detalle = detalle_venta::where('nrof',$nrof)->where('sucursal',$sucursal)
        ->delete();
    }
    public function estado_pago(Request $request, $id){
        $ventas = ventas::find($id);
        $ventas->estado_pago = $request->monto;
        $ventas->save();
        return back();
    }
    public function cambiar_estado(Request $request,$id){
        $ventas = ventas::find($id);
        $ventas->estado = 'credito';
        $ventas->save();
        return back();
    }
    public function subir_stock_venta(Request $request){
        foreach($request['ArrayAnular'] as $value){
            $almacen = almacen::where('barra_almacen',$value['barra'])
            ->where('sucursal',$value['sucursal'])->first();
            $stock = almacen::find($almacen->id);
            $stock->stock_almacen = intval($almacen->stock_almacen) + intval($value['cantidad']);
            $stock->save();
            //movimiento
            $movimiento = new Movimientos;
            $movimiento->nro_documento = $value['nrof'];
            $movimiento->barra_mov = $value['barra'];
            $movimiento->precio = $almacen->precio_venta;
            $movimiento->condicion = "entrada";
            $movimiento->fecha = date('Y-m-d');
            $movimiento->detalle = "anulado";
            $movimiento->tipo = "venta";
            $movimiento->cantidad = $value['cantidad'];
            $movimiento->sucursal = $value['sucursal'];
            $movimiento->save();
        }
        return back();
    }
    public function buscarclienteventa($cliente){
        return cliente::where('ruc_dni',$cliente)->first();
    }
    public function llenar_listaV($nrof){
        return detalle_venta::select(DB::raw('detalle_venta.id,products.barra,products.url_imagen,products.nompro as producto,products.marca,detalle_venta.precio,detalle_venta.descuento,detalle_venta.cantidad,detalle_venta.nrof,detalle_venta.sucursal'))
        ->join('products','detalle_venta.barra_detalles','products.barra')
        ->where('detalle_venta.nrof',trim($nrof))
        ->get();
    }
}
