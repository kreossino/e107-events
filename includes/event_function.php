<?php
// Datum für mktime formatieren
// function datum_mktime($datum)
// {
	// $stunde = substr($datum,13,2);
	// $minute = substr($datum,16,2);
	// $tag	= substr($datum,0,2);
	// $monat	= substr($datum,3,2);
	// $jahr	= substr($datum,6,4);
	// return mktime($stunde, $minute, 0, $monat, $tag, $jahr);	
// }

?>

<?
class formreload {
    /**
     * In welchem Array werden die Tokens in der Session gespeichert?
     * @var        string
     * @access    private
     */
    var $tokenarray = '__token';
    /**
     * Wie soll das hidden element heißen?
     * @var        string
     * @access    public
     */
    var $tokenname = '__token';

    function get_formtoken() {
        $tok = md5(uniqid("foobarmagic"));
		return sprintf("<input type='hidden' name='%s' value='%s'>",$this->tokenname,htmlspecialchars($tok));
    }

    function easycheck() {
        $tok = $_POST[$this->tokenname];
        if (isset($_SESSION[$this->tokenarray][$tok])) {
            return false;
        } else {
            $_SESSION[$this->tokenarray][$tok] = true;
            return true;
        }
    }
}
?>