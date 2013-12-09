<?php
/** Testing Assignments **/
require dirname(__FILE__) . "/SnowTestsuite.php";

class AssignTest extends SnowTestsuite {
	
	public function testAssignments() {
		$this->compare(<<<CODE
a = b
a %= b
a -= b
a += b
a *= b
[a, b, c] = d
[a,, c] = c
CODE
		, '$a  =  $b;
$a  %=  $b;
$a  -=  $b;
$a  +=  $b;
$a  *=  $b;
list($a, $b, $c) = $d;
list($a,, $c) = $c;
null;');
	}
}
?>
