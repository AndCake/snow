<?php
require_once dirname(__FILE__) . "/SnowTestsuite.php";
class LoopTest extends SnowTestsuite {
	public function testWhile() {
		$this->compare('while a
	pass', 'while ($a) {
	$pass;;
}
;
null;');
	}

	public function testFor() {
		$this->compare('for b in a
	pass
for b,c in a
	pass
for b in [1,2,3]
	pass
for b in 1 to 10
	echo b
for b in 10 downto 1
	echo b
for b in 1 to 10 step 2
	echo b
for b in 10 downto 1 step 2
	echo b
for b in Foo..getIt() downto func() step !YES_WE_CAN
	echo b', 'foreach ($a as $b) {
	$pass;
}
unset($b);
;
foreach ($a as $c => $b) {
	$pass;
}
unset($b, $c);
;
foreach (Array(1,2,3) as $b) {
	$pass;
}
unset($b);
;
for ($b = 1; $b <= 10; $b += 1) {
	echo($b);;
}
unset($b);
;
for ($b = 10; $b >= 1; $b -= 1) {
	echo($b);;
}
unset($b);
;
for ($b = 1; $b <= 10; $b += 2) {
	echo($b);;
}
unset($b);
;
for ($b = 10; $b >= 1; $b -= 2) {
	echo($b);;
}
unset($b);
;
for ($b = Foo::getIt(); $b >= func(); $b -= YES_WE_CAN) {
	echo($b);;
}
unset($b);
;
null;');
	}
}
?>
