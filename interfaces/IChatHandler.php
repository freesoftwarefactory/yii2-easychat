<?php
namespace freesoftwarefactory\yii2easychat\interfaces;

interface IChatHandler
{
    /**
        returns the Messages list (from $fromMessageId)
        
        @param $conversation_id string the Conversation identitificator.
        @param $identity string the ID of the person who is seeing the conversation
        
        @return models\Message[] 
    */
    public function yii2easychat_messages($conversation_id, $identity, $fromMessageId);
   
    /**
        creates a new Message owned by $identity and push it into the conversation queue.

        @param $conversation_id string the Conversation identitificator.
        @param $identity string the ID of the person who is seeing the conversation
        @param $messageContents string serialized information of: ['type'=>'text|picture|audio', 'data'=>'...']
        
        @return models\Message;
    */
    public function yii2easychat_new_message($conversation_id, $identity, $messageContents);
    
    /**
    

        @param $conversation_id string the Conversation identitificator.
        @param $identity string the ID of the person who is seeing the conversation
        @param $mode integer 1=(default) me only, 2=clear for everybody 
    */
    public function yii2easychat_clear_messages($conversation_id, $identity, $mode);
}
