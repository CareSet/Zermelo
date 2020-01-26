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
use CareSet\Zermelo\Reports\Graph\AbstractGraphReport;
use CareSet\Zermelo\Reports\Tabular\AbstractTabularReport;
use CareSet\Zermelo\Reports\Tree\AbstractTreeReport;
use CareSet\Zermelo\Reports\Cards\AbstractCardsReport;

class ZermeloReportCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zermelo:report_check {--include_durc}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loop Through Each Zermelo Report with their default arguments and see if they correctly run. Log to the Zermelo Config Database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {

	$is_debug = false;

	$db_name = DB::connection()->getDatabaseName();

	$pdo = DB::connection()->getPdo();

	$zermelo_cache_db_name = config( 'zermelo.ZERMELO_CACHE_DB' );

	$create_log_sql = "

CREATE TABLE IF NOT EXISTS $zermelo_cache_db_name._ReportTestLog (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `error_type` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_with_problem` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_with_problem` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sql_with_problem` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

	DB::statement($create_log_sql);
	


	$report_dir = report_path(); //from helpers.php

	$file_list = [];

	$is_durc = $this->option('include_durc');

	$Dir = new \DirectoryIterator($report_dir);
	foreach ($Dir as $FileInfo) {
    		if (!$FileInfo->isDot()) {
        		$this_file = $FileInfo->getFilename();

			if($is_durc){
				//then every file gets added to the list!!
				$file_list[] = $report_dir .'/'.  $this_file;
			}else{
				//then only custom (non DURC generated) results are being tested
				if(strpos($this_file,'DURC_') !== false){
					$is_match_durc = true;
				}else{
					$is_match_durc = false;
					$file_list[] = $report_dir . '/'. $this_file;
				}
			}
    		}
	}
	
	if($is_debug) { echo "Testing the following reports\n"; }

	$error_array = []; //hopefully this is empty in the end...

	foreach($file_list as $this_file){
		if($is_debug){ echo "Requiring $this_file\n"; }
		$previously_declared_classes = get_declared_classes();
		require_once($this_file); //do it the old school way prevents us from worrying about 'use'
		$diff = array_diff(get_declared_classes(),$previously_declared_classes);
		$class = array_pop($diff); //returns the last element of the array...
	
		if($is_debug) { echo "Which contains $class\n"; }

		$test_cases = $class::testMeWithThis();

		if(!$test_cases){
			//then we should use just one test case
			$test_cases = [ 
				[
					'Code' => null,
					'Parameters' => [],
					'Input' => [],
				]
				];	
				
		}
	
		$need_these = [
			'Code',
			'Parameters',
			'Input',
		];

		foreach($test_cases as $this_test_case){
			
			foreach($need_these as $need_this){
				if(array_key_exists($need_this,$this_test_case)){ //some of our values can be null. so we annot use isset
					$$need_this = $this_test_case[$need_this]; //pull the variable into local scope, in the end all of the need_these will be in local scope now..
				}else{
					echo "Error: Testing $this_file looking for ['$need_this'] in the test_case array... and I do not find it.. \n";
					var_export($this_test_case);
					exit();
				}
			}
			
			//now we shoud have all of the need_these variables as local variables...
			//So we have $Code, $Parameters, and $Input all correctly defined...
			//so now we try to run the SQL for each one...

			$reflection_of_class = new \ReflectionClass($class);

			if(!$reflection_of_class->isAbstract()){

				try {
					$report = new $class($Code,$Parameters,$Input);
					$sql = $report->getSQL();
				} catch (\Exception $e) {

						$error_array[] = [
							'error_type' => 'Report Class Failed to Instantiate',
							'error_message' => $e->getMessage(),	
							'file_with_problem' => $this_file,
							'class_with_problem' => $class,
						];
						continue; //no need to test sql if we cannot create the class
				}

				if(!is_array($sql)){
					//we need to make it array so that we always have array..
					$sql_array = [ $sql ];
				}else{
					$sql_array = $sql;
				}

				$success_count = 0;
				$fail_count = 0;
				foreach($sql_array as $this_sql){
			
					try {
						if($is_debug){
							echo "Processing $this_file $class by running \n $this_sql";
						}

						$stmt = $pdo->query($this_sql); //we just need to know if it runs... 

						$success_count++;

					} catch(\Exception $e) {

						$fail_count++;

						$error_array[] = [
							'error_type' => 'SQL Crash',
							'error_message' => $e->getMessage(),	
							'sql_with_problem' => $this_sql,
							'file_with_problem' => $this_file,
							'class_with_problem' => $class,
						];
	
					}
			

				} //end loop over sql_array
			}else{
				echo "$this_file $class is an abstract class... not testing\n";
			}
		} //end test_cases loop
	}//end loop over file_list

	if(count($error_array) == 0){
		echo "All Reports worked!!\n";
	}else{
		echo "The following reports had problems..\n";
		foreach($error_array as $this_error){
			
			//prep for the database..
			foreach($this_error as $key => $value){
				$this_error[$key] = $pdo->quote($value); //should fix escape related problems... important since we are inserting SQL...
			}
		
			$error_type = $this_error['error_type'];
			$error_message = $this_error['error_message'];
			if(isset($this_error['sql_with_problem'])){
				$sql_with_problem = $this_error['sql_with_problem'];
			}else{
				$sql_with_problem = "''";
			}

			$file_with_problem = $this_error['file_with_problem'];
			$class_with_problem = $this_error['class_with_problem'];

			$insert_log_sql = "
INSERT INTO $zermelo_cache_db_name._ReportTestLog 
	(`id`, `error_type`, 
	`error_message`, `file_with_problem`, 
	`class_with_problem`, `sql_with_problem`, 
	`created_at`, `updated_at`) 
VALUES 
	(NULL, $error_type, 
	$error_message, $file_with_problem, 
	$class_with_problem, $sql_with_problem, 
	CURRENT_TIME(), CURRENT_TIME());
";

			//echo "Running\n$insert_log_sql\n";

			$pdo->exec($insert_log_sql);

			echo "$file_with_problem\n\t\t$error_message\n";
		
		}
	}

    }
}
