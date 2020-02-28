(function ($) {

    if ($('.hdi-tab-filter').length > 0) {

        var first_class = $('.hdi-tag-tab:first').data('filter');
        $('.hdi-tag-tab:first').addClass('hdi-active');

        var $container = $('.hdi-demo-box-wrap').imagesLoaded(function () {
            $container.isotope({
                itemSelector: '.hdi-demo-box',
                filter: first_class
            });
        });

        $('.hdi-tab-filter').on('click', '.hdi-tag-tab', function () {
            var filterValue = $(this).attr('data-filter');
            $container.isotope({filter: filterValue});
            $('.hdi-tag-tab').removeClass('hdi-active');
            $(this).addClass('hdi-active');
        });

    }

    $('.hdi-modal-button').on('click', function (e) {
        e.preventDefault();
        $('body').addClass('hdi-modal-opened');
        var modalId = $(this).attr('href');
        $(modalId).fadeIn();

        $("html, body").animate({scrollTop: 0}, "slow");
    });

    $('.hdi-modal-back, .hdi-modal-cancel').on('click', function (e) {
        $('body').removeClass('hdi-modal-opened');
        $('.hdi-modal').hide();
        $("html, body").animate({scrollTop: 0}, "slow");
    });

    $('body').on('click', '.hdi-import-demo', function () {
        var $el = $(this);
        var demo = $(this).attr('data-demo-slug');
        var reset = $('#checkbox-reset-' + demo).is(':checked');
        var reset_message = '';

        if (reset) {
            reset_message = hdi_ajax_data.reset_database;
            var confirm_message = 'Are you sure to proceed? Resetting the database will delete all your contents.';
        } else {
            var confirm_message = 'Are you sure to proceed?';
        }

        $import_true = confirm(confirm_message);
        if ($import_true == false)
            return;

        $("html, body").animate({scrollTop: 0}, "slow");

        $('#hdi-modal-' + demo).hide();
        $('#hdi-import-progress').show();

        $('#hdi-import-progress .hdi-import-progress-message').html(hdi_ajax_data.prepare_importing).fadeIn();

        var info = {
            demo: demo,
            reset: reset,
            next_step: 'hdi_install_demo',
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
                security: hdi_ajax_data.nonce
            };

            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: data,
                beforeSend: function () {
                    if (info.next_step_message) {
                        $('#hdi-import-progress .hdi-import-progress-message').hide().html('').fadeIn().html(info.next_step_message);
                    }
                },
                success: function (response) {
                    var info = JSON.parse(response);

                    if (!info.error) {
                        if (info.complete_message) {
                            $('#hdi-import-progress .hdi-import-progress-message').hide().html('').fadeIn().html(info.complete_message);
                        }
                        setTimeout(function () {
                            do_ajax(info);
                        }, 4000);
                    } else {
                        $('#hdi-import-progress .hdi-import-progress-message').html(hdi_ajax_data.import_error);

                    }
                },
                error: function (xhr, status, error) {
                    var errorMessage = xhr.status + ': ' + xhr.statusText
                    $('#hdi-import-progress .hdi-import-progress-message').html(hdi_ajax_data.import_error);
                    $('#hdi-import-progress').addClass('import-error');
                }
            });
        } else {
            $('#hdi-import-progress .hdi-import-progress-message').html(hdi_ajax_data.import_success);
            $('#hdi-import-progress').addClass('import-success');
        }
    }
})(jQuery);
