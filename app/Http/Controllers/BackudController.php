<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;

class BackudController extends Controller
{
    public function start(Request $request){
        // if($request->password == "backudbd@2021@copiasegura"){
            $nombre = 'copia.sql';
              $directorio = 'C:\\backud';
              $dir = $directorio.'\\'.$nombre;
              $user = "root";
              $password = "";
              $host = "localhost";
              $bd = "giomar_sistema";
            //   $comando = "C:\\AppvServ\mysql\bin\mysqldump.exe --opt --user=$user --password=$password giomar_sistema > $dir";
            $comando = "mysqldump --host=$host --user=$user --password=$password --opt$bd > $dir";
               system($comando,$outpub);
        // }else{
        //     return "contraseña incorrecta";
        // }
    }
}
