(function ($) {
    if ($('.hdi-tab-filter').length > 0) {
        $('.hdi-tab-group').each(function () {
            $(this).find('.hdi-tab:first').addClass('hdi-active');
        });

        // init Isotope
        var $grid = $('.hdi-demo-box-wrap').imagesLoaded(function () {
            $grid.isotope({
                itemSelector: '.hdi-demo-box',
            });
        });

        // store filter for each group
        var filters = {};

        $('.hdi-tab-group').on('click', '.hdi-tab', function (event) {
            var $button = $(event.currentTarget);
            // get group key
            var $buttonGroup = $button.parents('.hdi-tab-group');
            var filterGroup = $buttonGroup.attr('data-filter-group');
            // set filter for group
            filters[filterGroup] = $button.attr('data-filter');
            // combine filters
            var filterValue = concatValues(filters);
            // set filter for Isotope
            $grid.isotope({filter: filterValue});
        });

        // change is-checked class on buttons
        $('.hdi-tab-group').each(function (i, buttonGroup) {
            var $buttonGroup = $(buttonGroup);
            $buttonGroup.on('click', '.hdi-tab', function (event) {
                $buttonGroup.find('.hdi-active').removeClass('hdi-active');
                var $button = $(event.currentTarget);
                $button.addClass('hdi-active');
            });
        });

        // flatten object by concatting values
        function concatValues(obj) {
            var value = '';
            for (var prop in obj) {
                value += obj[prop];
            }
            return value;
        }
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
        var excludeImages = $('#checkbox-exclude-image-' + demo).is(':checked');
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
            excludeImages: excludeImages,
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
                excludeImages: info.excludeImages,
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
                        }, 2000);
                    } else {
                        $('#hdi-import-progress .hdi-import-progress-message').html(info.error_message);
                        $('#hdi-import-progress').addClass('import-error');

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
