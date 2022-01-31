<?php

use Illuminate\Support\Facades\Route;
use App\permisos;
Route::get('imprimirticked/{nrof}', 'DompdfController@ticked');
Route::post('imprimircotizar', 'DompdfController@cotizar');
// ***** RUTA INDEX CARGAR PERMISOS DE USUARIO *****
Route::get('/{optional?}',function(){
    if(Auth::user()){
        $permisos = permisos::where('rol',Auth::user()->rol)->get();
        return view('index')->with('permisos',$permisos);
    }else{
        return view('index');
    }
});
Route::post('/ingresarcaja','LoginController@ingresarcaja');
// ****** FIN PERMISOS *******
Auth::routes();
Route::post('/Error','LoginController@iniciarsesion');
Route::post('/cerrar','LoginController@cerrar');
Route::post('/productcreate','SystemController@store');
Route::post('/editarimagen','SystemController@editarImagen');
Route::post('/createmarca','SystemController@createmarca');
Route::post('/createsucursal','SystemController@createsucursal');
Route::post('/agregarcliente','SystemController@agregarcliente');
Route::post('/createrol','SystemController@createrol');
Route::post('/asignarpermisos','PermisosController@asignar');
Route::post('/newusuario','PermisosController@newusuario');
Route::get('/deletecliente/{id}','SystemController@deletecliente');
Route::get('/deleteingreso_salida/{id}','HomeController@deleteingreso_salida');
Route::post('/editarcliente/{id}','SystemController@editarcliente');
// ******** ALMACEN ********
Route::post('/editarproductalma/{id}','SystemController@editarproductalma');
// ****** traslados ******
Route::post('/stock_ingresos_tras/{sucursal}','TrasladosController@stock_ingresos_tras');
Route::post('/estado_ingresos/{id}','TrasladosController@estado_ingresos');
Route::post('/agregar_numero','TrasladosController@agregar_numero');
Route::post('/agregarRequer','TrasladosController@agregarRequer');
Route::post('/stock_pendiente/{sucursal}','TrasladosController@stock_pendiente');
Route::post('/estado_pendiente/{id}','TrasladosController@estado_pendiente');
Route::post('/editrequer/{id}','TrasladosController@editrequer');
Route::get('/deleterequer/{id}','TrasladosController@deleterequer');
Route::get('/deleterequerimiento/{id}/{cod}','TrasladosController@deleterequerimiento');
Route::get('/deleteroles/{id}','SystemController@deleteroles');
//NOTA DE INGRESO
Route::post('/nota_ingreso','ScannerController@nota_ingreso');
Route::post('/cambiarestado_nota/{id}','NotasController@cambiarestado_nota');
Route::post('/subirstock_nota/{sucursal}','NotasController@subirstock_nota');
Route::get('/eliminarnota/{codigo}','NotasController@eliminarnota');
Route::get('/DarBajanota/{codigo}/{id}','NotasController@DarBajanota');
Route::post('/bajarstock_nota/{sucursal}','NotasController@bajarstock_nota');
// VENTAS
Route::post('/generar_venta','VentasController@generar_venta');
Route::post('/detalle_venta','VentasController@detalle_venta');
Route::post('/modificaracumulado/{id}','VentasController@modificaracumulado');
Route::post('/anularfactura','VentasController@anularfactura');
Route::post('/cambiar_estado_ventas/{id}','VentasController@cambiar_estado');
// **** MARCAS *****
Route::get('/deletemarca/{id}','SystemController@deletemarca');
Route::post('/editarmarca/{id}','SystemController@editarmarca');
Route::post('/editarol/{id}','PermisosController@editarol');
// ***** NOTAS DE PEDIDO,INGRESO,PROVEEDOR ******
Route::post('crearempresa','NotasController@crearempresa');

Route::post('/agregar_detalle_nota','NotasController@agregar_detalle_nota');
Route::post('/agregar_nota','NotasController@agregar_nota');
// ******** PERMISOS *********
Route::post('/editarpermisos/{id}','PermisosController@editarpermisos');
Route::get('/deletesucursal/{id}','SystemController@deletesucursal');
Route::post('/editarsucursal/{id}','SystemController@editarsucursal');
// ****** PRODUCTOS *******
Route::get('/eliminarproduct/{id}','SystemController@destroy');
Route::post('/producteditar/{id}','SystemController@update');
// ***** PROVEEDOR ******
Route::get('/deleteproveedor/{id}','NotasController@deleteproveedor');
// ***** USUARIOS *****
Route::get('/deleteuser/{id}','PermisosController@deleteuser');
Route::post('/editarusuarios/{id}','PermisosController@editarusuarios');
Route::post('/ingresos_salidas_create','HomeController@ingresos_salidas_create');
// ************** SALIDA DE PRODUCTOS **************
Route::post('/agregar_salida','SalidaProductController@agregar_salida');
Route::post('/detalle_product','SalidaProductController@detalle_product');
Route::post('/bajarstockSalida','SalidaProductController@bajarstockSalida');