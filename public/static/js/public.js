function openTast(){
    $('#mask').show();
    $('body').css("overflow", "hidden");
}
function closeTask(){

    $('#mask').hide();
    $('body').css("overflow", "auto");
}
$('#mask').click(function(){
    closeTask()
})
$('#mask>div').click(function(e){
    window.event? window.event.cancelBubble = true : e.stopPropagation();
})

function ressize() {
    let htmlWidth = document.documentElement.clientWidth || document.body.clientWidth;
    let htmlDom = document.getElementsByTagName("html")[0];
    htmlDom.style.fontSize = htmlWidth / 46.875 + "px";
}
ressize();

