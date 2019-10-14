<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 9/18/19
 * Time: 6:21 PM
 */

namespace CareSet\Zermelo\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ZermeloDebugCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zermelo:debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print information that may be helpful for debugging zermelo data issues';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
	$db_name = DB::connection()->getDatabaseName();

        // Check encoding and warn if there's non-matching encodings
        $encodingResult = DB::connection()->select("
            SHOW VARIABLES 
            WHERE 
			( Variable_name LIKE 'character\_set\_%' OR Variable_name LIKE 'collation%'  ) 
		AND 
            		Variable_name != 'character_set_filesystem' 
		AND 
			Variable_name != 'character_set_system'
		AND 
			Value != 'utf8mb4_unicode_ci'
");

        if (count($encodingResult) > 0) {
            $this->comment("Your database connection (through $db_name)  has character sets which may cause issues with displaying your data");
            $headers = ['Database setting', 'Value'];
            $array = [];
            foreach ($encodingResult as $value) {
                $row = [$value->Variable_name, $value->Value];
                $array[]= $row;
            }
            $this->table($headers, $array);
		$this->comment("Please consider just switching everything to utf8mb4_unicode_ci");
		$this->comment("Because https://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci/766996#766996");	
        }
    }
}
