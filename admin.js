(function ($) {
$(document).ready(function () {

let currentUser = parseInt(adminScript.userId);
const tutorSettingNav = $('.tutor-option-tabs .tutor-nav-item');

if(tutorSettingNav.length > 0 && currentUser !== 1) {
    // Hide setting navs except 1st and last
    tutorSettingNav.slice(1, -1).hide();

    // Add hide all setting tabs except ceertificate
    $('.tutor-option-tab-pages .tutor-option-nav-page:not(#tutor_certificate)').css({
        'pointer-events' : 'none',
        'opacity' : '0.5'
    });
    $('.tutor-options-search').hide();
    $('button.modal-reset-open').hide();
};

});
})(jQuery);