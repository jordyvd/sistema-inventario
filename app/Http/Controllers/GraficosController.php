<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ventas;
use App\cliente;
use App\Notas;
use App\salida_product;
use Illuminate\Support\Facades\DB;

class GraficosController extends Controller
{
    public function ventas($desde,$hasta,$sucursal){
       if($sucursal === "todos")
          {
            return ventas::select(DB::raw('sum(total_v) as total_venta,fecha'))
            ->whereBetween('fecha',[$desde,$hasta])
            ->where('estado','!=','0')
            ->where('anulado',0)
            ->groupBy('fecha')
            ->get();
        }else{
            return ventas::select(DB::raw('sum(total_v) as total_venta,fecha'))
            ->whereBetween('fecha',[$desde,$hasta])
            ->where('sucursal',$sucursal)
            ->where('estado','!=','0')
            ->where('anulado',0)
            ->groupBy('fecha')
            ->get();   
        }
    }
    public function listclientes_select(){
        return cliente::all();
    }
    public function grafico_clientes($desde,$hasta,$cliente){
        return ventas::select(DB::raw('sum(total_v) as total_venta,fecha'))
        ->whereBetween('fecha',[$desde,$hasta])
        ->where('ruc_dni_v',$cliente)
        ->where('estado','!=','0')
        ->where('anulado',0)
        ->groupBy('fecha')
        ->get();
    }
    public function grafico_vendedor($desde,$hasta,$usuario){
         return ventas::select(DB::raw('sum(total_v) as total_venta,fecha'))
         ->whereBetween('fecha',[$desde,$hasta])
         ->where('usuario',$usuario)
         ->where('estado','!=','0')
         ->where('anulado',0)
         ->groupBy('fecha')
         ->get();
    }
    public function grafico_ganancias($desde,$hasta,$sucursal){
        if($sucursal === "todos")
          {
            return ventas::select(DB::raw('sum(total_ganancia) as total_ganancias,fecha'))
            ->whereBetween('fecha',[$desde,$hasta])
            ->where('estado','!=','0')
            ->where('anulado',0)
            ->groupBy('fecha')
            ->get();
        }else{
            return ventas::select(DB::raw('sum(total_ganancia) as total_ganancias,fecha'))
            ->whereBetween('fecha',[$desde,$hasta])
            ->where('sucursal',$sucursal)
            ->where('estado','!=','0')
            ->where('anulado',0)
            ->groupBy('fecha')
            ->get();   
        }
    }
    public function grafico_proveedor($desde,$hasta,$sucursal){
        if($sucursal === "todos"){
            return Notas::select(DB::raw('sum(total) as total_egreso,fecha'))
            ->whereBetween('fecha',[$desde,$hasta])
            ->where('estado',1)
            ->where('anulado',0)
            ->groupBy('fecha')
            ->get();
        }else{
            return Notas::select(DB::raw('sum(total) as total_egreso,fecha'))
            ->whereBetween('fecha',[$desde,$hasta])
            ->where('estado',1)
            ->where('sucursal',$sucursal)
            ->where('anulado',0)
            ->groupBy('fecha')
            ->get();
        }
        
    }
    public function grafico_Sinternras($desde,$hasta,$sucursal){
            return salida_product::select(DB::raw('sum(total) as total_egreso,fecha'))
            ->whereBetween('fecha',[$desde,$hasta])
            ->where('sucursal',$sucursal)
            ->groupBy('fecha')
            ->get();
    }
}
