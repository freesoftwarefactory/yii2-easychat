<?php
namespace freesoftwarefactory\yii2easychat\actions;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
    every derivated class must implements IChatHandler interface
*/
abstract class ChatAction extends \yii\base\Action 
{
    public function init()
    {
        parent::init();
        
        if(!Yii::$app->request->isAjax) die('invalid ajax request');
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
        $this->controller->enableCsrfValidation = false;
    }

    public function run($conversation_id, $identity)
    {
        $operation  = Yii::$app->request->post('operation');
        $lastId     = Yii::$app->request->post('last_id');
    
        $result = false; $payload = null;

        if('init' == $operation)
        {
            $result = true; 
            $payload = $this->processList($this->yii2easychat_messages($conversation_id, $identity, null));
        }
        elseif('refresh' == $operation)
        {
            $result = true;
            $payload = $this->processList($this->yii2easychat_messages($conversation_id, $identity, $lastId));
        }
        elseif('message' == $operation)
        {
            $message = Yii::$app->request->post('message');

            $result = true;
            $payload = $this->processMessage($this->yii2easychat_new_message($conversation_id, $identity, $message));
        }
        elseif('clear' == $operation)
        {
            $mode = intval(Yii::$app->request->post('mode'));
            $result = true;
            $payload = $this->yii2easychat_clear_messages($conversation_id, $identity, $mode);
        }
        else
        {
            sleep(3);
        }

        return 
        [
            'result'    => $result, 
            'payload'   => $payload, 
        ];
    }

    private function processList($list)
    {
        $list2 = [];

        foreach($list as $m)
        {
            $list2[] = self::processMessage($m);
        }

        return $list2;
    }

    private function processMessage($m)
    {
        $item = [];

        foreach($m->attributes as $attr=>$val)
            $item[$attr] = $val;

        foreach(['friendlyTimestamp', 'friendlyOwnerName', 'messageStatus'] as $attr)
            $item[$attr] = $m->$attr;
   
        return $item;
    }
 }
