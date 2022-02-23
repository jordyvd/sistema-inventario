<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Luecano\NumeroALetras\NumeroALetras;
use Illuminate\Support\Facades\DB;

class SunatController extends Controller
{
    public $data = [];

    public $count = 0;

    public $api = "http://facturadorgiomar.site/api_cpe/ReceiveInformation.php"; //pro

    //public $api = "http://localhost/FacturadorSunat/api_cpe/ReceiveInformation.php"; //local

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
    public function getDni($number){{}
        if($number == "111"){
            return [
                    "nombre" => "CLIENTE VARIOS",
                    "tipoDocumento" => "1",
                    "numeroDocumento" => "11111111",
                    "estado" => "",
                    "condicion" => "",
                    "direccion" => "",
                    "ubigeo" => "",
                    "viaTipo" => "",
                    "viaNombre" => "",
                    "zonaCodigo" => "",
                    "zonaTipo" => "",
                    "numero" => "",
                    "interior" => "",
                    "lote" => "",
                    "dpto" => "",
                    "manzana" =>"",
                    "kilometro" => "",
                    "distrito" => "",
                    "provincia" =>"",
                    "departamento" => "",
            ];
        }else{
            $token = '';
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
    }
    public function getVenta(Request $request, $tipo) {
        $procedure = "call get_venta_nota_credito(?, ?, ?)";
        $parameter = [
            $request['id'],
            $tipo == "03" ? "B001" : "F001",
            $tipo == "03" ? "07-B001" : "07-F001",
        ];
        $data = DB::select($procedure, $parameter);
        return $data[0];
    }
    public function generarNotaCredito(Request $request, $productos_p){
        $productos = [];
        $igv = 0;
        $total = 0;
        $total_sin_igv = 0;
        $dataSucursal = $this->dataSucursal($request);
        $codigoTypoDocumento = $this->codigoTypoDocumentoF($request);
        $explodeAnular = explode("-", $request->serie);
        $venta = $this->getVenta($request, $explodeAnular[0]);

        foreach($productos_p as $producto){
             
              $descuento = $producto['descuento'] / $producto['cantidad'];

              $precio =  $producto['precio'] - $descuento;
             
              $importe = $precio * $producto['cantidad'];

              $precio_igv = $precio / 1.18;

              $importe_igv = $precio_igv * $producto['cantidad'];
              $productos[] = [
                   "codigo" => $producto['barra'],
                   "precio" => str_replace(",","", number_format($precio, 2)),
                   "precioSinIgv" => str_replace(",","", number_format($precio_igv, 2)),
                   "igv" => str_replace(",","", number_format($importe / 1.18 * 0.18, 2)),
                   "descripcion" => $producto['nompro'],
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
               "emisor" => json_encode([
                    "ruc" => "10405163131",
                    "nombre" => "CAMONES MARCELO YOMAR WALTER",
                    "direccion" => $dataSucursal['direccion'],
                    "telefono" => "937522124",
               ]),
               "cliente" => json_encode([
                    "nombre" => $venta->nombre_cliente,
                    "numeroDocumento" => $venta->ruc_dni_v,
                    "tipoDocumento" => $venta->doc_sunat == "03" ? "1" : "6",
               ]),
               "operacion" => "firmar",
               "code" => $venta->code,
               "tipoDocumento" => "07",
               "anular" => $request->serie,
               "productos" => json_encode($productos),
               "fecha" => date("Y-m-d h:i:s a"),
               "fechaPdf" => date("d-m-Y h:i:s a"),
               "igv" => str_replace(",","", number_format($igv, 2)),
               "total" => str_replace(",","", number_format($total, 2)),
               "totalText" => $totalText,
               "totalSinIgv" => str_replace(",","", number_format($total_sin_igv, 2)),
               "porcentajeIgv" => 18,
        ];
        $this->guardarCredito($request, $parameter, $venta);
        return $this->firmarNotaCredito($parameter);
    }
    public function firmarNotaCredito($parameter){
        $postdata = http_build_query(
            array(
                $parameter
            )
        );
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        $context = stream_context_create($opts);
        $result = file_get_contents($this->api, false, $context);
        return $result;
    }
    public function guardarCredito(Request $request, $param, $venta){
        $explode = explode("-", $venta->code);
        $procedure = "call insertar_credito(?,?,?,?,?,?,?,?,?)";
        $parameter = [
            $request->id, // id venta
            $venta->code,
            ltrim($explode[2], "0"),
            $explode[0]."-".$explode[1],
            null,
            $request->sucursal,
            null,
            null,
            date("Y-m-d h:i:s")
        ];
        DB::statement($procedure, $parameter);
    }
    public function editarCredito(Request $request, $res, $mensaje){
        //$explode = explode("-", $request['anular']);
        $procedure = "call insertar_credito(?,?,?,?,?,?,?,?,?)";
        $parameter = [
            null,
            $request['serie'],
            null,
            null,
            $res->estado,
            null,
            $mensaje->msj_sunat,
            $mensaje->cod_sunat,
            date("Y-m-d h:i:s")
        ];
        DB::statement($procedure, $parameter);
    }
    public function generarDocumento(Request $request, $serie){
        $productos = [];
        $igv = 0;
        $total = 0;
        $total_sin_igv = 0;
        $dataSucursal = $this->dataSucursal($request);
        $codigoTypoDocumento = $this->codigoTypoDocumentoF($request);

        foreach($request['productos'] as $producto){
             
              $descuento = $producto['descuento'] / $producto['cantidad'];

              $precio =  $producto['precio'] - $descuento;
             
              $importe = $precio * $producto['cantidad'];

              $precio_igv = $precio / 1.18;

              $importe_igv = $precio_igv * $producto['cantidad'];
              $productos[] = [
                   "codigo" => $producto['barra'],
                   "precio" => str_replace(",","", number_format($precio, 2)),
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
               "emisor" => json_encode([
                    "ruc" => "10405163131",
                    "nombre" => "CAMONES MARCELO YOMAR WALTER",
                    "direccion" => $dataSucursal['direccion'],
                    "telefono" => $dataSucursal['telefono'],
               ]),
               "cliente" => json_encode([
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
               ]),
               "operacion" => "firmar",
               "code" => $serie,
               "codeInterno" => $request->nrof,
               "tipoDocumento" => $codigoTypoDocumento['codigo'],
               "productos" => json_encode($productos),
               "fecha" => date("Y-m-d h:i:s a"),
               "fechaPdf" => date("d-m-Y h:i:s a"),
               "igv" => str_replace(",","", number_format($igv, 2)),
               "total" => str_replace(",","", number_format($total, 2)),
               "totalText" => $totalText,
               "totalSinIgv" => str_replace(",","", number_format($total_sin_igv, 2)),
               "porcentajeIgv" => 18,
               "medioPago" => $request->condicion == 0 ? "Credito" : "Contado",
        ];
        return $this->sendSunat($request, $parameter);
    }
    public function sendSunat($request, $parameter){   
        $postdata = http_build_query(
            array(
                $parameter
            )
        );
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        $context = stream_context_create($opts);
        $result = file_get_contents($this->api, false, $context);
        $res [] = json_decode($result);
        return $this->guardarDocumento($request, $parameter, $res[0]);
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
        }else if($request['documento'] == "credito"){
            $codigo = "07";
            $tipoComprobante = "F001";
        }
        return [
            "codigo" => $codigo,
            "tipoComprobante" => $tipoComprobante
        ];
    }
    public function dataSucursal(Request $request){
        $direccion = "";
        $telefono = "";
        if($request['sucursal'] == "huaral"){
            $direccion = "Calle Morales Bermúdez # 340 y 342, Huaral";
            $telefono = "6049611";
        }else if($request['sucursal'] == "lima-abancay"){
            $direccion = "Av. Abancay # 368 Int. 1090 - Galería La Casona, Lima";   
            $telefono = "937522124";
        }else if($request['sucursal'] == "barranca"){
            $direccion = "JR. Castilla 148, Barranca";  
            $telefono = "5984852"; 
        }
        return [
            "direccion" => $direccion,
            "telefono" => $telefono
        ];
    }
    public function guardarDocumento(Request $request, $par, $result){
       $explode = explode("-", $par['code']);
       $procedure = "call insert_documento_electronico(?,?,?,?,?,?,?,?)";
       $parameter = [
            $request->sucursal,
            $par['code'],
            $explode[0],
            $par['total'],
            $result->qr,
            null,
            1,
            date("Y-m-d h:i:s")
       ];
       DB::statement($procedure, $parameter);
       return $par['code'];
    } 
    public function listarDocumentos(Request $request){
        $date = $request->fecha == null ? date('Y-m-d') : $request->fecha;
        $date_end = $request->fecha_end == null ? date('Y-m-d') : $request->fecha_end;
        $procedure = "call listar_documentos(?,?,?)";
        $parameter = [
            $request->sucursal,
            $date,
            $date_end
        ];
        $data = DB::select($procedure, $parameter);
        usort($data, function ($a, $b) {
            return strcmp($b->created_at, $a->created_at);
        });
        return $data;
    }

    // ***** ENVIAR A SUNAT *******
    public function enviarComprobantes(Request $request){
        $parameter = [
            "emisor" => json_encode([
                 "ruc" => "10405163131",
                 "nombre" => "CAMONES MARCELO YOMAR WALTER",
            ]),
            "operacion" => "enviarSunat",
            "code" => $request['serie'],
        ];
        
        $postdata = http_build_query(
            array(
                $parameter
            )
        );
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        $context = stream_context_create($opts);
        $result = file_get_contents($this->api, false, $context);
        $res [] = json_decode($result);
        $mensaje [] = $res[0]->mensaje;
        if($request['tipo'] != 7){
            $this->EditarDocumento($request, $res[0], $mensaje[0]);
        }else{
             $this->editarCredito($request, $res[0], $mensaje[0]);
        }
    }
    public function EditarDocumento(Request $request, $res, $mensaje){
        $procedure = "call update_documento_electronico(?,?,?,?,?)";
        $parameter = [
            $request['serie'],
            $res->estado,
            $mensaje->msj_sunat, 
            $mensaje->cod_sunat,
            date("Y-m-d h:i:s")
        ];
        DB::statement($procedure, $parameter);
    }
    public function enviarComprobantesMasivo(Request $request){
        $array = [];
        foreach($request->documentos as $value){
            if($value->estado === null){
                $array[] = $value;
            }
        }
        $this->data = array_values($array);
        $this->count = count($this->data);
        usort($this->data, function ($a, $b) {
            return strcmp($a->tipo, $b->tipo);
        });
        if($this->count > 0){
            $this->recursiveEnvio();
            return "Enviados correctamente";
        }else{
            return "no hay pendientes";
        }
    }
    public function recursiveEnvio(){
        if($this->count > 0){
            $parameter = [
                "serie" => $this->data[0]->serie,
                "tipo" => $this->data[0]->tipo,
            ];
            $request = new Request(
              $parameter 
            );
            $this->enviarComprobantes($request);
            $this->eliminarEnviado();
            sleep(8);
            $this->recursiveEnvio();
        }else{
            return "Enviados correctamente";
        }
    }
    public function eliminarEnviado(){
       unset($this->data[0]);
       $this->data = array_values($this->data);
       $this->count = count($this->data);
    }
    public function listarDocumentosEnviar(){
        $date_now = date('Y-m-d');
        $date = strtotime('-1 day', strtotime($date_now));
        $procedure = "call listar_documentos(?,?)";
        $parameter = [
            "huaral",
            date('Y-m-d', $date),
            date('Y-m-d', $date),
        ];
        $data = DB::select($procedure, $parameter);
        return $data;
        usort($data, function ($a, $b) {
            return strcmp($b->created_at, $a->created_at);
        });
        $params = [
            "documentos" => $data
        ];
        $request = new Request($params);
        if(count($data) > 0){
           $this->enviarComprobantesMasivo($request);
        }
    }

}
