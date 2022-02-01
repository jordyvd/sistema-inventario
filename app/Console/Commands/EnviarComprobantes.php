<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SunatController;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class EnviarComprobantes extends Command
{
   
    //public $api = "http://127.0.0.1:8000/api/listar-documentos-enviar";
    public $api = "http://facturadorgiomar.site/api/listar-documentos-enviar";

    protected $signature = 'enviar:comprobantes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'enviar comprobantes electronicos a sunat';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $opts = array('http' =>
        array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded'
        )
       );
        $context = stream_context_create($opts);
        $result = file_get_contents($this->api, false, $context);
        // $text = "[".date('H:i:s'). "]: Hola mundo";
        // Storage::append("archivo.txt", $data);
    }
}
