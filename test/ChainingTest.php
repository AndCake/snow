<?php
require_once dirname(__FILE__) . "/SnowTestsuite.php";

class ChainingTest extends SnowTestsuite {
	public function testChain() {
		$this->compare('xx->func(32)
42->doit() - 41
hi->ok()
array_slice(guy, start, guy->count()-stop)', 'func($xx, 32);
doit(42) - 41;
ok($hi);
array_slice($guy, $start, count($guy)-$stop);
null;');
	}

	public function testMultiChains() {
		$this->compare('xx->substr(2, 3)->tolowercase()->ucfirst()', 'ucfirst(tolowercase(substr($xx, 2, 3)));
null;');
	}
}