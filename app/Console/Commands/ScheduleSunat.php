<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScheduleSunat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:sunat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cron sunat';

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
        $this->info('inicio de horario envio comprobantes sunat.');
        while (true) {
            if ( date("Y-m-d H:i:s") == date("Y-m-d")." 02:30:00") {
                $this->call('enviar:comprobantes');
            }
            sleep(1);
        }
    }
}
