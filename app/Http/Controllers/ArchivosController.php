<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArchivosController extends Controller
{
    public function getArchivos(Request $request)
    {
        $statement = "call get_archivos(?,?)";
        $parameter = [
           $request->id,
           $request->folder != "" ? $request->folder : null,
        ];
        return DB::select($statement, $parameter);
    }

    public function insertFolder(Request $request){
        $statement = "insert into archivos(foreign_id, url, nombre, extension, tipo, padre, descripcion, created_at, updated_at) values(?,?,?,?,?,?,?,?,?)";
        $parameter = [
            $request->id,
            null,
            $request->nombre,
            null,
            1,
            $request->folder != "" ? $request->folder : null,
            $request->descripcion,
            date("Y-m-d H:i:s"),
            date("Y-m-d H:i:s")
        ];
        DB::statement($statement, $parameter);
        return $this->getArchivos($request);
    }

    public function insertArchivos(Request $request){
        foreach($request['files'] as $file){
            $folder = public_path().'/images/bd_archivos';
            $nombre = $file->getClientOriginalName();
            $file->move($folder, $file->getClientOriginalName());
            $extension = $file->getClientOriginalExtension();
            $statement = "insert into archivos(foreign_id, url, nombre, extension, tipo, padre, descripcion, created_at, updated_at) values(?,?,?,?,?,?,?,?,?)";
            $parameter = [
                $request->id,
                '/images/bd_archivos/'.$nombre,
                $nombre,
                $extension,
                2,
                $request->folder != "" ? $request->folder : null,
                $request->descripcion,
                date("Y-m-d H:i:s"),
                date("Y-m-d H:i:s")
            ];
            DB::statement($statement, $parameter);
        } 
        return $this->getArchivos($request);
    }

    public function guardarDescripcion(Request $request){
        $statement = "update archivos set descripcion = ? where id = ?";
        $parameter = [
            $request->descripcion,
            $request->id,
        ];
        DB::statement($statement, $parameter);
    }

    public function deleteFile(Request $request){
        $statement = "delete from archivos where id = ? or padre = ?";
        $parameter = [
           $request->id,
           $request->id
        ];
        DB::statement($statement, $parameter);
    }
}
