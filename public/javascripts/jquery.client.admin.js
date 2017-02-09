jQuery(document).ready(function(){

//        var rightFormCount = 1;
//        var topFormCount = 1;

        var setAjaxUpload = function (form) {
            var thisButton = form.find('input[name=image_filename]');
//            console.log(thisButton);
            new AjaxUpload(thisButton, {
                    action: '/advertisment/index/upload-image',
                    name: 'image_filename',
                    data: {
                        id : form.find('#id').val(),
                        type : form.find('#type').val()
                    },
                    autoSubmit: true,
                    onComplete : function(file, response){
                        form.find('#id').val(response)
                        thisButton.parent().before('<b>' + file + '<i></i></b>')
                    }
            });
        }

        jQuery(".adminNameOfBanner#top a").click(function() {
           var form = jQuery(this).parent().next('.adminAddReklamRow').clone();
           form.show();
           form.find("h5 b").html(topFormCount);
           setAjaxUpload(form);
           jQuery(this).parent().next('.adminAddReklamRow').after(form);
           topFormCount++;
           return false
        });

        jQuery(".adminNameOfBanner#right a").click(function() {
           var form = jQuery(this).parent().next('.adminAddReklamRow').clone();
           form.show();
           form.find("h5 b").html(rightFormCount);
           setAjaxUpload(form);
           jQuery(this).parent().next('.adminAddReklamRow').after(form);
           rightFormCount++;
           return false
        });

        jQuery(".adminAddReklamRowCol3Delete").live('click', function() {
           var id = jQuery(this).parent().find("#id").val();
           if (id) {
               var form = jQuery(this).parent().parent().parent();
               var confirmResult = confirm("Вы действительно хотите удалить этот баннер?")
               if (confirmResult) {
                   jQuery.ajax({
                            type: "POST",
                            url: '/advertisment/index/delete/format/json',
                            data: "id=" + id,
                            dataType: "json",
                            success: function(response) {
                                if (response['success'] == true) {
                                    form.remove();
                                    return false;
                                } else {
                                   alert(response['message']);
                                }
                            }
                   });
               }
           } else {
               jQuery(this).parent().parent().parent().remove();
           }
           return false
        });

	jQuery(".adminSmallCheckBox").mousedown(
	function() {
	 
	     changeCheck(jQuery(this));
	      
	});
	 
	 
	jQuery(".adminSmallCheckBox").each(
	/* ��� �������� �������� ����� ��������� ����� �������� ����� ������� � � ������������ � ��� ��������� ��� */
	function() {
	      
	     changeCheckStart(jQuery(this));
	      
	});
        
	jQuery(".adminOtzivGoods2 p:first-child").show();
        
	jQuery(".adminOtzivGoods2 span").click(function() {
	    jQuery(this).parent().find("p:first").nextAll('p').toggle();
            if (jQuery(this).parent().find("p :visible").length > 1){
                jQuery(this).html('свернуть');
            } else {
                jQuery(this).html('развернуть...');
            }
	});

});
	 
	function changeCheck(el)
	/*
	    ������� ����� ���� � �������� ��������
	    el - span ��������� ��� �������� ��������
	    input - �������
	*/
	{
	     var el = el,
	          input = el.find("input").eq(0);
	     if(input.val()=="off")
	     {
	          el.css("background-position","0 -13px");    
	          input.val("on");
	     }
	     else
	     {
	          el.css("background-position","0 0");    
	          input.val("off");
	     }
	     return true;
	}
	 
	function changeCheckStart(el)
	/*
	    ���� �������� ����������� � on, ������ ��� �������� �� ����������
	*/
	{
	     var el = el,
	          input = el.find("input").eq(0);
	     if(input.val()=="on")
	     {
	          el.css("background-position","0 0");    
	        
	     }
	     return true;
	}
	 