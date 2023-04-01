<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\permisos;
use App\caja;
use App\ventas;
use App\traslados;
use App\almacen;
use App\sucursal;
use App\ingresos_salidas;

class HomeController extends Controller
{
    public function index($sucursal)
    {  
       return ventas::where('sucursal',$sucursal)
       ->where('fecha',date('Y-m-d'))
       ->count();
    } 
    public function total_venta_actual($sucursal){
        return ventas::select(DB::raw('sum(total_v) as total_venta'))
        ->where('fecha',date('Y-m-d'))
        ->where('sucursal',$sucursal)
        ->where('estado','!=','0')
        ->get();
    }
    public function total_salida($sucursal){
       return ingresos_salidas::select(DB::raw('sum(monto) as monto_salida'))
       ->where('sucursal',$sucursal)
       ->where('condicion','salida')
       ->where('fecha',date("Y-m-d"))
       ->first();
    }
    public function total_ingresos($sucursal){
        return ingresos_salidas::select(DB::raw('sum(monto) as monto_ingreso'))
        ->where('sucursal',$sucursal)
        ->where('condicion','ingreso')
        ->where('fecha',date("Y-m-d"))
        ->first();
    }
    public function total_pendientes($sucursal){
        return ventas::select(DB::raw('sum(total_v) as total_venta'))
        ->where('sucursal',$sucursal)
        ->where('estado',1)
        ->where('fecha',date('Y-m-d'))
        ->get();
    }
    public function total_agotados($sucursal){
        return almacen::where('sucursal',$sucursal)
        ->where('stock_almacen','<',1)
        ->count();
    }
    public function buscar_id_sucursal($sucursal){
       return sucursal::where('nombre',$sucursal)->first();
    }
    public function ingresos_salidas_create(Request $request){
        $date = new ingresos_salidas;
        $date->descripcion = $request->descripcion;
        $date->monto = $request->monto;
        $date->condicion = $request->condicion;
        $date->fecha = date('Y-m-d');
        $date->sucursal = $request->sucursal;
        $date->credit_id = $request->credit_id;
        $date->save();
    }
    public function listaringresos_salidas(Request $request){
        if($request->fecha == null){
            $datos = ingresos_salidas::where('sucursal',$request->sucursal)
            ->where('fecha',date('Y-m-d'))
            ->orderBy('id','DESC')
            ->paginate('8');
        }else{
            $datos = ingresos_salidas::where('sucursal',$request->sucursal)
            ->where('fecha',$request->fecha)
            ->orderBy('id','DESC')
            ->paginate('8');
        }
        return [
            'paginate' => [
                 'total'        => $datos->total(),
                 'current_page' => $datos->currentPage(),
                 'per_page'     => $datos->perPage(),
                 'last_page'    => $datos->lastPage(),
                 'from'         => $datos->firstItem(),
                 'to'           => $datos->lastPage(),
            ],
            'datos' => $datos
         ];
    }

    public function deleteingreso_salida($id){
       $dato = ingresos_salidas::where('id',$id);
       $dato->delete();
    }

    public function montoCajaEfectivo(Request $request){
        $total_tranferencia = $this->cajaTrasnferencia($request);
        $date = $request->fecha == null ? date('Y-m-d') : $request->fecha;
        $total_ventas = $this->total_caja($request->sucursal, $date);
        $procedure = "call get_monto_caja_efectivo(?,?)";
        $parameter = [
            $request->sucursal,
            $date
        ];
        $data = DB::select($procedure, $parameter);
        $ingreso = 0;
        $salida = 0;
        foreach($data as $value){
            if($value->condicion == "salida"){
                $salida += str_replace("soles", "", $value->monto);
            }else{
                if($value->condicion_cp !== 2)
                {
                    $ingreso += str_replace("soles", "", $value->monto);
                }
            }
        }
        return [
            "ingreso" => $ingreso,
            "salida" => $salida,
            "transferencia" => $total_tranferencia,
            "ventas" => $total_ventas["total_venta"],
        ];
    }
    public function cajaTrasnferencia(Request $request){
        $date = $request->fecha == null ? date('Y-m-d') : $request->fecha;
        $procedure = "call get_caja_transfarencias(?,?)";
        $parameter = [
            $request->sucursal,
            $date
        ];
        $data = DB::select($procedure, $parameter);
        $total = 0;
        foreach($data as $value){
            $total += $value->total_ventas;
        }
        return $total;
    }
    public function total_caja($sucursal,$fecha){
        $statement = "call total_venta_dia(?,?)";
        $parameter = [
            $fecha == 1 ? null : $fecha,
            $sucursal
        ];
        $data = DB::select($statement, $parameter);
        if(count($data) > 0){
            return [
               "total_venta" => $data[0]->total_venta
            ];
        }
        return [
            "total_venta" => 0
         ];
    }
    public function descripsea($search,$sucursal,$fecha){
        if($fecha === "1"){
            $datos = ingresos_salidas::where('descripcion','LIKE','%'.$search.'%')
            ->where('sucursal',$sucursal)
            ->where('fecha',date('Y-m-d'))
            ->orderBy('id','desc')
            ->paginate(30);  
            return [
                'paginate' => [
                     'total'        => $datos->total(),
                    //  'current_page' => $datos->currentPage(),
                     'per_page'     => $datos->perPage(),
                     'last_page'    => $datos->lastPage(),
                     'from'         => $datos->firstItem(),
                     'to'           => $datos->lastPage(),
                ],
                'datos' => $datos
             ]; 
        }
        $datos = ingresos_salidas::where('descripcion','LIKE','%'.$search.'%')
        ->where('sucursal',$sucursal)
        ->where('fecha',$fecha)
        ->orderBy('id','desc')
        ->paginate(30);  
        return [
            'paginate' => [
                 'total'        => $datos->total(),
                //  'current_page' => $datos->currentPage(),
                 'per_page'     => $datos->perPage(),
                 'last_page'    => $datos->lastPage(),
                 'from'         => $datos->firstItem(),
                 'to'           => $datos->lastPage(),
            ],
            'datos' => $datos
         ]; 
    }
}
