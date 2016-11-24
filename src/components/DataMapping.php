<?php
namespace lucasguo\import\components;

use yii\base\Object;

class DataMapping extends Object
{
	/**
	 * Indicate whether this field is required, if true, this line in input file will be skip and not display after processed by consumer
	 * @var boolean
	 */
	public $required;
	/**
	 * which attribute will be mapped to.
	 * @var string
	 */
	public $attribute;
	/**
	 * an anonymous function or a string that is used to determine the value to transfer into the data.
	 * If set it as a function, its signature should be: `function ($orgValue)`
	 * where $orgValue is input in the input file.
	 * You may need to do some extra work to fit this value to the model.
	 * For example, one column named 'status' in input file is 'Ready', you may need to translate it to '1' into the model
	 * then you can define the function as 
	 * ```php
	 * function($orgValue, $rowData) {
	 * 	if ($orgValue == 'Ready') return 1;
	 * }
	 * ```
	 * $orgValue is the value which display in the origin file.
	 * $rowData is the array contains the data in current row.
	 * @var \Closure
	 */
	public $translation;
	
}