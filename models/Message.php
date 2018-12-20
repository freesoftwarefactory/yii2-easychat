<?php
namespace freesoftwarefactory\yii2easychat\models;

/**
 represents a single Message

 @property $id string unique hash md5 identificator of this message 
 @property $conversation_id string the conversation who owns this message 
 @property $identity string who owns this message 
 @property $created_at integer the unix timestamp
 @property $type string any of "text|picture|audio"
 @property $data string base64 encoded payload, depends on type
*/
class Message
{
    public $id;
    public $conversation_id;
    public $identity;
    public $created_at;
    public $type;
    public $data;

    // this attributes are used in ChatAsset
    public $friendlyTimestamp;
    public $friendlyOwnerName;
}
