#!/usr/bin/env php -q
<?php
if(!function_exists('in')){function in($needle, $haystack){switch(gettype($haystack)){case 'string': return strpos($haystack, $needle) !== false; case 'array': return in_array($needle, $haystack); case 'integer': return $haystack - $needle >= 0; case 'object':return isset($haystack->$needle);default:return null;}}}
if(!function_exists('replace')){function replace($haystack, $pattern, $rep) { if (is_array($haystack)) { $pos = array_search($pattern, $haystack, true); if ($pos === false) return $haystack; return array_replace($haystack, Array($pos => $rep)); } $pp = '/^[^a-zA-Z0-9\\\\s].*' . str_replace('/', '\/', $pattern[0]) . '[imsxADSUXJu]*$/m'; if (preg_match($pp, $pattern)) return preg_replace($pattern, $rep, $haystack); else return str_replace($pattern, $rep, $haystack);}}
if(!function_exists('template')){function template($file, $data, $rootCore = '$data', $eval = true, $depth = 0) { $code = str_replace('\'', '\\\'', (file_exists($file.'.tpl') ? file_get_contents($file . '.tpl') : $file)); $parseVar = function($var, $root = '$data') use ($rootCore) { if (trim($var) == '.') { return $root; } $parts = explode('.', trim($var)); if ($parts[0] == '') $start = $rootCore; else $start = $root . '[\'' . $parts[0] . '\']'; for ($i = 1; $i < count($parts); $i++) { $start .= '[\'' . $parts[$i]. '\']'; } return $start; }; $parseVars = function($code, $root = '$data') use ($parseVar) { preg_match_all('/\{\{(?!#\/\^)((?:[^}]|\}[^}])+)\}\}/m', $code, $matches); foreach ($matches[1] as $key => $match) { $var = $parseVar($match, $root); $var = '(is_object('.$var.') && is_callable('.$var.') ? '.$var.'() : '.$var . ')'; if (trim($match) != '.') { $var = 'function_exists(\''.$match.'\') ? '.$match.'() : ' . $var; } $code = str_replace($matches[0][$key], '\' . (' . $var . ') . \'', $code); } return $code; }; $parseTpl = function($code, $root = '$data') use ($depth) { preg_match_all('/\{\{>((?:[^}]|\}[^\}])+)\}\}/m', $code, $matches); foreach ($matches[1] as $key => $match) { $code = str_replace($matches[0][$key], template(trim($match), null, $root, false, $depth + 1), $code); } return $code; }; preg_match_all('/\{\{(#|\^)((?:[^}]|\}[^}])+)\}\}((?:[^{]|\{[^{]|\{\{[^#\^])*)\{\{\/\2\}\}/m', $code, $matches); foreach ($matches[1] as $key => $match) { $var = $parseVar($matches[2][$key]); if ($match == '^') { $code = str_replace($matches[0][$key], '\'; if (empty('.$var.')) { $__p .= \'' . $matches[3][$key] . '\'; } $__p .= \'', $code); } else if ($match == '#') { $replacement = '\'; if (!empty('.$var.')) { if (is_array('.$var.')) foreach (' . $var . ' as $counter => $entry'.$depth.') { $__p .= \''; $replacement .= $parseVars($parseTpl($matches[3][$key], '$entry'.$depth), '$entry'.$depth) . '\'; } else { $__p .= \'' . $matches[3][$key] . '\'; }} $__p .= \''; $code = str_replace($matches[0][$key], $replacement, $code); } } $code = $parseVars($parseTpl($code, $rootCore), $rootCore); if ($eval) { $code = '$__p = \'' . $code . '\';'; eval($code); return $__p; } return $code; }}
/*
	Copyright 2013 Robert Kunze

	Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at

	    http://www.apache.org/licenses/LICENSE-2.0

	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.
*/;
include_once("snowcompiler.php");
$outputFile  =  null;
$interactive  =  true;
$compileOnly  =  false;
if ((gettype($_tmp1 = count($argv)) === gettype($_tmp2 = 1) && ($_tmp1  >  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null))) {
	$result  =  getopt("o:c:h");
	if ((gettype($_tmp1 = count($result)) === gettype($_tmp2 = 0) && ($_tmp1  <=  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null))) {
		foreach ($argv as $i => $val) {
			if ((gettype($_tmp1 = $i) === gettype($_tmp2 = 0) && ($_tmp1  >  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null))) {
				if ($val[0] === '-') {
					$result[$val[1]]  =  "";
				} else {
					$code  =  file_get_contents($val);
					$interactive  =  false;
				}
;
				// end block;
		}
;
			// end block;
		}
		unset($val, $i);
;
		// end block;
}
;
	if ((!empty($result['o']))) {
		$outputFile  =  $result['o'];
}
;
	if ((!empty($result['c']))) {
		$code  =  file_get_contents($result['c']);
		$interactive  =  false;
		$compileOnly  =  true;
} else if (isset($result['c'])) {
		$compileOnly  =  true;
	}
;
	if (isset($result['h'])) {
		$ver  =  SnowCompiler::$version;
		$help  =  <<<EOF

Snow Script Compiler Version $ver

Syntax:
	$argv[0] [-o <output file>] [-ch] [<input file>]

	-o <output file> - write the compiled result into the given file. If this parameter is missing, the compiled result will be written to stdout.
	-c               - the input should only be compiled - not executed
	-h               - shows this help
	<input file>     - the file that should be used as input. if this parameter is omitted, an interactive console will let you enter commands.

EOF
	;
		die($help);
}
;
	// end block;
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
	if ((!empty($outputFile))) {
		file_put_contents($outputFile, $result);
	} else {
		echo($result);
	}
;
	// end block;
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
		// end block;;
	}
	unset($i);
;
	$result  =  eval(implode(";", $lines));
	echo($result);
}
;
null;;
?>