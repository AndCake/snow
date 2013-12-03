<?php
require_once dirname(__FILE__) . "/SnowTestsuite.php";

class FunctionTest extends SnowTestsuite {
	public function testConditionalFunctionDefinition() {
		$this->compare('if true
	fn a
		pass', 'if (true) {
	function a() {
		$pass;
	};
	// end block;
}
;
null;');
	}

	public function testDefaultValues() {
		$this->compare(<<<EOT
fn a(b = null, 
     c = 'foo', 
     f = -1.0, 
     g = [], 
     h = ['foo', 'bar': 'baz'], 
     i = ['foo'])
	pass
EOT
		, 'function a($b = null, 
     $c = \'foo\', 
     $f = -1.0, 
     $g = Array(), 
     $h = Array(\'foo\', \'bar\' => \'baz\'), 
     $i = Array(\'foo\')) {
	$pass;
};
null;');
	}

	public function testSpecialVars() {
		$this->compare(<<<CODE
A = 2
b = 3
fn c
	echo A
	echo b
CODE
		, '$A  =  2;
$b  =  3;
function c() {global $A;

	echo($A);
	echo($b);
};
null;');
	}
}
