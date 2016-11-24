<?php
namespace lucasguo\import\generators;

use lucasguo\import\components\Importer;
use yii\base\Object;
use yii\helpers\ArrayHelper;

class ModelGenerator extends Object implements GeneratorInterface
{
	public function generate(&$data, &$importer)
	{
		$models = [];
		foreach ($data as $lineno => $rowData) {
			$className = $importer->modelClass;
			$model = new $className;
			foreach ($importer->getColumns() as $index => $mapping) {
				if (array_key_exists($index, $rowData)) {
					$fieldName = $mapping->attribute;
					$model->$fieldName = $this->getFieldValue($rowData[$index], $rowData, $mapping);
				}
			}
			$models = ArrayHelper::merge($models, [$lineno => $model]);
		}
		return $models;
	}
	
	/**
	 *
	 * @param string $orgValue origin value from input file
	 * @param DataMapping $mapping
	 */
	protected function getFieldValue($orgValue, $rowData, $mapping)
	{
		if ($mapping->translation !== null) {
			return call_user_func($mapping->translation, $orgValue, $rowData);
		} else {
			return $orgValue;
		}
	}
}