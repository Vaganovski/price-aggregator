(function(jQuery){
    /* Очищаем select */
    jQuery.fn.clearSelect = function() {
        return this.each(function(){
            /* Проверяем является ли элемент select`ом */
            if(this.tagName=='SELECT') {
                this.options.length = 0;
                /* Блокируем на время заполнения */
//                jQuery(this).attr('disabled','disabled');
            }
        });
    }

    /* Удаляем старшие элементы */
    jQuery.fn.clearField = function(selector) {
        /**
         * Ищем все элементы следующие за вызывавшим
         * и удовлеторяющие переданному селектору
         */
        this.nextAll(selector).remove();
        return this;
    }

    /* Заполняем select переданными данными */
    jQuery.fn.fillSelect = function(dataArray, selected) {
        return this.clearSelect().each(function(){
            /* Проверяем является ли элемент select`ом */
            if(this.tagName=='SELECT') {
                var currentSelect = this;

                /* Устанавливаем этот option первым в списке */
                //if(jQuery.support.cssFloat) {
                //    currentSelect.add(start,null);
               // } else {
               //     currentSelect.add(start);
               // }

                jQuery.each(dataArray,function(index,data){
                    /* Если определено 'name' */
                    if(data.text) {
                        /* Создаем новый option */
                        var option = new Option(data.text,data.id);

                        if (data.id == selected) {
                            option.selected = true;
                        }
                        /* Добавляем новый option к select`у */
                        if(jQuery.support.cssFloat) {
                            currentSelect.add(option,null);
                        } else {
                            currentSelect.add(option);
                        }
                    }
                });
                /* Выделяем первый элемент списка */
                //jQuery(this).removeAttr('disabled').find('option:first').attr('selected', 'selected');
            }
        });
    }
})(jQuery);

// путь для аякс запроса для получения списка категорий
categoriesUrlListJson = '/categories/index/list-json';
var ajaxDataCategories = {};
var ajaxDataAccessories = {};
var maxLevel = 0;var minLevel = 0;
var maxIndex = 0;var minIndex = 0;
/* Функция отсылает ajax-запрос */
function getCategory(pcategory, level, selected, selectedToDelete, isShow, selectId, selectName, containerId) {

    jQuery.ajax({
        url: categoriesUrlListJson,
//        @todo генерировать урл автоматически
//        url: '/categories/index/list',
        type: 'POST',
        data: 'id='+ pcategory +'&level='+ level + '&selected='+ selectedToDelete,
        dataType: 'JSON',
        beforeSend: function(){
            // Блокируем все необходимы select`ы
            jQuery(self).parents('#'+containerId).find('select[id^=' + selectId + ']').attr('disabled', 'disabled');
        },
        complete: function(){
            // Снимаем блокировку
            jQuery(self).parents('#'+containerId).find('select').removeAttr('disabled');
        },
        success: function(response){
            var data = eval('('+ response +')');
            dataForShow = {"data":data,
                           "selected":selected,
                           "selectId": selectId,
                           "selectName": selectName,
                           "containerId": containerId};
            if (isShow == 1) {
                var id = selectId.match('[0-9]')
                if (id) {
                    if (maxIndex < id) maxIndex = id[0];
                    if (minIndex > id) minIndex = id[0];
                    if (maxLevel < data.level) maxLevel = data.level;
                    if (minLevel > data.level) minLevel = data.level;
                    ajaxDataAccessories[id[0] + ',' + data.level] = dataForShow;

                    for (var i = minIndex; i <= maxIndex; i++) {
                        for (var j = minLevel; j <= maxLevel; j++) {
                            if (ajaxDataAccessories[i + ',' + j] instanceof Object) {
                                showSelect(ajaxDataAccessories[i + ',' + j]);
                            }
                        }
                    }
                } else {
                    ajaxDataCategories[data.level] = dataForShow;
                    var count = 0;
                    for (var k in ajaxDataCategories) {
                        if (ajaxDataCategories.hasOwnProperty(k)) {
                           ++count;
                        }
                    }
                    for (i = 1; i <= count; i++) {
                        showSelect(ajaxDataCategories[i]);
                    }
                }
            } else {
                showSelect(dataForShow);
            }
            return false;
        },
        error: function(){
            // Сообщаем пользователю, что произошла ошибка
            alert('Some error with categories!');
            return false;
        }
    });
}

function showSelect(ajaxData) {
    var data = ajaxData.data
    var selected = ajaxData.selected
    var selectId = ajaxData.selectId
    var selectName = ajaxData.selectName
    var containerId = ajaxData.containerId
    jQuery('select[id^=' + selectId + ']').removeAttr('disabled');
    // Если количество категорий в ответе 0 либо не определено
    if(data.count === 'undefined' || data.count == 1) {
        if ((data.level - 1) >= 1) {
            // просто удаляет старшие уровни каскада
            jQuery('select[id=' + selectId + (data.level - 1) +']').parent()
                .clearField('.adminBaraholkaAddThemeFormSelectMiddle');
            return false;
        }
    }
    if( jQuery('select[id=' + selectId + data.level +']').length ) {
        // Если select этого уровня уже существует
        // мы должны удалить все старшие select`ы,
        // очистить старые данные и заполнить новым контентом
        jQuery('select[id=' + selectId + data.level +']').parent()
            .clearField('.adminBaraholkaAddThemeFormSelectMiddle')
            .find('select')
            .fillSelect(data.item, selected);
    } else {
        // Если select этого уровня не существует,
        // мы должны его создать и заполнить данными
        var select = jQuery('.baraholkaMyListItemWideSelect select[id=' + selectId + (data.level - 1) +']')
        if (select.length) {
            select.parent().parent().after('<div class="baraholkaMyListItemWideSelect left"><select name="' + selectName + '" id="' + selectId + data.level +'"></select></div>');
        } else if (jQuery('#' + containerId + ' .adminBaraholkaAddThemeFormSelectMiddle').length) {
            jQuery('#' + containerId + ' select:last').parent().after('<div class="adminBaraholkaAddThemeFormSelectMiddle"><select name="' + selectName + '" id="' + selectId + data.level +'"></select></div>');
        } else {
            jQuery('#' + containerId).after('<dd><select name="' + selectName + '" id="' + selectId + data.level +'"></select></dd>');
        }
        jQuery('select[id=' + selectId + data.level +']').fillSelect(data.item, selected);
    }

    /* Сбрасываем старый обработчик */
    jQuery('select[id=' + selectId + data.level +']').unbind('change');
    /* Вешаем новый */
    jQuery('select[id=' + selectId + data.level +']').bind('change', function(){
        return clickEvent(jQuery(this), selectId, selectName, containerId);
    });
    return true;
}

function clickEvent(select, selectId, selectName, containerId)
{
    var id = select.find('option:selected').attr('value');
    /**
     * Если id=-1, значит выбран пункт "Выбор.."
     * значит мы должны просто очистить старшие списки
     */
    if (id == '') {
    	select.parent().clearField('.adminBaraholkaAddThemeFormSelectMiddle');
    	return false;
    }

    var level = parseInt(select.attr('id').replace(selectId, '')) + 1;
    return getCategory(id, level, null, null, 0, selectId, selectName, containerId);
}

var countAccessories = 0;

var addAccessory = function (count) {
    cloneAccessory = jQuery('#accessory-element #fieldset-accessory dl:first').clone();
    cloneAccessory.find('#0-element').attr('id', count + '-element');
    var selectId = 'accessory_' + count + '_'
    var selectName = 'accessory[' + count + '][]'
    var containerId = 'accessory-element #fieldset-accessory #' + count + '-element dd:last';
    cloneAccessory.find('select').attr('id', selectId + '1')
    cloneAccessory.find('select').attr('name', selectName)
    cloneAccessory.find('select[id ^= ' + selectId + ']').unbind('change');
    cloneAccessory.find('select[id ^= ' + selectId + ']').bind('change', function(){
        return clickEvent(jQuery(this), selectId, selectName, containerId);
    });
    jQuery('#accessory-element #fieldset-accessory > dl:last').after(cloneAccessory.show());

    return false;
}

var deleteAccessory = function (element) {
    element.parent().parent().parent().remove();
    return false;
}

jQuery(document).ready(function(){
    /* Получаем список категорий */
    if (typeof(dataArray) != 'undefined') {
         jQuery.each(dataArray,function(index,data) {
             getCategory(data.id, data.level, data.selected, data.selectedToDelete, 1, 'category_', 'category[]', 'categories-container');
         });
    }
    /* Получаем список категорий */
    if (typeof(dataAccessoriesArray) != 'undefined') {
         jQuery.each(dataAccessoriesArray,function(indexAccessory,dataAccessory) {
             if (indexAccessory != 0) {
                 addAccessory(indexAccessory)
             }
             jQuery.each(dataAccessory,function(index,data) {
                getCategory(data.id, data.level, data.selected, data.selectedToDelete, 1,
                    'accessory_' + indexAccessory + '_',
                    'accessory[' + indexAccessory + '][]',
                    'accessory-element #fieldset-accessory #' + indexAccessory + '-element dd:last')
             });
             countAccessories = indexAccessory + 1;
         });
    }
     if (jQuery('#accessory-element').length) {

        jQuery('#features-container > dt').remove()
        jQuery('#accessory-element #fieldset-accessory > dl').each(function (){
            jQuery(this).find('> dt').remove()
        })
        jQuery('#accessory-element #fieldset-accessory').append('<a href="#" id="add-accessory">Добавить аксессуар</a>');
        jQuery('#accessory-element #fieldset-accessory fieldset').append('<div style="clear:both;float:none"></div><a href="#" id="delete-accessory">Удалить аксессуар</a>');
        jQuery('#accessory-element #fieldset-accessory dl:first').hide()
        jQuery('#accessory-element #fieldset-accessory dl:first select').unbind('change');
        jQuery('#add-accessory').live('click', function(){return addAccessory(countAccessories++)})
        jQuery('#delete-accessory').live('click', function(){return deleteAccessory(jQuery(this))})
     }
});