#!/usr/bin/env php -q
<?php
include_once("snowcompiler.php");
$outputFile  =  null;
$interactive  =  true;
$compileOnly  =  false;
if ((gettype($_tmp1 = count($argv)) === gettype($_tmp2 = 1) && ($_tmp1  >  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null))) {
	$result  =  getopt("o:c:h");
	if ((gettype($_tmp1 = count($result)) === gettype($_tmp2 = 0) && ($_tmp1  <=  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null))) {
		foreach ($argv as $i => $_val) {
			if ((gettype($_tmp1 = $i) === gettype($_tmp2 = 0) && ($_tmp1  >  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null))) {
				if ($_val[0] === '-') {
					$result[$_val[1]]  =  "";
				} else {
					$code  =  file_get_contents($_val);
					$interactive  =  false;
				}
;
				null;
		}
;
			null;
		}
		unset($_val, $i);
;
		null;
}
;
	if (((($_tmp1 = ($result['o'])) || true) && isset($_tmp1) && !empty($_tmp1) && (($_tmp1 = null) || true) || ($_tmp1 = null))) {
		$outputFile  =  $result['o'];
}
;
	if (((($_tmp1 = ($result['c'])) || true) && isset($_tmp1) && !empty($_tmp1) && (($_tmp1 = null) || true) || ($_tmp1 = null))) {
		$code  =  file_get_contents($result['c']);
		$interactive  =  false;
		$compileOnly  =  true;
} else if (isset($result['c'])) {
		$compileOnly  =  true;
	}
;
	if (isset($result['h'])) {
		$ver  =  SnowCompiler::$VERSION;
		$help  =  <<<EOF

Snow Script Compiler Version $ver

Syntax:
	$argv[0] [-o <output file>] [-ch] [<input file>]

	-o <output file> - write the compiled result into the given file. If this parameter is missing, the compiled result will be written to stdout.
	-c               - the input should only be compiled - not executed
	-h               - shows this help
	<input file>     - the file that should be used as input. if this parameter is omitted, an interactive console will let you enter commands.

EOF;
		die($help);
}
;
	null;
}
;
if ($interactive) {
	$fp  =  fopen("php://stdin", 'r');
	$input  =  "";
	while (!feof($fp)) {
		$input  =  $input . fread($fp, 4096);;
	}
;
	$code  =  $input;
}
;
$snow  =  new SnowCompiler($code);
$result  =  $snow->compile();
;
if ($compileOnly === true) {
	$result  =  "<?php\n" . ($result) . ";\n?>";
	if (((($_tmp1 = ($outputFile)) || true) && isset($_tmp1) && !empty($_tmp1) && (($_tmp1 = null) || true) || ($_tmp1 = null))) {
		file_put_contents($outputFile, $result);
	} else {
		echo($result);
	}
;
	null;
} else if ($compileOnly === false) {
	echo("\n==> Result: \n");
	$lines  =  explode(";", $result);
	for ($i = count($lines) - 1; $i >= 0; $i -= 1) {
		$trimmed  =  trim($lines[$i]);
		if (!empty($trimmed)) {
			$lines[$i]  =  "return " . $lines[$i];
			break;
	}
;
		null;;
	}
	unset($i);
;
	$result  =  eval(implode(";", $lines));
	echo($result);
}
;
null;;
?>