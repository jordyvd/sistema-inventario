<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\salida_product;
use App\detalle_salidap;
use App\almacen;
use App\Movimientos;
use Illuminate\Support\Facades\DB;

class SalidaProductController extends Controller
{
    public function nrofsalidas($sucursal)
    {
        return salida_product::where('sucursal',$sucursal)->max('nrof');
    }
    public function agregar_salida(Request $request){
        $salida = new salida_product;
        $salida->cod_sucursal = $request->cod_sucursal;
        $salida->nrof = $request->nrof;
        $salida->condicion = $request->condicion;
        $salida->total = $request->total;
        $salida->descripcion = $request->descripcion;
        $salida->fecha = date('Y-m-d');
        $salida->sucursal = $request->sucursal;
        $salida->save();
        return back();
    }
    public function detalle_product(Request $request){
        foreach($request['arrayDate'] as $value){
            $salida = new detalle_salidap;
            $salida->barra_salida = $value['barra'];
            $salida->precio = $value['precio'];
            $salida->cantidad = $value['cantidad'];
            $salida->descuento = $value['descuento'];
            $salida->nrof = $value['nrof'];
            $salida->sucursal = $value['sucursal'];
            $salida->save();
             //bajar stock
             $almacen = almacen::where('barra_almacen',$value['barra'])
             ->where('sucursal',$value['sucursal'])->first();
             $stock = almacen::find($almacen->id);
             $stock->stock_almacen = intval($almacen->stock_almacen) - intval($value['cantidad']);
             $stock->save(); 
               //movimiento
             $movimiento = new Movimientos;
             $movimiento->nro_documento = $value['nrof'];
             $movimiento->barra_mov = $value['barra'];
             $movimiento->precio = $value['precio'];
             $movimiento->condicion = "salida";
             $movimiento->fecha = date('Y-m-d');
             $movimiento->detalle = $request['condicion'];
             $movimiento->tipo = "salida_interna";
             $movimiento->cantidad = $value['cantidad'];
             $movimiento->sucursal = $value['sucursal'];
             $movimiento->save();
        }
    }
    public function listarsalidas($sucursal,$desde,$hasta){
        if($desde === "1")
        {  
            $salida = salida_product::where('sucursal',$sucursal)
            ->where('nrof','>',0)
            ->where('fecha',date('Y-m-d'))
            ->orderBy('id','DESC')
            ->paginate(9);
        }else{
            $salida = salida_product::where('sucursal',$sucursal)
            ->where('nrof','>',0)
            ->whereBetween('fecha',[$desde,$hasta])
            ->orderBy('id','DESC')
            ->paginate(9);
        }
        return [
            'paginate' => [
                 'total'        => $salida->total(),
                 'current_page' => $salida->currentPage(),
                 'per_page'     => $salida->perPage(),
                 'last_page'    => $salida->lastPage(),
                 'from'         => $salida->firstItem(),
                 'to'           => $salida->lastPage(),
            ],
            'salida' => $salida
         ];
    }
    public function detalle_salidas($nro,$sucursal)
    {
        return detalle_salidap::select('products.barra','products.nompro','detalle_salidap.precio','detalle_salidap.cantidad','detalle_salidap.descuento','detalle_salidap.nrof')
        ->join('products','detalle_salidap.barra_salida','products.barra')
        ->where('nrof',$nro)
        ->where('sucursal',$sucursal)
        ->get();
    }
    public function total_perdida($sucursal,$desde,$hasta){
       if($desde === "1"){
           return salida_product::select(DB::raw('sum(total) as total_salida'))
           ->where('sucursal',$sucursal)
           ->where('nrof','>',0)
           ->where('fecha',date('Y-m-d'))
           ->first();
       }else{
            return salida_product::select(DB::raw('sum(total) as total_salida'))
            ->where('sucursal',$sucursal)
            ->where('nrof','>',0)
            ->whereBetween('fecha',[$desde,$hasta])
            ->first();
       }
    }
    public function bajarstockSalida(Request $request){
        $stock = almacen::where('barra_almacen',$request->barra)
        ->where('sucursal',$request->sucursal)
        ->first();
        $subir = almacen::find($stock->id);
        $subir->stock_almacen = intval($stock->stock_almacen) + intval($request->cantidad);
        $subir->save();
          //movimiento
          $movimiento = new Movimientos;
          $movimiento->nro_documento = $request->nrof;
          $movimiento->barra_mov = $request->barra;
          $movimiento->precio = $stock->precio_venta;
          $movimiento->condicion = "entrada";
          $movimiento->fecha = date('Y-m-d');
          $movimiento->detalle = "anulado";
          $movimiento->tipo = "salida_interna";
          $movimiento->cantidad = $request->cantidad;
          $movimiento->sucursal = $request->sucursal;
          $movimiento->save();
    }
    public function deletesalidas($codigo){
        $salida = salida_product::where('cod_sucursal',$codigo)
        ->delete();
        $detalle = detalle_salidap::where('nrof',$codigo)
        ->delete();
    }
}
