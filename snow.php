<?php
# T_FN_DEF: fn <T_IDENTIFIER>(\(<T_PARAMETERS>\))?(<T_NEWLINE><T_FNBODY>)|(<T_RETURN>)<T_NEWLINE>"
# T_ASSIGNMENT: <T_IDENTIFIER> = <T_EXPRESSION><T_NEWLINE>
# T_RETURN: <- <T_EXPRESSION>
# T_FNCALL: <T_IDENTIFIER>\((<T_LITERAL>|<T_IDENTIFIER>)?\)|do <T_IDENTIFIER><T_NEWLINE>
# T_CONST: !<T_IDENTIFIER> = <T_SIMPLE_EXPRESSION><T_NEWLINE>
# T_IF: if <T_PCONDITION>(<T_NEWLINE><T_EXPRESSIONS>(<T_NEWLINE>else<T_NEWLINE><T_EXPRESSION>)|(then <T_SIMPLE_EXPRESSION>(else <T_SIMPLE_EXPRESSION>))
# T_PCONDITION: \(<T_CONDITION>\)|<T_CONDITION>
# T_CONDITION: <T_PCOMPARISON>(<T_BOOL_OP> <T_PCOMPARISON>)*
# T_WHILE: while <T_PCONDITION><T_NEWLINE><T_EXPRESSIONS>
#
# T_PCOMPARISON: \(<T_COMPARISON>\)|<T_COMPARISON>
# T_COMPARISON: <T_EQUALS_COMPARISON>|<T_NEQUALS_COMPARISON>|<T_GT_COMPARISON>|<T_LT_COMPARISON>
# T_GT: >
# T_GTE: >=
# T_LT: <
# T_LTE: <=
# T_NEWLINE: [\n]+
# T_FN: fn
# T_BOOL_OP: and|or
# T_IDENTIFIER: _*[a-z][_a-zA-Z0-9]+
# T_PARAMETERS: <T_PARAMETER>(, <T_PARAMETER>)*
# T_PARAMETER: <T_IDENTIFIER>(=<T_LITERAL>)?
# T_LITERAL: <T_NUMBER_LITERAL>|<T_STRING_LITERAL>|<T_REGEXP_LITERAL>|<T_ARRAY_LITERAL>|<T_BOOLEAN_LITERAL>
# T_BOOLEAN_LITERAL: true|false
# T_NUMBER_LITERAL: (-?[0-9]*.?[0-9]+)
# T_STRING_LITERAL: <T_STRING_LITERAL_UQUOTE>|<T_STRING_LITERAL_DQUOTE>|<T_STRING_LITERAL_TQUOTE>
# T_STRING_LITERAL_UQUOTE: '([^']*)'
# T_STRING_LITERAL_DQUOTE: "((\{<T_SIMPLE_EXPRESSION>\}|[^"])*)"
# T_STRING_LITERAL_TQUOTE: """([^"]|"[^"]|""[^"])+"""
# T_REGEXP_LITERAL: /([^/]+)/[imsxADSUXJu]+
# T_ARRAY_LITERAL: \[(<T_ARRAY_VALUES>)?\]
# T_ARRAY_VALUES: <T_KEYVALUE_PAIR>|<T_SIMPLE_EXPRESSION>([,\n]\s*<T_KEYVALUE_PAIR>|<T_SIMPLE_EXPRESSION>)*
# T_KEYVALUE_PAIR: <T_LITERAL>: <T_SIMPLE_EXPRESSION>
# T_EXPRESSIONS: (<T_EXPRESSION><T_NEWLINE>)+
# T_SIMPLE_EXPRESSION: <T_ASSIGNMENT>|<T_FNCALL>|<T_LITERAL>|<T_IDENTIFIER>|<T_RETURN>|<T_IF>|<T_CONST>|<T_CONDITION>
# T_EQUALS_COMPARISON: <T_SIMPLE_EXPRESSION> (is|==) <T_SIMPLE_EXPRESSION>
# T_NEQUALS_COMPARISON: <T_SIMPLE_EXPRESSION> (isnt|!=) <T_SIMPLE_EXPRESSION>
# T_GT_COMPARISON: <T_SIMPLE_EXPRESSION> <T_GT>|<T_GTE> <T_SIMPLE_EXPRESSION>
# T_LT_COMPARISION: <T_SIMPLE_EXPRESSION> <T_LT>|<T_LTE> <T_SIMPLE_EXPRESSION>
#
# T_EXPRESSION: <T_FN_DEF>|<T_SIMPLE_EXPRESSION>	# ANYTHING is an expression!
# 
/*
class SnowLexer {
	protected $_keywords = "if|then|else|elif|true|false|null|downto|to|throw|empty|default|switch|catch|try|in|for|while|and|or|xor|not|class|array|step|isa|when|fn|break|do|echo";
	protected $_terminals = Array(
		"( {4}|\t)" => "T_INDENT",
		"(\s+)" => "T_WHITESPACE",
		"(-?[0-9]*\.[0-9]+)" => "T_FLOAT_NUMBER",
		"(-?[0-9]+)" => "T_DEC_NUMBER",
		"(0x[0-9A-F]+)" => "T_HEX_NUMBER",
		"(0[0-7]+[1L]?)" => "T_OCT_NUMBER",
		"(\\\n)" => "T_ESCAPED_NEWLINE",
		"(\r?$)" => "T_NEWLINE",
		"(![A-Z]+)" => "T_CONSTANT",
		"([a-z_][_a-zA-Z0-9\\.]*)" => "T_IDENTIFIER",
		"([A-Z][a-zA-Z0-9]*)" => "T_CLASSNAME",
		"(\\+=)" => "T_PLUS_EQ",
		"(-=)" => "T_MINUS_EQ",
		"(\\*=)" => "T_MUL_EQ",
		"(\\/=)" => "T_DIV_EQ",
		"(&&)" => "T_DOUBLE_AND",
		"(###)" => "T_TCOMMENT",
		"(#)" => "T_SCOMMENT",
		"(%=|mod=)" => "T_MOD_EQ",
		"(\\+\\+)" => "T_INC",
		"(--)" => "T_DEC",
		"(%|mod)" => "T_MOD",
		'("[^"]*[^\\\\]?")' => "T_STRING_LITERAL_DQUOTE",
		'("""([^"]|"[^"]|""[^"])*""")' => "T_STRING_LITERAL_TQUOTE",
		"('[^']*[^\\\\]')" => "T_STRING_LITERAL_SQUOTE",
		"(\\\"|\\\\{|\\\\)" => "T_INDQUOTE_ESCAPE",
		"(\\/[^\\/]+\\/[imsxADSUXJu]+)" => "T_REGEXP_LITERAL",
		"(\\')" => "T_INSQUOTE_ESCAPE",
		"(\\[)" => "T_LSQB",
		"(\\])" => "T_RSQB",
		"(is|==)" => "T_EQUALS",
		"(isnt|!=)" => "T_NEQUALS",
		"(<-)" => "T_RETURN",
		"(->)" => "T_CHAIN",
		"(<=)" => "T_LTE",
		"(>=)" => "T_GTE",
		"(<)" => "T_LT",
		"(>)" => "T_GT",
		"(!)" => "T_NOT",
		"(\\.\\.)" => "T_STATIC_CALL",
		"(\\.)" => "T_DOT",
		"(=)" => "T_ASSIGN",
		"(:)" => "T_MAP_COLON",
		"(,)" => "T_COMMA",
		"(\\+)" => "T_PLUS",
		"(\\-)" => "T_MINUS",
		"(\\()" => "T_LPAREN",
		"(\\))" => "T_RPAREN",
		"(\\{)" => "T_LBRACE",
		"(\\})" => "T_RBRACE",
	);

	function __construct() {
		$keywords = explode("|", $this->_keywords);
		$res = Array();
		foreach ($keywords as $key) {
			$res["($key)"] = "T_".strtoupper($key);
		}
		$this->_terminals = array_merge($res, $this->_terminals);
	}

	function run($code) {
		$lines = explode("\n", $code);
		$tokens = Array();
		foreach ($lines as $number => $line) {
			$offset = 0;
			while ($offset < strlen($line)) {
				$result = $this->match($line, $number, $offset);
				if ($result == false) {
					throw new Exception("Unable to parse line ".($number + 1).".");
				}
				$tokens[] = $result;
				$offset += strlen($result['match']);
			}
			$tokens[] = Array("match" => "\n", "token" => "T_NEWLINE", "line" => $number + 1);
		}
		return $tokens;
	}

	function tokenizeString($str, $line) {
		$str = preg_replace('/^"""|^"|"""$|"$/m', '', $str);
		$tokens = Array();
		$lastPos = 0;
		for ($i = 0; $i < strlen($str); $i++) {
			if ($str[$i] == "\n") $line++;
			if ($str[$i] == "{") {
				if ($str[$i - 1] != "\\") {
					$end = strpos($str, "}", $i);
					$tokens[] = Array("match" => substr($str, $lastPos, $i), "token" => "T_STRING", "line" => $line);
					$substr = substr($str, $i + 1, ($i + 1) - $end);
					while ($offset < strlen($substr)) {
						$result = $this->match($substr, $line, $offset);
						$tokens[] = $result;
						$offset += strlen($result["match"]);
					}
					$lastPos = $end + 1;
					$i = $lastPos;
				}
			}
		}
		$tokens[] = Array("match" => substr($str, $lastPos), "token" => "T_STRING", "line" => $line);

		return $tokens;
	}

	protected function match($line, $number, $offset) {
		$string = substr($line, $offset);
		foreach ($this->_terminals as $pattern => $result) {
			if (preg_match("/^$pattern/", $string, $matches)) {
				return Array(
					"match" => $matches[1],
					"token" => $result,
					"line" => $number + 1
				);
			}
		}
	}
}

class SnowCompiler {
	protected $_tokens;
	protected $blockTokens = Array("T_IF", "T_FN", "T_TRY", "T_SWITCH", "T_WHILE", "T_FOR", "T_CATCH", "T_CLASS");
	protected $literalTokens = Array("T_FLOAT_NUMBER", "T_DEC_NUMBER", "T_HEX_NUMBER", "T_OCT_NUMBER", "T_CLASSNAME", "T_STRING_LITERAL_DQUOTE", "T_STRING_LITERAL_TQUOTE", "T_STRING_LITERAL_SQUOTE", "T_REGEXP_LITERAL");
	protected $indentationLevel = 0;
	protected $state = Array();
	protected $tokenPos = 0;
	protected $currentToken = null;
	protected $lexer = null;
	protected $prevProcessed = null;
	protected $wasIndented = false;

	function __construct($code) {
		$this->lexer = new SnowLexer();
		$this->_tokens = $this->lexer->run($code);
	}

	function getNextToken() {
		$this->currentToken = $this->_tokens[$this->tokenPos++];
		return $this->currentToken["token"];
	}

	function getPreviousToken() {
		return $this->_tokens[$this->tokenPos-2];
	}

	function pushState($name) {
		array_push($this->state, $name);
		echo ">".$name."\n";
	}
	function popState($name = "") {
		$val = $this->state[count($this->state) - 1];
		$val = array_pop($this->state);
		echo ">".$val."?".$name."\n";
		if (!empty($name) && $name != $val && !empty($val)) {
			array_push($this->state, $val);
			throw new Exception("Unclosed '".$val."' while trying to close ".$name." in line ".$this->currentToken["line"]);
		}
	}

	function resetToken() {
		$this->currentToken = $this->_tokens[--$this->tokenPos];
		return $this->currentToken["token"];
	}

	function run() {
		$result = "";
		while ($token = $this->getNextToken()) {
			$result .= $this->handleToken($token);
		}
		if ($this->wasIndented && $this->indentationLevel > 0 && !in_array($this->prevProcessed, $this->blockTokens)) {
			$result .= "\n}";
		}
		return $result;
	}

	function handleCondition() {
		$inParens = 0;
		do {
			$nextToken = $this->getNextToken();
			switch ($nextToken) {
				case "T_LPAREN":
					$inParens++;
					$result .= "(";
					break;
				case "T_RPAREN":
					$inParens--;
					$result .= ")";
					break;
				case "T_AND":
					$result .= " && ";
					break;
				case "T_OR":
					$result .= " || ";
					break;
				case "T_NOT":
					$result .= "!";
					break;
				case "T_IDENTIFIER":
					$result .= $this->handleIdentifier();
					break;
				case "T_LTE":
				case "T_GTE":
				case "T_GT":
				case "T_LT":
					$result .= $this->currentToken["match"];
					break;
				case "T_EQUALS":
					$result .= "===";
					break;
				case "T_NEQUALS":
					$result .= "!==";
					break;
				case "T_WHITESPACE":
					$result .= $this->currentToken["match"];
					break;
				case "T_NEWLINE":
					break;
				default: 
					if (!in_array($nextToken, $this->literalTokens)) {
						throw new Exception("Unexpected ".$nextToken." in line ".$this->currentToken["line"].": ".$this->currentToken["match"]);
					} else {
						$result .= $this->handleLiteral();
					}
			}
		} while ($nextToken != "T_NEWLINE" || $inParens > 0);
		return $result;
	}

	function handleIdentifier() {
		$name = $this->currentToken["match"];
		$result = "";
		$whiteSpace = false;
		$end = false;
		$token = $this->getNextToken();
		if ($token == "T_WHITESPACE") $token = $this->getNextToken();
		if ($token == "T_LPAREN") {
			if (strpos($name, ".") !== false) {
				throw new Exception(". not allowed for function names.");
			}
			$result .= $name."(";
			list($res, $pos) = SnowCompiler::handleSimpleExpression($this->_tokens, $this->tokenPos, "T_RPAREN");
			$this->tokenPos = $pos;
			$result .= $res.")";
		} else {
			$name = str_replace(".", "->", $name);
			$result .= "\$".$name;
			$this->resetToken();
		}
		return $result;
	}

	function handleReturn() {
		$result = "return ";
		do {
			$token = $this->getNextToken();
			try {
				if ($token) $result .= $this->handleToken($token);
			} catch(Exception $e) { 
				$token = false;
				$this->resetToken();
			}
		} while ($token);
		$result .= ";";
		return $result;
	}

	function handleLiteral() {
		$result = "";
		switch ($this->currentToken["token"]) {
			case "T_FLOAT_NUMBER":
			case "T_DEC_NUMBER":
			case "T_HEX_NUMBER":
			case "T_OCT_NUMBER":
			case "T_CLASSNAME":
			case "T_STRING_LITERAL_SQUOTE":
				return $this->currentToken["match"];
			case "T_REGEXP_LITERAL":
				return "'".str_replace("'", "\\'", $this->currentToken["match"])."'";
			default:
				$result = '"';
				# parse the string for snow...
				$tokens = $this->lexer->tokenizeString($this->currentToken["match"], $this->currentToken["line"]);
				for ($i = 0; $i < count($tokens); $i++) {
					$token = $tokens[$i];
					if ($token["token"] == "T_STRING") {
						$result .= $token["match"];
					} else {
						$result .= '" . (';
						list($res, $pos) = SnowCompiler::handleSimpleExpression($tokens, $i);
						$result .= $res;
						$result .= ') . "';
						$i = $pos - 1;
					}
				}
				$result .= '"';
				break;
		}
		return $result;
	}

	static function handleSimpleExpression($tokens, $tokenPos, $until = "T_STRING") {
		$compiler = new SnowCompiler("");
		$compiler->_tokens = $tokens;
		$compiler->tokenPos = $tokenPos;
		$compiler->currentToken = $tokens[$i];
		
		$result = "";
		while ($token = $compiler->getNextToken()) {
			if ($token != $until) {
				$result .= $compiler->handleToken($token);
			} else {
				break;
			}
		}

		return Array($result, $compiler->tokenPos); 
	}

	function handleToken($token) {
		if (in_array($token, $this->blockTokens)) {
			$this->indentationLevel++;
		}
		$result = "";
		$oldToken = $token;
		switch ($token) {
			case "T_ELIF":
				$result = str_repeat("\t", $this->indentationLevel - 1) . "} else ";
			case "T_IF":
				# scan for condition
				$result .= "if (";
				$result .= $this->handleCondition();
				$result .= ") {";
				$this->pushState("IN_IF");
				break;
			case "T_WHILE":
				$result .= "while (";
				$result .= $this->handleCondition();
				$result .= ") {";
				$this->pushState("IN_WHILE");
				break;
			case "T_DO":
				$this->getNextToken(); // Ignore Whitespace
				$this->getNextToken(); // function name
				$result = $this->currentToken["match"]."()";
				break;
			case "T_TRY":
				$result = "try {";
				$this->pushState("IN_TRY");
				break;
			case "T_CATCH":
				$result = "} catch(";
				$this->popState("IN_TRY");
				do {
					$nextToken = $this->getNextToken();
					if ($nextToken == "T_CLASSNAME") {
						$result .= $this->currentToken["match"]." ";
					} else if ($nextToken == "T_IDENTIFIER") { 
						$result .= "\$".$this->currentToken["match"];
					} else if ($nextToken == "T_WHITESPACE") {
					} else {
						throw new Exception("Unexpected ".$nextToken." in line ".$this->currentToken["line"].": ".$this->currentToken["match"]);
					}
				} while ($nextToken != "T_NEWLINE");
				$result .= ") {\n";
				$this->pushState("IN_CATCH");
				break;
			case "T_NEWLINE":
				if (!$this->wasIndented) {
					if ($this->indentationLevel > 0 && !in_array($this->prevProcessed, $this->blockTokens)) {
						$result .= "\n}";
						$this->indentationLevel--;
						$this->popState();
					}
				}
				$result .= ";\n";
				$this->wasIndented = false;
				break;
			case "T_IDENTIFIER":
				$result = $this->handleIdentifier();
				break;
			case "T_FN":
				$result = "function ";
				$end = false;
				$this->pushState("FUNCTION_BODY");
				$token = $this->getNextToken();
				if ($token == "T_WHITESPACE") $token = $this->getNextToken();
				if ($token == "T_IDENTIFIER") {
					$result .= $this->currentToken["match"];
					$token = $this->getNextToken();
					if ($token == "T_WHITESPACE") $token = $this->getNextToken();
				} 
				# parameter list or simple body?
				if ($token == "T_LPAREN") {
					$result .= "(";
					# parameter definition
					do {
						$token = $this->getNextToken();
						if ($token == "T_IDENTIFIER") {
							$result .= "\$".$this->currentToken["match"];
						} else if ($token == "T_COMMA") {
							$result .= ", ";
						} else if ($token == "T_ASSIGN") {
							# handle assignment
							$result .= " = ";
							$token = $this->getNextToken();
							if ($token == "T_WHITESPACE") $token = $this->getNextToken();
							if (in_array($token, $this->literalTokens)) {
								$result .= $this->handleLiteral();
							} else {
								throw new Exception("Unexpected ".$token." in parameter list in line ".$this->currentToken["line"].".");
							}
						} else if ($token == "T_RPAREN" || $token == "T_WHITESPACE") {
						} else {
							throw new Exception("Unexpected ".$token." in line ".$this->currentToken["line"].": ".$this->currentToken["match"]);
						}
					} while ($token != "T_RPAREN");
					$result .= ") {\n";
					$token = $this->getNextToken();
					if ($token == "T_WHITESPACE") $token = $this->getNextToken();
					if ($token == "T_RETURN") {
						$result .= $this->handleReturn();
						$result .= " }";
						$this->popState("FUNCTION_BODY");
						$this->indentationLevel--;
					} else {
						$this->resetToken();
					}
				} else if ($token == "T_RETURN") {
					$result .= "() { ";
					$result .= $this->handleReturn();
					$result .= " }";
					$this->indentationLevel--;
					$this->popState("FUNCTION_BODY");
				}
				break;
			case "T_INDENT":
				$this->wasIndented = true;
				$indent = 1;
				do {
					$token = $this->getNextToken();
					if ($token == "T_INDENT") $indent++;
				} while ($token == "T_INDENT");
				$this->resetToken();
				if ($indent < $this->indentationLevel) {
					$this->indentationLevel--;
					$result = str_repeat("\t", $this->indentationLevel) .  "}\n" . str_repeat("\t", $this->indentationLevel);
					$this->popState();
				} else {
					$result = str_repeat("\t", $this->indentationLevel);
				}
				break;
			case "T_WHITESPACE":
				$result = $this->currentToken["match"];
				break;
			case "T_ELSE":
				$result = str_repeat("\t", $this->indentationLevel - 1) . "} else {";
				$this->popState("IN_IF");
				$this->pushState("IN_ELSE");
				break;
			case "T_LPAREN":	# @TODO
				$result .= "(";
				$this->pushState("IN_PARENS");
				break;
			case "T_RPAREN":
				$result .= ")";
				$this->popState("IN_PARENS");
				break;
			case "T_COMMA":
				$result .= ",";
				break;
			case "T_ECHO":
				$result = "echo ";
				break;
			case "T_DOT":
				$result .= "->";
				break;
			case "T_MOD_EQ":
			case "T_MUL_EQ":
			case "T_PLUS_EQ":
			case "T_MINUS_EQ":
			case "T_DIV_EQ":
			case "T_ASSIGN":
			case "T_GT":
			case "T_GTE":
			case "T_LTE":
			case "T_LT":
				if ($token == "T_MOD_EQ") {
					$result .= " %= ";
				} else {
					$result .= " ".$this->currentToken["match"]." ";
				}
				break;
			case "T_LSQB":
				if ($this->getPreviousToken() == "T_IDENTIFIER") {
					# array access
				} else {
					$result .= "Array(";
					$this->pushState("IN_ARRAY");
				}
				break;
			case "T_RSQB":
				$result .= ")";
				$this->popState("IN_ARRAY");
				break;
			case "T_FLOAT_NUMBER":
			case "T_DEC_NUMBER":
			case "T_HEX_NUMBER":
			case "T_OCT_NUMBER":
			case "T_CLASSNAME":
			case "T_STRING_LITERAL_SQUOTE":
			case "T_REGEXP_LITERAL":
			case "T_STRING_LITERAL_DQUOTE":
			case "T_STRING_LITERAL_TQUOTE":
				$result = $this->handleLiteral();
				break;
			case "T_CHAIN":
				# look ahead until the end of the expression (last part of chain)
				# then, while moving backwards, generate the code
				# ...
				break;
			case "T_RETURN":
				$result = $this->handleReturn();
				break;
		}
		$this->prevProcessed = $oldToken;
		return $result;
	}
}

$code = "
while frog.ass.is_watertight
	echo \"Rinse and repeat.\"
for title, data in flowers
	echo \"{data.id}: {title}.\"
for i in 1 to 10 step 2
	echo i
if white_walkers.numbers < 500
	fight_valiantly()
elif feeling_lucky
	do improvise
else
	do run

filter(guys, (fn(guy) <- weight(guy) > 100))
\"peter\"->ucfirst()->str_rot13()
";
	
echo $code."\n\n";
$sc = new SnowCompiler($code);
$result = $sc->run();
var_dump($result);
*/
$outerStack = Array();
class SnowCompiler {
	protected $ebnf = '
{
	"T_KEYWORD": {"|": ["<T_WHILE_K>", "<T_IF_K>", "<T_FOR_K>", "<T_FN_K>", "<T_DO_K>", "<T_ECHO_K>", "<T_BOOL_AND>", "<T_BOOL_OR>", "<T_BOOLEAN_LITERAL>", "<T_NULL>", "<T_THEN_K>", "<T_ELSE_K>"]},
	"T_FOR_K": "for\\\\s+",
	"T_ECHO_K": "echo\\\\s+",
	"T_NULL": "null\\\\b",
	"T_INDENT": "\\\\s{4}|\\\\t",
	"T_INCDEC": ["<T_IDENTIFIER>", "\\\\s*(\\\\+\\\\+)|(--)"],
	"T_INDENTED_EXPRESSIONS": {"+": ["<T_NEWLINE>", "<T_INDENT>", "<T_EXPRESSION>"]},
	"T_EXPRESSIONS": {"+": ["<T_EXPRESSION>", "<T_NEWLINE>"]},
	"T_EXPRESSION": {"|": ["<T_FN_DEF>", "<T_IF>", "<T_LOOP>", "<T_SIMPLE_EXPRESSION>"]},
	"T_FN_DEF": ["fn\\\\s+", {"?": "<T_FNNAME>"}, {"?": ["\\\\s*\\\\(\\\\s*", "<T_PARAMETERS>", "\\\\s*\\\\)"]}, {"|": ["<T_INDENTED_EXPRESSIONS>", "<T_RETURN>"]}], 
	"T_SIMPLE_EXPRESSION": {"|": ["<T_ASSIGNMENT>", "<T_OPERATION>", "<T_CONDITION>", "<T_FNCALL>", "<T_RETURN>", "<T_IDENTIFIER>", "<T_LITERAL>", "<T_CONST_DEF>", "<T_CONST>"]},
	"T_CONDITION_EXPRESSION": {"|": ["<T_ASSIGNMENT>", "<T_OPERATION>", "<T_CONDITION>", "<T_FNCALL>", "<T_LITERAL>", "<T_IDENTIFIER>", "<T_CONST>"]},
	"T_CHAIN_EXPRESSION": {"|": ["<T_ASSIGNMENT>", "<T_OPERATION>", "<T_CONDITION>", "<T_FNCALL>", "<T_LITERAL>", "<T_IDENTIFIER>", "<T_CONST>"]},
	"T_ASSIGNMENT": ["<T_IDENTIFIER>", "\\\\s*=\\\\s*", "<T_SIMPLE_EXPRESSION>"],
	"T_RETURN": ["<-\\\\s*", "<T_SIMPLE_EXPRESSION>"],
	"T_FNCALL": {"|": ["<T_FNDOCALL>", "<T_FNPLAINCALL>", "<T_FN_CHAINCALL>"]},
	"T_FNDOCALL": ["do\\\\s+", "<T_FNNAME>", "<T_NEWLINE>"],
	"T_FNPLAINCALL": ["<T_FNNAME>", "\\\\s*\\\\(\\\\s*", "<T_FN_PARAMETERS>", "\\\\s*\\\\)"],
	"T_FN_CHAINCALL": ["<T_CHAIN_EXPRESSION>", {"+": ["->", "<T_FNNAME>", "\\\\s*\\\\(\\\\s*", "<T_FN_PARAMETERS>", "\\\\s*\\\\)"]}],
	"T_FN_PARAMETERS": {"?": ["<T_SIMPLE_EXPRESSION>", {"*": ["\\\\s*,\\\\s*", "<T_SIMPLE_EXPRESSION>"]}]},
	"T_CONST_DEF": ["<T_CONST>", "\\\\s*=\\\\s*", "<T_SIMPLE_EXPRESSION>"],
	"T_CONST": ["!", "<T_UPPERCASE_IDENTIFIER>"],
	"T_LOOP": {"|": ["<T_FOR_LOOP>", "<T_WHILE>"]},
	"T_FOR_LOOP": ["for\\\\s+", "<T_IDENTIFIER>", {"?": [", ", "<T_IDENTIFIER>"]}, "\\\\s+in\\\\s+", "<T_IDENTIFIER>", "<T_INDENTED_EXPRESSIONS>"],
	"T_FNNAME": "_*[a-z][_a-zA-Z0-9.]*",
	"T_IF": ["if\\\\s+", "<T_PCONDITION>", "<T_INDENTED_EXPRESSIONS>", {"*": "<T_ELIF>"}, {"?": "<T_ELSE>"}],
	"T_ELSE": ["else\\\\s*", "<T_INDENTED_EXPRESSION>"],
	"T_ELIF": ["<T_NEWLINE>", "elif\\\\s+", "<T_PCONDITION>", "<T_INDENTED_EXPRESSIONS>"],
	"T_IF_THEN": ["if\\\\s+", "<T_PCONDITION>", "\\\\s+then\\\\s+", "<T_SIMPLE_EXPRESSION>", {"?": ["\\\\s+else\\\\s+", "<T_SIMPLE_EXPRESSION>"]}],
	"T_PCONDITION": {"|": [["\\\\s*\\\\(\\\\s*", "<T_CONDITION>", "\\\\s*\\\\)\\\\s*"], "<T_CONDITION>"]},
	"T_CONDITION": [{"|": ["<T_PCOMPARISON>", [{"?": ["<T_BOOL_NEGATION>"]}, "<T_SIMPLE_EXPRESSION>"]]}, {"*": ["<T_BOOL_OP>", "<T_PCOMPARISON>"]}],
	"T_WHILE": ["while\\\\s+", "<T_PCONDITION>", "<T_INDENTED_EXPRESSIONS>"],
	"T_PCOMPARISON": {"|": [["\\\\s*\\\\(\\\\s*", "<T_COMPARISON>", "\\\\s*\\\\)\\\\s*"], "<T_COMPARISON>"]},
	"T_COMPARISON": {"|": ["<T_EQUALS_COMPARISON>", "<T_NEQUALS_COMPARISON>", "<T_GT_COMPARISON>", "<T_LT_COMPARISON>"]},
	"T_EQUALS_COMPARISON": ["<T_CONDITION_EXPRESSION>", "\\\\s+(is|==)\\\\s+", "<T_CONDITION_EXPRESSION>"],
	"T_NEQUALS_COMPARISON": ["<T_CONDITION_EXPRESSION>", "\\\\s+(isnt|!=)\\\\s+", "<T_CONDITION_EXPRESSION>"],
	"T_GT_COMPARISON": ["<T_CONDITION_EXPRESSION>", {"|": ["<T_GT>", "<T_GTE>"]}, "<T_CONDITION_EXPRESSION>"],
	"T_LT_COMPARISON": ["<T_CONDITION_EXPRESSION>", {"|": ["<T_LTE>", "<T_LT>"]}, "<T_CONDITION_EXPRESSION>"],
	"T_PARAMETERS": ["<T_PARAMETER>", {"*": ["\\\\s*,\\\\s*", "<T_PARAMETER>"]}],
	"T_PARAMETER": ["<T_IDENTIFIER>", {"?": ["\\\\s*=\\\\s*", "<T_LITERAL>"]}],
	"T_LITERAL": {"|": ["<T_REGEXP_LITERAL>", "<T_ARRAY_LITERAL>", "<T_BOOLEAN_LITERAL>", "<T_NULL>", "<T_STRING_LITERAL>", "<T_NUMBER_LITERAL>"]},
	"T_ARRAY_LITERAL": ["\\\\[\\\\s*", {"?": "<T_ARRAY_VALUES>"}, "\\\\s*\\\\]"],
	"T_ARRAY_VALUES": {"|": ["<T_KEYVALUE_PAIR>", "<T_CONDITION_EXPRESSION>"], "*": ["[,\\n]\\\\s*", {"|": ["<T_KEYVALUE_PAIR>", "<T_CONDITION_EXPRESSION>"]}]},
	"T_KEYVALUE_PAIR": ["<T_LITERAL>", "\\\\s*:\\\\s*", "<T_CONDITION_EXPRESSION>"],
	"T_STRING_LITERAL": {"|": ["<T_STRING_LITERAL_UQUOTE>", "<T_STRING_LITERAL_TQUOTE>", "<T_STRING_LITERAL_DQUOTE>"]},
	"T_IDENTIFIER": ["_*[a-zA-Z]([_a-zA-Z0-9.]*)", {"*": ["\\\\[", "<T_CONDITION_EXPRESSION>", "\\\\]"]}],
	"T_UPPERCASE_IDENTIFIER": "_*[A-Z_]+",
	"T_OPERATION": {"|": ["<T_COMPLEX_OPERATION>", "<T_COMPLEX_STRING_OPERATION>", "<T_INCDEC>"]},
	"T_COMPLEX_OPERATION": [{"|": ["<T_NUMBER_LITERAL>", "<T_IDENTIFIER>"]}, {"+": ["<T_OPERATOR>", {"|": ["<T_NUMBER_LITERAL>", "<T_IDENTIFIER>"]}]}],
	"T_COMPLEX_STRING_OPERATION": [{"|": ["<T_STRING_LITERAL>", "<T_IDENTIFIER>"]}, {"+": ["\\\\s*[\\\\+~]\\\\s*", {"|": ["<T_STRING_LITERAL>", "<T_IDENTIFIER>"]}]}],
	"T_OPERATOR": "\\\\s*[\\\\-\\\\+\\\\*/]\\\\s*",
	"T_STRING_LITERAL_UQUOTE": "\'([^\']*)\'",
	"T_STRING_LITERAL_DQUOTE": "\\"([^\\"]*)\\"",
	"T_STRING_LITERAL_TQUOTE": ["\\"\\"\\"", "([^\\"]|\\"[^\\"]|\\"\\"[^\\"])+", "\\"\\"\\""],
	"T_BOOL_OP": {"|": ["<T_BOOL_AND>", "<T_BOOL_OR>"]},
	"T_BOOL_AND": "\\\\s+and\\\\s+",
	"T_BOOL_OR": "\\\\s+or\\\\s+",
	"T_BOOL_NEGATION": "\\\\s*not\\\\s+",
	"T_BOOLEAN_LITERAL": "true|false",
	"T_NUMBER_LITERAL": {"|": ["<T_HEX_NUMBER>", "<T_OCT_NUMBER>", "<T_FLOAT_NUMBER>", "<T_DEC_NUMBER>"]},
	"T_HEX_NUMBER": "(0x[0-9A-Fa-f]+)",
	"T_OCT_NUMBER": "(0[0-7]+[1L]?)",
	"T_FLOAT_NUMBER": "(-?[0-9]*\\\\.[0-9]+)",
	"T_DEC_NUMBER": "(-?[0-9]+)",
	"T_REGEXP_LITERAL": "/([^/]+)/[imsxADSUXJu]*",
	"T_NEWLINE": "[\\n]+|$",
	"T_GTE": "\\\\s*>=\\\\s*",
	"T_LTE": "\\\\s*<=\\\\s*",
	"T_GT": "\\\\s*>\\\\s*",
	"T_LT": "\\\\s*<\\\\s*"
}';
	protected $mapRules = '{
	"T_IF": "if (\\\\2) {\\\\3;}\\n",
	"T_NEWLINE": ";\\n",
	"T_BOOL_AND": "&&",
	"T_BOOL_OR": "||",
	"T_BOOL_NEGATION": "!",
	"T_INCDEC": "\\\\1\\\\2",
	"T_COMPLEX_OPERATION": "\\\\1\\\\2",
	"T_COMPLEX_STRING_OPERATION": "\\\\1 . \\\\2.2",
	"T_FN_DEF": "function \\\\2(\\\\3.2) {\\\\4}",
	"T_EQUALS_COMPARISON": "\\\\1 === \\\\3",
	"T_NEQUALS_COMPARISON": "\\\\1 !== \\\\3",
	"T_GT_COMPARISON": "(gettype($_tmp1 = \\\\1) === gettype($_tmp2 = \\\\3) && ($_tmp1 \\\\2 $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null))",
	"T_LT_COMPARISON": "(gettype($_tmp1 = \\\\1) === gettype($_tmp2 = \\\\3) && ($_tmp1 \\\\2 $_tmp2 && (($_tmp1 = $_tmp2 = null) || true)) || ($_tmp1 = $_tmp2 = null))",
	"T_IDENTIFIER": "$${R\\\\1/\\\\./->}\\\\2",
	"T_CONST_DEF": "define(\\"\\\\1\\", \\\\3);",
	"T_CONST": "\\\\2",
	"T_FOR_LOOP": "foreach (\\\\5 as ${\\\\3.2?\\\\3.2 => /}\\\\2) {\\\\6;\\n}",
	"T_NUMBER_LITERAL": "\\\\1",
	"T_BOOLEAN_LITERAL": "\\\\1",
	"T_REGEXP_LITERAL": "\'\\\\1\'",
	"T_WHILE": "while (\\\\2) {\\\\3;}\\n",
	"T_FNPLAINCALL": "${R\\\\1/^(\\\\w+)\\\\./$\\\\1->}(\\\\3)",
	"T_FNDOCALL": "${R\\\\2/^(\\\\w+)\\\\./$\\\\1->}();\\n",
	"T_ASSIGNMENT": "\\\\1 = \\\\3",
	"T_RETURN": "return \\\\2;\\n",
	"T_STRING_LITERAL_UQUOTE": "\\\\1",
	"T_STRING_LITERAL_DQUOTE": "\\\\1",
	"T_STRING_LITERAL_TQUOTE": "<<<EOF\\n\\\\2\\nEOF;",
	"T_FN_CHAINCALL": "${v4\\\\2.2}\\\\2.2(\\\\1${\\\\2.4?, \\\\2.4/})"
}';
# ${vx\y} - recursive call: replace \y with last call's result and go x forward in tree
# \x.y - look in tree at position x and in x look at position y
# ${\x?a/b} - if \x is not empty, replace this expression with a, else with b (either one can be left empty)
# ${R\x/a/b} - replace a in \x with b
	protected $language = null;
	protected $mapping = null;
	protected $code = null;
	protected $stack = null;
	protected $successStack = null;

	function __construct($code) {
		$this->language = json_decode($this->ebnf, true);
		$this->mapping = json_decode($this->mapRules, true);
		$this->code = $code;
		$this->stack = Array();
		$this->successStack = Array();
	}

	function compile($debug = false) {
		$result = "";
		if ($tree = $this->checkRuleByName("T_EXPRESSIONS", 0, $debug)) {
			if ($tree["len"] < strlen($this->code)) {
				throw new Exception("Error while parsing input.");
			}
//			var_dump($tree);
			$result = $this->doMapping($tree);
		} else {
			throw new Exception("Unable to parse input: given input is no T_EXPRESSIONS.");
		}

		return $result;
	}

	function parseMapping($template, $tree) {
		$replacements = Array();
		preg_match_all('/\\$\\{v([1-9][0-9]*)\\\\([1-9][0-9.]*)\\}/m', $template, $recurse);
		preg_match_all('/\\\\([1-9][0-9.]*)/m', $template, $matches);
		if (count($recurse[1]) > 0) {
			foreach ($recurse[1] as $rkey => $rep) {
				$template = str_replace($recurse[0][$rkey], "", $template);
				$r = intval($rep) - 1;
				$works = true;
				$rCounter = 0;
				$replacement = Array();
				while ($works) {
					$empty = 0;
					foreach ($matches[1] as $key => $match) {
						$parts = explode(".", $match);
						$oldValue = $tree;
						for ($i = 0; $i < count($parts) - 1; $i++) {
							$tree = $tree[intval($parts[$i]) - 1];
						}
						$val = $this->getValue($tree, intval($parts[$i]) - 1);
						if (empty($val)) $empty++;
						$replacement[$matches[0][$key]] = $val;
						$tree = $oldValue;
					}
					$replacements[] = $replacement;
					# if the repeat counter is after it's first step and 
					if ($empty >= count($matches[1]) && $rCounter > 0) {
						break;
					}
					# seek forward by <Rx>
					for ($i = 0; $i < $r; $i++) array_shift($tree);
					$rCounter += $r;
				}
			}
		} else {
			$replacements = Array();
			foreach ($matches[1] as $key => $match) {
				$parts = explode(".", $match);
				$oldValue = $tree;
				for ($i = 0; $i < count($parts) - 1; $i++) {
					$tree = $tree[intval($parts[$i]) - 1];
				}
				$val = $this->getValue($tree, intval($parts[$i]) - 1);
				$replacements[0][$matches[0][$key]] = $val;
				$tree = $oldValue;
			}			
		}

		preg_match_all('/\\$\\{\\\\([1-9][0-9.]*)\\?([^\\/]*)\\/([^}]*)\\}/m', $template, $ifMatches);
		preg_match_all('/\\$\\{R\\\\([1-9][0-9]*)\\/([^\\/]*)\\/([^}]*)\\}/m', $template, $replaceMatches);
		$result = "";
		if (count($replacements) <= 0) $result = $template;
		foreach ($replacements as $repl) {
			$resultTemplate = $template;
			foreach ($ifMatches[1] as $key => $match) {
				if (!empty($repl["\\".$match])) {
					$resultTemplate = str_replace($ifMatches[0][$key], $ifMatches[2][$key], $resultTemplate);
				} else {
					$resultTemplate = str_replace($ifMatches[0][$key], $ifMatches[3][$key], $resultTemplate);
				}				
			}
			foreach ($replaceMatches[1] as $key => $match) {
				$repl["\\".$match] = preg_replace('/'.$replaceMatches[2][$key].'/m', $replaceMatches[3][$key], $repl["\\".$match]);
				$resultTemplate = str_replace($replaceMatches[0][$key], "\\".$match, $resultTemplate);
			}
			# need to somehow make it also take the recurse command into consideration...
			foreach ($repl as $key => $re) {
				# probably only needs to take the sub string into account that comes after the <Rx>
				$resultTemplate = str_replace($key, $re, $resultTemplate);
			}
			$result = $resultTemplate; 
		}
		return $result;
	}

	function doMapping($tree, $name = "") {
		$result = "";
		if ($this->mapping[$name]) {
#			echo "Mapping1 ".$name.": ".json_encode($tree)."\n";
			$result .= $this->parseMapping($this->mapping[$name], $tree);
		} else {
			foreach ($tree as $nodeName => $value) {
				if ($this->mapping[$nodeName]) {
#					echo "Mapping2 ".$nodeName.": ".json_encode($value)."\n";
					$result .= $this->parseMapping($this->mapping[$nodeName], $value);
				} else {
					if (is_array($value)) {
						$result .= $this->doMapping($value, $nodeName);
					}
				}
			}
		}
		return $result;
	}

	function getValue($ruleArray, $pos = -1) {
		# go down until I find a match
		$matches = "";
		if (isset($ruleArray["match"])) {
			$matches .= $ruleArray["match"];
		} else {
			if ($pos != -1) {
				$ruleArray = $ruleArray[$pos];
				if ($ruleArray["match"]) {
					$matches .= $ruleArray["match"];
					return $matches;
				}
			}
			if (is_array($ruleArray)) {
				foreach ($ruleArray as $name => $rule) {
					if ($this->mapping[$name]) {
						$matches .= $this->doMapping($rule, $name);
					} else {
						if ($rule["match"]) {
							$matches .= $rule["match"];
						} else {
							$matches .= $this->getValue($rule);
						}
					}
				}
			}
		}
		return $matches;
	}

	function checkRuleByName($ruleName, $pos = 0, $debug = false, $depth = 0) {
		global $outerStack;
		if ($rule = $this->language[$ruleName]) {
			if (is_array($this->stack[$pos]) && in_array($ruleName, $this->stack[$pos])) {
				return false;
			}
			if ($this->successStack[$pos][0] == $ruleName) return $this->successStack[$pos][1];
			if ($debug) echo str_repeat("\t", $depth)."checking rule ".$ruleName." at pos ".$pos."\n";
			$this->stack[$pos][] = $ruleName;
	 		if ($debug) var_dump($this->stack);
 			$result = $this->checkRule($rule, $pos, (gettype($debug) != "string" ? $debug : false) || ($ruleName == (gettype($debug) == "string" ? $debug : "")), $depth);
			if ($result !== false) {
				$res = Array();
				if ($debug) echo str_repeat("\t", $depth)."Success for rule ".$ruleName." at pos ".$pos."\n";
				$res[$ruleName] = $result;
				$res["len"] = $result["len"];
				$this->successStack[$pos] = Array($ruleName, $res);
				$this->stack[$pos] = array_diff($this->stack[$pos], Array($ruleName));
			} else {
				if ($debug) echo str_repeat("\t", $depth)."Rule ".$ruleName." does not apply at pos ".$pos."\n";
#				$this->successStack[$pos] = null;
				$res = false;
			}
			return $res;
		} else {
			throw new Exception("Unable to check rule ".$ruleName.": no such rule!");
		}
	}
	function checkRule($rule, $pos = 0, $debug = false, $depth = 0) {
		if (gettype($rule) == "string") {
			if (preg_match("`^<([\\w_]+)>\$`m", $rule, $matches)) {
				# found sub rule
				return $this->checkRuleByName($matches[1], $pos, $debug, $depth + 1);
			} else {
				# found base rule
				if ($debug) echo str_repeat("\t", $depth)."found base rule ".$rule."\n";
				if ($debug) echo str_repeat("\t", $depth)."/^(".$rule.")/ <==> ".substr($this->code, $pos)."\n";
				if (preg_match("`^(".$rule.")`", substr($this->code, $pos), $matches)) {
					if ($debug) echo str_repeat("\t", $depth)."Success!\n";
					return Array("match" => $matches[1], "pos" => $pos, "len" => strlen($matches[1]));
				} else {
					if ($debug) echo str_repeat("\t", $depth)."Failed!\n";
					return false;
				}
			}
		} else {
			$matches = true;
			$resultTree = Array();
			$checkPos = $pos;
			foreach ($rule as $modifier => $subRule) {
/*
				if ($checkPos >= strlen($this->code) && !in_array($modifier, Array("*", "?"))) {
					if ($subRule != "<T_NEWLINE>") {
						$matches = false;
					}
					if ($debug) echo str_repeat("\t", $depth)."Killing rule ".json_encode($subRule)."\n";
					break;
				}*/
				if (in_array($modifier, Array("+", "?", "*", "|"), true)) {
					# complex rule
					if ($modifier == "|") {
						$matches = false;
						if (gettype($subRule) == "string") {
							$result = $this->checkRule($subRule, $checkPos, $debug, $depth + 1);
							$matches = $matches || $result != false;
							if ($matches) {
								$resultTree[] = $result;
								$checkPos += $result["len"];
							}
						} else {
							foreach ($subRule as $subsubRule) {
								if ($debug) {
									echo str_repeat("\t", $depth)."| multi rule: ".json_encode($subsubRule)."\n";
								}
								if ($checkPos >= strlen($this->code)) {
									if ($debug) echo str_repeat("\t", $depth)."Cancelled | multi rule!\n";
									$matches = false;
									break;
								}
								$result = $this->checkRule($subsubRule, $checkPos, $debug, $depth + 1);
								$matches = $matches || $result != false;
								if ($matches) {
									if ($debug) echo str_repeat("\t", $depth)."Success | multi rule\n";
									$resultTree[] = $result;
									$checkPos += $result["len"];
									break;
								}
							}
						}
					} else {
						# quantity modifier
						$found = 0;
						$matches = true;
						$oldCheckPos = $checkPos;
						$oldResultTree = array_merge($resultTree, Array());
						while ($matches) {
							if ($checkPos >= strlen($this->code)) {
								if ($debug) echo str_repeat("\t", $depth)."Cancelled!\n";
								$matches = false;
								break;
							}
							if (gettype($subRule) == "string") {
								$result = $this->checkRule($subRule, $checkPos, $debug, $depth + 1);
								$matches = $matches && $result !== false;
								if ($matches) {
									$checkPos += $result["len"];
									$resultTree[] = $result;
								}
							} else {
								$oldCheckPos = $checkPos;
								$oldResultTree = $resultTree;
								foreach ($subRule as $subsubRule) {
									if ($debug) {
										echo str_repeat("\t", $depth)."Quantifier multi rule pos ".$checkPos.": ".json_encode($subRule)."\n";
									}
									if ($checkPos >= strlen($this->code)) {
										if (is_array($subsubRule) || $subsubRule != "<T_NEWLINE>") {
											$matches = false;
										}
										if ($debug) echo str_repeat("\t", $depth)."Cancelled quantifier multi rule!\n";
										break;
									}
									$result = $this->checkRule($subsubRule, $checkPos, $debug, $depth + 1);
									$matches = $matches && $result !== false;
									if ($matches) {
										if ($debug) echo str_repeat("\t", $depth)."Success quantifier multi rule: ".json_encode($subsubRule)."\n";
										$checkPos += $result["len"];
										$resultTree[] = $result;
									} else {
										if ($debug) echo str_repeat("\t", $depth)."Failed quantifier multi rule: ".json_encode($subsubRule)."\n";
										break;
									}
								}
								if (!$matches) {
									# roll back
									$resultTree = $oldResultTree;
									$checkPos = $oldCheckPos;									
								}
							}
							if ($matches) $found++;
						}
						if ($debug) echo str_repeat("\t", $depth)."Modifier: ".$modifier." (".$found.")\n";
						if (($found >= 1 && $modifier == "+") || $modifier == "*" || ($found <= 1 && $modifier == "?")) {
							if ($debug) echo str_repeat("\t", $depth)."Matched modifier conditions.\n";
							$matches = true;
						} else {
							$resultTree = $oldResultTree;
							$checkPos = $oldCheckPos;
							$matches = false;
						}
					}
				} else {
					# simple rule
					if ($debug) echo str_repeat("\t", $depth)."Simple multi rule at pos ".$checkPos.": ".json_encode($subRule)."\n";
					$result = $this->checkRule($subRule, $checkPos, $debug, $depth + 1);
					$matches = true;
					if ($result == false) {
						if ($debug) echo str_repeat("\t", $depth)."Failed simple multi rule: ".json_encode($subRule)."\n";
						return false;
					}
					if ($debug) echo str_repeat("\t", $depth)."Success simple multi rule: ".json_encode($subRule)."\n";
					$resultTree[] = $result;
					$checkPos += $result["len"];
				}
			}
			$len = 0;
			foreach ($resultTree as $rt) {
				$len += $rt["len"];
			}
			$resultTree["len"] = $len;
			return ($matches ? $resultTree : false);
		}
	}
}

$snow = new SnowCompiler("!VAR = _POST['var']\nvar_dump(_POST['var'])\nif not !VAR\n\t<- 'hallo!'\nfor a, b in _POST['var']\n\tprint(a ~ '=>' ~ b)\nb = 10\nwhile a < b\n\ta++\n<- 'test'\n\n\"hehe\"\n");
//$snow = new SnowCompiler("data.print('hallo')\n");
var_dump($snow->compile());

?>