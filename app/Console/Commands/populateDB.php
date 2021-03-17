<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Models\Province;
use App\Models\Region;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
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
        $inicio = now();
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
        // Desacctiva el modo mantenimiento
        Artisan::call('up');
        $fin = now();
        echo "acabe";
        File::delete($path);
        return 0;
    }
    public function trata_empleo($empleo) {

        $region = Region::where('name', $empleo['jobLocation']['autonomia'])->first();
        if ($region == null) {
            $newRegion = new Region;
            $newRegion->name = $empleo['jobLocation']['autonomia'];
            $region = $newRegion;
            $region->save();
        }

        $province = Province::where('name', $empleo['jobLocation']['provincia'])->first();

        if ($province == null) {
            $newProvince = new Province();
            $newProvince->name = $empleo['jobLocation']['provincia'];
            $province = $newProvince;
        }
        $province->region_id = $region->id;
        $province->save();

        $newJob = new Job;
        $newJob->datePosted = $empleo['datePosted'];
        $newJob->title = $empleo['title'];
        $newJob->excerpt =  $empleo['excerpt'];
        $newJob->jobUrl =  $empleo['JobUrl'];
        $newJob->jobSource = $empleo['JobFuente'];
        $newJob->logo = $empleo['logo'];
        $newJob->contract = $empleo['jobData']['contrato'];
        $newJob->workingDay = $empleo['jobData']['jornada'];
        $newJob->experience = $empleo['jobData']['experiencia'];
        $newJob->vacancies = $empleo['jobData']['vacantes'];
        $newJob->salario = $empleo['jobData']['vacantes'];
        $newJob->province_id = $province->id;
        $newJob->autonomia = $empleo['jobLocation']['autonomia'];
        $newJob->provincia = $empleo['jobLocation']['provincia'];
        $newJob->localidad = $empleo['jobLocation']['localidad'];
        $newJob->save();
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
