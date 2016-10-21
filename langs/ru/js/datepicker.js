jQuery(function($){
$.datepicker.regional['ru'] = {
monthNames: ['Яварь', 'Февраль', 'Март', 'Апрель',
'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь',
'Октябрь', 'Ноябрь', 'Декабрь'],
closeText:"Готово",
prevText:"Назад",
nextText:"Вперёд",
currentText:"Сегодня",
dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
firstDay: 1,
dateFormat: 'dd.mm.yy', firstDay: 0, 
};
$.datepicker.setDefaults($.datepicker.regional['ru']);
});