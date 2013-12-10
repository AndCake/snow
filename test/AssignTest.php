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

	public function testInlineFunction() {
		$this->compare('result = db.find("user", "name", fn(val) <- (val->strlen() > 3))', '$result  =  $db->find("user", "name", function ($val) {return ((gettype($_tmp1 = strlen($val)) === gettype($_tmp2 = 3) && ($_tmp1  >  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null)));

});
null;');
	}
}
?>
