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
        // Check encoding and warn if there's non-matching encodings
        $encodingResult = DB::connection()->select("
            SHOW VARIABLES 
            WHERE ( Variable_name LIKE 'character\_set\_%' OR Variable_name LIKE 'collation%'  ) AND 
            Variable_name != 'character_set_filesystem' AND Variable_name != 'character_set_system'");

        if (count($encodingResult) > 0) {
            $this->comment("You have mismatched character sets which may cause issues with displaying your data");
            $headers = ['Variable_name', 'Value'];
            $array = [];
            foreach ($encodingResult as $value) {
                $row = [$value->Variable_name, $value->Value];
                $array[]= $row;
            }
            $this->table($headers, $array);
        }
    }
}