<?php
namespace freesoftwarefactory\yii2easychat\assets;

use yii\web\AssetBundle;

class ChatAsset extends AssetBundle
{
    public $sourcePath = '@app/vendor/freesoftwarefactory/yii2-easychat/assets/chat';
    
    public $css = 
    [
        'module.css',
    ];
    
    public $js = [
        'module.js'
    ];

    public $depends = 
    [
        'yii\web\JqueryAsset',
    ];
}
