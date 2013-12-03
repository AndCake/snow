<?php
require_once dirname(__FILE__) . "/SnowTestsuite.php";

class IfTest extends SnowTestsuite {
	public function testIf() {
		$this->compare('if a
	pass
elif b
	pass
elif c
	pass
else
	pass', 'if ($a) {
	$pass;
} else if ($b) {
	$pass;
} else if ($c) {
	$pass;
} else {
	$pass;
}
;
null;');
	}

	public function testTernary() {
		$this->compare('echo if a then b else c', 'echo(($a ? $b : $c));
null;');
	}

	public function testTernaryWithComparison() {
		$this->compare('echo if a == d then b else c', 'echo(($a === $d ? $b : $c));
null;');
	}
}
