<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Authitem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
	<div class="col-md-4">
		<div class="authitem-form">

		    <?php $form = ActiveForm::begin(); ?>

		    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

		    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

		    <div class="form-group">
		        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
		    </div>



		</div>

	</div>

	<div class="col-md-8">
<?php
$roles=$model->getAllroles();
foreach ($roles as $k => $v):
?>
		<div class="panel panel-default">

		  <div class="panel-heading">
		    <h3 class="panel-title"><?=$k?></h3>
		  </div>

		  <div class="panel-body">
		    <?php
	    	foreach ($v as $item) {
	    		echo Html::checkBox("Items[{$item['name']}]",$item['checked'],['label'=>$item['label']]);
	    		echo "<br/>";
	    	}
		    ?>
		  </div>

		</div>
<?php endforeach; ?>
	</div>
</div>










		    <?php ActiveForm::end(); ?>