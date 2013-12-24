<?
# define the IN function
if (!function_exists('in')) {
	#
	# in(needle, haystack) -> boolean
	# - needle (mixed) - what to find in the haystack
	# - haystack (mixed) - what to search in
	# 
	# The `in(needle, haystack)` function will return true, if the first argument is a 
	# part of the second argument. The `haystack` can be numbers, arrays, strings and objects.
	#
	function in($needle, $haystack) {
		switch(gettype($haystack)) {
			case 'string': return strpos($haystack, $needle) !== false; 
			case 'array': return in_array($needle, $haystack); 
			case 'integer': return $haystack - $needle >= 0; 
			case 'object': return isset($haystack->$needle);default:return null;
		}
	}
}

# define the REPLACE function
if (!function_exists('replace')) {
	#
	# replace(haystack, pattern, replacement) -> mixed
	# - haystack (mixed) - where to replace things in
	# - pattern (mixed) - what to search for
	# - replacement (mixed) - what to replace it with
	# 
	# will replace the `needle` with the `replacement` in the `haystack`. The `needle` can 
	# be either a regular expression, a normal string. If the `haystack` is an array, the 
	# `replace()` function will simply replace the `needle` with the `replacement`.
	function replace($haystack, $pattern, $rep) { 
		if (is_array($haystack)) { 
			$pos = array_search($pattern, $haystack, true); 
			if ($pos === false) return $haystack; 

			return array_replace($haystack, Array($pos => $rep)); 
		} 
		$pp = '/^[^a-zA-Z0-9\\\\s].*' . str_replace('/', '\/', $pattern[0]) . '[imsxADSUXJu]*$/m'; 
		if (preg_match($pp, $pattern)) 
			return preg_replace($pattern, $rep, $haystack); 
		else 
			return str_replace($pattern, $rep, $haystack);
	}
}

# the TEMPLATE function
if (!function_exists('template')) {
	# 
	# template(file, data) -> mixed
	# - file (string) - what to use as a template (can be the template name (without .tpl) or the actual template)
	# - data (Array) - the data to be used in the template variables
	#
	# provides a basic templating system with a syntax resembling [Mustache](http://mustache.github.io). The 
	# first argument `tpl` can either be the template itself or the name of the template file. All template 
	# file names must end in `.tpl`. The file path provided should be without the ending `.tpl`. 
	#
	function template($file, $data, $rootCore = '$data', $eval = true, $depth = 0) { 
		$code = str_replace('\'', '\\\'', (file_exists($file.'.tpl') ? file_get_contents($file . '.tpl') : $file)); 

		$parseVar = function($var, $root = '$data') use ($rootCore) { 
			if (trim($var) == '.') return $root; 
			$parts = explode('.', trim($var)); 
			if ($parts[0] == '') 
				$start = $rootCore; 
			else 
				$start = $root . '[\'' . $parts[0] . '\']'; 
			for ($i = 1; $i < count($parts); $i++) { 
				$start .= '[\'' . $parts[$i]. '\']'; 
			} 
			return $start; 
		}; 
		$parseVars = function($code, $root = '$data') use ($parseVar) { 
			preg_match_all('/\{\{(?!#\/\^)((?:[^}]|\}[^}])+)\}\}/m', $code, $matches); 
			foreach ($matches[1] as $key => $match) { 
				$var = $parseVar($match, $root); 
				$var = '(is_object('.$var.') && is_callable('.$var.') ? '.$var.'() : '.$var . ')'; 
				if (trim($match) != '.') { 
					$var = 'function_exists(\''.$match.'\') ? '.$match.'() : ' . $var; 
				} 
				$code = str_replace($matches[0][$key], '\' . (' . $var . ') . \'', $code); 
			} 
			return $code; 
		}; 
		$parseTpl = function($code, $root = '$data') use ($depth, $file) { 
			preg_match_all('/\{\{>((?:[^}]|\}[^\}])+)\}\}/m', $code, $matches); 
			foreach ($matches[1] as $key => $match) { 
				$code = str_replace($matches[0][$key], template(dirname((file_exists($file.'.tpl') ? $file : __FILE__))."/".trim($match), null, $root, false, $depth + 1), $code); 
			} 
			return $code; 
		}; 

		preg_match_all('/\{\{(#|\^)((?:[^}]|\}[^}])+)\}\}((?:[^{]|\{[^{]|\{\{[^#\^])*)\{\{\/\2\}\}/m', $code, $matches); 
		foreach ($matches[1] as $key => $match) { 
			$var = $parseVar($matches[2][$key]); 
			if ($match == '^') { 
				$code = str_replace($matches[0][$key], '\'; if (empty('.$var.')) { $__p .= \'' . $matches[3][$key] . '\'; } $__p .= \'', $code); 
			} else if ($match == '#') { 
				$replacement = '\'; if (!empty('.$var.')) { if (is_array('.$var.')) foreach (' . $var . ' as $counter => $entry'.$depth.') { $__p .= \''; 
				$replacement .= $parseVars($parseTpl($matches[3][$key], '$entry'.$depth), '$entry'.$depth) . '\'; } else { $__p .= \'' . $matches[3][$key] . '\'; }} $__p .= \''; 
				$code = str_replace($matches[0][$key], $replacement, $code); 
			} 
		} 
		$code = $parseVars($parseTpl($code, $rootCore), $rootCore); 
		if ($eval) { 
			$code = '$__p = \'' . $code . '\';'; 
			eval($code); 
			return $__p; 
		} 
		return $code; 
	}
}
