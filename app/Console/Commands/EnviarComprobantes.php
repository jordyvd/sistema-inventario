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
    public function handle()
    {
        $objeto = new SunatController();
        return $objeto->listarDocumentosEnviar();
        // $text = "[".date('H:i:s'). "]: Hola mundo";
        // Storage::append("archivo.txt", $data);
    }
}
