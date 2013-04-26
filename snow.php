#!/usr/bin/env php -q
<?php
	include("snowcompiler.php");
	$outputFile = null;
	$code = file_get_contents("test.snow");

	if (count($argv) > 1) {
		$result = getopt("o:i:h");
		if (isset($result["o"]) && !empty($result["o"])) {
			$outputFile = $result["o"];
		} 
		if (isset($result["i"]) && !empty($result["i"])) {
			$code = file_get_contents($result["i"]);
		}
		if (isset($result['h'])) {
			$ver = SnowCompiler::VERSION;
			$help = <<<ENDL
Snow Script Compiler Version {$ver}

Syntax:
	{$argv[0]} [-o <output file>] [-i <input file>]
	
	-o <output file> - write the compiled result into the given file. If this parameter is missing, the compiled result will be written to stdout.
	-i <input file>  - the file that should be compiled. If this parameter is omitted, a test script will be compiled.

ENDL;
			die($help);
		}	
	}

	$snow = new SnowCompiler($code);
	$result = $snow->compile();

	$result = "<?php\n" . $result.";\n?>";

	if (!empty($outputFile)) {
		file_put_contents($outputFile, $result);
	} else {
		echo $result;
	}
?>
