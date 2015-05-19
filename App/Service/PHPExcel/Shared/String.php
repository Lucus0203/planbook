<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2009 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.0, 2009-08-10
 */


/**
 * PHPExcel_Shared_String
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_String
{
	/**
	 * Control characters array
	 *
	 * @var string[]
	 */
	private static $_controlCharacters = array();

	/**
	 * Is mbstring extension avalable?
	 *
	 * @var boolean
	 */
	private static $_isMbstringEnabled;

	/**
	 * Is iconv extension avalable?
	 *
	 * @var boolean
	 */
	private static $_isIconvEnabled;

	/**
	 * Build control characters array
	 */
	private static function _buildControlCharacters() {
		for ($i = 0; $i <= 19; ++$i) {
			if ($i != 9 && $i != 10 && $i != 13) {
				$find = '_x' . sprintf('%04s' , strtoupper(dechex($i))) . '_';
				$replace = chr($i);
				self::$_controlCharacters[$find] = $replace;
			}
		}
	}

	/**
	 * Get whether mbstring extension is available
	 *
	 * @return boolean
	 */
	public static function getIsMbstringEnabled()
	{
		if (isset(self::$_isMbstringEnabled)) {
			return self::$_isMbstringEnabled;
		}

		self::$_isMbstringEnabled = function_exists('mb_convert_encoding') ?
			true : false;

		return self::$_isMbstringEnabled;
	}

	/**
	 * Get whether iconv extension is available
	 *
	 * @return boolean
	 */
	public static function getIsIconvEnabled()
	{
		if (isset(self::$_isIconvEnabled)) {
			return self::$_isIconvEnabled;
		}

		self::$_isIconvEnabled = function_exists('iconv') ?
			true : false;

		return self::$_isIconvEnabled;
	}

	/**
	 * Convert from OpenXML escaped control character to PHP control character
	 *
	 * Excel 2007 team:
	 * ----------------
	 * That's correct, control characters are stored directly in the shared-strings table.
	 * We do encode characters that cannot be represented in XML using the following escape sequence:
	 * _xHHHH_ where H represents a hexadecimal character in the character's value...
	 * So you could end up with something like _x0008_ in a string (either in a cell value (<v>)
	 * element or in the shared string <t> element.
	 *
	 * @param 	string	$value	Value to unescape
	 * @return 	string
	 */
	public static function ControlCharacterOOXML2PHP($value = '') {
		if(empty(self::$_controlCharacters)) {
			self::_buildControlCharacters();
		}

		return str_replace( array_keys(self::$_controlCharacters), array_values(self::$_controlCharacters), $value );
	}

	/**
	 * Convert from PHP control character to OpenXML escaped control character
	 *
	 * Excel 2007 team:
	 * ----------------
	 * That's correct, control characters are stored directly in the shared-strings table.
	 * We do encode characters that cannot be represented in XML using the following escape sequence:
	 * _xHHHH_ where H represents a hexadecimal character in the character's value...
	 * So you could end up with something like _x0008_ in a string (either in a cell value (<v>)
	 * element or in the shared string <t> element.
	 *
	 * @param 	string	$value	Value to escape
	 * @return 	string
	 */
	public static function ControlCharacterPHP2OOXML($value = '') {
		if(empty(self::$_controlCharacters)) {
			self::_buildControlCharacters();
		}

		return str_replace( array_values(self::$_controlCharacters), array_keys(self::$_controlCharacters), $value );
	}

	/**
	 * Try to sanitize UTF8, stripping invalid byte sequences. Not perfect. Does not surrogate characters.
	 *
	 * @param string $value
	 * @return string
	 */
	public static function SanitizeUTF8($value)
	{
		if (self::getIsIconvEnabled()) {
			$value = @iconv('UTF-8', 'UTF-8', $value);
			return $value;
		}

		if (self::getIsMbstringEnabled()) {
			$value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
			return $value;
		}

		// else, no conversion
		return $value;
	}
	
	/**
	 * Check if a string contains UTF8 data
	 *
	 * @param string $value
	 * @return boolean
	 */
	public static function IsUTF8($value = '') {
		return utf8_encode(utf8_decode($value)) === $value;
	}

	/**
	 * Formats a numeric value as a string for output in various output writers forcing
	 * point as decimal separator in case locale is other than English.
	 *
	 * @param mixed $value
	 * @return string
	 */
	public static function FormatNumber($value) {
		if (is_float($value)) {
			return str_replace(',', '.', $value);
		}
		return (string) $value;
	}

	/**
	 * Converts a UTF-8 string into BIFF8 Unicode string data (8-bit string length)
	 * Writes the string using uncompressed notation, no rich text, no Asian phonetics
	 * If mbstring extension is not available, ASCII is assumed, and compressed notation is used
	 * although this will give wrong results for non-ASCII strings
	 * see OpenOffice.org's Documentation of the Microsoft Excel File Format, sect. 2.5.3
	 *
	 * @param string $value UTF-8 encoded string
	 * @return string
	 */
	public static function UTF8toBIFF8UnicodeShort($value)
	{
		// character count
		$ln = self::CountCharacters($value, 'UTF-8');

		// option flags
		$opt = (self::getIsIconvEnabled() || self::getIsMbstringEnabled()) ? 
			0x0001 : 0x0000;

		// characters
		$chars = self::ConvertEncoding($value, 'UTF-16LE', 'UTF-8');

		$data = pack('CC', $ln, $opt) . $chars;
		return $data;
	}

	/**
	 * Converts a UTF-8 string into BIFF8 Unicode string data (16-bit string length)
	 * Writes the string using uncompressed notation, no rich text, no Asian phonetics
	 * If mbstring extension is not available, ASCII is assumed, and compressed notation is used
	 * although this will give wrong results for non-ASCII strings
	 * see OpenOffice.org's Documentation of the Microsoft Excel File Format, sect. 2.5.3
	 *
	 * @param string $value UTF-8 encoded string
	 * @return string
	 */
	public static function UTF8toBIFF8UnicodeLong($value)
	{
		// character count
		$ln = self::CountCharacters($value, 'UTF-8');

		// option flags
		$opt = (self::getIsIconvEnabled() || self::getIsMbstringEnabled()) ? 
			0x0001 : 0x0000;

		// characters
		$chars = self::ConvertEncoding($value, 'UTF-16LE', 'UTF-8');

		$data = pack('vC', $ln, $opt) . $chars;
		return $data;
	}

	/**
	 * Convert string from one encoding to another. First try mbstring, then iconv, or no convertion
	 *
	 * @param string $value
	 * @param string $to Encoding to convert to, e.g. 'UTF-8'
	 * @param string $from Encoding to convert from, e.g. 'UTF-16LE'
	 * @return string
	 */
	public static function ConvertEncoding($value, $to, $from)
	{		

		if (self::getIsMbstringEnabled()) {
			$value = mb_convert_encoding($value, $to, $from);
			return $value;
		}
		
		if (self::getIsIconvEnabled()) {
			$value = iconv($from, $to, $value);
			return $value;
		}
		
		if($from == 'UTF-16LE'){
			return self::utf16_decode($value, false);
		}else if($from == 'UTF-16BE'){
			return self::utf16_decode($value);
		}
		// else, no conversion
		return $value;
	}
	
	/**
	 * Decode UTF-16 encoded strings.
	 *
	 * Can handle both BOM'ed data and un-BOM'ed data.
	 * Assumes Big-Endian byte order if no BOM is available.
	 * This function was taken from http://php.net/manual/en/function.utf8-decode.php
	 * and $bom_be parameter added.
	 *
	 * @param   string  $str  UTF-16 encoded data to decode.
	 * @return  string  UTF-8 / ISO encoded data.
	 * @access  public
	 * @version 0.2 / 2010-05-13
	 * @author  Rasmus Andersson {@link http://rasmusandersson.se/}
	 * @author vadik56
	 */
	public static function utf16_decode( $str, $bom_be=true ) {
		if( strlen($str) < 2 ) return $str;
		$c0 = ord($str{0});
		$c1 = ord($str{1});
		if( $c0 == 0xfe && $c1 == 0xff ) { $str = substr($str,2); }
		elseif( $c0 == 0xff && $c1 == 0xfe ) { $str = substr($str,2); $bom_be = false; }
		$len = strlen($str);
		$newstr = '';
		for($i=0;$i<$len;$i+=2) {
			if( $bom_be ) { $val = ord($str{$i})   << 4; $val += ord($str{$i+1}); }
			else {        $val = ord($str{$i+1}) << 4; $val += ord($str{$i}); }
			$newstr .= ($val == 0x228) ? "\n" : chr($val);
		}
		return $newstr;
	}
	
	/**
	 * Get character count. First try mbstring, then iconv, finally strlen
	 *
	 * @param string $value
	 * @param string $enc Encoding
	 * @return int Character count
	 */
	public static function CountCharacters($value, $enc = 'UTF-8')
	{
		if (self::getIsIconvEnabled()) {
			$count = iconv_strlen($value, $enc);
			return $count;
		}

		if (self::getIsMbstringEnabled()) {
			$count = mb_strlen($value, $enc);
			return $count;
		}

		// else strlen
		$count = strlen($value);
		return $count;
	}

	/**
	 * Get a substring of a UTF-8 encoded string
	 *
	 * @param string $pValue UTF-8 encoded string
	 * @param int $start Start offset
	 * @param int $length Maximum number of characters in substring
	 * @return string
	 */
	public static function Substring($pValue = '', $pStart = 0, $pLength = 0)
	{
		if (self::getIsIconvEnabled()) {
			$string = iconv_substr($pValue, $pStart, $pLength, 'UTF-8');
			return $string;
		}

		if (self::getIsMbstringEnabled()) {
			$string = mb_substr($pValue, $pStart, $pLength, 'UTF-8');
			return $string;
		}

		// else substr
		$string = substr($pValue, $pStart, $pLength);
		return $string;
	}

}
