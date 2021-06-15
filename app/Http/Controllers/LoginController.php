<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginFormRequest;
use Illuminate\Support\Facades\Auth;
use App\caja;

class LoginController extends Controller
{
    public function iniciarsesion(LoginFormRequest $request)
    {
       if(Auth::attempt(['dni' => $request->dni,'password'=>$request->password],false))
       {
           return response()->json("Sesión iniciada",200);
           
       }else{
           return response()->json(['errors'=>['login'=>['los datos ingresados son incorrectos']]],422);
       }
    }
    public function cerrar(){
        Auth::logout();
    }
    public function ingresarcaja(Request $request){
        // if(caja::where('fecha', date("Y-m-d")->where('sucursal',$request->sucursal)->exist()){
        //     $caja = new caja;
        //     $caja->cantidad = $request->cantidad;
        //     $caja->fecha = date("Y-m-d");
        //     $caja->sucursal = $request->sucursal;
        //     $caja->save();
        // }else{

        // }
        // $caja = new caja;
        // $caja->cantidad = $request->cantidad;
        // $caja->fecha = date("Y-m-d");
        // $caja->sucursal = $request->sucursal;
        // $caja->save();
    }
}
