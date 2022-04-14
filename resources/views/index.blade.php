<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv='X-UA-Compatible' content='IE=8'>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if(Auth::user())
      <title>{{Auth::user()->sucursal}} - sistema de administración</title>
      <meta name="user_name" content="{{ Auth::user()->name}}">
      <meta name="user_sucursal" content="{{ Auth::user()->sucursal}}">
      <meta name="user_rol" content="{{ Auth::user()->rol}}">
      <meta name="user_id" content="{{ Auth::user()->id}}">
      <meta name="user_id_sucursal" content="{{ Auth::user()->id_sucursal}}">
      @foreach($permisos as $info)
        <meta name="traslados" content="{{ $info->traslados }}">
        <meta name="ventas" content="{{ $info->ventas }}">
        <meta name="sucursal_ventas" content="{{ $info->sucursal_ventas }}">
        <meta name="reportes" content="{{ $info->reportes }}">
        <meta name="report_ingre" content="{{ $info->reporte_ingres }}">
        <meta name="report_egre" content="{{ $info->reporte_egreso }}">
        <meta name="ganancias" content="{{ $info->ganancias }}">
        <meta name="clientes" content="{{ $info->clientes }}">
        <meta name="mantenimiento" content="{{ $info->mantenimiento }}">
        <meta name="agregar_modif_mante" content="{{ $info->agregar_modif_mante }}">
        <meta name="almacen" content="{{ $info->almacen }}">
        <meta name="permisos" content="{{ $info->permisos }}">
        <meta name="compras" content="{{ $info->compras }}">
      @endforeach
    @else
    <title>sistema de administración</title>
    @endif
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{asset('css/util.css')}}">
    <link rel="stylesheet" href="{{asset('css/main.css')}}">
    <link rel="stylesheet" href="{{asset('css/pace.css')}}">
    <link rel="stylesheet" href="{{asset('css/menu.css')}}">
    <link rel="stylesheet" href="{{asset('css/estilos.css?version=2022-02-26-12:30:00')}}">
    <link rel="stylesheet" href="{{asset('css/bienvenida.css')}}">
    <link rel="shortcut icon" type="image/x-icon" href="images/iconoApp.ico"/>
</head>
  <body>
    @if(Auth::user())
       <div id="app">
       <menu_sistema></menu_sistema>
       </div>
    @else
       <div id="app">
       <login_user></login_user>
       </div>
    @endif
    <!-- importante -->
       <script src="{{ asset('js/app.js?version=2022-04-13 23:48:00')}}"></script>
       <!-- <script src="{{ asset('js/app.js') }}"></script> -->
       <script src="{{asset('js/jquery.js')}}"></script>
       <script src="{{asset('js/main.js')}}"></script>
       <script src="{{asset('js/poper.js')}}"></script>
       <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
       <script src="{{asset('js/num-text.js')}}"></script>
       <script src="{{asset('js/funciones.js')}}"></script>
       <script src="{{asset('js/pace.min.js')}}"></script>
  </body>
</html>