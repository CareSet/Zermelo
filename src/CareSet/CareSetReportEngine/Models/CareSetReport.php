<?php

namespace CareSet\CareSetReportEngine\Models;

abstract class CareSetReport
{

	private $_code = null;
	private $_parameters = [];
	private $_input = [];
	private $_bolt_id = false;


	/*These are the valid column formats used in OverrideHeader function*/
	public $VALID_COLUMN_FORMAT = ['TEXT','DETAIL','URL','CURRENCY','NUMBER','DECIMAL','DATE','DATETIME','TIME','PERCENT'];
	public $SUPPORTED_TAGS      = ['HIDDEN','BOLD','ITALIC','RIGHT'];


	public $DETAIL     = ['Sentence'];
	public $URL        = ['URL'];
	public $CURRENCY   = ['Amt','Amount','Paid','Cost'];
	public $NUMBER     = ['id','#','Num','Sum','Total','Cnt','Count'];
	public $DECIMAL    = ['Avg','Average'];
	public $PERCENT    = ['Percent','Ratio','Perentage'];

	/**
	 * This will mark the column that should not be used for statistical summary
	 */
	public $SUGGEST_NO_SUMMARY = [];

	/**
	 * What the engine should consider as the 'noun' or 'subject'
	 */
	public $SUBJECTS = [];

	/**
	 * What the engine should consider the weight between the subjects.
	 * This is a priority list. the first 'weight' it sees, it will use.
	 */
	public $WEIGHTS = [];




	public function __construct($Code, array $Parameters = [], array $Input = [])
	{
		$this->_code = $Code;
		$this->_parameters = $Parameters;
		$this->_input = $Input;
	}

	public function getCode(): ?string
	{
		return $this->_code;
	}
	public function getParameters(): ?array
	{
		return $this->_parameters;
	}
	public function getInput(): ?array
	{
		return $this->_input;
	}

	public function GetSQL()
	{
		return null;
	}
	public function MapRow(array $row)
	{
		return $row;
	}
	public function OverrideHeader(array &$format, array &$tags): void
	{

	}

	public function GetReportName(): string
	{
		$me = get_called_class();
		return $me::REPORT_NAME;
	}
	public function getReportDescription(): ?string
	{
		$me = get_called_class();
		return $me::DESCRIPTION;
	}

	public function GetClassName(): string
	{
		return substr(strrchr(get_class($this), '\\'), 1);
	}

	public function getBolt()
	{
		$me = get_called_class();
		return $me::BOLT;
	}

	public function SetBolt($bolt_id)
	{
		$this->_bolt_id = $bolt_id;
	}
	public function GetBoltId()
	{
		return $this->_bolt_id;
	}

	public function RetrieveBolt()
	{

		if (empty($this->getBolt())) return null;
		$this_table = \Bolt::resolveBolt($this->getBolt(), $this->getUserId(), $this->_bolt_id);

		return $this_table;
	}

	public function RetrieveAllBolts()
	{
		$me = get_called_class();
		return \Bolt::allPossibleBoltMaps($this->getBolt());
	}

	public function getFileLocation(): string
	{
		$reflector = new \ReflectionClass(get_class($this));
		$fn = $reflector->getFileName();
		return $fn;
	}

}


?>
