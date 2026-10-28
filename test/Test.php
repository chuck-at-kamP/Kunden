<?php
class Test {

	private $array = []; // ["constructorTest" => ["beg-message","end-message"], "getTest"=> [...] ]
	private $index = []; // [ 0 => "constructorTest", 1 => "getTest" ]

	public function __construct($message) {
		Logger::info("=============== Testclass $message ==================");
	}   

	public function add($test,$message,$dat="") {	// add - Add a test to the output queue
		if(isset($dat) and $dat !== "") { 
			$string = json_encode($dat);
			$tmp = "\n  " . $string; 
		} else { $tmp = ""; }
        $beg = date('Y-m-d H:i:s') . " BEG - " . $message . $tmp;
		$this->array[$test] = [$beg];
		array_push($this->index,$test);
		Logger::info("----------------- Test $test ----------------");
	}
	public function bet($test,$message,$dat="") {		// bet (between) - Prints some data between add/fin.
		if(isset($dat) and $dat !== "") { 
			$string = json_encode($dat);
			$tmp = "\n  " . $string; 
		} else { $tmp = ""; }
        $msg = date('Y-m-d H:i:s') . "     - " . $message . $tmp;
		array_push($this->array[$test], $msg);
	}
	public function fin($test,$message,$dat="") {	// fin - Finishs the output queue.
		if(isset($dat) and $dat !== "") { 
			$string = json_encode($dat);
			# $string = implode(" ", $dat);
			$tmp = "\n  " . $string; 
		} else { $tmp = ""; }
        $end = date('Y-m-d H:i:s') . " END - " . $message . $tmp;
		array_push($this->array[$test], $end);
	}

	// --------------------- Static BEG/END Test output  -------------------
    public static function beg($message) {
		print "\n===============================================\n";
        print date('Y-m-d H:i:s') . " BEG - " . $message . "\n";
    }
    public static function end($message,$dat='') {
        print date('Y-m-d H:i:s') . " END - " . $message . "\n";
		if(isset($dat) and $dat !== "") { print "\t" . $dat . "\n"; }
		print "===============================================\n";
    }

	// --------------------- Output -------------------
	public function out($test) {
		foreach($this->array[$test] as $t) { print $t . "\n"; }

		# print $this->array[$test][0] . "\n";
		# if(isset($this->array[$test][1])) {
		# 	print $this->array[$test][1] . "\n";
		# }
	}
	public function outAll() {
		print "\n===============================================\n";
		print "Test Output:\n";
		print "------------\n";
		foreach($this->index as $i) {
			print "Test $i\n";
			$this->out($i);
			print "-----------------------------------------------\n";
		}
		print "===============================================\n";
	}
}
