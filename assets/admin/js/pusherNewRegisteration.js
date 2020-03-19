var notificationsWrapper = $('.dropdown-notifications');
var notificationsToggle = notificationsWrapper.find('a[data-toggle]');
var notificationsCountElem = notificationsToggle.find('span[data-count]');
var notificationsCount = parseInt(notificationsCountElem.data('count'));
var notifications = notificationsWrapper.find('li.scrollable-container');

// Subscribe to the channel we specified in our Laravel Event
var channel = pusher.subscribe('new-registration');
// Bind a function to a Event (the full Laravel class)
channel.bind('App\\Events\\NewRegisteration', function (data) {
    var existingNotifications = notifications.html();
    var avatar = Math.floor(Math.random() * (71 - 20 + 1)) + 20;
    var newNotificationHtml = `<a href="`+data.path+`"><div class="media"><div class="media-left"><span class="avatar avatar-sm avatar-online rounded-circle"> <img  style="height: 60px;" src="`+data.photo+`" class="img-circle" alt="50x50" style="width: 50px; height: 50px;" alt="avatar"><i></i></span> </div> <div class="media-body"><h6 class="media-heading">` + data.title + `</h6> <p class="notification-text font-small-3 text-muted">` + data.content + `</p><small style="direction: ltr;"><time class="media-meta text-muted" style="direction: ltr;">` + data.date + `</time><br>` + data.time + ` </small></div></div></a>`;
    notifications.html(newNotificationHtml + existingNotifications);
    notificationsCount += 1;
    notificationsCountElem.attr('data-count', notificationsCount);
    notificationsWrapper.find('.notif-count').text(notificationsCount);
    notificationsWrapper.show();
});
