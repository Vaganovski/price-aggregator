(function(jQuery){
    /* Очищаем select */
    jQuery.fn.clearSelect = function() {
        return this.each(function(){
            /* Проверяем является ли элемент select`ом */
            if(this.tagName=='SELECT') {
                this.options.length = 0;
                /* Блокируем на время заполнения */
                jQuery(this).attr('disabled','disabled');
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

/* Функция отсылает ajax-запрос */
function getCategory(pcategory, level, selected, selectedToDelete) {
    jQuery.ajax({
        url: categoriesUrlListJson,
//        @todo генерировать урл автоматически
//        url: '/categories/index/list',
        type: 'POST',
        data: 'id='+ pcategory +'&level='+ level + '&selected='+ selectedToDelete,
        dataType: 'JSON',
        timeout: 5000,
        beforeSend: function(){
            // Блокируем все необходимы select`ы
            jQuery('select[id^=category_]').attr('disabled', 'disabled');
        },
        complete: function(){
            // Снимаем блокировку
            jQuery('select[id^=category_]').removeAttr('disabled');
        },
        success: function(response){
            var data = eval('('+ response +')');
            // Если количество категорий в ответе 0 либо не определено
            if(data.count === 'undefined' || data.count == 1) {
                if ((data.level - 1) >= 1) {
                    // просто удаляет старшие уровни каскада
                    jQuery('select[id=category_'+ (data.level - 1) +']')
                        .clearField('select[id^=category]')
                        .clearField('span');
                    return false;
                }
            }
            if( jQuery('select[id=category_'+ data.level +']').length ) {
                // Если select этого уровня уже существует
                // мы должны удалить все старшие select`ы,
                // очистить старые данные и заполнить новым контентом
                jQuery('select[id=category_'+ data.level +']')
                    .clearField('select[id^=category]')
                    .clearField('span')
                    .fillSelect(data.item, selected);
            } else {
                // Если select этого уровня не существует,
                // мы должны его создать и заполнить данными
                var select = jQuery('.baraholkaMyListItemWideSelect select[id=category_'+ (data.level - 1) +']')
                if (select.length) {
                    select.parent().parent().after('<div class="baraholkaMyListItemWideSelect left"><select name="category[]" id="category_'+ data.level +'"></select></div>');
                } else {
                    jQuery('#category-element select:last').after('<select name="category[]" id="category_'+ data.level +'"></select>');
                }
                jQuery('select[id=category_'+ data.level +']').fillSelect(data.item, selected);
            }

            /* Сбрасываем старый обработчик */
            jQuery('select[id=category_'+ data.level +']').unbind('change');
            /* Вешаем новый */
            jQuery('select[id=category_'+ data.level +']').change(function(){return clickEvent(jQuery(this));});

            return false;
        },
        error: function(){
            // Сообщаем пользователю, что произошла ошибка
            alert('Some error with categories!');
            return false;
        }
    });
}

function clickEvent(select)
{
    var id = select.find('option:selected').attr('value');
    /**
     * Если id=-1, значит выбран пункт "Выбор.."
     * значит мы должны просто очистить старшие списки
     */
    if (id == '') {
    	select.clearField('select[id^=category]').clearField('span');
    	return false;
    }

    var level = parseInt(select.attr('id').replace('category_', '')) + 1;
    return getCategory(id, level);
}

jQuery(document).ready(function(){
    /* Получаем список категорий */
     jQuery.each(dataArray,function(index,data) {
         getCategory(data.id, data.level, data.selected, data.selectedToDelete)
     });
});