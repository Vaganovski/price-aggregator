var catalogMinPrice = 0
var catalogMaxPrice = 50000
var catalog_f_default_from = {}
var catalog_f_default_to = {}
/**
 * Namespace
 * Управление сортировкой и фильтрами на странице списка товаров
 */
var filtering = new Object();

/**
 * тип сортировки
 */
filtering.type = '';

/**
 * направление сортировки
 */
filtering.direction = '';

/**
 * цена от
 */
filtering.price_from = 0;

/**
 * цена до
 */
filtering.price_to = 50000;

/**
 * цена от
 */
filtering.f_from = {};

/**
 * цена до
 */
filtering.f_to = {};

/**
 * город
 */
filtering.city = '';

/**
 * Страница
 */
filtering.page = '';

/**
 * ключевые слова для поиска
 */
filtering.keywords = '';

/**
 * массив с идентификаторами брэндов
 */
filtering.brands = new Array();

/**
 * массив с идентификаторами категорий
 */
filtering.categories = new Array();

/**
 * массив с идентификаторами значений хар-к
 */
filtering.features = {};

/**
 * Возвращает отсортированый список по цене или популярноти или новизне
 */
filtering.sort = function(event) {
    // если пустой тип сортировки удаляем стили активности
    if (filtering.type != '') {
        jQuery(".innerCatalogSort #" + filtering.type).parent().removeClass("innerCatalogSortActive")
        jQuery(".innerCatalogSort #" + filtering.type).parent().removeClass("innerCatalogSortActive2")
    }
    // получаем текущий тип сортировки
    var type =  jQuery(this).attr('id');
    // определяем направление сортировки
    if (filtering.direction == '') {
        filtering.direction = 'ASC';
    } else if (filtering.direction == 'ASC' && type == filtering.type) {
        filtering.direction = 'DESC';
    } else {
        filtering.direction = 'ASC';
    }
    filtering.type = type;
    jQuery('#backFormPopup').show();
    jQuery('#backFormloading').show();
    // получаем хэш со всеми параметрами
    var hash = filtering.getUrlHash();
    window.location.hash = hash;
    // отправляем через аякс запрос для получение списка товаров
    jQuery.ajax({
        type: "POST",
        url: catalogCategoriesViewUrl,
        data: hash,
        dataType: 'html',
        success: function(content) {
            filtering.requestSuccess(content);
        }
    });
    return false;
}

/**
 * Возвращает список с фильтром по городу
 */
filtering.sortCity = function() {
    // получение города
    filtering.city = jQuery(this).children("b").html();
    if (filtering.city == 'Все') {
        filtering.city = ''
    }
    jQuery('#backFormPopup').show();
    jQuery('#backFormloading').show();
    // получаем хэш со всеми параметрами
    var hash = filtering.getUrlHash();
    window.location.hash = hash;
    // отправляем через аякс запрос для получение списка товаров
    jQuery.ajax({
        type: "POST",
        url: catalogCategoriesViewUrl,
        data: hash,
        dataType: 'html',
        success: function(content) {
            jQuery('#backFormloading').hide();
            jQuery('#backFormPopup').hide();
            if (content == false) {
                alert(content.message);
            } else {
                jQuery('.contentCatalog').replaceWith(content);
                filtering.updateSortIndicator();
                if (filtering.city == '') {
                    jQuery(".innerCatalogSortAndStat #city i").html('Все');
                } else {
                    jQuery(".innerCatalogSortAndStat #city i").html(filtering.city);
                }
                jQuery("#innerSortChooseCity").hide()
            }
        }
    });
    return false;
}

/**
 * Возвращает список с фильтром по ключевому слову
 */
filtering.filterKeyword = function() {
    // получаем ключевое слово
    filtering.keywords = jQuery(this).val();
    jQuery('#backFormPopup').show();
    jQuery('#backFormloading').show();
    // получаем хэш со всеми параметрами
    var hash = filtering.getUrlHash();
    window.location.hash = hash;
    // отправляем через аякс запрос для получение списка товаров
    jQuery.ajax({
        type: "POST",
        url: catalogCategoriesViewUrl,
        data: hash,
        dataType: 'html',
        success: function(content) {
            filtering.requestSuccess(content);
        }
    });
    return false;
}

/**
 * Возвращает список с фильтром по цене
 */
filtering.filterPrice = function(price_from, price_to) {
    filtering.price_from = price_from; // цена от
    filtering.price_to = price_to; // цена до
    jQuery('#backFormPopup').show();
    jQuery('#backFormloading').show();
    // получаем хэш со всеми параметрами
    var hash = filtering.getUrlHash();
    window.location.hash = hash;
    // отправляем через аякс запрос для получение списка товаров
    jQuery.ajax({
        type: "POST",
        url: catalogCategoriesViewUrl,
        data: hash,
        dataType: 'html',
        success: function(content) {
            filtering.requestSuccess(content);
        }
    });
    return false;
}

/**
 * Возвращает список с фильтром по цене
 */
filtering.filterSliderFeature = function(id, f_from, f_to) {
    filtering.f_from[id] = f_from;
    filtering.f_to[id] = f_to;
    jQuery('#backFormPopup').show();
    jQuery('#backFormloading').show();
    // получаем хэш со всеми параметрами
    var hash = filtering.getUrlHash();
    window.location.hash = hash;
    // отправляем через аякс запрос для получение списка товаров
    jQuery.ajax({
        type: "POST",
        url: catalogCategoriesViewUrl,
        data: hash,
        dataType: 'html',
        success: function(content) {
            filtering.requestSuccess(content);
        }
    });
    return false;
}

/**
 * Возвращает список с фильтром по брэнду
 */
filtering.filterBrand = function() {
    var thisButton = jQuery(this).children('div');
    var classButton = thisButton.attr('class');
    jQuery('#backFormPopup').show();
    jQuery('#backFormloading').show();
    // получаем идентификатор брэнда
    var id = jQuery(this).children('div').attr('id');
    id = id.substr(1);
    // если брэнд не активный заносим его в массив, иначе удалям с массива
    // и делаем его не активным
    if (classButton != 'innerSearchBlockChoosenActive') {
        var countBrands = filtering.brands.length;
        if (countBrands) {
            filtering.brands[countBrands] = id;
        } else {
            filtering.brands[0] = id;
        }
        thisButton.toggleClass('innerSearchBlockChoosenActive');
    } else {
        var index = filtering.brands.indexOf(id);
        filtering.brands.splice(index, 1);
        thisButton.toggleClass('innerSearchBlockChoosenActive');
    }

    // получаем хэш со всеми параметрами
    var hash = filtering.getUrlHash();
    window.location.hash = hash;
    // отправляем через аякс запрос для получение списка товаров
    jQuery.ajax({
        type: "POST",
        url: catalogCategoriesViewUrl,
        data: hash,
        dataType: 'html',
        success: function(content) {
            filtering.requestSuccess(content);
        }
    });
    return false;
}

/**
 * Возвращает список с фильтром по категориям
 */
filtering.filterCategory = function() {
    var thisButton = jQuery(this).children('a');
    if (thisButton.length) {
        var classButton = thisButton.attr('class');
        jQuery('#backFormPopup').show();
        jQuery('#backFormloading').show();
        // получаем идентификатор брэнда
        var id = thisButton.attr('id');
        id = id.substr(1);
        // если брэнд не активный заносим его в массив, иначе удалям с массива
        // и делаем его не активным
        if (classButton != 'withClose') {
            var countCategories = filtering.categories.length;
            if (countCategories) {
                filtering.categories[countCategories] = id;
            } else {
                filtering.categories[0] = id;
            }
            thisButton.toggleClass('withClose');
        } else {
            var index = filtering.categories.indexOf(id);
            filtering.categories.splice(index, 1);
            thisButton.toggleClass('withClose');
        }
        jQuery(".innerSearchBlock .listOfComp div span").removeClass('withClose');
    } else {
        filtering.categories = new Array();
        jQuery(this).addClass('withClose');
        jQuery(".innerSearchBlock .listOfComp li").each(function(){
            jQuery(this).children('a').removeClass('withClose');
        })
    }

    // получаем хэш со всеми параметрами
    var hash = filtering.getUrlHash();
    window.location.hash = hash;
    // отправляем через аякс запрос для получение списка товаров
    jQuery.ajax({
        type: "POST",
        url: catalogCategoriesViewUrl,
        data: hash,
        dataType: 'html',
        success: function(content) {
            filtering.requestSuccess(content);
        }
    });
    return false;
}



/**
 * Возвращает список с фильтром по страницах
 */
filtering.pagination = function() {
    jQuery('#backFormPopup').show();
    jQuery('#backFormloading').show();
    self = this;
    filtering.page = jQuery(this).text();
    // получаем хэш со всеми параметрами
    var hash = filtering.getUrlHash();
    window.location.hash = hash;
    // отправляем через аякс запрос для получение списка товаров
    jQuery('html:not(:animated),body:not(:animated)').animate({scrollTop: jQuery('.contentCatalog').offset().top}, 0);
    jQuery.ajax({
        type: "POST",
        url: jQuery(self).attr('href'),
        data: hash,
        dataType: 'html',
        success: function(content) {
            filtering.requestSuccess(content);
        }
    });
    return false;
}


/**
 * Возвращает список с фильтром по характеристикам
 */
filtering.filterFeatures = function() {
    var thisButton = jQuery(this).children('div');
    var classButton = thisButton.attr('class');
    jQuery('#backFormPopup').show();
    jQuery('#backFormloading').show();
    // получаем идентификатор характеристики
    var id = jQuery(this).children('div').attr('id');
    id = id.split('-');
    fieldId = id[1];
    id = id[2];
    if (!(filtering.features[fieldId] instanceof Array)) {
        filtering.features[fieldId] = new Array()
    }
    // если характеристика не активна заносим ее в массив, иначе удалям с массива
    // и делаем ее не активной
    if (classButton != 'innerSearchBlockChoosenActive') {
        var countFeatures = filtering.features[fieldId].length;
        if (countFeatures) {
            filtering.features[fieldId][countFeatures] = id;
        } else {
            filtering.features[fieldId][0] = id;
        }
        thisButton.toggleClass('innerSearchBlockChoosenActive');
    } else {
        var index = filtering.features[fieldId].indexOf(id);
        filtering.features[fieldId].splice(index, 1);
        thisButton.toggleClass('innerSearchBlockChoosenActive');
    }

// получаем хэш со всеми параметрами
    var hash = filtering.getUrlHash();
    window.location.hash = hash;
    // отправляем через аякс запрос для получение списка товаров
    jQuery.ajax({
        type: "POST",
        url: catalogCategoriesViewUrl,
        data: hash,
        dataType: 'html',
        success: function(content) {
            filtering.requestSuccess(content);
        }
    });
    return false;
}

/**
 * Возвращает хэш со всеми параметрами
 */
filtering.getUrlHash = function() {
    hash = '';
    // добавляем в хэш брэнды
    if (filtering.brands.length) {
        hash += "&brands="
        for (var key in filtering.brands) {
            hash += (key == 0 ? '' : '-') + filtering.brands[key];
        }
    }
    // добавляем в хэш категории
    if (filtering.categories.length) {
        hash += "&categories="
        for (var key in filtering.categories) {
            hash += (key == 0 ? '' : '-') + filtering.categories[key];
        }
    }

    // добавляем в хэш значения характеристик ОТ
    for (var key in filtering.f_from) {
        hash += '&f_from_' + key + '=' + filtering.f_from[key];
    }

    // добавляем в хэш значения характеристик ДО
    for (var key in filtering.f_to) {
        hash += '&f_to_' + key + '=' + filtering.f_to[key];
    }

    // добавляем в хэш тип сортировки и направление
    if (filtering.type != '') {
        hash += '&orderby=' + filtering.type;
        hash += '&sort_direction=' + filtering.direction;
    }
    // добавляем в хэш город
    if (filtering.city != '') {
        hash += '&city=' + filtering.city;
    }
    // добавляем в хэш страницу
    if (filtering.page != '') {
        hash += '&page=' + filtering.page;
    }
    // добавляем в хэш ключевые слова
    if (filtering.keywords != '') {
        hash += '&keywords=' + filtering.keywords;
    }
    // добавляем в хэш цену от
    if (filtering.price_from != '') {
        hash += '&price_from=' + filtering.price_from;
    }
    // добавляем в хэш цену до
    if (filtering.price_to != '') {
        hash += '&price_to=' + filtering.price_to;
    }
    // добавляем в хэш характеристики
    for (var key in filtering.features) {
        if (filtering.features[key].length) {
            hash += "&feature_" + key + "="
            for (var keyin in filtering.features[key]) {
                hash += (keyin == 0 ? '' : '-') + filtering.features[key][keyin];
            }
        }
    }
    return hash;
}


/**
 * Обновление контента
 */
filtering.requestSuccess = function(response) {
    jQuery('#backFormloading').hide();
    jQuery('#backFormPopup').hide();
    if (response == false) {
        alert(response.message);
    } else {
        jQuery('.contentCatalog').replaceWith(response);
        filtering.updateSortIndicator();
    }
}

/**
 * Обновления состояния сортировки
 */
filtering.updateSortIndicator = function() {
    jQuery(".innerCatalogSort span").removeAttr('class');
    if (filtering.type == '') {
        filtering.type = 'score';
    }
    if (filtering.direction == '' && filtering.type == 'score') {
        filtering.direction = 'ASC';
    }
    if (filtering.direction == 'ASC') {
        jQuery(".innerCatalogSort #" + filtering.type).parent().addClass("innerCatalogSortActive");
    } else {
        jQuery(".innerCatalogSort #" + filtering.type).parent().addClass("innerCatalogSortActive2");
    }
}

/**
 * Делаем активными выбраные параметры
 */
filtering.setActiveParams = function() {
    // добавляем в хэш брэнды
    if (filtering.brands.length) {
        for (var key in filtering.brands) {
            jQuery(".innerSearchBlock .innerSearchBlockProducerList li div[id=b" + filtering.brands[key] + "]").addClass('innerSearchBlockChoosenActive');
        }
    }
    // добавляем активность для категорий
    if (filtering.categories.length) {
        for (var key in filtering.categories) {
            jQuery(".innerSearchBlock .listOfComp li a[id=c" + filtering.categories[key] + "]").addClass('withClose');
        }
    }
    // добавляем в хэш тип сортировки и направление
    if (filtering.type != '') {
        if (filtering.direction == 'ASC') {
            jQuery(".innerCatalogSortAndStat #" + filtering.type).parent().addClass("innerCatalogSortActive");
        } else {
            jQuery(".innerCatalogSortAndStat #" + filtering.type).parent().addClass("innerCatalogSortActive2");
        }
    }
    // добавляем в хэш город
    if (filtering.city != '') {
        jQuery(".innerCatalogSortAndStat #city i").html(filtering.city);
    }
    // добавляем в хэш ключевые слова
    if (filtering.keywords != '') {
        jQuery(".innerSearchBlock #searchKeyWord").val(filtering.keywords);
    }

    if (jQuery('#slider-range').size()) {
        // добавляем в хэш цену от
        if (filtering.price_from != '') {
            jQuery(".innerSearchBlock #price_from").val(filtering.price_from)
            jQuery("#slider-range").slider("values", 0, filtering.price_from)
        }
        // добавляем в хэш цену до
        if (filtering.price_to != '') {
            jQuery(".innerSearchBlock #price_to").val(filtering.price_to)
            jQuery("#slider-range").slider("values", 1, filtering.price_to)
        }
        // добавляем в хэш значения характеристик ОТ
        for (var key in filtering.f_from) {
            jQuery(".innerSearchBlock #f_from_" + key).val(filtering.f_from[key])
            jQuery("#slider-range-" + key).slider("values", 0, filtering.f_from[key])
        }

        // добавляем в хэш значения характеристик ДО
        for (var key in filtering.f_to) {
            jQuery(".innerSearchBlock #f_to_" + key).val(filtering.f_to[key])
            jQuery("#slider-range-" + key).slider("values", 1, filtering.f_to[key])
        }
    }

    // добавляем в хэш характеристики
    if (filtering.features.length) {
        for (var key in filtering.features) {

        }
    }
    for (var key in filtering.features) {
        if (filtering.features[key].length) {
            for (var keyin in filtering.features[key]) {
                jQuery(".innerSearchBlock .innerSearchBlockCol li div[id=f-" + key + "-" + filtering.features[key][keyin] + "]").addClass('innerSearchBlockChoosenActive');
            }
        }
    }
}


/**
 * Возвращает список с фильтром по категориям
 */
filtering.simpleFilterCategory = function() {
    var thisButton = jQuery(this).children('a');
    if (thisButton.length) {
        var classButton = thisButton.attr('class');
        jQuery('#backFormPopup').show();
        jQuery('#backFormloading').show();
        // получаем идентификатор категории
        var id = thisButton.attr('id');
        id = id.substr(1);
        // если категория не активная заносим его в массив, иначе удалям с массива
        // и делаем его не активным
        if (classButton != 'withClose') {
            var countCategories = filtering.categories.length;
            if (countCategories) {
                filtering.categories[countCategories] = id;
            } else {
                filtering.categories[0] = id;
            }
            thisButton.toggleClass('withClose');
        } else {
            var index = filtering.categories.indexOf(id);
            filtering.categories.splice(index, 1);
            thisButton.toggleClass('withClose');
        }
        jQuery(".innerSearchBlock .listOfComp div span").removeClass('withClose');
    } else {
        filtering.categories = new Array();
        jQuery(this).addClass('withClose');
        jQuery(".innerSearchBlock .listOfComp li").each(function(){
            jQuery(this).children('a').removeClass('withClose');
        })
    }

    // получаем хэш со всеми параметрами
    var hash = '';

    // добавляем в хэш категории
    if (filtering.categories.length) {
        hash += "&categories="
        for (var key in filtering.categories) {
            hash += (key == 0 ? '' : '-') + filtering.categories[key];
        }
    }

    window.location.hash = hash;
    // отправляем через аякс запрос для получение списка товаров
    jQuery.ajax({
        type: "POST",
        url: catalogCategoriesViewUrl,
        data: hash,
        dataType: 'html',
        success: function(content) {
            filtering.requestSuccess(content);
        }
    });
    return false;
}

$(document).ready(function() {

        filtering.price_from = catalogMinPrice;
        filtering.price_to = catalogMaxPrice;

        if (jQuery(".innerCatalogSortAndStat #city i").length) {
            filtering.city = jQuery(".innerCatalogSortAndStat #city i").html();
        }

        // вешаем обработчик для сортировке по релевантности
        jQuery(".innerCatalogSortAndStat #score").live("click", filtering.sort);
        // вешаем обработчик для сортировке по цене
        jQuery(".innerCatalogSortAndStat #price").live("click", filtering.sort);
        // вешаем обработчик для сортировке по популярности
        jQuery(".innerCatalogSortAndStat #popular").live("click", filtering.sort);
        // вешаем обработчик для сортировке по новизне
        jQuery(".innerCatalogSortAndStat #new").live("click", filtering.sort);
        // вешаем обработчик для открытия окна выбора города
        jQuery(".innerCatalogSortAndStat #city").live("click", function(){jQuery("#innerSortChooseCity").toggle()});
        // вешаем обработчик для фильтра по городу
        jQuery(".innerCatalogSortAndStat #innerSortChooseCity li").live("click", filtering.sortCity);
        // вешаем обработчик для фильтра по ключевым словам
        jQuery(".innerSearchBlock #searchKeyWord").live("keyup", filtering.filterKeyword);
        // вешаем обработчик для фильтра по брэндам
        jQuery(".innerSearchBlock .innerSearchBlockProducerList li").live("click", filtering.filterBrand);
        // вешаем обработчик для фильтра по характеристикам
        jQuery(".innerSearchBlock .innerSearchBlockCol li").live("click", filtering.filterFeatures);
        // вешаем обработчик при вводе в поле цены от и до меняется положение ползунка
        jQuery(".innerSearchBlock #price_from").live("keyup", function(){
            var price_from = jQuery(this).val();
            var price_to = jQuery(".innerSearchBlock #price_to").val();
            if (price_from <= price_to) {
                jQuery("#slider-range").slider("values", 0, price_from);
                filtering.filterPrice(price_from, price_to);
            }
        });
        jQuery(".innerSearchBlock #price_to").live("keyup", function(){
            var price_from = jQuery(".innerSearchBlock #price_from").val();
            var price_to = jQuery(this).val();
            if (price_from <= price_to) {
                jQuery("#slider-range").slider("values", 1, price_to)
                filtering.filterPrice(price_from, price_to);
            }
        });
        // устанавливаем ползунок
       	jQuery("#slider-range").slider({
                range: true,
                min: catalogMinPrice,
                max: catalogMaxPrice,
                step: 1,
                values: [catalogMinPrice, catalogMaxPrice],
                slide: function(event, ui) {
                    jQuery(".innerSearchBlock #price_from").val(ui.values[0]);
                    jQuery(".innerSearchBlock #price_to").val(ui.values[1]);
                },
                stop: function(event, ui) {
                    filtering.filterPrice(ui.values[0], ui.values[1]);
                }
        });

        jQuery('.ui-slider-handle').next().addClass('right-slider-handle');
        // устанавливаем ползунок
       	jQuery("div[id^=slider-range-]").each(function (){
            var id = jQuery(this).attr("id");
            id = id.replace('slider-range-', '');
            var value = catalog_f_default_to[id].toString();
            var pos = value.indexOf('.');
            var step = 1;
            if (pos != -1) {
                var i = 0;
                var digits = value.length - (pos + 1);
                step = 1 / (Math.pow(10, digits));
            }
            jQuery(this).slider({
                    range: true,
                    min: catalog_f_default_from[id],
                    max: catalog_f_default_to[id],
                    step: step,
                    values: [catalog_f_default_from[id], catalog_f_default_to[id]],
                    slide: function(event, ui) {
                        jQuery(".innerSearchBlock #f_from_" + id).val(ui.values[0]);
                        jQuery(".innerSearchBlock #f_to_" + id).val(ui.values[1]);
                    },
                    stop: function(event, ui) {
                        filtering.filterSliderFeature(id, ui.values[0], ui.values[1]);
                    }
            });
            jQuery('.ui-slider-handle').next().addClass('right-slider-handle');
            jQuery(".innerSearchBlock #f_from_" + id).val(jQuery("#slider-range-"+id).slider("values", 0));
            jQuery(".innerSearchBlock #f_to_" + id).val(jQuery("#slider-range-"+id).slider("values", 1));
        })
        // устанавлеваем значения по умолчания в поля цена от и до
        if (jQuery("#slider-range").length) {
            jQuery(".innerSearchBlock #price_from").val(jQuery("#slider-range").slider("values", 0));
            jQuery(".innerSearchBlock #price_to").val(jQuery("#slider-range").slider("values", 1));
        }

        var hash = window.location.hash;
        if (hash) {
            pair = hash.split('&');
            for (var key in  pair) {
                posDelimiter = pair[key].indexOf('=')
                if (posDelimiter != -1) {
                    var keyValue = pair[key].substring(0, posDelimiter);
                    var value = (pair[key].substr(posDelimiter + 1)).replace(/<\/?[^>]+>/gi, '');
                    var pos = keyValue.indexOf('f_from_');
                    if (pos != -1) {
                        fieldId = keyValue.replace('f_from_', '');
                        filtering.f_from[fieldId] = value;
                    }
                    pos = keyValue.indexOf('f_to_');
                    if (pos != -1) {
                        fieldId = keyValue.replace('f_to_', '');
                        filtering.f_to[fieldId] = value;
                    }
                    if (keyValue == 'orderby') {
                        filtering.type = value;
                    }
                    if (keyValue == 'sort_direction') {
                        filtering.direction = value;
                    }
                    if (keyValue == 'city') {
                        filtering.city = value;
                    }
                    if (keyValue == 'keywords') {
                        filtering.keywords = value;
                    }
                    if (keyValue == 'price_from') {
                        filtering.price_from = value;
                    }
                    if (keyValue == 'price_to') {
                        filtering.price_to = value;
                    }
                    if (keyValue == 'brands') {
                        brands = value.split('-');
                        filtering.brands = brands;
                    }
                    if (keyValue == 'categories') {
                        filtering.categories = value.split('-');
                    }
                    pos = keyValue.indexOf('feature');
                    if (pos != -1) {
                        fieldId = keyValue.replace('feature_', '');
                        features = value.split('-');
                        filtering.features[fieldId] = features;
                    }
                }
            }

            jQuery('#backFormPopup').show();
            jQuery('#backFormloading').show();
            jQuery.ajax({
                type: "POST",
                url: catalogCategoriesViewUrl,
                data: hash.substr(1),
                dataType: 'html',
                success: function(content) {
                    jQuery('#backFormloading').hide();
                    jQuery('#backFormPopup').hide();
                    if (content == false) {
                        alert(content.message);
                    } else {
                        jQuery('.contentCatalog').replaceWith(content);
                        filtering.setActiveParams();
                        filtering.updateSortIndicator();
                    }
                }
            });
        }

        jQuery(".innerSearchBlock .innerSearchBlockShowLine").live("click", function () {
            var thisClass = jQuery(this).attr('class')
            if (thisClass == 'innerSearchBlockShowLine') {
                jQuery(this).addClass('innerSearchBlockShowLineOpen');
                jQuery(this).parent().find('ul').show();
                jQuery(this).parent().find('.innerSearchBlockCol').each(function (){
                    var head = jQuery(this).find('h6 span b').html();
                    jQuery(this).find('h6 span').html(head);
                });
            } else {
                jQuery(this).removeClass('innerSearchBlockShowLineOpen');
                jQuery(this).parent().find('ul').hide();
                jQuery(this).parent().find('.innerSearchBlockCol').each(function (){
                    var head = jQuery(this).find('h6 span').html();
                    jQuery(this).find('h6 span').html('<b>' + head + '</b>');
                });
            }
        });


        // вешаем обработчик для фильтра по категориям
        if (jQuery('.innerSearchBlock.prices-list').size()) {
            jQuery(".innerSearchBlock.prices-list .listOfComp li").live("click", filtering.simpleFilterCategory);
        } else {
            jQuery(".innerSearchBlock .listOfComp li").live("click", filtering.filterCategory);
            jQuery(".innerSearchBlock .listOfComp div span").live("click", filtering.filterCategory);
        }

        jQuery(".contentCatalog.prices-list .innerPageNav a").live("click", function(){
            jQuery('#backFormPopup').show();
            jQuery('#backFormloading').show();
            self = this;
            filtering.page = jQuery(this).text();
            // получаем хэш со всеми параметрами
            var hash = '';
            // добавляем в хэш категории
            if (filtering.categories.length) {
                hash += "&categories="
                for (var key in filtering.categories) {
                    hash += (key == 0 ? '' : '-') + filtering.categories[key];
                }
            }
            // добавляем в хэш страницу
            if (filtering.page != '') {
                hash += '&page=' + filtering.page;
            }
            window.location.hash = hash;
            // отправляем через аякс запрос для получение списка товаров
            jQuery('html:not(:animated),body:not(:animated)').animate({scrollTop: jQuery('.contentCatalog').offset().top}, 0);
            jQuery.ajax({
                type: "POST",
                url: jQuery(self).attr('href'),
                data: hash,
                dataType: 'html',
                success: function(content) {
                    filtering.requestSuccess(content);
                }
            });
            return false;
        });

        jQuery(".contentCatalog .innerPageNav a").live("click", filtering.pagination);
        filtering.updateSortIndicator();
});