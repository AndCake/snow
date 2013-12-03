<?php
require_once dirname(__FILE__) . "/../snowcompiler.php";
class SnowTestsuite extends PHPUnit_Framework_TestCase {
	protected function compare($a, $b) {
		$snow = new SnowCompiler($a);
		$result = $snow->compile();
		$result = str_replace($snow->mapping['SETUP'], '', $result);
		$this->assertEquals($b, $result);
	}
}
?>
