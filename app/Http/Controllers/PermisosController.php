<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\permisos;
use App\usuario;
use App\rol;

class PermisosController extends Controller
{
    public function menu_permisos($id){
    return usuario::select('permisos.traslados','permisos.ventas','permisos.sucursal_ventas','permisos.reportes','permisos.reporte_ingres','permisos.reporte_egreso','permisos.ganancias','permisos.clientes','permisos.mantenimiento','permisos.agregar_modif_mante','permisos.almacen','permisos.permisos','permisos.compras')
    ->join('permisos','usuarios.rol','permisos.rol')
    ->where('usuarios.id',$id)
    ->first();
    }
    public function existerol($id){
        $confirm = "";
        if(permisos::where('rol',$id)->exists()){
               return $confirm = "si";
        }else{
            return $confirm = "no";
        }
        return $confirm;
    }
    public function view_permisos($id){
       return permisos::where('rol',$id)
       ->first();
    }
    public function editarpermisos(Request $request, $id)
    {
        $id_permiso = permisos::where('rol',$id)->first();
        $permisos = permisos::find($id_permiso->id);
        $permisos->traslados = $request->traslados;
        $permisos->ventas = $request->ventas;
        $permisos->sucursal_ventas = $request->sucursal_ventas;
        $permisos->reportes = $request->reportes;
        $permisos->reporte_ingres = $request->report_ingre;
        $permisos->reporte_egreso = $request->report_egre;
        $permisos->ganancias = $request->ganancias;
        $permisos->clientes = $request->clientes;
        $permisos->mantenimiento = $request->mantenimiento;
        $permisos->agregar_modif_mante = $request->agregar_modif_mante;
        $permisos->almacen = $request->almacen;
        $permisos->permisos = $request->permiso;
        $permisos->compras = $request->compras;
        $permisos->save();
        return $permisos;
    }
    public function asignar(Request $request){
       $permisos = new permisos;
       $permisos->traslados = $request->traslados;
       $permisos->ventas = $request->ventas;
       $permisos->sucursal_ventas = $request->sucursal_ventas;
       $permisos->reportes = $request->reportes;
       $permisos->reporte_ingres = $request->report_ingre;
       $permisos->reporte_egreso = $request->report_egre;
       $permisos->ganancias = $request->ganancias;
       $permisos->clientes = $request->clientes;
       $permisos->mantenimiento = $request->mantenimiento;
       $permisos->agregar_modif_mante = $request->agregar_modif_mante;
       $permisos->almacen = $request->almacen;
       $permisos->permisos = $request->permiso;
       $permisos->rol = $request->rol;
       $permisos->compras = $request->compras;
       $permisos->save();
       return back();
    }
    public function newusuario(Request $request){
        $usuario = new usuario;
        $usuario->rol = trim($request->rol);
        $usuario->sucursal = trim($request->sucursal);
        $usuario->id_sucursal = trim($request->id_sucursal);
        $usuario->name = trim($request->name);
        $usuario->dni = trim($request->dni);
        $usuario->password = bcrypt($request->password);
        $usuario->save();
        return back();
    }
    public function listusuarios()
    {
        $usuario = usuario::select('usuarios.id','usuarios.name','usuarios.dni','usuarios.sucursal','usuarios.password','usuarios.rol','rol.nomrol','usuarios.sucursal')
        ->join('rol','usuarios.rol','rol.id')
        ->orderBy('usuarios.name')
        ->paginate(10);
        return [
            'paginate' => [
                 'total'        => $usuario->total(),
                 'current_page' => $usuario->currentPage(),
                 'per_page'     => $usuario->perPage(),
                 'last_page'    => $usuario->lastPage(),
                 'from'         => $usuario->firstItem(),
                 'to'           => $usuario->lastPage(),
            ],
            'usuarios' => $usuario
         ];
    }
    public function deleteuser($id)
    {
        $usuario = usuario::find($id);
        $usuario->delete();
    }
    public function editarusuarios(Request $request, $id)
    {
        $usuario = usuario::find($id);
        $usuario->name = trim($request->name);
        $usuario->dni = trim($request->dni);
        $usuario->rol = trim($request->rol);
        $usuario->sucursal = trim($request->sucursal);
        $usuario->id_sucursal = trim($request->id_sucursal);
        if($request->password == "passvacio123")
        {
           
        }else{
           $usuario->password = bcrypt($request->password);
        }
        $usuario->save();
        return $usuario;

    }
    public function editarol(Request $request,$id)
    {
        $rol = rol::find($id);
        $rol->nomrol = $request->nomrol;
        $rol->save();
        return $rol;
    }
}
