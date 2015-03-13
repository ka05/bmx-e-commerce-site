/**
 * Created by ka05th30ry on 10/9/2014.
 */

//showMessage('enter an organization name', 'error', 'error.png');
function showMessage(_message, _type, _timeout){
  var iconName, type;
  switch (_type){
    case "error":
      iconName = "img/error.png";
      type = "error";
      break;
    case "success":
      iconName = "img/success.png";
      type = "success";
      break;
  }
  // create the notification
  var notification = new NotificationFx({
    message : '<span class="icon"><img src="' + iconName + '" height="40" width="40" /></span><p>' + _message + '</p>',
    layout : 'bar',
    effect : 'slidetop',
    type : type, // notice, warning or error
    ttl:_timeout,
    wrapper:document.getElementById("message-div")
  });

  // show the notification
  notification.show();
}
