<?php
namespace lucasguo\import\consumers;

use yii\base\Object;
use lucasguo\import\exceptions\InvalidFileException;
use lucasguo\import\components\Importer;

class ExcelConsumer extends Object implements ConsumerInterface
{
	/**
	 * {@inheritDoc}
	 * @see \backend\modules\import\consumers\ConsumerInterface::consume()
	 * @param $importer Importer
	 */
	public function consume(&$importer)
	{
		try {
			$fileType = \PHPExcel_IOFactory::identify($importer->file);
			$objReader = \PHPExcel_IOFactory::createReader($fileType);
			$objPHPExcel = $objReader->load($importer->file);
		} catch (Exception $e) {
			throw new InvalidFileException();
		}
		$sheet = $objPHPExcel->getSheet();
		$highestRow = $sheet->getHighestRow();
		$data = [];
		$highestCol = $this->getNameFromNumber($importer->getMaxColIndex());
		for ($i = $importer->skipRowsCount + 1; $i < $highestRow; $i++) {
			$rowDataArray = $sheet->rangeToArray('A' . $i . ':' . $highestCol . $i);
			$rowData = $rowDataArray[0];
			$skip = false;
			foreach ($importer->getRequiredCols() as $col) {
				if ($rowData[$col] == null) {
					$skip = true;
					break;
				}
			}
			if ($skip) {
				$importer->addSkipRow($i);
			} else {
				$data[$i] = $rowData;
			}
		}
		unset($objPHPExcel);
		unset($objReader);
		return $data;
	}
	
	protected function getNameFromNumber($num) 
	{
	    $numeric = ($num - 1) % 26;
	    $letter = chr(65 + $numeric);
	    $num2 = intval(($num - 1) / 26);
	    if ($num2 > 0) {
	        return getNameFromNumber($num2) . $letter;
	    } else {
	        return $letter;
	    }
	}
	
	
}