(function ($) {

    $('.viral-pro-modal-button').on('click', function (e) {
        e.preventDefault();
        $('body').addClass('viral-pro-modal-opened');
        var modalId = $(this).attr('href');
        $(modalId).fadeIn();

        $("html, body").animate({scrollTop: 0}, "slow");
    });

    $('.viral-pro-modal-back, .viral-pro-modal-cancel').on('click', function (e) {
        $('body').removeClass('viral-pro-modal-opened');
        $('.viral-pro-modal').hide();
        $("html, body").animate({scrollTop: 0}, "slow");
    });

    $('body').on('click', '.viral-pro-import-demo', function () {
        var $el = $(this);
        var demo = $(this).attr('data-demo-slug');
        var reset = $('#checkbox-reset-' + demo).is(':checked');
        var reset_message = '';

        if (reset) {
            reset_message = viral_pro_ajax_data.reset_database;
            var confirm_message = 'Are you sure to proceed? Resetting the database will delete all your contents.';
        } else {
            var confirm_message = 'Are you sure to proceed?';
        }

        $import_true = confirm(confirm_message);
        if ($import_true == false)
            return;

        $("html, body").animate({scrollTop: 0}, "slow");

        $('#viral-pro-modal-' + demo).hide();
        $('#viral-pro-import-progress').show();

        $('#viral-pro-import-progress .viral-pro-import-progress-message').html(viral_pro_ajax_data.prepare_importing).fadeIn();

        var info = {
            demo: demo,
            reset: reset,
            next_step: 'viral_pro_install_demo',
            next_step_message: reset_message
        };

        setTimeout(function () {
            do_ajax(info);
        }, 2000);
    });

    function do_ajax(info) {
        console.log(info);
        if (info.next_step) {
            var data = {
                action: info.next_step,
                demo: info.demo,
                reset: info.reset,
                security: viral_pro_ajax_data.nonce
            };

            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: data,
                beforeSend: function () {
                    if (info.next_step_message) {
                        $('#viral-pro-import-progress .viral-pro-import-progress-message').hide().html('').fadeIn().html(info.next_step_message);
                    }
                },
                success: function (response) {
                    var info = JSON.parse(response);

                    if (!info.error) {
                        if (info.complete_message) {
                            $('#viral-pro-import-progress .viral-pro-import-progress-message').hide().html('').fadeIn().html(info.complete_message);
                        }
                        setTimeout(function () {
                            do_ajax(info);
                        }, 4000);
                    } else {
                        $('#viral-pro-import-progress .viral-pro-import-progress-message').html(viral_pro_ajax_data.import_error);

                    }
                },
                error: function (xhr, status, error) {
                    var errorMessage = xhr.status + ': ' + xhr.statusText
                    $('#viral-pro-import-progress .viral-pro-import-progress-message').html(viral_pro_ajax_data.import_error);
                    $('#viral-pro-import-progress').addClass('import-error');
                }
            });
        } else {
            $('#viral-pro-import-progress .viral-pro-import-progress-message').html(viral_pro_ajax_data.import_success);
            $('#viral-pro-import-progress').addClass('import-success');
        }
    }
})(jQuery);
