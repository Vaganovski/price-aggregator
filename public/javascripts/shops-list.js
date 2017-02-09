jQuery(function()
{
    var shopsCity = 'Алматы';
    var shopsWithComment = false;
    // вешаем обработчик для открытия окна выбора города
    jQuery(".alphabetWrapper #headerSearchChooseCity2").live("click", function(){jQuery("#innerSortChooseCity").toggle()});
    // вешаем обработчик для фильтра по городу
    jQuery(".alphabetWrapper #headerSearchChooseCity2 li").live("click", function() {
        // получение города
        shopsCity = jQuery(this).children("b").html();
        recieveShopsList();
        return false;
    });

    jQuery(".alphabetWrapper #all").live("click", function() {
        shopsWithComment = false;
        recieveShopsList();
        return false;
    });
    jQuery(".alphabetWrapper #withComment").live("click", function() {
        shopsWithComment = true;
        recieveShopsList();
        return false;
    });

    var recieveShopsList = function () {
        // получаем хэш со всеми параметрами
        var hash = 'withComments=' + shopsWithComment.toString();
        if (shopsCity) {
            hash += '&city=' + shopsCity;
        }

        window.location.hash = hash;
        // отправляем через аякс запрос для получение списка товаров
        jQuery.ajax({
            type: "POST",
            url: '/shops',
            data: hash,
            dataType: 'html',
            success: function(content) {
                if (content == false) {
                    alert(content.message);
                } else {
                    jQuery('.contentWrap').html(content);
                    if (shopsCity) {
                        jQuery(".alphabetWrapper #headerSearchChooseCity2 span").html('город ' + shopsCity);
                        jQuery("#innerSortChooseCity").hide()
                    }
                    if (!shopsWithComment) {
                        jQuery(".alphabetWrapper #all").addClass('chooseAllComentsPiked');
                        jQuery(".alphabetWrapper #withComment").removeClass('chooseAllComentsPiked');
                    } else {
                        jQuery(".alphabetWrapper #withComment").addClass('chooseAllComentsPiked');
                        jQuery(".alphabetWrapper #all").removeClass('chooseAllComentsPiked');
                    }
                }
            }
        });
    }
    var hash = window.location.hash;
    if (hash) {
        pair = hash.split('&');
        for (var key in  pair) {
            posDelimiter = pair[key].indexOf('=')
            if (posDelimiter != -1) {
                var keyValue = pair[key].substring(0, posDelimiter);
                var value = pair[key].substr(posDelimiter + 1);
                if (keyValue == 'city') {
                    shopsCity= value;
                }
                if (keyValue == 'withComments') {
                    shopsWithComment = value;
                }
            }
        }

    }
    recieveShopsList();
});