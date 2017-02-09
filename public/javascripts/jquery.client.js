var categoriesUrlList = '/catalog/categories/list'
var productsUrlVisible = '/catalog/products/visible-admin'
var categoriesMessageError = 'Произошла ошибка, обратитесь в администрацию'
jQuery(document).ready(function(){

    jQuery(".innerCatalogCheckBox").live('mousedown',
        function() {
            changeCheck(jQuery(this));
        });
	 
	 
    jQuery(".innerCatalogCheckBox").each(
        function() {
            changeCheckStart(jQuery(this));
        });

   jQuery('#catalog_form_price_upload').submit(function() {
        jQuery('#backFormPopup').show();
        jQuery('#backFormloading').show();
		setTimeout("showUpload()", 1500);
   });


    showRequiredFieldsForNewShop();
    jQuery('#shops_form_shop_new #type').change(function() {
        showRequiredFieldsForNewShop();
    });


   jQuery('#headerLogin').click(function() {
       jQuery('#TB_overlay').show();
       var login = jQuery('#windowLogin');
       jQuery(login).show();
       jQuery(login).find('input[type=text], input[type=password]').val('');
       jQuery(login).find('ul.errors').remove();
       return false;
   })

   jQuery('#windowLogin #windowLoginClose').live('click', function() {
       jQuery('#TB_overlay').hide();
       jQuery('#windowLogin').hide();
       return false;
   })

   jQuery('.indexCategoriesShowAll').click(function() {
       var alias = jQuery(this).attr('id');
       jQuery.ajax({
                type: "POST",
                url: categoriesUrlList,
                data: "alias=" + alias,
                success: function(content) {
                    jQuery("#windowPodcategory .windowPodcategoryPadding").html(content);
                    jQuery('#TB_overlay').show();
                    jQuery("#windowPodcategory").show('slow');
                },
                error: function () {
                    alert(categoriesMessageError);
                }
        });
       return false;
   })

   jQuery('#windowPodcategoryClose').click(function() {
       jQuery('#TB_overlay').hide();
       jQuery('#windowPodcategory').hide();
       return false;
   })

   jQuery('a[class*=visible-]').click(function() {
       var id = jQuery(this).attr('id');
       id = id.substr(1);
       var className = jQuery(this).attr('class');
       className = className.substr(8);
       if (className == 'hide') {
           visible = 0;
       } else if (className == 'show') {
           visible = 1;
       }
       jQuery.ajax({
                type: "POST",
                url: productsUrlVisible,
                data: "id=" + id + "&visible=" + visible,
                success: function(content) {
                    if (content == 1) {
                        jQuery('#s' + id).hide();
                        jQuery('#h' + id).show();
                    } else {
                        jQuery('#h' + id).hide();
                        jQuery('#s' + id).show();
                    }
                },
                error: function () {
                    alert(categoriesMessageError);
                }
        });
       return false;
   })

   // кнопка "скрыть все параметры" на странице товара в каталоге
   var catalogFeaturesVisible = 1
   jQuery('.contentCatalog .allParams').click(function() {
       if (catalogFeaturesVisible == 1) {
           jQuery('.tableContenter .wrapperForGoods').hide();
           jQuery(this).find('span').html('показать все параметры')
           catalogFeaturesVisible = 0;
       } else {
           jQuery('.tableContenter .wrapperForGoods').show();
           jQuery(this).find('span').html('скрыть все параметры')
           catalogFeaturesVisible = 1;
       }
       return false;
   });

    jQuery('.contactsTable .shop-contact i').live('click',function(){
        jQuery(this).parent().hide();
    });
    jQuery('.contactsTable span span').live('click',function(){
        jQuery('.shop-contact').hide();
        jQuery(this).parents('.contactsTable').find('.shop-contact').show();
    });

    // нашли ошибку в описании ---- начало
    jQuery('.wrapperForGoods .findAnError').click(function(){
        var product_id = jQuery('#find-error input[name=product_id]').val();
        jQuery.ajax({
            type: "POST",
            url: "/feedback/index/get-find-error-form/format/json",
            data: 'product_id=' + product_id,
            dataType: 'json',
            success: function(content) {
                jQuery('#find-error form').replaceWith(content.form)
            }
        });
        jQuery('#TB_overlay').show()
        jQuery('#find-error').show()
        return false;
    });

    jQuery('#find-error #windowLoginClose').live('click', function(){
      jQuery('#find-error').hide();
      jQuery('#TB_overlay').hide();
      return false;
    });

    jQuery("#find-error #submit").live('click', function() {
        jQuery(this).parents('form').submit();
        return false;
    });

    jQuery("#find-error form").live('submit', function() {
        jQuery(this).find('input[type=submit]').attr('disabled', 'disabled');
        data = jQuery(this).serialize();
        var thisForm = jQuery(this);
        var button = jQuery(this).find('input[type=submit]');
        jQuery.ajax({
            type: "POST",
            url: "/feedback/index/find-error/format/json",
            data: data,
            dataType: 'json',
            success: function(content) {
                button.removeAttr('disabled');
                if (content.success == true) {
                    alert(content.message);
                    thisForm.parent().find('#windowLoginClose').click();
                } else {
                    var oldHeight = jQuery("#find-error").height();
                    jQuery("#find-error .form-errors").remove();
                    thisForm.replaceWith(content.form);
                    var newHeight = jQuery("#find-error").height();
                    var diffHeight = newHeight - oldHeight;
                    var marginTop = parseInt(jQuery("#find-error").css("margin-top"));
                    jQuery("#find-error").css("margin-top", ((marginTop - diffHeight/2)));
                }
            }
        });
        return false;
    });
    // нашли ошибку в описании ---- конец

    jQuery('.toBuyTable span').live('click', function(){
      var id = jQuery(this).attr('id');
      id = id.substr(1);
      jQuery('#fastOrder input[name=price_id]').val(id);
      if (jQuery(this).parent().parent().find('.variant-desc a').length) {
        jQuery('#fastOrder span[id=description]').html(jQuery(this).parent().parent().find('.variant-desc a').text());
      }
      jQuery('#TB_overlay').show()
      jQuery('#fastOrder').show()
      return false;
    });

    jQuery('#fastOrder #windowLoginClose').click(function(){
      jQuery('#fastOrder').hide()
      jQuery('#TB_overlay').hide()
      return false;
    });

    jQuery("#fastOrder form").submit( function() {
        jQuery(this).find('input[type=submit]').attr('disabled', 'disabled');
        data = jQuery(this).serialize();
        var button = jQuery(this).find('input[type=submit]');
        jQuery.ajax({
            type: "POST",
            url: "/catalog/products/buy",
            data: data,
            dataType: 'json',
            success: function(content) {
                button.removeAttr('disabled');
                if (content.success == true) {
                    alert(content.message);
                    jQuery('#fastOrder #windowLoginClose').click();
                } else {
                    alert( content.message);
                }
            }
        });
        return false;
    });

// -------------ПОИСК-----------

    // вешаем обработчик для открытия окна выбора города
    jQuery(".headerSearch #headerSearchChooseCity").live("click", function(){jQuery(this).find("ul").toggle()});
    // вешаем обработчик для выбора города в форме поиска
    jQuery(".headerSearch #headerSearchChooseCity li").live("click", function() {
        var city = jQuery(this).children("b").html();
        jQuery(".headerSearch #city").val(city);
        jQuery(".headerSearch #headerSearchChooseCity span").html(city);

    });

    function formatItem(row) {
            return row[0] + "<span>" + row[1] + "</span>";
    }
    function formatResult(row) {
            return row[0].replace(/(<.+?>)/gi, '');
    }

    if (jQuery(".headerSearchForInp input[type=text]").length) {
        jQuery(".headerSearchForInp input[type=text]").focus();
        jQuery(".headerSearchForInp input[type=text]").autocomplete("/catalog/products/search-autocomplete/format/html", {
            delay:500,
            minChars:1,
            matchSubset:1,
            autoFill:false,
            matchContains:1,
            cacheLength:20,
            selectFirst:false,
            maxItemsToShow:20,
            width: 400,
            formatItem: formatItem,
            formatResult: formatResult,
            cityElement: '#catalog_form_product_searchuser input[name=city]'
        });
    }
// -------------Конец - ПОИСК-----------


    jQuery.openCompare = function (linkAddCompare,linkOpenCompare) {
        var href = catalogCompareUrl;
//            linkOpenCompare.attr('href');

//        if (href[href.lastIndexOf('products') + 'products'.length] != '/') {
//            href += '/';
//        }
//        for (var key in productIds) {
//            href += '-' + productIds[key];
//        }
        linkOpenCompare.attr('href', href);
        linkOpenCompare.toggle();
        linkAddCompare.toggle();
    }

    jQuery.addToCompare = function (linkAddCompare,linkOpenCompare) {
        var postData = "products=";
        if (productIds.length < 1) {
            return false;
        }
        for (var key in productIds) {
            postData += (key == 0 ? '' : '-') + productIds[key];
        }
        // отправляем через аякс запрос для получение списка товаров
        jQuery.ajax({
            type: "POST",
            url: '/catalog/products/add-to-compare/format/json',
            data: postData,
            dataType: 'json',
            success: function(response) {
                var result = response.result
                if (result.success == true) {
                    catalogCompareUrl = result.url;
                    if (jQuery("#link-container #compare-link").length){
                        jQuery("#link-container #compare-link").replaceWith(result.htmlLink);
                    } else if (jQuery("#link-container #my-list-link").length) {
                        jQuery("#link-container #my-list-link").before(result.htmlLink);
                    } else {
                        jQuery("#link-container").append(result.htmlLink);
                    }
                    jQuery.openCompare(linkAddCompare, linkOpenCompare);
                } else {
                    alert("Произошла ошибка. Пожалуйста обратитесь в администрацию." )
                }
            }
        });
        return true;
    }

    jQuery(".innerCatalogActions #innerCatalogAddComparison").live("click", function() {
        var linkOpenCompare = jQuery(".innerCatalogActions #innerCatalogOpenComparison")
        jQuery.addToCompare(jQuery(this), linkOpenCompare)
        return false;
    });

    jQuery(".priceAndActions #addToList").live("click", function() {
        var id = jQuery(".tableContenter .tableColumn5").attr("id");
        id = id.substr(1);
        productIds[0] = id;
        addToMyList();
        return false;
    });

    jQuery(".priceAndActions #addToCompare").live("click", function() {
        var id = jQuery(".tableContenter .tableColumn5").attr("id");
        id = id.substr(1);
        productIds[0] = id;
        var linkOpenCompare = jQuery(".priceAndActions #goToCompare")
        jQuery.addToCompare(jQuery(this), linkOpenCompare)
        return false;
    });

    jQuery("#clear-compare").live("click", function() {
        var thisContainer = jQuery(this).parent()
        jQuery.ajax({
            type: "POST",
            url: '/catalog/products/add-to-compare/format/json',
            dataType: 'json',
            success: function(response) {
                var result = response.result
                if (result.success == true) {
                    catalogCompareUrl = result.url;
                    thisContainer.remove();
                    jQuery("#link-container #compare-link").remove();
                } else {
                    alert("Произошла ошибка. Пожалуйста обратитесь в администрацию." )
                }
            }
        });
    });

    jQuery(".innerCatalogActions #innerCatalogMoveToList").live("click", function() {
        addToMyList();
        return false;
    });

    // на отзывах к товару
    jQuery(".wideDescrItem #addToCompare").live("click", function() {
        var id = jQuery(this).parent().attr("id");
        id = id.substr(1);
        productIds[0] = id;
        var linkOpenCompare = jQuery(".wideDescrItem #goToCompare")
        jQuery.addToCompare(jQuery(this), linkOpenCompare);
        return false;
    });
    jQuery(".wideDescrItem #addToMyList").live("click", function() {
        var id = jQuery(this).parent().attr("id");
        id = id.substr(1);
        productIds[0] = id;
        addToMyList();
        return false;
    });

    var addToMyList = function() {
        var postData = "products=";
        if (productIds.length < 1) {
            return false;
        }
        for (var key in productIds) {
            postData += (key == 0 ? '' : '-') + productIds[key];
        }

        // отправляем через аякс запрос для получение списка товаров
        jQuery.ajax({
            type: "POST",
            url: '/catalog/products/add-to-my-list/format/json',
            data: postData,
            dataType: 'json',
            success: function(content) {
                if (jQuery("#link-container #my-list-link").length){
                    jQuery("#link-container #my-list-link").replaceWith(content.result.htmlLink);
                } else {
                    jQuery("#link-container").append(content.result.htmlLink);
                }
                alert(content.result.message);
            }
        });
        return false;
    }

    jQuery(".myListUl .myListDel").live("click", function() {
        var id = jQuery(this).find("span").attr("id");
        id = id.substr(1);
        thisSpan = jQuery(this);
        // отправляем через аякс запрос для получение списка товаров
        jQuery.ajax({
            type: "POST",
            url: '/catalog/products/delete-from-my-list',
            data: "product_id=" + id,
            dataType: 'html',
            success: function(content) {
                if (content == false) {
                    alert(content.message);
                } else {
                    thisSpan.parent().remove();
                }
            }
        });
        return false;
    });



    var sortDirection = '';
    var sortType = '';
    /**
     * Возвращает отсортированый список по цене или популярноти или новизне
     */
    var sortPrices = function() {
        // если пустой тип сортировки удаляем стили активности
        if (sortType != '') {
            jQuery(".tableColumn5 #" + sortType).removeClass("priceSortActiveUp")
            jQuery(".tableColumn5 #" + sortType).removeClass("priceSortActiveDown")
        }
        // получаем текущий тип сортировки
        var type =  jQuery(this).attr('id');
        var productId = jQuery(this).parent().parent().attr("id");
        productId = productId.substr(1);
        // опредиляем направление сортировки
        if (sortDirection == '') {
            sortDirection = 'ASC';
        } else if (sortDirection == 'ASC' && type == sortType) {
            sortDirection = 'DESC';
        } else {
            sortDirection = 'ASC';
        }
        sortType = type;
        // отправляем через аякс запрос для получение списка товаров
        jQuery.ajax({
            type: "POST",
            url: '/catalog/products/get-prices',
            data: 'order_by=' + sortType + '&sort_direction=' + sortDirection + '&product_id=' + productId,
            dataType: 'html',
            success: function(content) {
                if (content == false) {
                    alert(message);
                } else {
                    jQuery('.tableContenter table tbody').html(content);
                    // устанавливаем стили активности
                    if (sortDirection == 'ASC') {
                        jQuery(".tableColumn5 #" + sortType).addClass("priceSortActiveUp");
                    } else {
                        jQuery(".tableColumn5 #" + sortType).addClass("priceSortActiveDown");
                    }
                }
            }
        });
        return false;
    }

    // вешаем обработчик для сортировке по цене
    jQuery(".tableColumn5 #price").live("click", sortPrices);
    // вешаем обработчик для сортировке по цене и наличию
    jQuery(".tableColumn5 #price-available").live("click", sortPrices);

    jQuery("#headerSearchExample span").click(function() {
        var txt = jQuery(this).html();
        jQuery("#catalog_form_product_searchuser #keywords").val(txt);
    })

    jQuery("#users_form_userbase_login #submit").live('click', function() {
        jQuery(this).parents('form').submit();
        return false;
    });

    jQuery("#users_form_userbase_login").live('submit', function() {
        var form = jQuery(this);
        data = jQuery(this).serialize();
        jQuery.ajax({
            type: "POST",
            url: jQuery(this).attr('action') + '/format/json',
            data: data,
            dataType: 'json',
            success: function(content) {
                if (content.result.success) {
                    var posHref = window.location.href.indexOf('users/index/login');
                    if (posHref != -1) {
                        window.location.href = '/';
                    } else {
                        window.location.reload(true);
                    }
                    return;
                } else {
                    jQuery(form).parent().after(content.result.form);
                    jQuery(form).parent().remove()
                    jQuery('#windowLogin').show();
                }
            }
        });
        return false;
    });

    // Кнопка Подробнее в спике товаров в барахолке
    jQuery(".baraholkaListItemWideDescrMoreInfo").click(function(){
        id = jQuery(this).attr('id');
        id = id.substr(1);
        var thisContext = jQuery(this);
        jQuery.ajax({
            type: "POST",
            url: '/marketplace/products/get-description/format/json',
            data: 'id=' + id,
            dataType: 'json',
            success: function(content) {
                if (content.result.success) {
                    thisContext.parent().find('#description').html(content.result.text)
                    thisContext.remove();
                    return;
                } else {
                    alert('Произошла ошибка. Обратитесь в администрацию.')
                }
            }
        });
        return false;
    })

// Окно Городов на странице листинга товаров в Барахоле
    // вешаем обработчик для открытия окна выбора города
    jQuery(".barholkaCity").live("click", function(){jQuery(this).find('ul').toggle()});
    // вешаем обработчик для фильтра по городу
    jQuery(".barholkaCity li").live("click", function() {
        // получение города
        shopsCity = jQuery(this).children("b").html();
        jQuery(this).parent().parent().find("span").html(shopsCity);
        jQuery(this).parent().hide()
        jQuery(this).parent().parent().find("form").attr('action', window.location.href);
        jQuery(this).parent().parent().find("form input[name=city]").val(shopsCity);
        if (jQuery('#marketplace_form_product_searchuser').length) {
            jQuery(this).parent().parent().find("form input[name=keywords]").val(
                jQuery('#marketplace_form_product_searchuser input[name=keywords]').val()
            );
        }
        if (jQuery(this).parent().parent().find("form input[name=keywords]").val() == '') {
            jQuery(this).parent().parent().find("form input[name=keywords]").remove();
        }
        jQuery(this).parent().parent().find("form").submit();
        return false;
    });
// Окно Городов на странице листинга товаров в Барахоле


// страница продавца
    jQuery('#shops_form_comment_new').hide();
    jQuery('.sellersListAll .feedback').click(function (){
        jQuery('#shops_form_comment_new').show()
    });

    jQuery('.sellersListAll #new_comment_need_login').click(function (){
        window.location.hash = 'shops_form_comment_new';
        jQuery('#headerLogin').click();
    });
    if (window.location.hash == '#shops_form_comment_new') {
        jQuery('#shops_form_comment_new').show()
    }
    jQuery('.show-variant a').live('click', function (){
        var id = jQuery(this).attr('id');
        id = id.substr(1);
        jQuery('.tableContenter tr[id^=s' + id + ']').toggle();
        var i = 0;
        if (jQuery('.tableContenter tr[id^=s' + id + ']').css('display') == 'table-row') {
           jQuery('.tableContenter tr[id=sm' + id + 'p0] td').each(function(){
              if (i > 0 && i < 4) {
                  jQuery(this).children().hide();
              }
              i++;
           });
        } else {
           jQuery('.tableContenter tr[id=sm' + id + 'p0] td').each(function(){
              if (i > 0 && i < 4) {
                  jQuery(this).children().show();
              }
              i++;
           });
        }
        return false;
    });

});
var productIds = new Array();
function changeCheck(el)
{
     var el = el,
          input = el.find("input").eq(0);
    var id = el.attr("id");
    id = id.substr(1);
    pos = productIds.indexOf(id);
    jQuery(".innerCatalogActions #innerCatalogOpenComparison").hide();
    jQuery(".innerCatalogActions #innerCatalogAddComparison").show();
    if (pos == -1) {
        var countProducts = productIds.length;
        if (countProducts) {
            productIds[countProducts] = id;
        } else {
            productIds[0] = id;
        }
    }else {
        productIds.splice(pos, 1);
    }
    jQuery(".innerCatalogActions #innerCatalogChooseAmount").html(productIds.length)
    if(!input.attr("checked")) {
        el.css("background-position","0 -17px");
        input.attr("checked", true)
    } else {
        el.css("background-position","0 0");
        input.attr("checked", false)
    }
     return true;
}

function changeCheckStart(el)
{
var el = el,
        input = el.find("input").eq(0);
      if(input.attr("checked")) {
        el.css("background-position","0 -17px");
        }
     return true;
}

function setPriceFile(inp) {
    jQuery("#fileName").val(jQuery(inp).val());
    jQuery("#fileSubmit").attr("disabled",false)
}

function showRequiredFieldsForNewShop() {
    var required = jQuery('label[for="address"]').find('.required');
    if (jQuery(this).find(':selected').val() == 'normal') {
        required.show();
    } else {
        required.hide();
    }
}

function showUpload() {
	$.get("/uploadprogress.php?id=" + $("#progress_key").val(), function(data) {
		if (!data)
			return;
 
		var response;
		eval ("response = " + data);
 
		if (!response)
			return;
 
		var percentage = Math.floor(100 * parseInt(response['bytes_uploaded']) / parseInt(response['bytes_total']));
		$("#backFormloading span").html(percentage+"% "+Math.floor(parseInt(response['bytes_uploaded']) / 1024)+"Кб");
 
	});
	setTimeout("showUpload()", 1500);
}