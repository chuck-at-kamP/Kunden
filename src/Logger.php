<?php
class Logger
{
	# public static $LEVEL = ['info'];
	# public static $LEVEL = ['info','db'];
	public static $LEVEL = ['info','debug','detail','db'];

	# ==================== Messages with different levels: "info" and "debug" ===============
    public static function info($message) {
		if(in_array("info", Logger::$LEVEL)) { Logger::log("INFO  " . $message); }
    }
    public static function debug($message) {
		if(in_array("debug", Logger::$LEVEL)) { Logger::log("DEBUG " . $message); }
    }
    public static function error($message) {
		# Logger::log("ERROR " . $message . Logger::getTrace());
		Logger::log("ERROR " . $message);
    }
	public static function exception($message,$e) {
		Logger::log("EXCEPTION " . $message . ", exception=" . $e->getMessage());
		$stacktrace = $e->getTraceAsString(); 
		if(isset($stacktrace) and $stacktrace !== "") { Logger::log("Stacktrace\n" . $stacktrace); }
	}

	public static function getTrace() {
		$arr = debug_backtrace();
		$t = gettype($arr);
		$message = "\nStacktrace\n";
		if($t === "array") {
			foreach($arr as $a) {
				$message .= "\t" . $a['file'] . ":" . $a['line'] . " - " . 
					$a['class'] . $a['type'] . $a['function'] . "()" . 
					" Args=" . json_encode($a['args'])."\n";
			}
		} else {
			$message .= $arr;
		}
		return $message;
	}
	public static function trace($arr) {
		$t = gettype($arr);
		if($t === "array") {
		foreach($arr as $a) {
			$message = "\t" . $a['file'] . ":" . $a['line'] . " - " . 
				$a['class'] . $a['type'] . $a['function'] . "() Args=" . json_encode($a['args']);
        	file_put_contents(
           		__DIR__ . '/../logs/kunden.log',
            	date('Y-m-d H:i:s') . " - " . $message . "\n",
            	FILE_APPEND
        	);
		}
		} else {
			Logger::debug("Logger::trace() Type '$t' Not an array: " . $arr);
		}
	}
	# ===================== Extra Level "db" ======================
	/* db($message); Prints debug message in level "db"; */
    public static function db($message) {
		if(in_array("db", Logger::$LEVEL)) { Logger::log("DEBUG " . $message); }
    }

	/* db_result($message,$array); prints debug message and array (in php-style) in level "db". */
    public static function db_result($message,$array) {
		if(in_array("db", Logger::$LEVEL)) { Logger::print_array($message,$array); }
    }

	# ===================== Print Data/Array additional to message in level "detail" ==============
	/* debug_array($message,$array,$ok); Prints debug message and array (in json) in level "detail".
		$message - Message to print.
		$array   - Array to print.
		$ok      - default is "false"
			false - prints only in level "detail".
			true  - prints also in level "info".
	*/
    public static function debug_array($message,$array,$ok=false) {
		if(in_array("detail", Logger::$LEVEL) or ($ok and in_array("info", Logger::$LEVEL))) {
			$string = json_encode($array);
			Logger::log("JSON  " . $message . " -- " . $string);
		}
    }

	/* debug_print($message,$array); Prints debug message and array (in php-style) in level "detail".
		$message - Message to print.
		$array   - Array to print.
	*/
    public static function debug_print($message,$array) {
		if(in_array("detail", Logger::$LEVEL)) {
			Logger::print_array($message,$array);
		}
    }

	public static function session($session) {
		Logger::debug_array("SESSION",$session,true);
	}

	# ================== Private log functions ====================
    private static function print_array($message,$array) {
		$string = print_r($array,true);
		Logger::log_no_nl("PRINT " . $message . "\n" . $string);
    }

    private static function log($message) {
        file_put_contents(
            __DIR__ . '/../logs/kunden.log',
            date('Y-m-d H:i:s') . " - " . $message . "\n",
            FILE_APPEND
        );
    }
    private static function log_no_nl($message) { 		# No newline.
        file_put_contents(
            __DIR__ . '/../logs/kunden.log',
            date('Y-m-d H:i:s') . " - " . $message,
            FILE_APPEND
        );
    }

	# ===================== Audit log ====================
	/* Audit log is encrypted and implemented because of DSGVO.
	*/
    public static function audit($message) {
		Logger::log_audit("AUDIT " . $message);
    }
    public static function log_audit($message) {
		Logger::apache("DEBUG log_audit() dir=" . __DIR__ . ", message=" . $message);
		$str = date('Y-m-d H:i:s') . " " . $_SESSION['admin_name'] . " - " . $message . "\n";
		$enc = Logger::encrypt($str);
		file_put_contents( __DIR__ . '/../logs/audit.log', $str . $enc . "\n", FILE_APPEND);
    }

	public static function encrypt($message) {
		$cipher = "aes-128-cbc";
		if (in_array($cipher, openssl_get_cipher_methods())) {
		    $ivlen = openssl_cipher_iv_length($cipher);
		    $iv = openssl_random_pseudo_bytes($ivlen);
		    $ciphertext = openssl_encrypt($message, $cipher, constant('AUDIT_PW'), $options=0, $iv);
		    // store $cipher, $iv, and $tag for decryption later
		    // $original_plaintext = openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv, $tag);
			return $ciphertext;
		}
	}

	# ==================== Log to apache log file =====================
	public static function apache($message) {
		error_log($message);
	}
}

