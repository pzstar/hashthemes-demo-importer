(function ($) {
    // Set once the import reaches an end state, so a late failure cannot
    // overwrite the success screen and the other way round.
    var import_finished = false;

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
                $buttonGroup.find('.hdi-active').removeClass('hdi-active').attr('aria-pressed', 'false');
                var $button = $(event.currentTarget);
                $button.addClass('hdi-active').attr('aria-pressed', 'true');
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

    function close_modal() {
        $('body').removeClass('hdi-modal-opened');
        $('.hdi-modal').hide();
        $("html, body").animate({scrollTop: 0}, "slow");
    }

    $('.hdi-modal-back, .hdi-modal-cancel').on('click', function (e) {
        close_modal();
    });

    $(document).on('keydown', function (e) {
        // Not once the import has started: the progress screen relies on the
        // same body class to keep the demo grid out of the way.
        if (e.key === 'Escape' && $('body').hasClass('hdi-modal-opened') && !$('#hdi-import-progress').is(':visible')) {
            close_modal();
        }
    });

    $('body').on('click', '.hdi-import-demo', function () {
        var $el = $(this);
        var demo = $(this).attr('data-demo-slug');
        var reset = $('#checkbox-reset-' + demo).is(':checked');
        var excludeImages = $('#checkbox-exclude-image-' + demo).is(':checked');
        var reset_message = '';
        var confirm_message = hdi_ajax_data.confirm_import;

        if (reset) {
            reset_message = hdi_ajax_data.reset_database;
            confirm_message = hdi_ajax_data.confirm_reset_import;
        }

        if (!confirm(confirm_message)) {
            return;
        }

        $("html, body").animate({scrollTop: 0}, "slow");


        var image = $('#' + demo).find('img').attr('src');
        var title = $('#' + demo).find('h4').text();

        $('#hdi-modal-' + demo).hide();

        import_finished = false;

        $('.hdi-import-preview').find('img').attr('src', image);
        $('.hdi-import-preview').find('h4').html(title);
        $('#hdi-import-progress').removeClass('import-error import-success').show();

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

    /**
     * Every way an import can end badly lands here: a dead connection, a
     * timeout, a 500 from a php fatal, a nonce failure, output that is not the
     * json we expect, or a step that simply stops without saying what is next.
     */
    function import_failed(message) {
        if (import_finished) {
            return;
        }

        import_finished = true;

        $('#hdi-import-progress .hdi-import-progress-message').stop(true, true).html(message || hdi_ajax_data.import_error);
        $('#hdi-import-progress').removeClass('import-success').addClass('import-error');
    }

    function import_succeeded() {
        import_finished = true;

        $('#hdi-import-progress .hdi-import-progress-message').stop(true, true).html(hdi_ajax_data.import_success);
        $('#hdi-import-progress').addClass('import-success');
    }

    /**
     * The steps echo raw json. A php notice or a plugin printing output ends up
     * in front of it, so fall back to reading from the first brace before
     * giving up. Returns null when there is nothing usable.
     */
    function parse_response(response) {
        if (response && typeof response === 'object') {
            return response;
        }

        if (typeof response !== 'string') {
            return null;
        }

        try {
            return JSON.parse(response);
        } catch (e) {
        }

        var brace = response.indexOf('{');

        if (brace > -1) {
            try {
                return JSON.parse(response.slice(brace));
            } catch (e) {
            }
        }

        return null;
    }

    function do_ajax(info) {
        if (import_finished) {
            return;
        }

        // The last step reports back with no next step and completed set.
        if (!info.next_step) {
            if (info.completed) {
                import_succeeded();
            } else {
                import_failed();
            }
            return;
        }

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
            // Importing content is slow, but no single step runs for a quarter
            // of an hour. Past that the request is hung, and without a ceiling
            // neither callback ever fires and the screen spins forever.
            timeout: 900000,
            beforeSend: function () {
                if (info.next_step_message) {
                    $('#hdi-import-progress .hdi-import-progress-message').hide().html('').fadeIn().html(info.next_step_message);
                }
            },
            success: function (response) {
                var result = parse_response(response);

                // Covers "0" and "-1" from admin-ajax as well as html error
                // pages served with a 200.
                if (!result || typeof result !== 'object') {
                    import_failed();
                    return;
                }

                if (result.error) {
                    import_failed(result.error_message);
                    return;
                }

                if (result.complete_message) {
                    $('#hdi-import-progress .hdi-import-progress-message').hide().html('').fadeIn().html(result.complete_message);
                }

                setTimeout(function () {
                    do_ajax(result);
                }, 2000);
            },
            error: function (xhr, status) {
                // status is 'timeout', 'abort', 'error' or 'parsererror'. All of
                // them mean the demo did not finish importing.
                import_failed();
            }
        });
    }
})(jQuery);
