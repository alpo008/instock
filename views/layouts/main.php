<?php

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $currentUrl string */
/* @var $user \app\models\User */

use app\widgets\Alert;
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

<!--<div class="wrap">-->

    <div class="wrapper" id="mainWrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <?= Html::a(Yii::t('app', 'Server page'), Yii::$app->params['startPageUrl'], [
                    'class' => 'server-root-link'
                ]) ?>
            </div>

            <ul class="list-unstyled components">
                <?php if (Yii::$app->user->isGuest) : ?>
                    <li class="<?= Url::to(['/login']) === $currentUrl ? 'active' : ''?>">
                        <?= Html::a(Yii::t('app', 'Login'), ['/login'], [
                                'class' => Url::to(['/login']) === $currentUrl ? 'active' : ''
                        ]) ?>
                    </li>
                <?php else: ?>
                    <?php if ($user->role === \app\models\User::ROLE_ADMIN) : ?>
                        <li  class="<?= Url::to(['/']) === $currentUrl ? 'active' : ''?>">
                            <?= Html::a(mb_strtoupper(Yii::t('app', 'Main')), ['/admin']) ?>
                        </li>
                        <li class="<?= strpos($currentUrl, Url::to(['/admin/user'])) !== false ? 'active' : '' ?>">
                            <?= Html::a(Yii::t('app', 'Users'), ['/admin/user']) ?>
                        </li>
                        <li class="<?= strpos($currentUrl, Url::to(['/admin/material'])) !== false ? 'active' : '' ?>">
                            <?= Html::a(Yii::t('app', 'Materials'), ['/admin/material']) ?>
                        </li>
                        <li class="<?= strpos($currentUrl, Url::to(['/admin/stock'])) !== false ? 'active' : '' ?>">
                            <?= Html::a(Yii::t('app', 'Stock places'), ['/admin/stock']) ?>
                        </li>
                        <li class="<?= strpos($currentUrl, Url::to(['/admin/stock-operation'])) !== false ? 'active' : '' ?>">
                            <?= Html::a(Yii::t('app', 'Operations'), ['/admin/stock-operation']) ?>
                        </li>
                    <li>
                        <a href="#settingsSubmenu" data-toggle="collapse" aria-expanded="<?= strpos($currentUrl, Url::to(['/admin/settings'])) !== false ? 'true' : 'false'?>" class="dropdown-toggle">
                            <?= Yii::t('app', 'Settings') ?>
                        </a>
                        <ul class="collapse list-unstyled <?= strpos($currentUrl, Url::to(['/admin/settings'])) !== false ? 'show' : '' ?>"
                            id="settingsSubmenu"
                        >
                            <li class="<?= strpos($currentUrl, Url::to(['/admin/settings/import'])) !== false ? 'active' : '' ?>">
                                <?= Html::a(Yii::t('app', 'Import format'), ['/admin/settings/import']) ?>
                            </li>
                            <li class="<?= strpos($currentUrl, Url::to(['/admin/settings/export'])) !== false ? 'active' : '' ?>">
                                <?= Html::a(Yii::t('app', 'Export format'), ['/admin/settings/export']) ?>
                            </li>
                        </ul>
                    </li>
                    <?php elseif ($user->role === \app\models\User::ROLE_USER) : ?>
                        <li  class="<?= Url::to(['/']) === $currentUrl ? 'active' : ''?>">
                            <?= Html::a(mb_strtoupper(Yii::t('app', 'Main')), Yii::$app->homeUrl) ?>
                        </li>
                    <?php endif; ?>
                    <li>
                        <?= Html::beginForm(['/logout'], 'post') ?>
                        <?= Html::submitButton(
                            (Yii::t('app', 'Logout')) . ' (' . Yii::$app->user->identity->fullName . ')',
                            ['class' => 'btn btn-link logout']
                        ) ?>
                        <?= Html::endForm() ?>
                    </li>
                <?php endif; ?>
            </ul>
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
<!--</div>-->

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
