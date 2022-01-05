<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Luecano\NumeroALetras\NumeroALetras;
use Illuminate\Support\Facades\DB;

class SunatController extends Controller
{
    public function getRuc($number){
        $token = '';

        $client = new Client(['base_uri' => 'https://api.apis.net.pe', 'verify' => false]);
        
        $parameters = [
            'http_errors' => false,
            'connect_timeout' => 5,
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Referer' => 'https://apis.net.pe/api-consulta-ruc',
                'User-Agent' => 'laravel/guzzle',
                'Accept' => 'application/json',
            ],
            'query' => ['numero' => $number]
        ];
        $res = $client->request('GET', '/v1/ruc', $parameters);
        $response = json_decode($res->getBody()->getContents(), true);
        return $response;
    }
    public function getDni($number){
        $token = '';
        $numero = '46027897';
        $client = new Client(['base_uri' => 'https://api.apis.net.pe', 'verify' => false]);
        $parameters = [
            'http_errors' => false,
            'connect_timeout' => 5,
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Referer' => 'https://apis.net.pe/api-consulta-dni',
                'User-Agent' => 'laravel/guzzle',
                'Accept' => 'application/json',
            ],
            'query' => ['numero' => $number]
        ];
        $res = $client->request('GET', '/v1/dni', $parameters);
        $response = json_decode($res->getBody()->getContents(), true);
        return $response;
    }
    public function generarDocumento(Request $request){
        $productos = [];
        $igv = 0;
        $total = 0;
        $total_sin_igv = 0;
        $direccion = $this->direccionSucursal($request);
        $codigoTypoDocumento = $this->codigoTypoDocumentoF($request);

        foreach($request['productos'] as $producto){
              $importe = $producto['precio'] * $producto['cantidad'];

              $precio_igv = $producto['precio'] / 1.18;

              $importe_igv = $precio_igv * $producto['cantidad'];
              $productos[] = [
                   "codigo" => $producto['barra'],
                   "precio" => str_replace(",","", number_format($producto['precio'], 2)),
                   "precioSinIgv" => str_replace(",","", number_format($precio_igv, 2)),
                   "igv" => str_replace(",","", number_format($importe / 1.18 * 0.18, 2)),
                   "descripcion" => $producto['producto'],
                   "marca" => $producto['marca'],
                   "cantidad" => $producto['cantidad'],
                   "importe" => str_replace(",","", number_format($importe, 2)),
                   "importSinIgv" => str_replace(",","", number_format($importe_igv, 2)),
              ];
              $igv += str_replace(",","", ($importe / 1.18) * 0.18);
              
              $total += str_replace(",","", $importe);

              $total_sin_igv += str_replace(",","", $importe_igv);
        }
        $formatter = new NumeroALetras();
        $totalText = $formatter->toInvoice($total, 2, 'SOLES');
        $parameter = [
               "emisor" => [
                    "ruc" => "10405163131",
                    "nombre" => "CAMONES MARCELO YOMAR WALTER",
                    "direccion" => $direccion,
                    "telefono" => "937522124",
               ],
               "cliente" => [
                    "nombre" => $request->cliente['nombre'],
                    "numeroDocumento" => $request->cliente['numeroDocumento'],
                    "tipoDocumento" => $request->cliente['tipoDocumento'],
                    "direccion" => $request->cliente['direccion'],
                    "provincia" => $request->cliente['provincia'],
                    "departamento" => $request->cliente['departamento'],
                    "distrito" => $request->cliente['distrito'],
                    "viaNombre" => $request->cliente['viaNombre'],
                    "viaTipo" => $request->cliente['viaTipo'],
                    "zonaCodigo" => $request->cliente['zonaCodigo'],
                    "zonaTipo" => $request->cliente['zonaTipo'],
                    "ubigeo" => $request->cliente['ubigeo']
               ],
               "code" => $codigoTypoDocumento['codigo']."-".$codigoTypoDocumento['tipoComprobante']."-0000006",
               "tipoDocumento" => $codigoTypoDocumento['codigo'],
               "productos" => $productos,
               "fecha" => date("Y-m-d h:i:s a"),
               "fechaPdf" => date("d-m-Y h:i:s a"),
               "igv" => str_replace(",","", number_format($igv, 2)),
               "total" => str_replace(",","", number_format($total, 2)),
               "totalText" => $totalText,
               "totalSinIgv" => str_replace(",","", number_format($total_sin_igv, 2)),
               "porcentajeIgv" => 18,
        ];
        $this->guardarDocumento($parameter);
        return $parameter;
    }
    public function codigoTypoDocumentoF(Request $request){
        $codigo = "";
        $tipoComprobante = "";
        if($request['documento'] == "factura"){
            $codigo = "01";
            $tipoComprobante = "F001";
        }else if($request['documento'] == "boleta"){
            $codigo = "03";
            $tipoComprobante = "B001";
        }
        return [
            "codigo" => $codigo,
            "tipoComprobante" => $tipoComprobante
        ];
    }
    public function direccionSucursal(Request $request){
        $direccion = "";
        if($request['sucursal'] == "huaral"){
            $direccion = "Calle Morales Bermúdez # 340 y 342";
        }else if($request['sucursal'] == "lima-abancay"){
            $direccion = "Av. Abancay # 368 Int. 1090 - Galería La Casona";   
        }else if($request['sucursal'] == "barranca"){
            $direccion = "JR. Castilla 148";   
        }
        return $direccion;
    }
    public function creditNote(Request $request){

    }
    public function guardarDocumento($request){
       $explode = explode("-", $request['code']);
       $procedure = "call insert_documento_electronico(?,?,?,?,?,?)";
       $parameter = [
            $request['code'],
            $explode[0],
            $request['total'],
            null,
            null,
            null
       ];
       DB::statement($procedure, $parameter);
    } 
    public function EditarDocumento(Request $request){
        $procedure = "call update_documento_electronico(?,?,?,?)";
        $parameter = [
            $request['code'],
            $request['qr'],
            $request['sunat']['estado'],
            1
        ];
        DB::statement($procedure, $parameter);
    }
    public function listarDocumentos(){
        $procedure = "call listar_documentos()";
        return DB::select($procedure);
    }
}
