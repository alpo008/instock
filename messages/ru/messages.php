<?php
/**
 * Long messages translations.
 *
 * It contains the localizable messages extracted from source code.
 * You may modify this file by translating the extracted messages.
 *
 * Each array element represents the translation (value) of a message alias (key).
 * If the value is empty, the message is considered as not translated.
 * Messages that no longer need translation will have their translations
 * enclosed between a pair of '@@' marks.
 *
 * Message string can be used with plural forms format. Check i18n section
 * of the guide for details.
 *
 * NOTE: this file must be saved in UTF-8 encoding.
 */

return [
'materials_import_info' =>
    'Основное назначение данного инструмента - добавление новых записей в справочник материалов.
    При совпадении SAP-кода будут обновляться только валидные поля, то есть те, которым присвоены
    корректные значения в таблице Excel. Если поле в исходной таблице пустое или значение,
    содержащееся в нем, не соответствуют формату данных таблицы БД, значение будет проигнорировано.
    Для ряда полей могут присваиваться значения по умолчанию, если соответствующее значение отсутствует
    в БД'
];