<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ventas;
use App\detalle_venta;
use Luecano\NumeroALetras\NumeroALetras;

class DompdfController extends Controller
{
    public function ticked($nrof){
        $details =  detalle_venta::select('detalle_venta.id','products.barra','products.codigo','products.nompro','detalle_venta.precio','detalle_venta.descuento','detalle_venta.cantidad','detalle_venta.nrof','detalle_venta.sucursal')
        ->join('products','detalle_venta.barra_detalles','products.barra')
        ->where('detalle_venta.nrof',$nrof)
        ->get();
        $venta = ventas::where('cod_sucursal',$nrof)->get();
        $sacar_total = ventas::where('cod_sucursal',$nrof)->first();
        $formatter = new NumeroALetras();
        $total = $formatter->toMoney($sacar_total->total_v, 2, 'SOLES', 'CENTIMOS');
        $datos = ['detalles' => $details , 'venta' => $venta, 'total' => $total];
        $view = \View::make('documentos.ticked',compact('datos'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('ticked'.'.pdf');
   }
   public function cotizar(Request $request){
      foreach($request['datos'] as $value){
           return $value['barra'];
      }
   }
}
