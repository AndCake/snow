Snow
====
[![Build Status](https://travis-ci.org/AndCake/snow.png?branch=master)](https://travis-ci.org/AndCake/snow)

This is a pure and simplified implementation of SnowScript as created by [runekaagaard](https://github.com/runekaagaard/snowscript). In general, Snowscript is a language that compiles to PHP. Its syntax is inspired by Python, Lua, Coffescript, Go and Scala and strives to be DRY, clean and easy to read as well as write. 

Snow is in fact not a 100% clone of SnowScript, but in some regards tries to closer resemble CoffeeScript. Also the language is in respect to SnowScript just a functional sub-set. The result of this simplification: a tiny compiler that can be embedded into almost any PHP application without any further dependencies. It comes with a command line tool to compile Snow sources to PHP files and the core compiler class, which is responsible for converting the Snow code to PHP code. 

Snow looks like this:
	
	fn how_big_is_it(number)
		if number < 100
			<- 'small'
		else
			<- 'big'
	
	randomNumber = rand(0, 1000)
	randomNumber->how_big_is_it()->echo(" is the number {randomNumber}.")

	for i in 1 to randomNumber
		print("{i}: hallo")
	
This code compiles to the following PHP code:

	<?php
	function how_big_is_it($number) {;
		if ((gettype($_tmp1 = $number) === gettype($_tmp2 = 100) && ($_tmp1  <  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null))) {;
				return 'small';
				;
		} else {;
				return 'big';
				;
		}
	;};
	$randomNumber = rand(0, 1000);
	echo(how_big_is_it($randomNumber), " is the number " . ($randomNumber) . ".");
	for ($i = 1; $i < $randomNumber; $i += 1) {;
		print("" . ($i) . ": hallo");
	}
	unset($i);
	?>

The notable differences are:

* Snow does $this-> via @ . So code like `@myvar` is translated to `$this->myvar`.
* in addition to `==` and `!=` the compare operators can also be expressed using `is` and `isnt`
* function calls without parameters can be written like `myfunc()` and `do myfunc`
* variable typing is not allowed
* array comprehension, named parameters and closures are not supported
* inner functions are not only available inside the scope they are defined
* classes work slightly different (only functions and comments are allowed within a class)
* namespaces are not implemented, nor is the `import()` function

Documentation
=============

Whitespace
----------
Snow has significant whitespace, meaning that the code structure is managed by indenting/dedenting and not by curly brackets "`{}`". Whitespace is not significant inside strings and brackets "`()[]`".

The only allowed indention format is 4 spaces or one tab.

Variables
---------
A variable matches the regular expression "`_*[a-zA-Z][a-zA-Z0-9_]+`".
Snow:

	fungus = "Sarcoscypha coccinea"

PHP:

	$fungus = "Sarcoscypha coccinea"

Global variables are all uppercase and are automatically imported within the functions they are used.
Snow:

	GLOB = 2
	loca = 3

	fn render
		# will render 2
		echo GLOB
		# will issue an E_NOTICE
		echo loca

PHP:

	$GLOB = 2;
	$loca = 3;

	function render() {
		global $GLOB;
		// will render 2
		echo $GLOB;
		// will issue an E_NOTICE
		echo $loca;
	}

Constants
---------
A constant has a prefixed "!" and supports assignment.

Snow:

	!DB_ENGINE = 'mysql'

PHP:

	define("DB_ENGINE", 'mysql');

The use of of constants in Snow is not recommended. This is because PHP constants are limited to scalar values and thus breaks the symmetry when you all of a sudden need to have a constant that is, say an array. All caps variables are recommended instead.

Comparison
----------
All comparison operators are strong and there are no weak versions. The supported operators are "`==`", "`is`, "`!=`", "`isnt`", "`<`", "`>`", "`<=`" and "`>=`".

Snow:

	a is b and c != d

	if myFeet() > averageFeet
		echo("BIGFOOT")

PHP:

	$a === $b && $c !== $d;

	if ((gettype($_tmp1 = myFeet()) === gettype($_tmp2 = $averageFeet) && ($_tmp1  >  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null))) {
		echo("BIGFOOT");
	}

Comments
--------

Snow:

	# single line comment
	###
		multi line 
		comment
	###

PHP:

	// single line comment
	/*
		multi line
		comment
	*/

Strings
-------
There are three kind of strings: `"""`, `"` and `'`, all multiline.

#### Quoted
Code inside "`{}`" concatenates to the string.

Snow:

	fn travel
	    echo "
	    	The {animal} went to {world.place()}
	        with his {!NUM} friends.
	    "

	"""<a href="https://snowscript.org">Snowscript</a>\n"""

PHP:

	function travel() {
		echo("
			The " . ($animal) . " went to " . ($world->place()) . "
			with his " . (NUM) . " friends.
		");
	}

	<<<EOL
	<a href="https://snowscript.org">Snowscript</a>\n
	EOL;

#### Unquoted

Snow:
	
	'no {magic} here'

PHP:

	'no {magic} here';

Concatenation
-------------
Strings can be concatenated with the "`%`" operator, but the "`Hello {current_planet()}`" form is preferred. If one of the two sides is a string literal, the `+` operator can be used instead.

Snow:

	myString = "test"
	value = _SERVER['REQUEST_STRING']
	echo("I am " + "legend!")
	echo(myString % value)

PHP:

	$myString = "test";
	$value = $_SERVER['REQUEST_STRING'];
	echo("I am " . "legend!");
	echo($myString . $value);

Regular Expressions
-------------------
Regular expressions can be written similar to the syntax in Javascript / CoffeeScript by simply surrounding it with `/`. It doesn't need to be a string.

Snow:

	/^[a-z]+$/i->preg_match(myvar, match)
	if match[0]??
		echo "It has only letters"

PHP:

	preg_match('/^[a-z]+$/i', $myvar, $match);
	if ((isset($match[0]) && !empty($match[0]))) {
		echo("It has only letters");
	}

Arrays
------
Arrays are defined using square brackets "`[]`". They can be defined in two different ways, either as a list of values or a dictionary of key/value pairs.

Each value or key/value pair are separated by "`,`".

#### List

Snow:

	pianists = ["McCoy Tyner", "Fred Hersch", "Bill Evans"]

PHP:

	$pianists = Array("McCoy Tyner", "Fred Hersch", "Bill Evans");

The values are assigned running integers and can be accessed with "`[]`".

Snow:

	# => Fred Hersch
	echo pianists[1]

	# => Fred Hersch, Bill Evans
	echo pianists[1...2]->join ", "

PHP:

	// => Fred Hersch
	echo($pianists[1]);

	// => Fred Hersch, Bill Evans
	echo(join(array_slice($pianists, $_tmp3 = (1), (2) - $_tmp3 + 1), ", "));

#### Dictionary
The key and value of each key/value pair are separated by ":".

Snow:

	series = [
	    'Heroes': [
	        'genre': 'Science Fiction',
	        'creator': 'Tim Kring',
	        'seasons': 4,
	    ],
	    'Game Of Thrones': [
	        'genre': 'Medieval fantasy',
	        'creator': 'David Benioff',
	        'seasons': 2,
	    ]
	]

PHP:

	$series = Array(
	    "Heroes" => Array(
	        'genre' => "Science Fiction",
	        'creator' => "Tim Kring",
	        'seasons' => 4,
	    ),
	    "Game Of Thrones" => Array(
	        'genre' => "Medieval fantasy",
	        'creator' => "David Benioff",
	        'seasons' => 2,
	    ),
	);

Accessing dictionaries is done using square brackets "[]".

Snow:

	echo series['Heroes']['genre']

PHP:

	echo($series['Heroes']['genre']);

Functions
---------
The "`fn`" keyword is used to define functions, and "`<-`" to return a value.

Snow:

	fn titlefy(fancystring)
		<- fancystring.make_fancy()

PHP:

	function titlefy($fancystring) {
		return $fancystring->make_fancy();
	}

Functions can be called by using normal PHP syntax and also by using standard CoffeeScript syntax: braces can be left away for simple function calls, but should not for nested function calls. If the function should be called without parameters, it's also possible to use the `do` keyword.

Snow:

	print("Test")
	var_dump fancyObject, someResult
	message = do findMessage

PHP:

	print("Test");
	var_dump($fancyObject, $someResult);
	$message = findMessage();

#### Chaining
Function calls can be chained using the "`->`" operator which passes the prior expression along as the first argument to the function.

Snow:

	"peter"->ucfirst()->str_rot13()

PHP:

	str_rot13(ucfirst("peter"));

Control structures
------------------
Two control structures are available: "`if`" and the ternary operator.

#### if
Snow:

	if white_walkers.numbers < 500
		do fight_valiantly
	elif feeling_lucky
		do improvise
	else
		do run

PHP:

	if ((gettype($_tmp1 = $white_walkers->numbers) === gettype($_tmp2 = 500) && ($_tmp1  <  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null))) {
		fight_valiantly();
	} else if ($feeling_lucky) {
		improvise();
	} else {
		run();
	}

#### Ternary operator
Ternary operator is a oneline `if a then b else c` syntax.

Snow:

	a = if height is veryHigh then "tall" else "small"

PHP:

	$a = ($height === $veryHigh ? "tall" : "small");

#### Type Checks
Snow:

	if a isa number then a + 2
	if a isa function then call_user_func(a)
	if a isa float then intval(a)
	if a isa Donkey then a.run()

PHP:
	
	(is_numeric($a) ? $a + 2 : null);
	(is_callable($a) ? call_user_func($a) : null);
	(is_float($a) ? intval($a) : null);
	($a instanceof Donkey ? $a->run() : null);

Existence
---------
There are two existence operators "`?`" and "`??`". The first checks with `isset(expr)` / `defined(const)`, the second with `!empty(expr)`.

Snow:

	if field['title']? or !RANDOM_STUFF?
		do_stuff()

	stuff = if try_this()?? then that['girl'] else "Default"

PHP:

	if (isset($field['title']) || defined("RANDOM_STUFF")) {
		do_stuff();
	}

	$stuff = (((($_tmp1 = (try_this())) || true) && isset($_tmp1) && !empty($_tmp1) && (($_tmp1 = null) || true) || ($_tmp1 = null)) ? $that['girl'] : "Default");

Loops
-----
Two kind of for loops are supported. Iterating over a collection, and iterating over a numeric range. Both key and value are local to the loop. 

Snow: 

	for data, title in flowers
		echo("{data.id}: {title}")
	
	for i in 1 to 10 step 2
		echo(i)
	for i in 10 downto 1
		echo(i)

PHP:

	foreach ($flowers as $title => $data) {
		echo("" . ($data->id) . ": " . ($title) . "");
	}
	unset($data, $title);

	for ($i = 1; $i <= 10; $i += 2) {
		echo($i);
	}
	unset($i);
	for ($i = 10; $i >= 1; $i -= 1) {
		echo($i);
	}
	unset($i);

#### While

Snow:

	while frog.ass.is_watertight
	    echo("Rinse and repeat.")

PHP:

	while ($frog->ass->is_watertight) {
	    echo("Rinse and repeat.");
	}

Classes
-------
The "`@`" is used to access the class instance.

Snow:

	class X extends Y

		fn X
			Y..a = "test"
			value = do Y..run
			@test = 12
			echo(value)

		fn getTest()
			<- @test

PHP:

	class X extends Y {
		function X() {
			Y::a = "test";
			$value = Y::run();
			$this->test = 12;
			echo($value);
		}

		function getTest() {
			return $this->test;
		}
	}

"`..`" is used to access the class. A class can inherit a single class, and implement multiple interfaces.

#### Usage
Properties and methods on instantiated classes is accessed with the "." operator. Using ".." accesses static members.

Snow:

	wind = new Wind(52, 12)
	do wind.blow

	Player..register("Ronaldo")
	Player..genders

PHP:

	$wind = new Wind(52, 12);
	$wind->blow();

	Player::register("Ronaldo");
	Player::$genders;

De-structuring assignments
--------------------------
Snowscript has simple destructuring.

Snow:

	[a,, c] = [b, c, a]
	[a, b, [c, d]] = do getLetters

PHP:

	list($a,, $c) = array($b, $c, $a);
	list($a, $b, list($c, $d)) = getLetters();

Helper Functions
----------------
In order to unify the coding experience - especially with the usual suspects in PHP, some helper functions exist.

#### The `in()` function

The `in(needle, haystack)` function will return true, if the first argument is a part of the second argument. The `haystack` can be numbers,
arrays, strings and objects.

Snow:

	if 3->in [1, 2, 3]
		echo "3 is in.\n"
	if "e"->in "test"
		echo "e is in test.\n"
	if 3->in 10
		echo "3 is in 10.\n"

PHP:
	
	if (in(3, Array(1, 2, 3))) {
		echo("3 is in.\n");
	}
	if (in("e", "test")) {
		echo("e is in test.\n");
	}
	if (in(3, 10)) {
		echo("3 is in 10.\n");
	}

#### The `replace()` function

The `replace(haystack, needle, replacement)` function will replace the `needle` with the `replacement` in the `haystack`. The `needle` can be either
a regular expression, a normal string. If the `haystack` is an array, the `replace()` function will simply replace the `needle` with the `replacement`.

Snow:

	# will output "TesT"
	echo "test"->replace 't', 'T'
	
	# will output "Test"
	echo "test"->replace /^[a-z]/, 'T'
	
	# will output "[1, 4, 3]"
	echo [1, 2, 3]->replace(2, 4)->json_encode()

#### The `template()` function

The `template(tpl, data)` function provides a basic templating system with a syntax resembling [Mustache](http://mustache.github.io). The 
first argument `tpl` can either be the template itself or the name of the template file. All template file names must end in `.tpl`. The 
file path provided should be without the ending `.tpl`. The second parameter `data` provides means to transmit data to the template for use
in template variables.

Template `nameList.tpl`:
	
	<h2>Names</h2>
	{{#names}}
  		{{> user}}
	{{/names}}

Template `user.tpl`:
	
	<strong>{{name}}</strong>

Snow:

	echo template "nameList", ["names": [
		["name": Carl"], 
		[name: "Fred"], 
		[name: "Dan"]
	]]

	### will output:
		<h2>Names</h2>
		<strong>Carl</strong>
		<strong>Fred</strong>
		<strong>Dan</strong>
	###

#### The `debugger()` function

This function stops the execution at the current point and renders the current program status (defined variables,
the current line of code that is executed next, call stack and some controls). The controls can be used to
resume execution, show the stack trace, do step-wise execution, and stop further execution of the program. 
It is designed to work even without Xdebug and other debugging extensions and works from within a web interface 
and the command line interface. 

Pre-condition for the debugger() function to work is, that the Snow code was compiled with the `-d` compiler
switch (if using the `snow.php` script) or the third parameter of the SnowCompiler constructor was set to
`true` (if using a custom script to compile/execute snow code). 