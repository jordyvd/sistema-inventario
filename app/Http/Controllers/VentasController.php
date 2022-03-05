<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ventas;
use App\almacen;
use App\detalle_venta;
use App\cliente;
use App\Movimientos;
use App\credito_payments;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SunatController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;

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
    }
    public function generar_venta(Request $request){
        $doc = $this->docSunat($request);
        $procedure = "call insert_venta(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
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
            $request->descripcion,
            date('Y-m-d'),
            date("Y-m-d h:i:s")
        ];
        $data = DB::select($procedure, $parameter);
        if($data[0]->serie !== 0) {
            $this->detalleVenta($request);
            if($request->tipo_v != "ticked"){
                $objeto = new SunatController();
                return $objeto->generarDocumento($request, $data[0]->serie);
            }
        }else{
            return back();
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
            $movimiento->descuento = $value['descuento'];
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
    public function agregarPagoCredit(Request $request){
        $ventas = ventas::find($request->id);
        $ventas->estado_pago = $request->acumulado;
        $ventas->save();
        $this->insertPaymentCredito($request);
    }
    public function insertPaymentCredito(Request $request){
        $procedure = "call insertar_pago_credito(?,?,?,?,?,?,?)";
        $parameter = [
           $request->id,
           $request->monto,
           $request->condicion,
           $request->descripcion,
           $request->caja,
           $request->user_id,
           date('Y-m-d H:i:s')
        ];
        $id = DB::select($procedure, $parameter);
        $params_caja = [
            "descripcion" =>  $request->descripcion . " (".$request->nrof. ")",
            "monto" =>  $request->monto,
            "condicion" => "ingreso",
            "sucursal" => $request->sucursal,
            "credit_id" => $id[0]->id,
        ];
        $params = new Request($params_caja);
        if($request->caja == 1){
            $this->insertarCaja($params);
        }
    }
    public function insertarCaja(Request $request){
        $caja = new HomeController();
        $caja->ingresos_salidas_create($request);
    }
    public function getPaymentsCredit(Request $request){
        $procedure = 'select *, false input_update from creditos_payments where credito_id = ? and deleted_at is null order by created_at desc';
        $parameter = [
             $request['id']
        ];
        $data = DB::select($procedure, $parameter);
        return [
            "data" => $data,
             "curdate_date" => date('Y-m-d')
        ];
    }
    public function updateMontoPagoCredit(Request $request){
        $procedure = "call update_monto_pago_credit(?,?,?)";
        $parameter = [
            $request->monto,
            $request->user_id,
            $request->id
        ];
        DB::select($procedure, $parameter);
        return $this->updateAcumuladoCredit($request);
    }
    public function updateAcumuladoCredit(Request $request){
        $acumulado = 0;
        $pagos = DB::select("select cp.monto from creditos_payments cp where cp.credito_id = ? and deleted_at is null", [$request->venta_id]);
        foreach($pagos as $value){
            $acumulado += $value->monto;
        }
        $procedure = "update ventas set estado_pago = ? where id = ?";
        $parameter = [
            $acumulado,
            $request->venta_id
        ];
        DB::select($procedure, $parameter);
        return $acumulado;
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
            $movimiento->descuento = $value['descuento'];
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
    public function eliminarPagoCredito(Request $request,$user_id){
        DB::select('update creditos_payments set deleted_at = ?, user_delete = ? where id = ?', [date('Y-m-d H:m:s'), $user_id , $request->id]);
        DB::select('delete from ingresos_salidas where credit_id = ?',[$request->id]);
        $acumulado = 0;
        $pagos = DB::select("select cp.monto from creditos_payments cp where cp.credito_id = ? and deleted_at is null", [$request->credito_id]);
        foreach($pagos as $value){
            $acumulado += $value->monto;
        }
        $procedure = "update ventas set estado_pago = ? where id = ?";
        $parameter = [
            $acumulado,
            $request->credito_id
        ];
        DB::select($procedure, $parameter);
        return $acumulado;
    }
}
