<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\product;
use Illuminate\Support\Facades\Storage;
use App\marca;
use App\sucursal;
use App\cliente;
use App\rol;
use App\traslados;
use App\ventas;
use App\almacen;
use App\Movimientos;
use App\Notas;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{

    public function index(Request $request)
    {
        $producto = product::orderBy('nompro')
        ->paginate(10);
        return [
           'paginate' => [
                'total'        => $producto->total(),
                'current_page' => $producto->currentPage(),
                'per_page'     => $producto->perPage(),
                'last_page'    => $producto->lastPage(),
                'from'         => $producto->firstItem(),
                'to'           => $producto->lastPage(),
           ],
           'product' => $producto
        ];
    }
    public function listmarca(Request $request){
        $marca = marca::orderBy('id','DESC')->paginate(10);
        return [
            'paginate' => [
                'total'        => $marca->total(),
                'current_page' => $marca->currentPage(),
                'per_page'     => $marca->perPage(),
                'last_page'    => $marca->lastPage(),
                'from'         => $marca->firstItem(),
                'to'           => $marca->lastPage(),
            ],
            'marca' => $marca
        ];
    }
    public function listsucursal(Request $request){
        $sucursal = sucursal::orderBy('id','DESC')->paginate(10);
        return [
            'paginate' => [
                'total'        => $sucursal->total(),
                'current_page' => $sucursal->currentPage(),
                'per_page'     => $sucursal->perPage(),
                'last_page'    => $sucursal->lastPage(),
                'from'         => $sucursal->firstItem(),
                'to'           => $sucursal->lastPage(),
            ],
            'sucursal' => $sucursal
        ];
    }
    public function listclientes(Request $request){
        $cliente = cliente::orderBy('id','DESC')->paginate(10);
        return [
            'paginate' => [
                'total'        => $cliente->total(),
                'current_page' => $cliente->currentPage(),
                'per_page'     => $cliente->perPage(),
                'last_page'    => $cliente->lastPage(),
                'from'         => $cliente->firstItem(),
                'to'           => $cliente->lastPage(),
            ],
            'cliente' => $cliente
        ];
    }
    public function listrol(Request $request){
        $roles = rol::orderBy('id','DESC')->paginate(10);
        return [
            'paginate' => [
                'total'        => $roles->total(),
                'current_page' => $roles->currentPage(),
                'per_page'     => $roles->perPage(),
                'last_page'    => $roles->lastPage(),
                'from'         => $roles->firstItem(),
                'to'           => $roles->lastPage(),
            ],
            'roles' => $roles
        ];
    }
    public function list_movimiento(Request $request){
        $procedure = "call get_movimientos(?,?,?,?,?,?)";
        $parameter = [
            $request->desde == null ? date('Y-m-d') : $request->desde,
            $request->hasta == null ? date('Y-m-d') : $request->hasta,
            $request->tipo,
            $request->sucursal,
            $request->page,
            10
        ];
        $data = DB::select($procedure, $parameter);
        $count = 0;
        if(count($data)){
            $count = $data[0]->count;
        }
        return ["data" => $data, "count" => $count, "perpage" => 10, "paginas" => round($count / 10)];
    }
    public function exportarmovimiento($desde,$hasta,$tipo,$sucursal){
        if($desde === "000-00-00" || $hasta === "000-00-00"){
            $movimiento = Movimientos::select('movimientos.nro_documento as DOCUMENTO','products.codigo as CODIGO','products.nompro as PRODUCTO/DESCRIPCION','products.marca as MARCA','movimientos.precio as PRECIO','movimientos.cantidad as CANTIDAD','movimientos.detalle as DETALLES','movimientos.sucursal as SUCURSAL','movimientos.fecha as FECHA')
            ->join('products','movimientos.barra_mov','products.barra')
            ->where('movimientos.fecha',date('Y-m-d'))
            ->where('movimientos.tipo',$tipo)
            ->where('movimientos.sucursal',$sucursal)
            ->orderBy('products.nompro')
            ->get();
        }else{
            $movimiento = Movimientos::select('movimientos.nro_documento as DOCUMENTO','products.codigo as CODIGO','products.nompro as PRODUCTO/DESCRIPCION','products.marca as MARCA','movimientos.precio as PRECIO','movimientos.cantidad as CANTIDAD','movimientos.detalle as DETALLES','movimientos.sucursal as SUCURSAL','movimientos.fecha as FECHA')
            ->join('products','movimientos.barra_mov','products.barra')
            ->whereBetween('movimientos.fecha',[$desde,$hasta])
            ->where('movimientos.tipo',$tipo)
            ->where('movimientos.sucursal',$sucursal)
            ->orderBy('products.nompro')
            ->get();
        }
        return $movimiento;
    }
    public function kardex(Request $request,$desde,$hasta,$tipo,$sucursal,$barra){
        if($tipo == "vacio" AND $desde == "0"){
            $movimiento = Movimientos::select('products.nompro','products.marca','movimientos.precio','movimientos.cantidad','movimientos.fecha','movimientos.condicion','movimientos.detalle','movimientos.sucursal','movimientos.nro_documento')
            ->join('products','movimientos.barra_mov','products.barra')
            ->where('products.barra',$barra)
            ->where('movimientos.sucursal',$sucursal)
            ->orderBy('movimientos.id','DESC')
            ->paginate(12);
        }elseif($tipo != "vacio" AND $desde == "0"){
            $movimiento = Movimientos::select('products.nompro','products.marca','movimientos.precio','movimientos.cantidad','movimientos.fecha','movimientos.condicion','movimientos.detalle','movimientos.sucursal','movimientos.nro_documento')
            ->join('products','movimientos.barra_mov','products.barra')
            ->where('products.barra',$barra)
            ->where('movimientos.tipo',$tipo)
            ->where('movimientos.sucursal',$sucursal)
            ->orderBy('movimientos.id','DESC')
            ->paginate(12);
        }
        else{
            $movimiento = Movimientos::select('products.nompro','products.marca','movimientos.precio','movimientos.cantidad','movimientos.fecha','movimientos.condicion','movimientos.detalle','movimientos.sucursal','movimientos.nro_documento')
            ->join('products','movimientos.barra_mov','products.barra')
            ->whereBetween('movimientos.fecha',[$desde,$hasta])
            ->where('movimientos.tipo',$tipo)
            ->where('products.barra',$barra)
            ->where('movimientos.sucursal',$sucursal)
            ->orderBy('products.nompro')
            ->paginate(12);
        }
        return [
            'paginate' => [
                'total'        => $movimiento->total(),
                'current_page' => $movimiento->currentPage(),
                'per_page'     => $movimiento->perPage(),
                'last_page'    => $movimiento->lastPage(),
                'from'         => $movimiento->firstItem(),
                'to'           => $movimiento->lastPage(),
            ],
            'movimiento' => $movimiento
        ];
    }
    public function marcas_select(){
        return marca::all();
    }
    public function createmarca(Request $request)
    {
        $marca = new marca;
        $marca->nommar = trim($request->nommar);
        $marca->save();
        return back();
    }
    public function store(Request $request)
    {
            if(product::where('barra',$request->barra)->exists()){
                return "este producto ya existe en tu almacen";
            }else{
                if($request->imagen === null){
                    $producto = new product;
                    $producto->codigo=trim($request->codigo);
                    $producto->barra=trim($request->barra);
                    $producto->nompro=trim($request->nompro);
                    $producto->precio=trim($request->precio);
                    $producto->marca=trim($request->marca);
                    $producto->url_imagen="default.png";
                    $producto->baja=0;
                    // $producto->day = date('d');
                    $producto->save();
                    return "agregado";
                }else{
            $file = $request->imagen;
            $destinoPath = public_path().'/images/productos';
            $url_imagen = $file->getClientOriginalName();
            $subir = $file->move($destinoPath,$file->getClientOriginalName());
            if($subir)
            {
                $producto = new product;
                $producto->codigo=trim($request->codigo);
                $producto->barra=trim($request->barra);
                $producto->nompro=trim($request->nompro);
                $producto->precio=trim($request->precio);
                $producto->marca=trim($request->marca);
                $producto->baja=0;
                $producto->url_imagen=$url_imagen;
                $producto->save();
                return "agregado";
            }
          }
        }
    }
    public function createsucursal(Request $request){
        $sucursal = new sucursal;
        $sucursal->nombre = $request->nombre;
        $sucursal->save();
        $ventas = new ventas;
        $ventas->sucursal = $request->nombre;
        $ventas->save();
        $traslados = new traslados;
        $traslados->sucursal = $request->nombre;
        $traslados->save();
        $notas = new Notas;
        $notas->sucursal = $request->nombre;
        $notas->save();
        return back();
    }
    public function agregarcliente(Request $request)
    {
        $cliente = new cliente;
        $cliente->nombre = trim($request->nombre);
        $cliente->ruc_dni = trim($request->ruc_dni);
        $cliente->telefono = trim($request->telefono);
        $cliente->direccion = trim($request->direccion);
        $cliente->save();
        return back();
    }
    public function createrol(Request $request){
        $rol = new rol;
        $rol->nomrol = trim($request->nomrol);
        $rol->save();
        return back();
    }
    public function show($id)
    {
        //
    }
    public function editarImagen(Request $request)
    {
        $file = $request->imagen;
        $destinoPath = public_path().'/images/productos';
        $url_imagen = $file->getClientOriginalName();
        $subir = $file->move($destinoPath,$file->getClientOriginalName());
        if($subir)
        {
            $producto = product::find($request->id);
            $image_path = public_path().'/images/productos/'.$producto->url_imagen;
            if($producto->url_imagen !== null){
                unlink($image_path);
            }
            $producto->url_imagen=$url_imagen;
            $producto->save();
        }   
    }
    public function update(Request $request, $id)
    {
        $producto = product::find($id);
        $producto->codigo=trim($request->codigo);
        $producto->barra=trim($request->barra);
        $producto->nompro=trim($request->nompro);
        $producto->precio=trim($request->precio);
        $producto->marca=trim($request->marca);
        $producto->save();
        return $producto;
    }
    public function baja($id,$estado){
        $producto = product::find($id);
        $producto->baja=$estado;
        $producto->save();
    }
    public function editarcliente(Request $request, $id)
    {
        $cliente = cliente::find($id);
        $cliente->nombre = trim($request->nombre);
        $cliente->ruc_dni = trim($request->ruc_dni);
        $cliente->telefono = trim($request->telefono);
        $cliente->direccion = trim($request->direccion);
        $cliente->save();
        return $cliente;
    }
    public function editarsucursal(Request $request, $id)
    {
        $sucursal = sucursal::find($id);
        $sucursal->nombre = $request->nombre;
        $sucursal->save();
        return $sucursal;
    }
    public function editarmarca(Request $request,$id)
    {
        $marca = marca::find($id);
        $marca->nommar = $request->nommar;
        $marca->save();
        return $marca;
    }
    public function destroy($id)
    {
        $producto = product::find($id);
        $image_path = public_path().'/images/productos/'.$producto->url_imagen;
        unlink($image_path);
        $producto->delete();
    }
    public function deletemarca($id){
        $marca = marca::find($id);
        $marca->delete();
    }
    public function deletesucursal($id){
        $sucursal = sucursal::find($id);
        $sucursal->delete();
    }
    public function deletecliente($id){
        $cliente = cliente::find($id);
        $cliente->delete();
    }
    public function deleteroles($id)
    {
        $rol = rol::find($id);
        $rol->delete();
    }
    public function bajostock($sucursal){
        return product::select('products.nompro','almacen.stock_almacen','products.codigo')
        ->join('almacen','products.barra','almacen.barra_almacen')
        ->where('almacen.sucursal',$sucursal)
        ->where('almacen.stock_almacen','<',4)
        ->where('almacen.stock_almacen','>',0)
        ->where('products.baja',0)
        ->limit('30')
        ->get();
    }
    // ******** EXPORTAR ********
    public function exportar_excel($marca,$sucursal){
      return almacen::select(DB::raw('products.codigo as CODIGO,products.nompro as PRODUCTO,products.marca as MARCA,almacen.precio_venta as PRECIO,almacen.stock_almacen as STOCK'))
       ->join('products','products.barra','almacen.barra_almacen')
       ->where('products.marca',$marca)
       ->where('almacen.sucursal',$sucursal)
       ->where('products.baja',0)
       ->orderBy('products.nompro')
       ->get();
    }
    // ****** EDITAR ALMACEN STOCK PRECIO *******
    public function editarproductalma(Request $request,$id){
        $almacen = almacen::find($id);
        $almacen->stock_almacen = $request->stock;
        $almacen->precio_venta = $request->precio;
        $almacen->precio_mayor = $request->precio_x_mayor;
        $almacen->save();
    }
}
