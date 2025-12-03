<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $apples frontend\models\Apple[] */

$this->title = 'Яблоки';
?>
<div class="apple-index container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?= Html::encode($this->title) ?></h1>

        <div>
            <?php $form = ActiveForm::begin(['action' => Url::to(['create-random']), 'method' => 'post', 'options' => ['class' => 'd-flex']]); ?>
            <?= Html::input('number', 'n', 5, ['class' => 'form-control me-2', 'min' => 1, 'max' => 100, 'style' => 'width:100px']) ?>
            <?= Html::submitButton('Сгенерировать', ['class' => 'btn btn-primary']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Flash -->
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= $type === 'error' ? 'danger' : 'success' ?>"><?= $message ?></div>
    <?php endforeach; ?>

    <div class="row g-3">
        <?php if (empty($apples)): ?>
            <div class="col-12"><div class="alert alert-info">Яблок пока нет. Сгенерируйте.</div></div>
        <?php endif; ?>

        <?php foreach ($apples as $apple): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Яблоко #<?= Html::encode($apple->id) ?></h5>
                        <p class="mb-1">Цвет: <strong><?= Html::encode($apple->color) ?></strong></p>
                        <p class="mb-1">Создано: <?= date('Y-m-d H:i:s', $apple->created_at) ?></p>
                        <p class="mb-1">Статус: <strong><?= Html::encode($apple->status) ?></strong></p>
                        <?php if ($apple->fell_at): ?>
                            <p class="mb-1">Упал: <?= date('Y-m-d H:i:s', $apple->fell_at) ?></p>
                        <?php endif; ?>
                        <p class="mb-2">Съедено: <?= number_format($apple->eaten_percent, 2) ?>% — Размер: <?= number_format($apple->getSize(), 2) ?></p>

                        <div class="d-flex gap-2">
                            <!-- Упасть -->
                            <?= Html::beginForm(['fall'], 'post', ['class' => 'm-0']) .
                            Html::hiddenInput('id', $apple->id) .
                            Html::submitButton('Упасть', [
                                'class' => 'btn btn-sm btn-warning',
                                'disabled' => $apple->status !== 'on_tree',
                            ]) .
                            Html::endForm(); ?>

                            <!-- Съесть -->
                            <?= Html::beginForm(['eat'], 'post', ['class' => 'd-flex align-items-center m-0']) .
                            Html::hiddenInput('id', $apple->id) .
                            Html::input('number', 'percent', 10, ['step' => '0.1', 'min' => '0.1','max'=>'100','class'=>'form-control form-control-sm','style'=>'width:90px','disabled' => ($apple->status === 'on_tree' || $apple->status === 'rotten')]) .
                            Html::submitButton('Съесть %', ['class' => 'btn btn-sm btn-success ms-2', 'disabled' => ($apple->status === 'on_tree' || $apple->status === 'rotten')]) .
                            Html::endForm(); ?>

                            <!-- Удалить -->
                            <?= Html::beginForm(['delete'], 'post', ['class' => 'm-0']) .
                            Html::hiddenInput('id', $apple->id) .
                            Html::submitButton('Удалить', ['class' => 'btn btn-sm btn-danger']) .
                            Html::endForm(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
