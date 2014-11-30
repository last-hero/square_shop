<?php
#
#
# SSException
# https://github.com/last-hero/square_shop
#
# (c) Gobi Selva
# http://www.square.ch
#
# Dieser Klasse dient als personalisierte Exception Handling
#

class SSException extends Exception{
	public function __toString() {
    	return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}
?>