<?php
require_once dirname(__FILE__) . "/SnowTestsuite.php";

class TryCatchTest extends SnowTestsuite {
	public function testTrySingleCatch() {
		$this->compare(<<<CODE
try
	pass1
catch b
	pass2
CODE
		, (PHP_VERSION_ID < 50500 ? 'try {
	$pass1;
} catch (Exception $b) {
$catchGuard = true;
	$pass2;
}
if (!isset($catchGuard)) {
} else {
unset($catchGuard);
}
;
null;' : 'try {
	$pass1;
} catch (Exception $b) {
	$pass2;
};
null;'));
	}

	public function testTryCatchFinally() {
		$this->compare(<<<CODE
try
	pass1
catch b
	pass2
finally
	pass3
CODE
	, (PHP_VERSION_ID < 50500 ? 'try {
	$pass1;
} catch (Exception $b) {
	$catchGuard = true;
	$pass2;
}
if (!isset($catchGuard)) {
	$pass3;
} else {
	unset($catchGuard);
}
;
null;' : 'try {
	$pass1;
} catch (Exception $b) {
	$pass2;
} finally {
	$pass3;
};
null;'));
	}
}
