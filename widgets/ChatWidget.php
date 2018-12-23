<?php
namespace freesoftwarefactory\yii2easychat\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use freesoftwarefactory\yii2easychat\assets\ChatAsset;

class ChatWidget extends \yii\base\Widget 
{
    public  $id;  // the widget id 

	public  $controllerName='/site';
    public  $defaultActionName='yii2easychat';

	public	$conversation_id;	// string/number
	public	$identity;	        // string, ID, can be: Yii::app()->user->id
	
    public	$minPostLen=2;
	public	$maxPostLen=140;
	public  $timerMs = 5000;
	
    public  $clearPromptText="You're about to clear your messages.";
    public  $messagePromptText="Type your message here";

	public	$myOwnPostCssStyle='you-post';
	public	$othersPostCssStyle='other-post';

    public $className='';
    public $sendSelector='.sendmsg';
    public $clearSelector='.clearmsg';

	private $action;

	public function init()
    {
		parent::init();

        $this->action = 
        [ 
            rtrim($this->controllerName,'/') . '/' . trim($this->defaultActionName, '/'),
            'conversation_id'=>$this->conversation_id,
            'identity'=>$this->identity,
        ];
    }

	public function run()
    {
        ChatAsset::register($this->view);
		
        $assetOptions = json_encode(
            [
                'conversation_id'=>$this->conversation_id,
                'identity'=>$this->identity,
                'action'=>Url::to($this->action, true),
                'widgetSelector'=>'#' . $this->id,
                'minPostLen'=>$this->minPostLen,
                'maxPostLen'=>$this->maxPostLen,
                'timerMs'=>$this->timerMs,
                'myOwnPostCssStyle'=>$this->myOwnPostCssStyle,
                'othersPostCssStyle'=>$this->othersPostCssStyle,
                'clearPrompt'=>$this->clearPromptText,
                'sendSelector'=>$this->sendSelector,
                'clearSelector'=>$this->clearSelector,
		    ]);
        
        $this->view->registerJs("(new Yii2EasyChat($assetOptions)).run();", \yii\web\View::POS_LOAD);

        return strtr($this->getMarkup(), 
            [
                ':id' => $this->id,
                ':Message' => $this->messagePromptText,
            ]);
	}

    private function getMarkup()
    {
        $text  = Html::tag("div", 
            Html::tag('textarea','', ['placeholder'=>':Message']),['class'=>'textinput']);
       
        $send = Html::tag('div', 'Enviar', ['class'=>'sendbtn sendmsg']);

        $exceed = Html::tag('div', '', ['class'=>'exceded']);

        $posts  = Html::tag('div', '', ['class'=>'posts']);
        
        $log    = Html::tag('div', '', ['class'=>'log']);

        $bar = 
        "
            <div class='bar'>
                {$text}
                {$send}
            </div>
        ";

        return Html::tag('div', $posts . $bar . $exceed . $log , 
            [
                'id'    => ':id',
                'class' => 'yii2-easychat '.$this->className,
            ]); 
    }
}
