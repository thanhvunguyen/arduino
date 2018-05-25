window.is_first_time = true;
window.sync_done = false;

/**
 * Show close status
 *
 * @author  <hieunt1@nal.vn>
 */
function showCloseStatus() {
    $('.box-status').css('background', '#ffbaba').text("切断する.").show();

    window.sync_done = false;
}

/**
 * Show reconnect status
 *
 * @author  <hieunt1@nal.vn>
 */
function showReconnectStatus() {
    var box = $('.box-status');

    window.sync_done = false;
    box.css('background', '#f5e5b6').text("接続中...").show();
}

/**
 * Show connect status
 *
 * @author  <hieunt1@nal.vn>
 */
function showConnectedStatus() {
    window.sync_done = false;
    $('.box-status').css('background', '#c7ffbe').text("接続済み.").show();

    setTimeout(function () {
        $('.box-status').hide(500);
    }, 1000);

    if ((!window.is_first_time || window.sync_done) && window.ticket) {
        window.ticket.sync_device();
    }

    is_first_time = false;
}