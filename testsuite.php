#!/usr/bin/env php -q
<?php
// test suite for the Snow compiler
include("snowcompiler.php");
include("libtest.php");

function test($code, $expected) {
	$sc = new SnowCompiler($code);
	assertEqual(str_replace("\nnull;", '', trim($sc->compile())), $expected);
}

describe("identifiers", function(){
	should("add \$ in front of it", function() {
		test("test", "\$test;");
		test("test['test']", "\$test['test'];");
	});

	should("convert . into ->", function() {
		test("gen.test", "\$gen->test;");
		test("gen.test.further", "\$gen->test->further;");
		test("gen.test.further.andmore", "\$gen->test->further->andmore;");
	});

	should("convert @ into \$this->", function() {
		test("@gen", "\$this->gen;");
		test("@gen.test", "\$this->gen->test;");
	});

	should("convert .. into ::", function() {
		test("MyClass..variable", "MyClass::\$variable;");
		test("MyClass..variable.test", "MyClass::\$variable->test;");
	});

	should("allow array access", function() {
		test("test[0]", "\$test[0];");
		test("test[100]", "\$test[100];");
		test("test[1...3]", "array_slice(\$test, \$_tmp3 = (1), (3) - \$_tmp3 + 1);");
		test("test['Hallo']", "\$test['Hallo'];");
		test("test[\"Hallo\"]", "\$test[\"Hallo\"];");
		test("test['Hallo']['test']", "\$test['Hallo']['test'];");
	});
});

describe("functions", function(){
	should("define functions", function(){
		test("fn myfn <- null", "function myfn() {return null;\n;};");
		test("fn myfn() <- null", "function myfn() {return null;\n;};");
		test("fn myfn()\n\t<- null", "function myfn() {;\n\treturn null;\n;};");
		test("fn myfn\n\t<- null", "function myfn() {;\n\treturn null;\n;};");
	});
	should("support parameters", function() {
		test("fn myfn(a)\n\t<- null", "function myfn(\$a) {;\n\treturn null;\n;};");
		test("fn myfn(a, b)\n\t<- null", "function myfn(\$a, \$b) {;\n\treturn null;\n;};");
		test("fn myfn(a, b, c)\n\t<- null", "function myfn(\$a, \$b, \$c) {;\n\treturn null;\n;};");
		test("fn myfn(a = 2)\n\t<- null", "function myfn(\$a = 2) {;\n\treturn null;\n;};");
		test("fn myfn(a = 2, b = \"test\")\n\t<- null", "function myfn(\$a = 2, \$b = \"test\") {;\n\treturn null;\n;};");
	});
	should("support calling functions", function() {
		test("myfn()", "myfn();");
		test("do myfn", "myfn();\n;");
		test("myfn 'test'", "myfn('test');");
		test("myfn('test', h)", "myfn('test', \$h);");
		test("myfn 'test', h", "myfn('test', \$h);");
		test("myfn('test', h())", "myfn('test', h());");
		test("myfn('test', h(2))", "myfn('test', h(2));");
		test("myfn 'test', h 2", "myfn('test', h(2));");
	});
	should("support chaining", function() {
		test("a->b()->c()->d()", "d(c(b(\$a)));");
		test("'test'->b()->c()", "c(b('test'));");
		test("2->b(x)", "b(2, \$x);");
		test("/[a-z]/->b()", "b('/[a-z]/');");
		test("'test'->b(5)->c('test')", "c(b('test', 5), 'test');");
		#test("a()->b()->c()", "c(b(a()));"); 		// - not yet supported @TODO: debug
	});
});

describe("Arrays", function() {
	should("do simple arrays", function() {
		test("[1, 2, 3, 4, 5, 6]", "Array(1, 2, 3, 4, 5, 6);");
		test("[1, 'test', 3, null]", "Array(1, 'test', 3, null);");
		test("[1, 'test', 3, [null]]", "Array(1, 'test', 3, Array(null));");
		test("[1, 'test', 3, [[null], 2]]", "Array(1, 'test', 3, Array(Array(null), 2));");
	});
	should("do dictionaries", function() {
		test("['test': 2, 'hallo': 'test']", "Array('test' => 2, 'hallo' => 'test');");
		test("['test': [2, 3], 'hallo': 'test']", "Array('test' => Array(2, 3), 'hallo' => 'test');");
		test("['test': ['huhu': 2, 'me': 3], 'hallo': 'test']", "Array('test' => Array('huhu' => 2, 'me' => 3), 'hallo' => 'test');");
	});
});

describe("control structures", function() {
	should("support normal IF", function() {
		test("if x\n\ta()", "if (\$x) {;\n\ta();\n}\n;");
		test("if x is 2\n\ta()", "if (\$x === 2) {;\n\ta();\n}\n;");
		test("if x == 2\n\ta()", "if (\$x === 2) {;\n\ta();\n}\n;");
		test("if x isnt 2\n\ta()", "if (\$x !== 2) {;\n\ta();\n}\n;");
		test("if x != 2\n\ta()", "if (\$x !== 2) {;\n\ta();\n}\n;");
		test("if x < 2\n\ta()", "if ((gettype(\$_tmp1 = \$x) === gettype(\$_tmp2 = 2) && (\$_tmp1  <  \$_tmp2 && ((\$_tmp1 = \$_tmp2 = null) || true)) || (\$_tmp1 = \$_tmp2 = null))) {;\n\ta();\n}\n;");
		test("if x <= 2\n\ta()", "if ((gettype(\$_tmp1 = \$x) === gettype(\$_tmp2 = 2) && (\$_tmp1  <=  \$_tmp2 && ((\$_tmp1 = \$_tmp2 = null) || true)) || (\$_tmp1 = \$_tmp2 = null))) {;\n\ta();\n}\n;");
		test("if x > 2\n\ta()", "if ((gettype(\$_tmp1 = \$x) === gettype(\$_tmp2 = 2) && (\$_tmp1  >  \$_tmp2 && ((\$_tmp1 = \$_tmp2 = null) || true)) || (\$_tmp1 = \$_tmp2 = null))) {;\n\ta();\n}\n;");
		test("if x >= 2\n\ta()", "if ((gettype(\$_tmp1 = \$x) === gettype(\$_tmp2 = 2) && (\$_tmp1  >=  \$_tmp2 && ((\$_tmp1 = \$_tmp2 = null) || true)) || (\$_tmp1 = \$_tmp2 = null))) {;\n\ta();\n}\n;");
		test("if x? and y?\n\ta()", "if (isset(\$x) && isset(\$y)) {;\n\ta();\n}\n;");
		test("if not x\n\ta()", "if (!\$x) {;\n\ta();\n}\n;");
		test("if x\n\ta()\n\tc()", "if (\$x) {;\n\ta();\n\tc();\n}\n;");
	});
	should("support IF-ELSE", function() {
		test("if x\n\ta()\nelse\n\tb()", "if (\$x) {;\n\ta();\n} else {;\n\tb();\n}\n;");
		test("if x\n\ta()\n\tc()\nelse\n\tb()\n\td()", "if (\$x) {;\n\ta();\n\tc();\n} else {;\n\tb();\n\td();\n}\n;");
		test("if x\n\ta()\n\tc()\nelse\n\tb()\n\td()", "if (\$x) {;\n\ta();\n\tc();\n} else {;\n\tb();\n\td();\n}\n;");
		test("if x\n\ta()\nelif y\n\tb()\nelse\n\td()", "if (\$x) {;\n\ta();\n} else if (\$y) {;\n\tb();\n} else {;\n\td();\n}\n;");
	});

	should("support ternary operator", function() {
		test("if x is y then a()", "(\$x === \$y ? a() : null);");
		test("if x is y then a() else b()", "(\$x === \$y ? a() : b());");
		#test("if x then a() else b()", "(\$x ? a() : b());");			// @TODO: debug
	});
});

describe("comments", function() {
	should("support single-line comments", function() {
		test("# hello", "// hello;");
		test("#hello", "//hello;");
		test("### hello", "// hello;");
		// comments in the same line as code are not yet supported!
		#test("test # hello", "\$test; // hello;");
	});
	should("support multi-line comments", function() {
		test("### test ###", "/* test */;");
		test("###\ntest\n###", "/*\ntest\n*/;");
		test("###\n# test\n###", "/*\n test\n*/;");
		test("###\ntest\n###\ntest\n#test", "/*\ntest\n*/;\n\$test;\n//test;");
	});
});

echo "All tests ran successfully.\n";
?>
