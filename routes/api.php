<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
//  ****** HOME ******
Route::get('/buscar_id_sucursal/{sucursal}','HomeController@buscar_id_sucursal');
Route::get('/contar_ventas/{sucursal}','HomeController@index');
Route::get('/total_venta_actual/{sucursal}','HomeController@total_venta_actual');
Route::get('/total_pendientes/{sucursal}','HomeController@total_pendientes');
Route::get('/total_agotados/{sucursal}','HomeController@total_agotados');
Route::get('/listaringresos_salidas/{sucursal}/{fecha}','HomeController@listaringresos_salidas');
Route::get('/descripsea/{search}/{sucursal}/{fecha}','HomeController@descripsea');
Route::get('/total_salida/{sucursal}','HomeController@total_salida');
Route::get('/total_ingresos/{sucursal}','HomeController@total_ingresos');
Route::get('/montoingresa/{sucursal}/{fecha}','HomeController@montoingresa');
Route::get('/montosalidas/{sucursal}/{fecha}','HomeController@montosalidas');
Route::get('/total_caja/{sucursal}/{fecha}','HomeController@total_caja');
Route::get('/caja_trasnferencia/{sucursal}/{fecha}','HomeController@caja_trasnferencia');
// *****  FIN HOME *****
Route::get('/productoslista','SystemController@index');
Route::get('/listmarca','SystemController@listmarca');
Route::get('/listsucursal','SystemController@listsucursal');
Route::get('/marcas_select','SystemController@marcas_select');
Route::get('/listclientes','SystemController@listclientes');
Route::get('/listrol','SystemController@listrol');
Route::get('/listusuarios','PermisosController@listusuarios');
Route::get('/menu_permisos/{id}','PermisosController@menu_permisos');
Route::get('/view_permisos/{id}','PermisosController@view_permisos');
Route::get('/existerol/{id}','PermisosController@existerol');
// ESCANEAR
Route::get('/escanear/{barra}','ScannerController@show');
//ALAMACEN 
Route::get('/listadoalmacen/{almacen}','ScannerController@listadoalmacen');
Route::get('/total_calculos/{sucursal}','ScannerController@total_calculos');
Route::get('/escanearventa/{condicion}/{scan}/{almacen}','ScannerController@escanearventa');
Route::get('/nrof/{sucursal}','VentasController@nuevaventa');
Route::get('/bajostock/{sucursal}','SystemController@bajostock');
Route::get('/exportar_excel/{marca}/{sucursal}','SystemController@exportar_excel');
Route::get('/listado_movimiento/{desde}/{hasta}/{cond}/{sucursal}/{cantidad}','SystemController@list_movimiento');
Route::get('/list_kardex/{desde}/{hasta}/{cond}/{sucursal}/{barra}','SystemController@kardex');
Route::get('/exportar_movimiento/{desde}/{hasta}/{cond}/{sucursal}','SystemController@exportarmovimiento');
// VENTAS 
Route::get('/agregadosP/{sucursal}','VentasController@agregadosP');
Route::get('/listventas/{sucursal}/{fecha}/{desde}/{estado}','VentasController@listventas');
Route::get('/listcreditos/{sucursal}/{fecha}/{desde}','VentasController@listcreditos');
Route::get('/listdetalles/{nrof}/{sucursal}','VentasController@listdetalles');
Route::get('/search_ventas/{search}/{sucursal}/{estado}','VentasController@search_ventas');
Route::get('/buscarclienteventa/{cliente}','VentasController@buscarclienteventa');
Route::get('/llenar_listaV/{nrof}','VentasController@llenar_listaV');
// *********** BUSCAR REGISTROS ************
Route::get('/buscaralmacen/{condicion}/{search}','ScannerController@buscaralmacen');
Route::get('/buscarclientes/{search}','ScannerController@buscarclientes');
Route::get('/productosbuscar/{search}','ScannerController@productosbuscar');
// ***************** REQUERIMIENTOS ************
Route::get('/gennro/{sucursal}','TrasladosController@gennro');
Route::get('/sucursales_list/{sucursal}','TrasladosController@sucursales_list');
Route::get('/listarrequerimientos/{sucursal}/{fecha}','TrasladosController@listarrequerimientos');
Route::get('/escanera_requer/{barra}/{sucursal}','TrasladosController@escanear');
Route::get('/detallerequer/{nro}','TrasladosController@detallerequer');
Route::get('/listarpendientes/{sucursal}/{fecha}','TrasladosController@listarpendientes');
Route::get('/buscar_pendientes/{sucursal}/{search}','TrasladosController@buscar_pendientes');
Route::get('/detalles_pen/{nro}/{de}/{para}','TrasladosController@detalles_pen');
Route::get('/ingresos_requer/{sucursal}/{fecha}','TrasladosController@ingresos_requer');
Route::get('/buscar_numero_tras/{sucursal}/{search}/{estado}','TrasladosController@buscar_numero_tras');
Route::get('/buscar_numero_ingresos/{sucursal}/{search}/{estado}','TrasladosController@buscar_numero_ingresos');
// ******************** GRAFICOS **************
Route::get('/grafico_ventas/{desde}/{hasta}/{sucursal}','GraficosController@ventas');
Route::get('/listclientes_select','GraficosController@listclientes_select');
Route::get('/grafico_clientes/{desde}/{hasta}/{cliente}','GraficosController@grafico_clientes');
Route::get('/grafico_vendedor/{desde}/{hasta}/{usuario}','GraficosController@grafico_vendedor');
Route::get('/grafico_ganancias/{desde}/{hasta}/{sucursal}','GraficosController@grafico_ganancias');
Route::get('/grafico_proveedor/{desde}/{hasta}/{sucursal}','GraficosController@grafico_proveedor');
Route::get('/grafico_Sinternras/{desde}/{hasta}/{sucursal}','GraficosController@grafico_Sinternras');
// ********** NOTAS DE INGRESO *****************
Route::get('/listarproveedor','NotasController@listarproveedor');
Route::get('/buscarproveedor/{ruc}','NotasController@buscarproveedor');
Route::get('/codigo_generado/{sucursal}','NotasController@codigo_generado');
Route::get('/listar_pedidos/{sucursal}/{fecha}','NotasController@listar_pedidos');
Route::get('/detallesnotas_pedido/{codigo}','NotasController@detallesnotas_pedido');
Route::get('/buscar_num_pedido/{search}/{sucursal}','NotasController@buscar_num_pedido');
Route::get('/llenar_listaN/{nrof}','NotasController@llenar_listaN');
// ************* SALIDA DE PRODUCTOS *************
Route::get('/nrofsalidas/{sucursal}','SalidaProductController@nrofsalidas');
Route::get('/listarsalidas/{sucursal}/{desde}/{hasta}','SalidaProductController@listarsalidas');
Route::get('/detalle_salidas/{nro}/{sucursal}','SalidaProductController@detalle_salidas');
Route::get('/total_perdida/{sucursal}/{desde}/{hasta}','SalidaProductController@total_perdida');
Route::get('/deletesalidas/{codigo}','SalidaProductController@deletesalidas');
Route::get('/baja/{id}/{estado}','SystemController@baja');

