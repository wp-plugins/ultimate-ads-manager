var url = 'http://swp2.codeneric.com';
function checkReview() {
    if (!jQuery.trim(jQuery('#cc-username').val()))
        return;
    jQuery('#cc-msg-box').remove();
    var msgBox = jQuery('<div id="cc-msg-box"></div>');
    msgBox.css({ 'margin-left': 0, 'margin-top': '1em' });
    jQuery('#cc-premium-spinner').css('display', 'inline-block');
    jQuery.get(url + '/check-review', { username: jQuery('#cc-username').val(), id: GLOB_id, product: 'uam' }, function (res) {
        console.log('Reviewed:', res);
        if (res === 'ok') {
            jQuery.post('admin-ajax.php', { action: 'cc_ultimate_ads_manage_prem' }, function (res) {
                msgBox.addClass('updated').html('Congratulations!</br> Refresh this page and enjoy your premium version.');

                jQuery('#cc-premium-spinner').hide();
                msgBox.insertAfter('#cc-premium-spinner');
                jQuery('#cc-go-premium').attr('disabled', true);
                jQuery('#cc-username').attr('disabled', true);
            });
        }
        else {
            if (res === 'already-reviewed') {
                msgBox.addClass('error').html('This review does not seem to be yours. </br> If you think that this is not true, contact us: <a href="mailto:contact@codeneric.com">contact@codeneric.com</a>');
            }
            if (res === 'no-review-found') {
                msgBox.addClass('error').html('There is no review by <strong>' + jQuery('#cc-username').val() + '</strong>.</br> If you think that this is not true, contact us: <a href="mailto:contact@codeneric.com">contact@codeneric.com</a>');
            }
            jQuery('#cc-premium-spinner').hide();
            msgBox.insertAfter('#cc-premium-spinner');
        }
    });
}
var chill_request_time = 0;
jQuery('#cc-username').on('input', function () {
    clearTimeout(chill_request_time);
    jQuery('#cc-go-premium').attr('disabled', true);
    jQuery('#cc-premium-spinner').css('display', 'inline-block');
    chill_request_time = setTimeout(function () {
        console.log('Uname changed!');
        var username = jQuery('#cc-username').val();
        var temp = username.match(/\((.*)\)/);
        if (temp !== null && temp.length === 2) {
            username = temp[1];
        }
        jQuery.get(url + '/register-username', { username: username, id: GLOB_id, product: 'uam' }, function (res) {
            console.log('Register-Username:', res);
            jQuery('#cc-go-premium').attr('disabled', false);
            jQuery('#cc-premium-spinner').hide();
        });
    }, 1000);
});
jQuery(document).ready(function () {
   /* if (jQuery('#cc-full-name').val() === '' && GLOB_clients_num >= 2 && GLOB_max < 100) {
        jQuery('#premium-message').show();
        jQuery('a.thickbox').click();
        jQuery('#poststuff input').attr('disabled', true);
    }*/
});