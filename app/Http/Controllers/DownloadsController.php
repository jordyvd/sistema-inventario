<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use ZipArchive;
use App\Exports\DocumentosElectronicos;
use Maatwebsite\Excel\Facades\Excel;

class DownloadsController extends Controller
{
    public function downloadZip()
    {
        $ruta = 'C:\xampp\htdocs\FacturadorSunat\api_cpe\pdf'; // local
       // $ruta = '/opt/lampp/htdocs/cpeE/api_cpe/pdf'; // pro
        $zip = new ZipArchive;
        $fileName = date('d-m-Y').'.zip';
        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
        {
            $files = File::files($ruta);
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }
        return response()->download(public_path($fileName));
    }
    public function exportarDocumentos(Request $request)
    {
        $data = [
            "data" => $request->documentos,
            "fecha" => $request->fecha != null ?$request->fecha : date('d-m-Y'),
            "fecha_end" => $request->fecha_end != null ? $request->fecha_end : date('d-m-Y'),
        ];
        // return $data;
        return Excel::download(new DocumentosElectronicos($data), 'vs_payment.xlsx');
    }
}
