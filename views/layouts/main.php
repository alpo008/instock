<?php

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $currentUrl string */
/* @var $user \app\models\User */

use app\widgets\Alert;
use app\widgets\Menu;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);

$currentUrl = Yii::$app->request->url;
$user = Yii::$app->user->identity;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

    <div class="wrapper" id="mainWrapper">

        <nav id="sidebar">
            <div class="sidebar-header">
                <?= Html::a(Yii::t('app', 'Server page'), Yii::$app->params['startPageUrl'], [
                    'class' => 'server-root-link'
                ]) ?>
            </div>

            <?= Menu::widget([
                'options' => ['class' => 'list-unstyled components'],
                'encodeLabels' => false
            ]); ?>
            <?php if (!Yii::$app->user->isGuest) : ?>
                <ul class="list-unstyled">
                    <li>
                        <?= Html::beginForm(['/logout'], 'post') ?>
                        <?= Html::submitButton(
                            (Yii::t('app', 'Logout')) . ' (' . Yii::$app->user->identity->fullName . ')',
                            ['class' => 'btn btn-link logout']
                        ) ?>
                        <?= Html::endForm() ?>
                    </li>
                </ul>
            <?php endif; ?>
        </nav>

        <a href="#" id="sidebarCollapse" class="sidebar-collapse">
            <?= \rmrevin\yii\fontawesome\FAS::icon('bars') ?>
        </a>

        <div class="container of-auto">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                'itemTemplate' => "<li>{link}&nbsp;>&nbsp;</li>\n"
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>

    </div>

<footer class="footer">
    <div class="container">
        <div>&copy; <?= Yii::$app->params['companyName'] . ' ' . date('Y') ?></div>
        <div><?= Yii::powered() ?></div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
