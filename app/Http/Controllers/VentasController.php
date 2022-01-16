<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ventas;
use App\almacen;
use App\detalle_venta;
use App\cliente;
use Illuminate\Support\Facades\Auth;
use App\Movimientos;
use App\credito_payments;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SunatController;


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
        return detalle_venta::select('detalle_venta.id','products.barra','products.codigo','products.nompro','detalle_venta.precio','detalle_venta.descuento','detalle_venta.cantidad','detalle_venta.nrof','detalle_venta.sucursal','products.marca')
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
        $tracking = new credito_payments;
        $tracking->monto = $request->mount;
        $tracking->created_by =  Auth::user()->id;
        $tracking->save();
    }
    public function generar_venta(Request $request){
        $doc = $this->docSunat($request);
        $procedure = "call insert_venta(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $parameter = [
            $doc['doc'],
            $doc['cod'],
            $request->nrof,
            $request->estado,
            0,
            $request->tipo_v,
            $request->total_v,
            $request->total_ganancia,
            $request->ruc_dni,
            $request->nom_cliente,
            $request->sucursal,
            $request->cod_sucursal,
            $request->usuario,
            $request->xmayor,
            date('Y-m-d'),
            date("Y-m-d h:i:s")
        ];
        $this->detalleVenta($request);
        $data = DB::select($procedure, $parameter);
        if($request->tipo_v != "ticked"){
            $objeto = new SunatController();
            return $objeto->generarDocumento($request, $data[0]->serie);
        }
    }
    public function docSunat(Request $request){
        $doc = null;
        $cod = null;
        if($request->tipo_v == "factura"){
           $doc = "01";
           $cod = "01-F001";
        }else if($request->tipo_v == "boleta"){
           $doc = "03";
           $cod = "03-B001";
        }
        return [
            "doc" => $doc,
            "cod" => $cod
        ];
    }
    public function detalleVenta(Request $request){
        foreach($request['productos'] as $value){
        //guardar detalles
            $detalle = new detalle_venta;
            $detalle->barra_detalles = $value['barra'];
            $detalle->cantidad = $value['cantidad'];
            $detalle->precio = $value['precio'];
            $detalle->descuento = $value['descuento'];
            $detalle->nrof = $request['cod_sucursal'];
            $detalle->sucursal = $value['sucursal'];
            $detalle->save();
            //bajar stock
            $almacen = almacen::where('barra_almacen',$value['barra'])
            ->where('sucursal',$value['sucursal'])->first();
            $stock = almacen::find($almacen->id);
            $stock->stock_almacen = intval($almacen->stock_almacen) - intval($value['cantidad']);
            $stock->save(); 
            $movimiento = new Movimientos;
            $movimiento->nro_documento = $request['cod_sucursal'];
            $movimiento->barra_mov = $value['barra'];
            $movimiento->precio = $value['precio'];
            $movimiento->condicion = $request->condicion;
            $movimiento->fecha = date('Y-m-d');
            $movimiento->detalle = "vendido";
            $movimiento->tipo = "venta";
            $movimiento->cantidad = $value['cantidad'];
            $movimiento->sucursal = $value['sucursal'];
            $movimiento->save();
        }
       // return back();
    }
    public function anularfactura(Request $request){
        $venta = ventas::find($request->id);
        $venta->anulado = "1";
        $venta->total_v = "0";
        $venta->save();
        $productos = $this->listdetalles($request->nrof, $request->sucursal);
        detalle_venta::where('nrof',$request->nrof)->where('sucursal',$request->sucursal)->delete();
        $this->subir_stock_venta($productos);
        if($request->d_sunat == 1){
            $objeto = new SunatController();
            return $objeto->generarNotaCredito($request, $productos);
        }else{
            return back();
        }
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
    public function subir_stock_venta($productos){
        foreach($productos as $value){
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
            $movimiento->condicion = "anulado";
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
