var messagesWrapper = $('.dropdown-messages');
var messagesToggle = messagesWrapper.find('a[data-toggle]');
var messagesCountElem = messagesToggle.find('span[data-count]');
var messagesCount = parseInt(messagesCountElem.data('count'));
var messages = messagesWrapper.find('li.scrollable-container');

// Subscribe to the channel we specified in our Laravel Event
var channel = pusher.subscribe('new-message');
// Bind a function to a Event (the full Laravel class)
channel.bind('App\\Events\\NewMessage', function (data) {
    var existingmessages = messages.html();
    var avatar = Math.floor(Math.random() * (71 - 20 + 1)) + 20;
    var newNotificationHtml = `<a href="`+data.path+'/'+data.id + `"><div class="media"><div class="media-left"><span class="avatar avatar-sm avatar-online rounded-circle"> <img  style="height: 60px;" src="` + data.photo + `" class="img-circle" alt="50x50" style="width: 50px; height: 50px;" alt="avatar"><i></i></span> </div> <div class="media-body"><h6 class="media-heading">` + data.title + `</h6> <p class="notification-text font-small-3 text-muted">` + data.message + `</p><small style="direction: ltr;"><time class="media-meta text-muted" style="direction: ltr;">` + data.date + `</time><br>` + data.time + ` </small></div></div></a>`;
    messages.html(newNotificationHtml + existingmessages);
    messagesCount += 1;
    messagesCountElem.attr('data-count', messagesCount);
    messagesWrapper.find('.notif-count').text(messagesCount);
    messagesWrapper.show();
});
