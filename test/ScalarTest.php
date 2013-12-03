<?php
/** Testing scalar values  **/
require_once dirname(__FILE__) . "/SnowTestsuite.php";
class ScalarTest extends SnowTestsuite {
	public function testScalarValues() {
		$this->compare(<<<CODE
1
"test"
'test'
/[a-z]/
/^\/.*/m
25.3
-12.7
0xdf
01234567
071L
true
false
null
-500000000
CODE
		, "1;
\"test\";
'test';
'/[a-z]/';
'/^\/.*/m';
25.3;
-12.7;
0xdf;
01234567;
071L;
true;
false;
null;
-500000000;
null;");
	}
}
?>
