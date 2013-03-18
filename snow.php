#!/usr/bin/env php -q
<?php
	include("snowcompiler.php");
	$outputFile = null;
	$code = <<<EOC
!VARI = _POST['var']
var_dump(_POST['var'])
if not !VARI
	<- 'hallo!'

for a, b in _POST['var']
	print("{a} => {b}")
	c = a % b

"""Hallo, Welt!
{b}
ich bin da"""

b = 10
a = 0
while a < b
	a->substr(2, 3)->strtoupper()->minkel('test')->lcfirst()

fn retrieve(a) <- data.getList('test' + a)
do retrieve

for i in b downto 20 step 2
	if i isnt b
		c = ', ' 
		i++
	elif i isnt a
		c = ''
		i--
		### 
			this is my large multiline comment
			here...
			and here too
		###
	elif a?
		c = ', '
	else
		c = 'test'
	echo(c % i)

# nice comment
class X extends Y

	fn __construct
		Y..a = "test"
		do Y..a
		echo(@test.doIt)

	fn doIt(what)
		<- what

b = new X()
a = [
	"test": 1,
	"ho": 2, 
	"sub": [5, 'test']
]
"hehe {a} hoho"

EOC;

	if (count($argv) > 1) {
		$result = getopt("o:i:");
		if (isset($result["o"]) && !empty($result["o"])) {
			$outputFile = $result["o"];
		} 
		if (isset($result["i"]) && !empty($result["i"])) {
			$code = file_get_contents($result["i"]);
		}
	}

	$snow = new SnowCompiler($code);
	$result = $snow->compile();

	$result = "<?php\n" . $result."\n?>";

	if (!empty($outputFile)) {
		file_put_contents($outputFile, $result);
	} else {
		echo $result;
	}
?>