<?php
require_once dirname(__FILE__) . "/SnowTestsuite.php";

class UnificationFunctionTest extends SnowTestsuite {
	public function testReplace() {
		$this->runAndCompare('echo [1, 2, 3]->replace(2, 4)->json_encode()
echo "test"->replace /^[a-z]/, \'T\'
echo "test"->replace \'t\', \'T\'', '[1,4,3]TestTesT');
	}

	public function testIn() {
		$this->runAndCompare('if 3->in([1, 2, 3]) is true
	echo "3 is in.\n"

if 4->in([1, 2, 3]) is true
	echo "4 is in.\n"	

if "e"->in("test") is true
	echo "e is in test.\n"

if "x"->in("test") is true
	echo "x is in test.\n"

if 3->in(10) is true
	echo "3 is in 10.\n"

if 11->in(10) is true
	echo "11 is in 10.\n"', '3 is in.
e is in test.
3 is in 10.
');
	}
}