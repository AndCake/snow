<?php
require_once dirname(__FILE__) . "/SnowTestsuite.php";

class ClassTest extends SnowTestsuite {
	public function testDeclaration() {
		$this->compare(<<<CODE
class FooBar
	static x = 3

	fn __construct()
		self..foo
		@foo
		parent..bos()

	fn baz
		self..baz
		@baz
		x = FooBar..x
		parent..baz()
CODE
		, 'class FooBar {
	static $x = 3;
	function __construct() {
		self::$foo;
		$this->foo;
		parent::bos();
	}
	function baz() {
		self::$baz;
		$this->baz;
		$x  =  FooBar::$x;
		parent::baz();
	}
	// end block
};
null;');
	}

	public function testComplexDeclaration() {
		$this->compare(<<<CODE
class A
	extends B
	implements C, D

	protected foo = 1922222
	final bar = 32
	!MY_CONST = 'HI'
	private baz = [1,2,3,4]
	dapub = 'uber'

	fn __construct(a, b, c=42, ds)
		for d in ds
			@ds = do_stuff_to(d)
		@x.y.z.v.f = 2000
		xs.superman

	fn x()
		<- 200

	static fn why()
		<- 'Dont know'
CODE
		, 'class A
	extends B
	implements C, D {
	protected $foo = 1922222;
	final $bar = 32;
	const MY_CONST = \'HI\';
	private $baz = Array(1,2,3,4);
	$dapub = \'uber\';
	function __construct($a, $b, $c=42, $ds) {
		foreach ($ds as $d) {
			$this->ds  =  do_stuff_to($d);
		}
		unset($d);
;
		$this->x->y->z->v->f  =  2000;
		$xs->superman;
	}
	function x() {
		return 200;
;
	}
	static function why() {
		return \'Dont know\';
;
	}
	// end block
};
null;');
	}

	public function testEmptyDeclaration() {
		$this->compare(<<<CODE
class XTest_And_2Y
CODE
			, 'class XTest_And_2Y {
};
null;');
	}
}