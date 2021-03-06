<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */

$this->title = '用户管理';
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"></h3>
                <?= Html::a('创建用户', ['create'], ['class' => 'btn btn-success']) ?>
                <div class="box-tools">
                    <div class="input-group input-group-sm" style="width: 150px;">
                        <input type="text" name="table_search" class="form-control pull-right" placeholder="Search">

                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'summary'      => '',
                    'tableOptions' => ['class' => 'table table-hover table-striped table-bordered m-b-0'],
                    'columns'      => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'nickname',
                        'username',
                        'email:email',
                        'status',
                        'created_at:date',
                        'updated_at:date',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{update} {ban}',
                            'buttons' => [
                                'ban' => function ($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('app', 'Ban'),
                                        'aria-label' => Yii::t('app', 'Ban'),
                                        'data-pjax' => '0',
                                    ];
                                    $url = Url::toRoute(['/user/ban', 'id' => (string) $key]);
                                    return Html::a("<span class=\"fa fa-ban\"></span>", $url, $options); 
                                },
                            ],
                        ],
                    ],
                ]);
                ?>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>
