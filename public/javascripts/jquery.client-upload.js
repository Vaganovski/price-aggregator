jQuery(document).ready(function(){

    jQuery(".adminGoodsUploadPhotoButton").hover(
        function(e)
        {
            if (!e) e = window.event;
            if (e.pageX){
                x = e.pageX;
                y = e.pageY;
            } else if (e.clientX){
                x = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.clientLeft;
                y = e.clientY + (document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.clientTop;
            }
            var posLeft = 0,
            posTop = 0;
            var obj = this.getElementsByTagName("input")[1];
            if (!obj) {
                obj = this.getElementsByTagName("input")[0];
            }
            while (obj.offsetParent)
            {
                posLeft += obj.offsetLeft;
                posTop += obj.offsetTop;
                obj = obj.offsetParent;
            }
            var offsetX = x-posLeft,
            offsetY = y-posTop;
            if(offsetX<10) jQuery(".adminGoodsUploadPhotoButton > input").css("left","-25px");
            if(offsetY<10) jQuery(".adminGoodsUploadPhotoButton > input").css("top","0");
            if(offsetY>10) jQuery(".adminGoodsUploadPhotoButton > input").css("top","12px");
            return;
        },
        function()
        {
            jQuery(".adminGoodsUploadPhotoButton > input").css({
                left:"0",
                top: "0"
            });
        });

    jQuery(".adminGoodsUploadPhotoButton").mousemove(
        function(e) {
            if (!e) e = window.event;
            if (e.pageX){
                x = e.pageX;
                y = e.pageY;
            } else if (e.clientX){
                x = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.clientLeft;
                y = e.clientY + (document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.clientTop;
            }
            var posLeft = 0,
            posTop = 0;
            var obj = this.getElementsByTagName("input")[1];
            if (!obj) {
                obj = this.getElementsByTagName("input")[0];
            }
            while (obj.offsetParent)
            {
                posLeft += obj.offsetLeft;
                posTop += obj.offsetTop;
                obj = obj.offsetParent;
            }
            var offsetX = x-posLeft,
            offsetY = y-posTop,
            currentX = jQuery(".adminGoodsUploadPhotoButton > input").css("left"),
            currentY = parseInt(jQuery(".adminGoodsUploadPhotoButton > input").css("top"));
	
	
            if(offsetX<10 && currentX=="0px") jQuery(".adminGoodsUploadPhotoButton > input").css("left","-25px");
            if(offsetX>=50 && currentX=="-12px") jQuery(".adminGoodsUploadPhotoButton > input").css("left","0px");
            if(offsetY<10 && currentY>=0) jQuery(".adminGoodsUploadPhotoButton > input").css("top","0");
            if(offsetY>=35 && currentY<0) jQuery(".adminGoodsUploadPhotoButton > input").css("top","0");
            return;
        });

    if (jQuery('#catalog_form_product_productimage').length) {
        new AjaxUpload(jQuery('#catalog_form_product_productimage #image_filename'), {
            action: '/catalog/products/upload-image',
            name: 'image_filename',
            data: {
                id : jQuery('#catalog_form_product_productimage #id').val()
            },
            autoSubmit: true,
            onComplete : function(file, response){
                jQuery('#another-images').append('<b><img src="' + response + '" alt="' + file + '"/><i></i></b>')
            }
        });
    }
    if (jQuery('#catalog_form_product_productmainimage').length) {
        new AjaxUpload(jQuery('#catalog_form_product_productmainimage #image_filename'), {
            action: '/catalog/products/upload-main-image',
            name: 'image_filename',
            data: {
                id : jQuery('#catalog_form_product_productmainimage #id').val()
            },
            autoSubmit: true,
            onComplete : function(file, response){
                var block = jQuery('#catalog_form_product_productmainimage').parent();
                jQuery(block).find('p').remove();
                jQuery(block).find('#main-image').html('<b><img src="' + response + '" alt="' + file + '"/><i></i></b>')
            }
        });
    }
    if (jQuery('#advertisment_form_banner_top').length || jQuery('#advertisment_form_banner_right').length) {
        jQuery('input[name=image_filename]').each(function(){
            var form = jQuery(this).parent().parent().parent().parent();
            var thisButton = jQuery(this);
            new AjaxUpload(jQuery(this), {
                action: '/advertisment/index/upload-image',
                name: 'image_filename',
                data: {
                    id : form.find('#id').val(),
                    type : form.find('#type').val()
                },
                autoSubmit: true,
                onComplete : function(file, response){
                    response = response.split('&amp;');
                    form.find('#id').val(response[0])
                    thisButton.parent().before('<b id="i' + response[3] + '"><a href="' + response[2] + '" class="thickbox">' + response[1] + '</a><i></i></b>')
                }
            });
        })

        jQuery('input[name=background_image]').each(function(){
            var form = jQuery(this).parent().parent().parent().parent();
            var thisButton = jQuery(this);
            new AjaxUpload(jQuery(this), {
                action: '/advertisment/index/upload-backgound-image',
                name: 'background_image',
                data: {
                    id : form.find('#id').val()
                },
                autoSubmit: true,
                onComplete : function(file, response){
                    response = response.split('&amp;');
                    form.find('#id').val(response[0])
                    var text = '<b id="b' + response[0] + '"><a href="' + response[2] + '" class="thickbox">' + response[1] + '</a><i></i></b>';
                    if (thisButton.parent().parent().find('b').length){
                        thisButton.parent().parent().find('b').replaceWith(text)
                    } else {
                        thisButton.parent().before(text)
                    }
                }
            });
        })
    }

    var catalogProductImageUrl = '/catalog/products/delete-image'
    var errorMessage = "Произошла ошибка"
    jQuery(".adminGoodsUploadPhoto b i").live("click", function(){
        var thisI = jQuery(this);
        var id = jQuery(this).parent().parent().attr('id')
        var data = ''
        if (id == 'main-image') {
            catalogProductImageUrl = '/catalog/products/delete-main-image'
            data = "id=" +jQuery('#catalog_form_product_productmainimage #id').val()
        } else {
            data = "filename=" + jQuery(this).siblings("img").attr('src')
        }
        jQuery.ajax({
            type: "POST",
            url: catalogProductImageUrl,
            data: data,
            success: function(content) {
                if (content == "true") {
                    thisI.parent().remove();
                } else {
                    alert(errorMessage);
                }
            }
        });
    });

    jQuery("#banner-images b i").live("click", function(){
        var thisI = jQuery(this);
        var id = thisI.parent().attr('id');
        id = id.substr(1);
        var confirmation = confirm('Вы уверенны?');
        if (confirmation) {
            jQuery.ajax({
                type: "POST",
                url: '/advertisment/index/delete-image/format/json',
                data: "id="+id,
                dataType: "json",
                success: function(response) {
                    if (response['success'] == true) {
                        thisI.parent().remove();
                        return false;
                    } else {
                        alert(response['message']);
                    }
                }
            });
        }
        return false;
    })

    jQuery("#backgound-image b i").live("click", function(){
        var thisI = jQuery(this);
        var id = thisI.parent().attr('id');
        id = id.substr(1);
        var confirmation = confirm('Вы уверенны?');
        if (confirmation) {
            jQuery.ajax({
                type: "POST",
                url: '/advertisment/index/delete-backgound-image/format/json',
                data: "id="+id,
                dataType: "json",
                success: function(response) {
                    if (response['success'] == true) {
                        thisI.parent().remove();
                        return false;
                    } else {
                        alert(response['message']);
                    }
                }
            });
        }
    })
});
