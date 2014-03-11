/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
var conn, container, text, template;


function connect(server, port) {
    conn = new WebSocket('ws://' + server + ':' + port);
    container = $('.container');
    template = Handlebars.compile($('#message').html());

    conn.onmessage = function (e) {
        var messages = JSON.parse(e.data);

        for (var i in messages) {
            var message = messages[i];

            if (me.uid == message.user.uid) {
                message.my = true;
            }

            var image = /^https?:\/\/(?:[a-z\-]+\.)+[a-z]{2,6}(?:\/[^\/#?]+)+\.(?:jpe?g|gif|png)$/i;
            if (image.test(message.text)) {
                message.image = true;
            }

            container.append(template(message));
            scroll();
        }
    };
}


function scroll() {
    var doc = $(document);
    doc.scrollTop(doc.height());
}

$(function () {
    text = $('#text');
    $('form').submit(function (event) {
        conn.send(text.val());
        text.val('');

        return false;
    });
    scroll();
});

