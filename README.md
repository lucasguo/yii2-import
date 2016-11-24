# yii2-import

Yii2-import is an extension for Yii Framework. It helps you transfer file into models. 
Currently support Excel format only. But it's easily to extend to support your file.

## Installation

You can use composer to install this extension by:
- run
```
$ php composer.phar require lucasguo/yii2-import "*"
```
- or add
```
"lucasguo/yii2-import": "*"
```
to your composer.json and then execute 'composer update'.

## Usage

Assume there is a model class
```php
class Post extends Model
{
	const STATUS_NEW = 0;
	const STATUS_APPROVED = 1;
	
	public $title;
	public $status;
	public $content;
	
	public static function getStatusList()
	{
		return [
			self::STATUS_NEW = 'New',
			self::STATUS_APPROVED = 'Approved',
		];
	}
}
```

And the form and view as below.
```php
class ImportForm extends Model
{
	public $file;
	
	public function rules()
	{
		return [
			['file', 'file', 'skipOnEmpty' => false, 'extensions' => 'xlsx'],
		];
	}
}
```

```php
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <?= $form->field($model, 'file')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>
```

User have uploaded one Excel file as below.

| Some Description 1 |          |                  |
| ------------------ | -------- | ---------------- |
| **Some Description 2** |          |                  |
| *Title*            | *Status* | *Content*        |
| Post 1             | New      |        Content 1 |
| Post 2             | Approved |        Content 2 |
|                    | Approved |        Content 3 |
| Post 3             | New      |        Content 4 |

Now you can use below code in the controller to help you get the file into models.
```php
$uploadFile = UploadedFile::getInstance($model, 'file');
$importer = new Importer([
	'file' => $uploadFile,
	'modelClass' => Post::className(),
	'skipRowsCount' => 3, // description lines and header lines should be skipped
	'columnMappings' => [
		[
			'attribute' => 'title',
			'required' => true, // if set this to true, the row that missing this value will be skipped. As in the example line 6 will be skipped
		],
		[
			'attribute' => 'status',
			'translation' => function($orgValue, $rowData) {
				return array_search($orgValue, Post::getStatusList());
			}, // this function help fill the status like '0' instead of 'New'
		]
		'content',
	],
	'validateWhenImport' => true, //if set this attribute to true, importer will help you validate the models and report the validation errors by $importer->validationErrors
]);

try {
	$posts = $importer->import();
} catch (InvalidFileException $e) {
	$model->addError('file', 'Invalid import file.');
}
```

Now you have your models in $posts. The structure of $posts will be
```php
[
	4 => Post{title='Post 1', status=0, content='Content 1'},
	5 => Post{title='Post 2', status=1, content='Content 2'},
	7 => Post{title='Post 3', status=0, content='Content 4'},
]
```

Also, you can check imported rows number by $importer->importRows, which is an array of successful imported rows number.

You can notify user about the import result as below.
```php
Yii::$app->session->setFlash("success", count($importer->getImportRows()) . ' rows had been imported');
foreach ($importer->getValidationErrors() as $lineno => $errors) {
	foreach ($errors as $attribute => $errorMessages) {
		$error = $errorMessages[0];
		break;
	}
	Yii::$app->session->addFlash("error", 'Line ' . $lineno . ' has error: ' . $error);
}
```
