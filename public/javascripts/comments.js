var commentsUrlDelete = "/shops/comments/delete/format/json";
var commentsUrlEdit = "/shops/comments/edit/format/json";
var commentsUrlVote = "/shops/comments/vote/format/json"
var commentsUrlSpamReport = "/shops/comments/report-as-spam/format/json";
var delSpamCommentUrl     = "/shops/comments/spam-extended-view/format/json";
var commentsMessageError = "Произошла ошибка, пожалуйста повторите!";
var commentsMessageEmpty = "Сообщение не должно быть пустым";
function enableVote() {
   jQuery(".vote-links").removeClass('disable');
}

function setCookie (name, value, expires, path, domain, secure) {
      document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}


function reportAsSpam(comment_id)
{
    jQuery.ajax({
        type: "POST",
        url: commentsUrlSpamReport,
        dataType: "json",
        data: "comment_id=" + comment_id,
        success: function(response) {
            if (response['result']['success']) {
                jQuery('#spam-' + comment_id)
                    .after(response['result']['message'])
                    .remove();
            } else {
                alert(response['result']['message']);
            }
            return false;
        }
    });
}


function modifySpamComment(mode, comment_id)
{
    jQuery.ajax({
        type: "POST",
        url: delSpamCommentUrl,
        dataType: "json",
        data: "comment_id=" + comment_id + '&mode=' + mode,
        success: function(response) {
            if (response['result']['success']) {
                jQuery('a[id^=del-comment-' + comment_id + ']').parent().parent().empty();
            } else {
                alert(response['result']['message']);
            }
            return false;
        }
    });
}

function getCookie(name) {
	var cookie = " " + document.cookie;
	var search = " " + name + "=";
	var setStr = null;
	var offset = 0;
	var end = 0;
	if (cookie.length > 0) {
		offset = cookie.indexOf(search);
		if (offset != -1) {
			offset += search.length;
			end = cookie.indexOf(";", offset)
			if (end == -1) {
				end = cookie.length;
			}
			setStr = unescape(cookie.substring(offset, end));
		}
	}
	return(setStr);
}

(function(jQuery){
    jQuery.fn.vote = function(mark) {
        var id = jQuery(this).attr('id');
        id = id.substr(1);
        jQuery.ajax({
                type: "POST",
                url: commentsUrlVote,
                data: "id=" + id + "&mark=" + mark,
                dataType: "json",
                success: function(response) {
                    if (response.result) {
                        jQuery(".vote-links").addClass('disable');
                        var voteScore = jQuery("li#c" + id + ">.koment .vote-score");
                        mark = Math.round(response.content);
                        if (mark >= 0) {
                            jQuery(voteScore).addClass('plus');
                            if (mark != 0) {
                                mark = '+'+mark;
                            }
                        } else {
                            jQuery(voteScore).addClass('minus');
                        }
                        jQuery(voteScore).html(mark);
                        var d = new Date();
                        d.setMinutes(d.getMinutes() + 1);
                        setCookie("voted", "1", d.toString(), "/");
                        setInterval("enableVote()",60000);
                    } else {
                        alert(response.error);
                    }
                }
        });
    }
})(jQuery);



function commentFormSubmit(formName, submitUrl)
{
        jQuery("form#" + formName).submit( function() {
            if (jQuery(this).find("textarea[name=text]").val() == '') {
                alert(commentsMessageEmpty);
                return false;
            } else {
                data = jQuery(this).serialize();
                var self = this;
                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: submitUrl,
                    data: data,
                    success: function(response) {
                        if (response.result) {
                            if (jQuery(self).find("#parent_id").val() == '') {
                                if (jQuery(".info-message").length == 1) {
                                    jQuery(".info-message").after(response.content);
                                    jQuery(".info-message").remove();
                                } else {
                                    jQuery(".sellersListAll").append(response.content);
                                }
                                jQuery(self).find("textarea[name=text]").val('');
                                var count = jQuery('.sellersListAll h2 span').html();
                                var pos = count.indexOf(')');
                                count = count.substr(1, pos - 1);
                                count = Math.round(count) + 1;
                                jQuery('.sellersListAll h2 span').html("(" + count + ")");
                                jQuery(self).hide();
                            } else {
                                jQuery(self).parent().parent().parent().hide();
                                var id = jQuery(self).find(" input[name=parent_id]").val();
                                jQuery("div#c" + id).append(response.content);
                            }
                        }
                        else
                            alert(response.error);
                    }
                });
                return false;
            }
        });
}

function removeEditForm(curId)
{
    comment = jQuery("div#r" + curId);
    jQuery(comment).find("p").show();
    jQuery(comment).find(".baraholkaMyListItemWideTextWrap").remove();
    jQuery(comment).find(".blueOtzivBlockChange").show();
    //jQuery("#e" + curId).show();
}

jQuery(document).ready( function() {
    var voted = getCookie('voted');
    if (voted == 1) {
        jQuery(".vote-links").addClass('disable');
    }
    jQuery(".vote-links a.minus").live("click", function(){
        if (voted != 1) {
            jQuery(this).vote(-1);
        }
        return false;
    });
    jQuery(".vote-links a.plus").live("click", function(){
        if (voted != 1) {
            jQuery(this).vote(1);
        }
        return false;
    });

    jQuery("a.delete-comment").live("click", function(){
//        console.log(commentsMessageDelete);
        if (confirm(commentsMessageDelete)) {
            var id = jQuery(this).attr('id');
            id = id.substr(1);
            var trToDelete = jQuery(this).parent().parent();
            jQuery.ajax({
                type: "POST",
                url: commentsUrlDelete,
                dataType: "json",
                data: "id="+id,
                success: function(response) {
                    if (response.result) {
                        trToDelete.remove();
                    } else
                        alert(response.error);
                }
            });
        }
        return false;
    });


// переменная для сохранения редактируемого идентификатора
    var curId;
// текст коментария
    var oldText;
    jQuery(".blueOtzivBlockChange").live("click", function(){
        // прячем окна редактирование, если есть
        if (curId) {
            removeEditForm(curId);
        }
        var id = jQuery(this).parent().attr('id');
        id = id.substr(1);
        curId = id;
        var currentComment = jQuery(this).parent().find("p");
        oldText = jQuery(currentComment).html();
        jQuery(currentComment).after('<div class="baraholkaMyListItemWideTextWrap heightAuto"><div class="baraholkaMyListItemWideText blueBack"><textarea name="edit_text" cols="50" rows="5">' + oldText + '</textarea></div><br/><input type="submit" name="save" value="Сохранить" id="submit" /><input id="submit" type="button" name="cancel" value="Отменить"/></div>');
        jQuery(currentComment).hide();
        jQuery(this).hide();
        return false;
    });

    jQuery("input[name=save]").live("click", function(){
        var text = jQuery("div#r" + curId + " textarea[name=edit_text]").val();
        if (text == '') {
            alert(commentsMessageEmpty);
            return false;
        }
        jQuery.ajax({
            type: "POST",
            url: commentsUrlEdit,
            dataType: 'json',
            data: "id=" + curId + "&text=" + text,
            success: function(response) {
                if (response.result) {
                    jQuery("div[id=r" + curId + "] p").html(response.content);
                    removeEditForm(curId);
                    curId = null;
                }
                else
                    alert(response.error);
            }
        });
        return false;
    });


    jQuery("input[name=cancel]").live("click", function(){
        removeEditForm(curId);
        curId = null;
        return false;
    });

    jQuery('.report-spam-comment').live('click', function(){
        var comment_id = jQuery(this).attr('id').match("[0-9]+");
        reportAsSpam(comment_id[0]);
        return false;
    });

    jQuery('a[id^=del-comment-]').live('click', function(){
        modifySpamComment('delete', jQuery(this).attr('id').match("[0-9]+")[0]);
        return false;
    });
    jQuery('a[id^=unmark-comment-]').live('click', function(){
        modifySpamComment('unmark', jQuery(this).attr('id').match("[0-9]+")[0]);
        return false;
    });
});