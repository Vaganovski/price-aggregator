/**
 * Namespace
 * Управление характеристиками
 */
var features = new Object();

/**
 * счетчик характеристик
 */
features.count = 0;

/**
 * массив счетчиков значений полей характеристик
 */
features.countValues = new Array();

/**
 * шаблон формы для наборов характеристик
 */
features.setFormTemplate = null;
/**
 * шаблон формы для полей характеристик
 */
features.fieldFormTemplate = null;

/**
 * урл к контролеру, который удаляет картинки
 */
features.stepUrlDeleteImage = "/instructions/index/delete-image"

/**
 * Добавление кнопок для удаления параметра
 */
features.addRemoveButtons = function(context) {
    jQuery(context).each(function(){
        // Если кпопки нет - то ставим
        if (!jQuery(this).parent().is(':has(.delete-step)')) {
            jQuery(this).parent().prepend('<a href="#" class="adminShablDellAParam">удалить параметр</a>');
        }
    });
    // Убираем кнопку для первого елемента
    jQuery(context).find('fieldset:first .delete-step').remove();
}

/**
 * Добавление кнопок для удаления значений характеристик
 */
features.addRemoveAddValuesButtons = function(context) {

    jQuery(context).each(function(){
        // Если кпопки нет - то ставим
        if (!jQuery(this).is(':has(.adminBaraholkaMainListPartsNoAdd)')) {
            jQuery(this).append('<i class="adminBaraholkaMainListPartsNoAdd"></i>');
        }
        if (jQuery(this).is(':has(.adminBaraholkaMainListPartsAdd)')) {
            jQuery(this).find('.adminBaraholkaMainListPartsAdd').remove();
        }
    });
    // Убираем кнопку для первого елемента
    last = jQuery(context).parent().find('fieldset:last .adminBaraholkaMainListPartsNoAdd');
    last.after('<i class="adminBaraholkaMainListPartsAdd"></i>');
    last.remove();
}

/**
 * Добавление формы для новой характеристики
 */
features.addField = function() {
    features.count++;
    var fieldFormTemplate = features.getFieldFormTemplate().clone();
    //setFormTemplate.attr('id','fieldset-' + (features.count+1));
   // features.replaceNamesInForm(stepFormTemplate, /steps\[[0-9]\]/, 'steps['+(features.count+1)+']');
 
    var lastStep = jQuery('.adminEditFeaturesSet>fieldset:last');
    // изменяем имена элементов формы
    fieldFormTemplate.attr('id','fieldset-' + features.count);
    fieldFormTemplate.find('input[name*=title]:first').attr('name','fields[' + features.count + '][title]');
    fieldFormTemplate.find('input[name*=unit]').attr('name','fields[' + features.count + '][unit]');
    fieldFormTemplate.find('select[name*=type]').attr('name','fields[' + features.count + '][type]');
    fieldFormTemplate.find('input[name*=slider]').attr('name','fields[' + features.count + '][slider]');
    fieldFormTemplate.find('input[name*=id]').attr('name','fields[' + features.count + '][id]');
    var i=0;
    // изменяем имена элементов формы значений
    fieldFormTemplate.find('#fieldset-values>fieldset').each(function(){
        if (i == 0) {
            jQuery(this).find('input[name*=id]:first').attr('name','fields[' + features.count + '][values][v1][id]');
            jQuery(this).find('input[name*=title]:first').attr('name','fields[' + features.count + '][values][v1][title]');
            features.countValues.push(1);
        } else {
            jQuery(this).remove();
        }
       i++;
    });

    // раставляет кнопки для удаления
    features.addRemoveAddValuesButtons(fieldFormTemplate.find('#fieldset-values>fieldset'));
    fieldFormTemplate.show();
    // вставляем форму в конец
    lastStep.after(fieldFormTemplate);
    features.count++;
    return false;
}

/**
 * Удаление характеристики
 */
features.deleteField = function(){
    jQuery(this).parent().remove();
    return false;
}


/**
 * Подготовка шаблона формы для создания характеристики (удаление заполненых значений, etc)
 */
features.prepareNewFieldForm = function() {
    // Клонируем уже имеющуюся форму
    features.fieldFormTemplate = jQuery('.adminEditFeaturesSet>fieldset:first').clone();
    // Чистим поле ввода
    features.fieldFormTemplate.find('input[name*=title]').attr('value','');
    features.fieldFormTemplate.find('input[name*=title]').empty();
    features.fieldFormTemplate.find('input[name*=unit]').attr('value','');
    features.fieldFormTemplate.find('input[name*=unit]').empty();
    features.fieldFormTemplate.find('input[name*=slider]').attr('value','');
    features.fieldFormTemplate.find('input[name*=slider]').empty();
    features.fieldFormTemplate.find('input[name*=id]').attr('value','');
    features.fieldFormTemplate.find('input[name*=id]').empty();
}

/**
 * Возвращает шаблон формы для характеристики
 */
features.getFieldFormTemplate = function() {
    // Если шаблона еще нет - создает
    if (features.fieldFormTemplate == null) {
        features.prepareNewFieldForm();
    }
    return features.fieldFormTemplate;
}



/**
 * Добавление формы для нового значения характеристики
 */
features.addValue = function() {
    var thisFieldset = jQuery(this).parent();
    var parentFieldset = jQuery(this).parent().parent().parent().parent();
    var id = parentFieldset.attr('id');
    id = id.substr(9);
    var count = ++features.countValues[id-1];
    
    var valueFormTemplate = features.getValueFormTemplate().clone();
    //setFormTemplate.attr('id','fieldset-' + (features.count+1));
   // features.replaceNamesInForm(stepFormTemplate, /steps\[[0-9]\]/, 'steps['+(features.count+1)+']');
   // var lastStep = jQuery('.adminEditFeaturesSet>fieldset:last');

    // изменяем имена элементов формы
    valueFormTemplate.attr('id','fieldset-' + count);
    valueFormTemplate.find('input[name*=title]').attr('name','fields[' + id + '][values][v' + count + '][title]');
    valueFormTemplate.find('input[name*=id]').attr('name','fields[' + id + '][values][v' + count + '][id]');

    valueFormTemplate.show();
    // вставляем форму в конец
    thisFieldset.after(valueFormTemplate);
    // раставляет кнопки для удаления
    features.addRemoveAddValuesButtons(thisFieldset.parent().find('fieldset'));
    return false;
}

/**
 * Удаление значения поля характеристики
 */
features.deleteValue = function(){
    jQuery(this).parent().remove();
    return false;
}


/**
 * Подготовка шаблона формы для создания значения характеристики (удаление заполненых значений, etc)
 */
features.prepareNewValueForm = function() {
    // Клонируем уже имеющуюся форму
    features.valueFormTemplate = jQuery('#fieldset-values fieldset:first').clone();
    // Чистим поле ввода
    features.valueFormTemplate.find('input[name*=title]').attr('value','');
    features.valueFormTemplate.find('input[name*=title]').empty();
    features.valueFormTemplate.find('input[name*=id]').attr('value','');
    features.valueFormTemplate.find('input[name*=id]').empty();

}
/**
 * Возвращает шаблон формы для значений характеристики
 */
features.getValueFormTemplate = function() {
    // Если шаблона еще нет - создает
    if (features.valueFormTemplate == null) {
        features.prepareNewValueForm();
    }
    return features.valueFormTemplate;
}


/**
 * Заменяет значение атрибутов "name" по указаному шаблону
 */
features.replaceNamesInForm = function(form, search, replace) {
    jQuery(form).find('*[name]').each(function(){
        jQuery(this).attr('name', this.name.replace(search, replace));
    });
}



/**
 * Добавление формы для нового набора характеристик
 */
features.addSet = function() {
    features.count++;
    // получаем шаблон формы
    var setFormTemplate = features.getSetFormTemplate().clone();

    var lastStep = jQuery('.wid100 fieldset:last');
    // изменяем имена элементов формы
    setFormTemplate.attr('id','fieldset-' + features.count);
    setFormTemplate.find('input[name*=title]').attr('name','sets[' + features.count + '][title]');
    setFormTemplate.find('input[name*=id]').attr('name','sets[' + features.count + '][id]');

    setFormTemplate.show();
    // вставляем форму в конец
    lastStep.after(setFormTemplate);
    features.count++;
    return false;
}

/**
 * Удаление набора характеристик
 */
features.deleteSet = function(){
    jQuery(this).parent().remove();
    return false;
}


/**
 * Подготовка шаблона формы для создания набора характеристик (удаление заполненых значений, etc)
 */
features.prepareNewSetForm = function() {
    // Клонируем уже имеющуюся форму
    features.setFormTemplate = jQuery('.wid100 fieldset:first').clone();
    // Добавляем кпопку удаления
    // Чистим поле ввода
    features.setFormTemplate.find('input[name*=title]').attr('value','');
    features.setFormTemplate.find('input[name*=title]').empty();
    features.setFormTemplate.find('input[name*=id]').attr('value','');
    features.setFormTemplate.find('input[name*=id]').empty();

}

/**
 * Возвращает шаблон формы для набора характеристик
 */
features.getSetFormTemplate = function() {
    // Если шаблона еще нет - создает
    if (features.setFormTemplate == null) {
        features.prepareNewSetForm();
    }
    return features.setFormTemplate;
}



jQuery(function()
{
    // если страница редактирования набора вешаем одни обработкчики, а если нет, то другие
    if (jQuery(".wid100 .adminEditFeaturesSet").length){
        // Елемент DOM, где находяться характеристики, редактируемого набора
        var fields = jQuery(".adminEditFeaturesSet>fieldset>dl");
        features.count = jQuery(".adminEditFeaturesSet>fieldset").length;
        // для каждой характеристики считаем количество значений и проставляем кнопки удаления
        jQuery(".adminEditFeaturesSet>fieldset").each(function (){
            var fieldsetVal = jQuery(this).find('#fieldset-values fieldset');
            features.countValues.push(fieldsetVal.length);
            features.addRemoveAddValuesButtons(fieldsetVal);
        });
        // вставляем кнопки удаления характеристик
        features.addRemoveButtons(fields);

        // Вешаем обработчик для добавления характеристики
        jQuery(".adminShablAParamRows .adminShablAddAParam").live("click", features.addField);
        // Вешаем обработчик для удаления характеристики
        jQuery("fieldset .adminShablDellAParam").live("click", features.deleteField);

        // Вешаем обработчик для добавления значения характеристики
        jQuery("fieldset .adminBaraholkaMainListPartsAdd").live("click", features.addValue);
        // Вешаем обработчик для удаления значения характеристики
        jQuery("fieldset .adminBaraholkaMainListPartsNoAdd").live("click", features.deleteValue);
    } else {
        // Елемент DOM, где находяться наборы характеристик
        var sets = jQuery(".wid100 fieldset>dl");
        // количество наборов
        features.count = jQuery(".wid100 fieldset").length;
        // вставляем кнопки удаления наборов
        features.addRemoveButtons(sets);

        // Вешаем обработчик для удаление наборов
        jQuery("fieldset .adminShablDellAParam").live("click", features.deleteSet);

        // Вешаем обработчик для добавления наборов
        jQuery(".adminShablAParamRows .adminShablAddAParam").live("click", features.addSet);
    }

    // показывание/скрытие кнопок создания, редактирования, удаления наборов хар-к
    jQuery(".adminBaraholkaMainListParts span").bind("mouseenter",function(){
        jQuery(this).parent().find("b:first").show();
        jQuery(this).addClass('adminBaraholkaMainListPartsActive');
    }).bind("mouseleave",function(){
        jQuery(this).parent().find("b:first").hide();
        jQuery(this).removeClass('adminBaraholkaMainListPartsActive');
    })

});
