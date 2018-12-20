var Yii2EasyChat = function(options) {
	
    var widget = null;
    var youSelector = null;
    var logSelector = null;
    var postListSelector = null; 
    var sendButton = null;
    var clearButton = null;
    var msgTextArea = null;

	var clear = function() {
		sendButton.attr('disabled',null);
		msgTextArea.attr('disabled',null);
	};

	var busy = function(){
		sendButton.attr('disabled','disabled');
		msgTextArea.attr('disabled','disabled');
	};

	var actionPost = function(text, callback) {
		
        busy();
		
        jQuery.ajax({ cache: false, type: 'post', 
			url: options.action,
			data: { operation : "message", message: { type : 'text' , data : text } },
			success: function(resp){
				if(resp == null || resp == "")
                {
				    onError('post_empty','');
				}
                else	
				if(!resp.result) 
                {
				    onError('post_rejected',text);
					callback(false);
				}
                else
                {
					add(resp.payload);
					callback(true);
				    onSuccess('post',text, resp.payload);
				}
				clear();
			},
			error: function(e){
				clear();
				onError('send_post_error',e.responseText,e);
				callback(false);
			}
		});
	};

	var actionInit = function(callback) {
		busy();
		jQuery.ajax({ cache: false, type: 'post', 
			url: options.action,
			data: { operation : 'init' , last_id : 0 },
			success: function(resp){
				if(resp == null || resp == "")
                {
					onError('init_empty','');
				}
                else	
				if(!resp.result)
                {
					onError('init_rejected','');
				}
                else
                {
					jQuery.each(resp.payload, function(k,post){
						add(post);
					});	
					onSuccess('init','');
					callback();
				}
				clear();
			},
			error: function(e){
				clear();
				onError('init_error',e.responseText,e);
			}
		});
	};

	var actionTimer = function(){
	

        var last_id = '';
		var lastPost = postListSelector.find('.post:not('+options.myOwnPostCssStyle+'):last');
		var data = lastPost.data('post'); //data setted in add method
		
        if(data != null)
			last_id = data.id;
        
		jQuery.ajax({ cache: false, type: 'post', 
			url: options.action,
			data: { operation : 'refresh', last_id: last_id },
			success: function(resp){
				if(resp == null || resp == "")
                {
					onError('timer_empty','');
				}
                else	
				if(!resp.result)
                {
					onError('timer_rejected','');
				}
                else
                {
					var hasPosts=false;
					jQuery.each(resp.payload, function(k,post){
						add(post);
						hasPosts=true;
					});	
					onSuccess('timer','');
					if(hasPosts==true)
						scroll();
				}
			},
			error: function(e){
				onError('timer_error',e.responseText,e);
			}
		});
	};

	var add = function(message) {

		var p = postListSelector.append("<div id='"+message.id+"' class='post'>"
			+"<div class='post-inner'>"
                +"<div class='track'></div>"
			    +"<pre class='text'></pre>"
            +"</div>"
		+"</div>")
            .find(".post[id='"+message.id+"']");
		
        p.data('post',message);
		
        if(options.identity == message.identity)
		{ 
            p.addClass(options.myOwnPostCssStyle); 
        }
        else
        {
			p.addClass(options.othersPostCssStyle);
		}

		p.find('.track')
         .html("<div class='owner'>"+message.friendlyOwnerName+"</div>"
			+"<div class='time'>"+message.friendlyTimestamp+"</div>");

        p.addClass(message.messageStatus);

        if('text' == message.type)
        {
		    p.find('.text').html(message.data);
        }
	};

    var clearMessages = function(){
		
        jQuery.ajax({ cache: false, type: 'post', 
			url: options.action,
			data: { operation : 'clear' , mode : 1 },
			success: function(resp){
				if(resp == null || resp == "")
                {
					onError('clear_error','');
				}
                else	
				if(!resp.result)
                {
					onError('clear_rejected','');
				}
                else
                {
                    postListSelector.find('.post').remove();
					onSuccess('clear','');
				}
			},
			error: function(e){
				onError('clear_error',e.responseText,e);
			}
		});
        
    };

	var scroll = function(){
		var h=0;
		postListSelector.find('.post').each(function(k){
			h += $(this).outerHeight();
		});
		postListSelector.scrollTop(h);
	};

    var launchTimer = function(){
        setTimeout(function(){
            try{
                actionTimer();
            }catch(e){}
            launchTimer();		
        },options.timerMs);	
    };

    var onError = function(a,b)
    {
        console.log('onError', a,b);
    };

    var onSuccess = function(a,b)
    {
        console.log('onSuccess',a,b);
    };
    
    this.run = function() {

        console.log('yii2_easychat initializing...', options);

        widget = $(options.widgetSelector);
        
        postListSelector    = widget.find('.posts');
        youSelector         = widget.find('.you');
        logSelector         = widget.find('.log');
        
        sendButton  = $(options.sendSelector);
        clearButton  = $(options.clearSelector);
        msgTextArea = widget.find('textarea');
        
        sendButton.click(function(e){

            var text = jQuery.trim(msgTextArea.val());
            
            if(text.length < options.minPostLen)
            {
                onError('very_short_text',text);
            }
            else
            if(text.length > options.maxPostLen)
            {
                onError('very_large_text',text);
            }
            else
            actionPost(text, function(ok){
                if(ok==true){
                    msgTextArea.val("");
                    scroll();
                    setTimeout(function(){ msgTextArea.focus(); },100);
                }
            });
        });
        
        clearButton.click(function(e){
            if(!confirm(options.clearPrompt))
                return;

            clearMessages();
        });
        
        msgTextArea.keyup(function(e){

            var text = jQuery.trim(msgTextArea.val());
            
            if(text.length > options.maxPostLen)
            {
                msgTextArea.css({ color: 'red' });
                msgTextArea.parent().find(".exceded").text("size exceded");
            }else{
                msgTextArea.css({ color: 'black' });
                msgTextArea.parent().find(".exceded").text("");
            }
        });
        
        actionInit(scroll);

        launchTimer();
        
        console.log('yii2_easychat initialized.');
    };//run

} //end
