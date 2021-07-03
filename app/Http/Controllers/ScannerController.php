<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\product;
use App\almacen;
use App\cliente;

class ScannerController extends Controller
{
    public function show($barra){
       return product::where('barra',$barra)
       ->orWhere('codigo',$barra)
       ->first();
    }
    public function nota_ingreso(Request $request)
    {
        if(almacen::where('barra_almacen',$request->barra)->where('sucursal',$request->sucursal)->exists()){
            return "este producto ya existe en tu almacen";
        }else{
            $almacen = new almacen;
            $almacen->barra_almacen = $request->barra;
            $almacen->stock_almacen = $request->stock;
            $almacen->precio_venta = $request->precio;
            $almacen->precio_mayor = $request->preciomayor;
            $almacen->sucursal = $request->sucursal;
            $almacen->save();
            return "producto guardado";
        }
   }    
    public function listadoalmacen(Request $request,$almacen)
    {
        $almacen = almacen::select('almacen.id','almacen.sucursal','products.precio','products.codigo','products.barra','products.nompro','products.marca','products.url_imagen','almacen.precio_venta','almacen.precio_mayor','almacen.stock_almacen')
        ->join('products','almacen.barra_almacen','products.barra')
        ->where('almacen.sucursal',$almacen)
        ->where('products.baja',0)
        ->orderBy('products.nompro')
        ->paginate(10);
        return [
            'paginate' => [
                'total'        => $almacen->total(),
                'current_page' => $almacen->currentPage(),
                'per_page'     => $almacen->perPage(),
                'last_page'    => $almacen->lastPage(),
                'from'         => $almacen->firstItem(),
                'to'           => $almacen->lastPage(),
            ],
            'almacen' => $almacen
        ];
    }
    public function total_calculos($sucursal){
        return almacen::select(DB::raw('sum(almacen.stock_almacen) as total_stock,sum(products.precio * almacen.stock_almacen) as total_inversion'))
        ->join('products','almacen.barra_almacen','products.barra')
        ->where('almacen.sucursal',$sucursal)
        ->first();
    }
    public function escanearventa($condicion,$scan,$almacen)
    {
        $codigo = str_replace("*","/",trim($scan));
        return almacen::select('products.nompro','products.barra','products.precio','products.url_imagen','products.marca','almacen.precio_venta','almacen.precio_mayor','almacen.stock_almacen')
        ->join('products','almacen.barra_almacen','products.barra')
        ->where('almacen.sucursal',$almacen)
        ->where('products.'.$condicion,$codigo)
        ->first();
    }
    // ********************* BUSCAR REGISTROS *****************************
    public function buscaralmacen($condicion,$search)
    {
            $string = str_replace("*","/",trim($search));
            $almacen = almacen::select('almacen.id','almacen.sucursal','products.codigo','products.barra','products.nompro','products.marca','products.precio','products.url_imagen','almacen.precio_venta','almacen.precio_mayor','almacen.stock_almacen')
            ->join('products','almacen.barra_almacen','products.barra')
            ->where('products.'.$condicion,'like','%'.$string.'%')
            ->where('products.baja',0)
            ->paginate(12);
        return [
            'paginate' => [
                'total'        => $almacen->total(),
                // 'current_page' => $almacen->currentPage(),
                'per_page'     => $almacen->perPage(),
                'last_page'    => $almacen->lastPage(),
                'from'         => $almacen->firstItem(),
                'to'           => $almacen->lastPage(),
            ],
            'almacen' => $almacen
        ];
    }
    public function buscarclientes($search)
    {
        $cliente = cliente::orderBy('id','DESC')
        ->where('ruc_dni',$search)
        ->orWhere('nombre',$search)
        ->paginate(8);
        return [
            'paginate' => [
                'total'        => $cliente->total(),
                // 'current_page' => $cliente->currentPage(),
                'per_page'     => $cliente->perPage(),
                'last_page'    => $cliente->lastPage(),
                'from'         => $cliente->firstItem(),
                'to'           => $cliente->lastPage(),
            ],
            'cliente' => $cliente
        ];
    }
    public function productosbuscar($search)
    {
        $string = str_replace("*","/",trim($search));
        $producto = product::orderBy('id','DESC')
        ->where('barra',$string)
        ->orWhere('codigo',$string)
        ->orWhere('nompro','like','%'.$string.'%')
        ->paginate(1);
        return [
           'paginate' => [
                'total'        => $producto->total(),
                // 'current_page' => $producto->currentPage(),
                'per_page'     => $producto->perPage(),
                'last_page'    => $producto->lastPage(),
                'from'         => $producto->firstItem(),
                'to'           => $producto->lastPage(),
           ],
           'product' => $producto
        ];
    }
}
