<?php
require_once dirname(__FILE__) . "/../snowcompiler.php";
class SnowTestsuite extends PHPUnit_Framework_TestCase {
	protected $debug = false;
	protected function compare($a, $b) {
		$snow = new SnowCompiler($a);
		$result = $snow->compile($this->debug);
		$result = str_replace($snow->mapping['SETUP'], '', $result);
		unset($snow);
		$this->assertEquals($b, $result);
	}

	protected function runAndCompare($code, $expected) {
		$snow = new SnowCompiler($code);
		$result = $snow->compile($this->debug);
		unset($snow);
		ob_start();
		eval($result);
		$result = ob_get_clean();
		$this->assertEquals($expected, $result);
	}
}
?>
