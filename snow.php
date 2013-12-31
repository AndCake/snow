#!/usr/bin/env php -q
<?php
 if (!function_exists('in')) { function in($needle, $haystack) { switch(gettype($haystack)) { case 'string': return strpos($haystack, $needle) !== false; case 'array': return in_array($needle, $haystack); case 'integer': return $haystack - $needle >= 0; case 'object': return isset($haystack->$needle);default:return null; } } } if (!function_exists('replace')) { function replace($haystack, $pattern, $rep) { if (is_array($haystack)) { $pos = array_search($pattern, $haystack, true); if ($pos === false) return $haystack; return array_replace($haystack, Array($pos => $rep)); } $pp = '/^[^a-zA-Z0-9\\\\s].*' . str_replace('/', '\/', $pattern[0]) . '[imsxADSUXJu]*$/m'; if (preg_match($pp, $pattern)) return preg_replace($pattern, $rep, $haystack); else return str_replace($pattern, $rep, $haystack); } } if (!function_exists('template')) { function template($file, $data, $rootCore = '$data', $eval = true, $depth = 0) { $code = str_replace('\'', '\\\'', (file_exists($file.'.tpl') ? file_get_contents($file . '.tpl') : $file)); $parseVar = function($var, $root = '$data') use ($rootCore) { if (trim($var) == '.') return $root; $parts = explode('.', trim($var)); if ($parts[0] == '') $start = $rootCore; else $start = $root . '[\'' . $parts[0] . '\']'; for ($i = 1; $i < count($parts); $i++) { $start .= '[\'' . $parts[$i]. '\']'; } return $start; }; $parseVars = function($code, $root = '$data') use ($parseVar) { preg_match_all('/\{\{(?!#\/\^)((?:[^}]|\}[^}])+)\}\}/m', $code, $matches); foreach ($matches[1] as $key => $match) { $var = $parseVar($match, $root); $var = '(is_object('.$var.') && is_callable('.$var.') ? '.$var.'() : '.$var . ')'; if (trim($match) != '.') { $var = 'function_exists(\''.$match.'\') ? '.$match.'() : ' . $var; } $code = str_replace($matches[0][$key], '\' . (' . $var . ') . \'', $code); } return $code; }; $parseTpl = function($code, $root = '$data') use ($depth, $file) { preg_match_all('/\{\{>((?:[^}]|\}[^\}])+)\}\}/m', $code, $matches); foreach ($matches[1] as $key => $match) { $code = str_replace($matches[0][$key], template(dirname((file_exists($file.'.tpl') ? $file : __FILE__))."/".trim($match), null, $root, false, $depth + 1), $code); } return $code; }; preg_match_all('/\{\{(#|\^)((?:[^}]|\}[^}])+)\}\}((?:[^{]|\{[^{]|\{\{[^#\^])*)\{\{\/\2\}\}/m', $code, $matches); foreach ($matches[1] as $key => $match) { $var = $parseVar($matches[2][$key]); if ($match == '^') { $code = str_replace($matches[0][$key], '\'; if (empty('.$var.')) { $__p .= \'' . $matches[3][$key] . '\'; } $__p .= \'', $code); } else if ($match == '#') { $replacement = '\'; if (!empty('.$var.')) { if (is_array('.$var.')) foreach (' . $var . ' as $counter => $entry'.$depth.') { $__p .= \''; $replacement .= $parseVars($parseTpl($matches[3][$key], '$entry'.$depth), '$entry'.$depth) . '\'; } else { $__p .= \'' . $matches[3][$key] . '\'; }} $__p .= \''; $code = str_replace($matches[0][$key], $replacement, $code); } } $code = $parseVars($parseTpl($code, $rootCore), $rootCore); if ($eval) { $code = '$__p = \'' . $code . '\';'; eval($code); return $__p; } return $code; } } 
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
include_once(dirname(__FILE__) . "/snowcompiler.php");
$outputFile  =  null;
$interactive  =  true;
$HELPERS  =  true;
$DEBUG  =  false;
$compileOnly  =  false;
$watch  =  false;
$LASTFAIL  =  999999999999999;
// this function waites for changes in the failed of the given directory;
// if a change occurs, the callback function is called with the file that ;
// changes as the argument.;
function watchChanges($dir, $callback) {global $LASTFAIL;

	$dp  =  opendir($dir);
	if (!$dp) {
		throw(new Exception("Unable to open directory " . ($dir) . "."));
}
;
	// read all the files and directory names;
	while (($file  =  readdir($dp)) !== false) {
		$path  =  "" . ($dir) . "/" . ($file) . "";
		// check change time of tge snow file;
		$oldtime  =  filemtime($path);
		$newfile  =  replace($path, '/\.snow$/', '.php');
		// if the current file is a snow file and it is newer than the compiled php file;
		if (preg_match('/\.snow$/', $file) !== false && (!file_exists($newfile) || (gettype($_tmp1 = filemtime($path)) === gettype($_tmp2 = filemtime($newfile)) && ($_tmp1  >  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null)) || (gettype($_tmp1 = $oldtime) === gettype($_tmp2 = $LASTFAIL) && ($_tmp1  >  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null)))) {
			try {
				// call the callback function with the changed snow file;
				call_user_func($callback, $dir . '/' . $file);
				$LASTFAIL  =  999999999999999;
} catch (Exception $e) {
$catchGuard = true;
				// if an exception occured, show the error message;
				echo("\n" . $e->getMessage());
				echo(" FAILED");
				// put the current time into the php file (so we will not try to ;
				// compile it again until the .snow file changed);
				file_put_contents($newfile, time());
				$LASTFAIL  =  time();
}
if (!isset($catchGuard)) {
} else {
unset($catchGuard);
}
;
			// end block;
	} else if (is_dir($path) === true && $file[0] !== '.') {
			// if the current file is a directory and not a hidden one;
			// recursively call youself;
			watchChanges($dir . '/' . $file, $callback);
		}
;
		// end block;;
	}
;
	closedir($dp);
};
// this function compiles the given file and writes it to the hard disk;
function compile($file) {global $DEBUG, $HELPERS;

	// inform the user that we are compiling the file;
	echo("\nCompiling " . ($file) . "...");
	// do the actual compilation;
	$snow  =  new SnowCompiler(file_get_contents($file), true, $DEBUG);
	$result  =  $snow->compile(false, $HELPERS);
	// write the result out into the php file;
	file_put_contents(replace($file, '/\.snow$/', '.php'), "<?php\n" . ($result) . ";\n?>");
	// inform the user that we are done.;
	echo(" OK");
};
// if the user provided some more arguments;
if ((gettype($_tmp1 = count($argv)) === gettype($_tmp2 = 1) && ($_tmp1  >  $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null))) {
	// parse them;
	$result  =  getopt("o:chwf", Array("outfile:", "compile", "help", "watch", "functions"));
	$args  =  array_slice($argv, count($result) + 1);
	if ((!empty($args[0])) && file_exists($args[0])) {
		$code  =  file_get_contents($args[0]);
		$interactive  =  false;
}
;
	// if the user provided the parameter to name his output file;
	if ((!empty($result['o'])) || (!empty($result['outfile']))) {
		// we store the value;
		$outputFile  =  $result['o'];
}
;
	if (isset($result['d']) || isset($result['debug'])) {
		$DEBUG  =  true;
}
;
	if (isset($result['f']) || isset($result['functions'])) {
		$HELPERS  =  false;
}
;
	// if the user chose to have the code only compiled & not executed;
	if (isset($result['c']) || isset($result['compile'])) {
		$compileOnly  =  true;
}
;
	// if the user wants to watch current execution directory and ;
	// auto-compile all snow files in it as soon as they change;
	if (isset($result['w']) || isset($result['watch'])) {
		$watch  =  true;
}
;
	// if the user wants to review the help;
	if (isset($result['h']) || isset($result['help'])) {
		$ver  =  SnowCompiler::$version;
		$help  =  <<<EOF

Snow Script Compiler Version $ver

Syntax:
	$argv[0] [-o <output file>] [-chwf] [<input file>]

	-o <output file> - write the compiled result into the given file. If this parameter is missing, the compiled result will be written to stdout.
	-c               - the input should only be compiled - not executed
	-h               - shows this help
	-f               - do not include helper functions into the compiled output
	-w               - watch mode: compile any changed snow file in given directory tree
	<input file>     - the file that should be used as input. if this parameter is omitted, an interactive console will let you enter commands.

EOF
	;
		die($help);
}
;
	// end block;
}
;
// in case the user activated the watch mode;
if ($watch) {
	// tell him so;
	echo("Waiting for file changes...");
	// and loop until the end of time (or the user presses CTRL+C);
	while (true === true) {
		// for all changes, compile the respective file;
		watchChanges(".", 'compile');
		sleep(1);;
	}
;
	die();
;
}
;
// if the options enable the interactive mode (no file given);
if ($interactive) {
	// read the code from stdin;
	$fp  =  fopen("php://stdin", 'r');
	$input  =  "";
	while (!feof($fp)) {
		// read until the user sends the EOF signal (CTRL+D);
		$input  =  $input . fread($fp, 4096);;
	}
;
	// and use the input as the source code;
	$code  =  $input;
}
;
// compile the source code;
$snow  =  new SnowCompiler($code, true, $DEBUG);
$result  =  $snow->compile(false, $HELPERS);
// if the user only wanted to compile the source;
if ($compileOnly === true) {
	// wrap it;
	$result  =  "<?php\n" . ($result) . ";\n?>";
	// if an output file was given;
	if ((!empty($outputFile))) {
		// write the resulting code into the output file;
		file_put_contents($outputFile, $result);
	} else {
		// else print it out on the screen;
		echo($result);
	}
;
	// end block;
} else if ($compileOnly === false) {
	// if the user wanted the code to be executed;
	$lines  =  explode(";", $result);
	// find the last code line;
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
	// then evaluate the resulting code;
	$result  =  eval(implode(";", $lines));
	// and print it's result;
	echo($result);
}
;
null;;
?>