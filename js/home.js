$(document).ready(function() {
  //reloj();
  $('#dni').numeric(".");
  

  // reloj
function reloj(){
  var Digital=new Date();
  var hours=Digital.getHours();
  var minutes=Digital.getMinutes();
  var seconds=Digital.getSeconds();

  var dn="PM";
  if (hours<12)
    dn="AM";
  if (hours>12)
    hours=hours-12;
  if (hours==0)
    hours=12;

  if (minutes<=9)
    minutes="0"+minutes;
  if (seconds<=9)
    seconds="0"+seconds;
  //change font size here to your desire
  $("#reloj").html(hours+":"+minutes+":"+seconds);
  $("#am_pm").html(dn);
  setTimeout(reloj,1000);
}

});