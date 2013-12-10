<?php
require_once dirname(__FILE__) . "/SnowTestsuite.php";

class ComparisonTest extends SnowTestsuite {
	public function testComparisions() {
		$this->compare(<<<CODE
a < b
a > b
a <= b
a >= b
a == b
a isa string
a isa number
a isa boolean
a isa B
CODE
		, '(gettype($_tmp1 = $a) === gettype($_tmp2 = $b) && ($_tmp1  <  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null));
(gettype($_tmp1 = $a) === gettype($_tmp2 = $b) && ($_tmp1  >  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null));
(gettype($_tmp1 = $a) === gettype($_tmp2 = $b) && ($_tmp1  <=  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null));
(gettype($_tmp1 = $a) === gettype($_tmp2 = $b) && ($_tmp1  >=  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null));
$a === $b;
is_string($a);
is_numeric($a);
is_bool($a);
$a instanceof B;
null;');
	}

	public function testExistence() {
		$this->compare(<<<CODE
if a? then a
if a['b']?? then a
not @a['c']?
not @a[b]??
CODE
		, '(isset($a) ? $a : null);
((!empty($a[\'b\'])) ? $a : null);
!isset($this->a[\'c\']);
!(!empty($this->a[$b]));
null;');
	}
}
