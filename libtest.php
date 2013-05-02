<?php
function oneOf($a, $b) {
	if (empty($a)) {
		return $b;
	} else {
		return $a;
	}
};

function assertC($cond) {
	if (!$cond) {
		throw new Exception("Assertion failed!");
	}
}

function assertEqual($a, $b, $message = "") {
	try {
  		assertC($a === $b);
	} catch(Exception $e) {
  		throw new Exception(oneOf($message, "Assertion failed: `" . $a . "` must be equal to `" . $b . "`."));
  	}
};

function assertEmpty($a, $message = "") {
	try {
		assertC(empty($a));
	} catch(Exception $e) {
		throw new Exception(oneOf($message, "Assertion failed: `" . $a . "` must be empty."));
	}
};

function assertNotEmpty($a, $message = "") {
	try {
		assertC(!empty($a));
	} catch (Exception $e) {
		throw new Exception(oneOf($message, "Assertion failed: `" . $a . "` must be non-empty."));
	}
};

function assertNotEqual($a, $b, $message = "") {
	try {
		assertC($a !== $b);
	} catch (Exception $e) {
		throw new Exception(oneOf($message, "Assertion failed: `" . $a . "` must not be equal to `" . $b . "`"));
	}
};

function should($name, $fn) {
	try {
		$fn();
		return "" . $name . " success.";
	} catch (Exception $error) {
		throw new Exception("" . $name . ": " . $error->getMessage());
	}
};

function describe($name, $fn) {
	return should($name, $fn);
}
?>