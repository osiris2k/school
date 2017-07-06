$(document).ready(function(){
    	
});
function setLable(obj)
{
    var ibox = $(obj).parents('.ibox');
    var label = $(ibox).find('.show-label');
    label.html("( "+$(obj).val()+" )");
}