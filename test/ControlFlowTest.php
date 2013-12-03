<?php
require_once dirname(__FILE__) . "/SnowTestsuite.php";

class ControlFlowTest extends SnowTestsuite {
	public function testControl() {
		$this->compare('break
continue
<-
<- 2', 'break;
continue;
return ;
;
return 2;
;
null;');
	}
}
?>
