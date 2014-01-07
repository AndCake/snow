<?php
require_once dirname(__FILE__) . "/SnowTestsuite.php";

class StringTest extends SnowTestsuite {
	public function testConcat() {
		$this->compare(<<<CODE
x = "test" + 'hallo'
x = x + "test"
x = "test" + x
x = x % y
x = 'test' + !DIRECTORY_SEPARATOR
x = x + 'test' + y % !DIRECTORY_SEPARATOR
CODE
			, '$x  =  "test" . \'hallo\';
$x  =  $x . "test";
$x  =  "test" . $x;
$x  =  $x . $y;
$x  =  \'test\' . DIRECTORY_SEPARATOR;
$x  =  $x . \'test\' . $y . DIRECTORY_SEPARATOR;
null;');
	}

	public function testInlineConcat() {
		$this->compare(<<<CODE
"Hello, {test}!"
"The {animal} went to {world.place()} 
with his {!NUM} friends."
'Hello, {test}!'
"""
Hello, {test}!
"""
CODE
			, '"Hello, " . ($test) . "!";
"The " . ($animal) . " went to " . ($world->place()) . " 
with his " . (NUM) . " friends.";
\'Hello, {test}!\';
<<<EOF
Hello, $test!

EOF
;
null;');
	}
}
?>