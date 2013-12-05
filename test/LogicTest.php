<?php
require_once dirname(__FILE__) . "/SnowTestsuite.php";

class LogicTest extends SnowTestsuite {
	public function testOperators() {
		$this->compare('if a and b then ""
a & b
if a or b then ""
a | b
if not a then ""
a xor b
if a and b or c and d then ""
if a and (b or c) and d then ""
a = if b or c then ""
a = b | c', '($a && $b ? "" : null);
$a & $b;
($a || $b ? "" : null);
$a | $b;
(!$a ? "" : null);
$a xor $b;
($a && $b || $c && $d ? "" : null);
($a && ($b || $c) && $d ? "" : null);
$a  =  ($b || $c ? "" : null);
$a  =  $b | $c;
null;');
	}
}