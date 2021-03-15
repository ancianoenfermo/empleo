<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;



class populateDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:empleos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Llena de empleos la Base de Datos';

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
        // Comprueba si hay un nuevo fichero de empleos
        if(!file_exists(public_path("XOempleos.json"))) {
            return 0;
        }
        // Activa el modo mantenimiento
        Artisan::call('down',['--redirect'=>null,'--retry'=>null,'--secret'=>null,'--status'=>'503']);

        // Lee el json de empleos
        $path = public_path() . "/XOempleos.json";
        $json = File::get($path);
        $jsonDecode = json_decode($json, true);
        $empleos = $jsonDecode['job'];
        // Vacia las tablas
        $this->vaciaTablas();

        foreach($empleos as $empleo) {

            $this->trata_empleo($empleo);
        }
        print("finalize comando");
        // Desacctiva el modo mantenimiento
        Artisan::call('up');
        return 0;
    }
    public function trata_empleo($empleo) {
        echo $empleo['title'], PHP_EOL;
        echo $empleo['excerpt'], PHP_EOL;
        echo $empleo['JobUrl'], PHP_EOL;
        echo $empleo['JobFuente'], PHP_EOL;
        echo $empleo['logo'], PHP_EOL;
        echo PHP_EOL;
        echo PHP_EOL;
    }


    public function vaciaTablas() {
        DB::statement("SET foreign_key_checks=0");
        $databaseName = DB::getDatabaseName();
        $tables = DB::select("SELECT * FROM information_schema.tables WHERE table_schema = '$databaseName'");
        foreach ($tables as $table) {
            $name = $table->TABLE_NAME;
            //if you don't want to truncate migrations
            if ($name == 'migrations') {
                continue;
            }
        DB::table($name)->truncate();
        }
        DB::statement("SET foreign_key_checks=1");
    }
}
