//時刻表示
function myTimeprev(){

	var weeks = new Array('日','月','火','水','木','金','土');

	var now = new Date();

	var year = now.getYear(); // 年
	var month = now.getMonth() + 1; // 月
	var day = now.getDate(); // 日
	var week = weeks[ now.getDay() ]; // 曜日
	var hour = now.getHours(); // 時
	var min = now.getMinutes(); // 分
	var sec = now.getSeconds(); // 秒

	if(year < 2000) { year += 1900; }

	// 数値が1桁の場合、頭に0を付けて2桁で表示する指定
	if(month < 10) { month = "0" + month; }
	if(day < 10) { day = "0" + day; }
	//if(hour < 10) { hour = "0" + hour; }
	if(min < 10) { min = "0" + min; }
	if(sec < 10) { sec = "0" + sec; }
	var myMsgDate = year+"/"+month+"/"+day+"("+week+")";
	document.getElementById("Realdate").innerHTML = myMsgDate;

	var myMsgTime = hour+":"+min+":"+sec;
	document.getElementById("Realtime").innerHTML = myMsgTime;
}
// -->
setInterval("myTimeprev()",1000);



//var account     = JSON.parse($script.attr('data-name'));
function dispname(account){
	//ユーザー削除時のダイアログ
	var $script     = $('#script');

	if(window.confirm(account+"さんのアカウントを削除しますか？")){
		return true; //
	}else{
		window.alert('キャンセルされました'); // 警告ダイアログを表示
		return false;
	}
}


$('.datepicker').datepicker({
  format: "yyyy/mm/dd",
  language: "ja",
  autoclose: true, //日付選択で自動的にカレンダーを閉じる
  orientation:'bottom left'
});



$(document).ready(function(){
    $('input.timepicker').timepicker({});
});

$('.timepicker').timepicker({
    timeFormat: 'h:mm p',
    interval: 60,
    minTime: '10',
    maxTime: '6:00pm',
    defaultTime: '11',
    startTime: '10:00',
    dynamic: false,
    dropdown: true,
    scrollbar: true
});

//ロード画面表示のやつ
$(function() {
  var h = $(window).height();

  $('#wrap').css('display','none');
  $('#loader-bg ,#loader').height(h).css('display','block');
});

$(window).load(function () { //全ての読み込みが完了したら実行
  $('#loader-bg').delay(900).fadeOut(0);
  $('#loader').delay(600).fadeOut(0);
  $('#wrap').css('display', 'block');
});

//10秒たったら強制的にロード画面を非表示
$(function(){
  setTimeout('stopload()',10000);
});

function stopload(){
  $('#wrap').css('display','block');
  $('#loader-bg').delay(900).fadeOut(0);
  $('#loader').delay(600).fadeOut(0);
}

//打刻時に使用する時刻を取得する。
function getTime(){

  var weeks = new Array('日','月','火','水','木','金','土');

	var now = new Date();

	var year = now.getYear(); // 年
	var month = now.getMonth() + 1; // 月
	var day = now.getDate(); // 日
	var week = weeks[ now.getDay() ]; // 曜日
	var hour = now.getHours(); // 時
	var min = now.getMinutes(); // 分
	var sec = now.getSeconds(); // 秒

	if(year < 2000) { year += 1900; }

	// 数値が1桁の場合、頭に0を付けて2桁で表示する指定
	if(month < 10) { month = "0" + month; }
	if(day < 10) { day = "0" + day; }
	//if(hour < 10) { hour = "0" + hour; }
	if(min < 10) { min = "0" + min; }
	if(sec < 10) { sec = "0" + sec; }

	var jsTime = hour+":"+min+":"+sec;
	time.time.value = jsTime;
}


function checkForm(){
 var content1 = registUser.registAccount.value;
 if(content1 ==""){
	 document.getElementById("test").textContent="必須です。";
 }else{
	 document.getElementById("test").textContent="OK";
 }


}


/* bootstrap alertをx秒後に消す */
$(document).ready(function()
{
	//	$(window).load(function()
	//	{
	window.setTimeout("$('#alertfadeout').fadeOut()", 5000);
	//	});

});
