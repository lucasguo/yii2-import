<?php
namespace lucasguo\import\generators;

use lucasguo\import\components\Importer;
use lucasguo\import\consumers\ConsumerInterface;

interface GeneratorInterface
{
	/**
	 * The generator that process the data created by consumer and transfer them into the models
	 * the result this function returned should be
	 * [
	 * 	1 => $model1
	 * 	2 => $model2
	 * ]
	 * the index is the source input file line number
	 * @param array $data data generated by consumer(@see ConsumerInterface)
	 * @param Importer $import the importer which call this generator
	 */
	public function generate(&$data, &$import);
}