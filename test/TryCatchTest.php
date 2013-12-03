<?php
require_once dirname(__FILE__) . "/SnowTestsuite.php";

class TryCatchTest extends SnowTestsuite {
	public function testTrySingleCatch() {
		$this->compare(<<<CODE
try
	pass
catch b
	pass
CODE
		, 'try {
	$pass;
} catch (Exception $b) {
$catchGuard = true;
	$pass;
}
if (!isset($catchGuard)) {
} else {
unset($catchGuard);
}
;
null;');
	}

	public function testTryCatchFinally() {
		$this->compare(<<<CODE
try
	pass
catch b
	pass
finally
	pass
CODE
	, 'try {
	$pass;
} catch (Exception $b) {
	$catchGuard = true;
	$pass;
}
if (!isset($catchGuard)) {
	$pass;
} else {
	unset($catchGuard);
}
;
null;');
	}
}
