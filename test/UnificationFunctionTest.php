<?php
require_once dirname(__FILE__) . "/SnowTestsuite.php";

class UnificationFunctionTest extends SnowTestsuite {
	public function testReplace() {
		$this->runAndCompare('echo [1, 2, 3]->replace(2, 4)->json_encode()
echo "test"->replace /^[a-z]/, \'T\'
echo "test"->replace \'t\', \'T\'', '[1,4,3]TestTesT');
	}

	public function testIn() {
		$this->runAndCompare('if 3->in([1, 2, 3]) is true
	echo "3 is in.\n"

if 4->in([1, 2, 3]) is true
	echo "4 is in.\n"	

if "e"->in("test") is true
	echo "e is in test.\n"

if "x"->in("test") is true
	echo "x is in test.\n"

if 3->in(10) is true
	echo "3 is in 10.\n"

if 11->in(10) is true
	echo "11 is in 10.\n"', '3 is in.
e is in test.
3 is in 10.
');
	}

	public function testTemplate() {
		$this->runAndCompare('echo template \'<p>Hallo, {{name}} {{test}}</p>
<ul>
	{{#users}}
		<li>{{.}}</li>
	{{/users}}
	{{^users}}<li>No users here!</li>{{/users}}
	{{^testers}}
		<li>No testers here.</li>
	{{/testers}}
</ul>
{{>test}}\', ["name": \'Max\', "users": [\'Alfred\', \'Michel\', \'Inga\'], "test": fn <- 3]'
		, '<p>Hallo, Max 3</p>
<ul>
	
		<li>Alfred</li>
	
		<li>Michel</li>
	
		<li>Inga</li>
	
	
	
		<li>No testers here.</li>
	
</ul>
<h1>Max</h1>
');
	}

	public function testTemplateObjects() {
		$this->runAndCompare('
class NixDa
	public name = "Max"
	public fn test <- 3
	public fn users <- [\'Alfred\', \'Michel\', \'Inga\']

nixDa = new NixDa()
echo template \'<p>Hallo, {{name}} {{test}}</p>
<ul>
	{{#users}}
		<li>{{.}}</li>
	{{/users}}
	{{^users}}<li>No users here!</li>{{/users}}
	{{^testers}}
		<li>No testers here.</li>
	{{/testers}}
</ul>\', nixDa', '<p>Hallo, Max 3</p>
<ul>
	
		<li>Alfred</li>
	
		<li>Michel</li>
	
		<li>Inga</li>
	
	
	
		<li>No testers here.</li>
	
</ul>');
	}
}