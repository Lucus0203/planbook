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
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.0, 2009-08-10
 */


/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
}

/** PHPExcel */
require_once PHPEXCEL_ROOT . 'PHPExcel.php';

/** PHPExcel_Reader_IReader */
require_once PHPEXCEL_ROOT . 'PHPExcel/Reader/IReader.php';

/** PHPExcel_Worksheet */
require_once PHPEXCEL_ROOT . 'PHPExcel/Worksheet.php';

/** PHPExcel_Cell */
require_once PHPEXCEL_ROOT . 'PHPExcel/Cell.php';

 /** PHPExcel_Reader_DefaultReadFilter */
require_once PHPEXCEL_ROOT . 'PHPExcel/Reader/DefaultReadFilter.php';


/**
 * PHPExcel_Reader_CSV
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_CSV implements PHPExcel_Reader_IReader
{
	/**
	 *	Input encoding
	 *
	 *	@access	private
	 *	@var	string
	 */
	private $_inputEncoding	= 'UTF-8';
	/**
	 * Delimiter
	 *
	 * @var string
	 */
	private $_delimiter;

	/**
	 * Enclosure
	 *
	 * @var string
	 */
	private $_enclosure;

	/**
	 * Line ending
	 *
	 * @var string
	 */
	private $_lineEnding;

	/**
	 * Sheet index to read
	 *
	 * @var int
	 */
	private $_sheetIndex;

	/**
	 * PHPExcel_Reader_IReadFilter instance
	 *
	 * @var PHPExcel_Reader_IReadFilter
	 */
	private $_readFilter = null;
	
	/**
	 *	Load rows contiguously
	 *
	 *	@access	private
	 *	@var	int
	 */
	private $_contiguous	= false;


	/**
	 *	Row counter for loading rows contiguously
	 *
	 *	@access	private
	 *	@var	int
	 */
	private $_contiguousRow	= -1;

	/**
	 * Create a new PHPExcel_Reader_CSV
	 */
	public function __construct() {
		$this->_delimiter 	= ',';
		$this->_enclosure 	= '"';
		$this->_lineEnding 	= PHP_EOL;
		$this->_sheetIndex 	= 0;
		$this->_readFilter 	= new PHPExcel_Reader_DefaultReadFilter();
	}
	
	/**
	 * Can the current PHPExcel_Reader_IReader read the file?
	 *
	 * @param 	string 		$pFileName
	 * @return 	boolean
	 */	
	public function canRead($pFilename) 
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}
		
		// Check if it is a CSV file (using file name)
		return (substr(strtolower($pFilename), -3) == 'csv');
	}

	/**
	 * Loads PHPExcel from file
	 *
	 * @param 	string 		$pFilename
	 * @throws 	Exception
	 */
	public function load($pFilename)
	{
		// Create new PHPExcel
		$objPHPExcel = new PHPExcel();

		// Load into this instance
		return $this->loadIntoExisting($pFilename, $objPHPExcel);
	}

	/**
	 * Read filter
	 *
	 * @return PHPExcel_Reader_IReadFilter
	 */
	public function getReadFilter() {
		return $this->_readFilter;
	}

	/**
	 * Set read filter
	 *
	 * @param PHPExcel_Reader_IReadFilter $pValue
	 */
	public function setReadFilter(PHPExcel_Reader_IReadFilter $pValue) {
		$this->_readFilter = $pValue;
	}
	
	/**
	 *	Set input encoding
	 *
	 *	@access	public
	 *	@param string $pValue Input encoding
	 */
	public function setInputEncoding($pValue = 'UTF-8')
	{
		$this->_inputEncoding = $pValue;
		return $this;
	}	//	function setInputEncoding()

	/**
	 *	Get input encoding
	 *
	 *	@access	public
	 *	@return string
	 */
	public function getInputEncoding()
	{
		return $this->_inputEncoding;
	}	//	function getInputEncoding()

	/**
	 * Loads PHPExcel from file into PHPExcel instance
	 *
	 * @param 	string 		$pFilename
	 * @param	PHPExcel	$objPHPExcel
	 * @throws 	Exception
	 */
	public function loadIntoExisting($pFilename, PHPExcel $objPHPExcel)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}
		

		// Create new PHPExcel
		while ($objPHPExcel->getSheetCount() <= $this->_sheetIndex) {
			$objPHPExcel->createSheet();
		}
		$objPHPExcel->setActiveSheetIndex( $this->_sheetIndex );

		// Open file
		$fileHandle = fopen($pFilename, 'r');
		if ($fileHandle === false) {
			throw new Exception("Could not open file $pFilename for reading.");
		}
	
		// Skip BOM, if any
		switch ($this->_inputEncoding) {
			case 'UTF-8':
				fgets($fileHandle, 4) == "\xEF\xBB\xBF" ?
					fseek($fileHandle, 3) : fseek($fileHandle, 0);
				break;
			case 'UTF-16LE':
				fgets($fileHandle, 3) == "\xFF\xFE" ?
					fseek($fileHandle, 2) : fseek($fileHandle, 0);
				break;
			case 'UTF-16BE':
				fgets($fileHandle, 3) == "\xFE\xFF" ?
					fseek($fileHandle, 2) : fseek($fileHandle, 0);
				break;
			case 'UTF-32LE':
				fgets($fileHandle, 5) == "\xFF\xFE\x00\x00" ?
					fseek($fileHandle, 4) : fseek($fileHandle, 0);
				break;
			case 'UTF-32BE':
				fgets($fileHandle, 5) == "\x00\x00\xFE\xFF" ?
					fseek($fileHandle, 4) : fseek($fileHandle, 0);
				break;
			default:
				break;
		}
		$escapeEnclosures = array( "\\" . $this->_enclosure,
								   $this->_enclosure . $this->_enclosure
								 );
		// Set our starting row based on whether we're in contiguous mode or not					 
		$currentRow = 1;
		if ($this->_contiguous) {
			$currentRow = ($this->_contiguousRow == -1) ? $objPHPExcel->getActiveSheet()->getHighestRow(): $this->_contiguousRow;
		}
		// Loop through each line of the file in turn
		//while (($rowData = fgetcsv($fileHandle, 0, $this->_delimiter, $this->_enclosure)) !== FALSE) {
		while (($rowData = $this->getcsvdata($fileHandle, 0, $this->_delimiter, $this->_enclosure)) !== FALSE) {

			$columnLetter = 'A';
			
			foreach($rowData as $rowDatum) {
				
				if ($rowDatum != '' && $this->_readFilter->readCell($columnLetter, $currentRow)) {
					// Unescape enclosures
				
					$rowDatum = str_replace($escapeEnclosures, $this->_enclosure, $rowDatum);

					// Convert encoding if necessary
					if ($this->_inputEncoding !== 'UTF-8') {
						$rowDatum = PHPExcel_Shared_String::ConvertEncoding($rowDatum, 'UTF-8', $this->_inputEncoding);
					}
					// Set cell value
					$objPHPExcel->getActiveSheet()->getCell($columnLetter . $currentRow)->setValue($rowDatum);
				}
				
				++$columnLetter;
			}
	
			++$currentRow;
		}
		

		// Close file
		fclose($fileHandle);

		if ($this->_contiguous) {
			$this->_contiguousRow = $currentRow;
		}

		// Return
		return $objPHPExcel;
	}	//	function loadIntoExisting()

	/**
	 * Get delimiter
	 *
	 * @return string
	 */
	public function getDelimiter() {
		return $this->_delimiter;
	}

	/**
	 * Set delimiter
	 *
	 * @param	string	$pValue		Delimiter, defaults to ,
	 * @return PHPExcel_Reader_CSV
	 */
	public function setDelimiter($pValue = ',') {
		$this->_delimiter = $pValue;
		return $this;
	}

	/**
	 * Get enclosure
	 *
	 * @return string
	 */
	public function getEnclosure() {
		return $this->_enclosure;
	}

	/**
	 * Set enclosure
	 *
	 * @param	string	$pValue		Enclosure, defaults to "
	 * @return PHPExcel_Reader_CSV
	 */
	public function setEnclosure($pValue = '"') {
		if ($pValue == '') {
			$pValue = '"';
		}
		$this->_enclosure = $pValue;
		return $this;
	}

	/**
	 * Get line ending
	 *
	 * @return string
	 */
	public function getLineEnding() {
		return $this->_lineEnding;
	}

	/**
	 * Set line ending
	 *
	 * @param	string	$pValue		Line ending, defaults to OS line ending (PHP_EOL)
	 * @return PHPExcel_Reader_CSV
	 */
	public function setLineEnding($pValue = PHP_EOL) {
		$this->_lineEnding = $pValue;
		return $this;
	}

	/**
	 * Get sheet index
	 *
	 * @return int
	 */
	public function getSheetIndex() {
		return $this->_sheetIndex;
	}

	/**
	 * Set sheet index
	 *
	 * @param	int		$pValue		Sheet index
	 * @return PHPExcel_Reader_CSV
	 */
	public function setSheetIndex($pValue = 0) {
		$this->_sheetIndex = $pValue;
		return $this;
	}
	
	/**
	 *	Set Contiguous
	 *
	 *	@access	public
	 *	@param string $pValue Input encoding
	 */
	public function setContiguous($contiguous = false)
	{
		$this->_contiguous = (bool)$contiguous;
		if (!$contiguous) {
			$this->_contiguousRow	= -1;
		}

		return $this;
	}	//	function setInputEncoding()

	/**
	 *	Get Contiguous
	 *
	 *	@access	public
	 *	@return boolean
	 */
	public function getContiguous() {
		return $this->_contiguous;
	}	//	function getSheetIndex()
	
	function getcsvdata(& $handle, $length = null, $d = ',', $e = '"') {
	     $d = preg_quote($d);
	     $e = preg_quote($e);
	     $_line = "";
	     $eof=false;
	     while ($eof != true) {
	         $_line .= (empty ($length) ? fgets($handle) : fgets($handle, $length));
	         $itemcnt = preg_match_all('/' . $e . '/', $_line, $dummy);
	         if ($itemcnt % 2 == 0)
	             $eof = true;
	     }
	     $_csv_line = preg_replace('/(?: |[ ])?$/', $d, trim($_line));
	     $_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
	     preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
	     $_csv_data = $_csv_matches[1];
	     for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
	         $_csv_data[$_csv_i] = preg_replace('/^' . $e . '(.*)' . $e . '$/s', '$1' , $_csv_data[$_csv_i]);
	         $_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
	     }
	     return empty ($_line) ? false : $_csv_data;
	}
}
