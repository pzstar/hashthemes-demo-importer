(function ($) {
    // Set once the import reaches an end state, so a late failure cannot
    // overwrite the success screen and the other way round.
    var import_finished = false;

    // Settings for the run in progress, kept here so a retry can repeat it.
    var current_import = null;

    // The full chain, used to work out how far along the import is. Defined in
    // php so the checklist, the bar and the cli command agree on the order. The
    // slider step is skipped when a demo does not ship one, so the bar can jump.
    var steps = $.map(hdi_ajax_data.import_steps || [], function (step) {
        return step.action;
    });

    /* ---------- Demo grid: filters and search ---------- */

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
        var search_term = '';

        /**
         * Isotope takes either a selector or a function. Both the tab filters
         * and the search box have to agree, so combine them into one test.
         */
        function apply_filters() {
            var selector = concatValues(filters);

            if (!search_term) {
                $grid.isotope({filter: selector || '*'});
                return;
            }

            $grid.isotope({
                filter: function () {
                    var $item = $(this);

                    if (selector && !$item.is(selector)) {
                        return false;
                    }

                    return $item.find('h4').text().toLowerCase().indexOf(search_term) > -1;
                }
            });
        }

        $('.hdi-tab-group').on('click', '.hdi-tab', function (event) {
            var $button = $(event.currentTarget);
            // get group key
            var $buttonGroup = $button.parents('.hdi-tab-group');
            var filterGroup = $buttonGroup.attr('data-filter-group');
            // set filter for group
            filters[filterGroup] = $button.attr('data-filter');
            apply_filters();
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

        var search_timer = null;

        $('#hdi-demo-search-input').on('input', function () {
            var value = $(this).val().toLowerCase().trim();

            // Relayouting on every keystroke is visibly janky with many demos.
            clearTimeout(search_timer);
            search_timer = setTimeout(function () {
                search_term = value;
                apply_filters();
            }, 200);
        });

        // flatten object by concatting values
        function concatValues(obj) {
            var value = '';
            for (var prop in obj) {
                if (obj[prop] && obj[prop] !== '*') {
                    value += obj[prop];
                }
            }
            return value;
        }
    }

    /* ---------- Modal ---------- */

    $('.hdi-modal-button').on('click', function (e) {
        e.preventDefault();
        $('body').addClass('hdi-modal-opened');
        var modalId = $(this).attr('href');
        var $modal = $(modalId);
        $modal.fadeIn();

        run_preflight($modal);

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

    /* ---------- Pre-flight ---------- */

    /**
     * The server side checks are rendered with the page. Only the demo package
     * needs a request, so it is asked for once per modal, when it opens.
     */
    function run_preflight($modal) {
        var $preflight = $modal.find('.hdi-preflight');

        if (!$preflight.length || $preflight.data('checked')) {
            return;
        }

        $preflight.data('checked', true);

        $.post(ajaxurl, {
            action: 'hdi_preflight',
            demo: $preflight.attr('data-demo-slug'),
            security: hdi_ajax_data.nonce
        }).done(function (response) {
            var result = parse_response(response);

            if (!result || !result.checks) {
                render_remote_check($modal, null);
                return;
            }

            $.each(result.checks, function (i, check) {
                render_remote_check($modal, check);
            });
        }).fail(function () {
            render_remote_check($modal, null);
        });
    }

    function render_remote_check($modal, check) {
        var $row = $modal.find('.hdi-check-remote');

        if (!check) {
            // The check itself failed, which says nothing about the package.
            $row.removeClass('hdi-check-pending').addClass('hdi-check-warning');
            $row.find('.hdi-check-value').text('—');
            return;
        }

        $row.removeClass('hdi-check-pending hdi-check-ok hdi-check-warning hdi-check-error')
                .addClass('hdi-check-' + check.status);
        $row.find('.hdi-check-value').text(check.value);
        $row.find('.hdi-check-message').text(check.status === 'ok' ? '' : check.message);
    }

    /**
     * Only a hard error blocks the import: a missing package, or an uploads
     * folder that cannot be written to. Warnings are advice, not a wall.
     */
    function preflight_blocked($modal) {
        return $modal.find('.hdi-preflight .hdi-check-error').length > 0;
    }

    /* ---------- Import ---------- */

    $('body').on('click', '.hdi-import-demo', function () {
        var demo = $(this).attr('data-demo-slug');
        var $modal = $('#hdi-modal-' + demo);
        var reset = $('#checkbox-reset-' + demo).is(':checked');
        var excludeImages = $('#checkbox-exclude-image-' + demo).is(':checked');
        var parts = [];

        $modal.find('.hdi-import-part:checked').each(function () {
            parts.push($(this).val());
        });

        if (!parts.length) {
            window.alert(hdi_ajax_data.no_parts_selected);
            return;
        }

        if (preflight_blocked($modal)) {
            window.alert(hdi_ajax_data.preflight_blocked);
            return;
        }

        if (!confirm(reset ? hdi_ajax_data.confirm_reset_import : hdi_ajax_data.confirm_import)) {
            return;
        }

        current_import = {
            demo: demo,
            reset: reset,
            excludeImages: excludeImages,
            parts: parts.join(','),
            // A retry after the reset already ran must not wipe the site twice.
            reset_done: false
        };

        $("html, body").animate({scrollTop: 0}, "slow");

        var image = $('#' + demo).find('img').attr('src');
        var title = $('#' + demo).find('h4').text();

        $modal.hide();

        $('.hdi-import-preview').find('img').attr('src', image);
        $('.hdi-import-preview').find('h4').html(title);

        start_import();
    });

    $('body').on('click', '.hdi-import-retry', function () {
        if (!current_import) {
            return;
        }

        start_import();
    });

    $('body').on('click', '.hdi-import-details-toggle', function () {
        var $details = $('.hdi-import-details');
        var shown = $details.is(':visible');

        $details.toggle(!shown);
        $(this).text(shown ? hdi_ajax_data.show_details : hdi_ajax_data.hide_details);
    });

    function start_import() {
        import_finished = false;

        $('#hdi-import-progress').removeClass('import-error import-success').show();
        $('.hdi-import-actions').hide();
        reset_steps();
        $('.hdi-import-details').hide().text('');
        $('.hdi-import-details-toggle').text(hdi_ajax_data.show_details);

        // Clear the high water mark, or a retry would start at the percentage
        // the failed run reached.
        $('.hdi-import-progress-bar-fill').attr('data-percent', 0);
        set_progress(0);

        $('#hdi-import-progress .hdi-import-progress-message').html(hdi_ajax_data.prepare_importing).fadeIn();

        setTimeout(function () {
            do_ajax({
                next_step: 'hdi_install_demo',
                next_step_message: current_import.reset ? hdi_ajax_data.reset_database : ''
            });
        }, 2000);
    }

    /**
     * Marks one step in the checklist. Status is pending, current, done,
     * skipped or failed.
     */
    function set_step_status(action, status) {
        $('.hdi-import-step[data-step="' + action + '"]')
                .removeClass('hdi-step-pending hdi-step-current hdi-step-done hdi-step-skipped hdi-step-failed')
                .addClass('hdi-step-' + status);
    }

    function reset_steps() {
        $('.hdi-import-step')
                .removeClass('hdi-step-current hdi-step-done hdi-step-skipped hdi-step-failed')
                .addClass('hdi-step-pending');
    }

    /**
     * Anything still pending before the step now running was jumped over by the
     * server, which happens when a demo ships no slider.
     */
    function skip_steps_before(index) {
        for (var i = 0; i < index; i++) {
            var $step = $('.hdi-import-step[data-step="' + steps[i] + '"]');

            if ($step.hasClass('hdi-step-pending')) {
                set_step_status(steps[i], 'skipped');
            }
        }
    }

    /**
     * Draws the bar and the "Step x of y" line from the step about to run.
     */
    function set_progress(index) {
        var percent = Math.round((index / steps.length) * 100);
        var $bar = $('.hdi-import-progress-bar-fill');
        var current = parseInt($bar.attr('data-percent') || '0', 10);

        // Never let the bar move backwards.
        percent = Math.max(percent, current);

        $bar.attr('data-percent', percent).css('width', percent + '%');

        if (index > 0 && hdi_ajax_data.step_counter) {
            $('.hdi-import-progress-step').text(
                    hdi_ajax_data.step_counter.replace('%1$s', index).replace('%2$s', steps.length)
                    );
        } else {
            $('.hdi-import-progress-step').text('');
        }
    }

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

        // Whichever step was running is the one that broke.
        $('.hdi-import-step.hdi-step-current').each(function () {
            set_step_status($(this).attr('data-step'), 'failed');
        });

        $('#hdi-import-progress .hdi-import-progress-message').stop(true, true).html(message || hdi_ajax_data.import_error);
        $('#hdi-import-progress').removeClass('import-success').addClass('import-error');
        $('.hdi-import-actions').show();

        load_import_log();
    }

    function import_succeeded() {
        import_finished = true;

        set_progress(steps.length);
        $('#hdi-import-progress .hdi-import-progress-message').stop(true, true).html(hdi_ajax_data.import_success);
        $('#hdi-import-progress').addClass('import-success');
        $('.hdi-import-actions').hide();
    }

    /**
     * Pulls whatever the importers printed during the run, so the failure
     * screen can show something more useful than "it failed".
     */
    function load_import_log() {
        $.post(ajaxurl, {
            action: 'hdi_import_log',
            security: hdi_ajax_data.nonce
        }).done(function (response) {
            var result = parse_response(response);

            $('.hdi-import-details').text(result && result.log ? result.log : hdi_ajax_data.no_details);
        }).fail(function () {
            $('.hdi-import-details').text(hdi_ajax_data.no_details);
        });
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
        if (import_finished || !current_import) {
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

        var step_index = $.inArray(info.next_step, steps);

        if (step_index > -1) {
            set_progress(step_index + 1);
            skip_steps_before(step_index);
            set_step_status(info.next_step, 'current');
        }

        var data = {
            action: info.next_step,
            demo: current_import.demo,
            // Once the reset has run, a retry must not run it again.
            reset: current_import.reset && !current_import.reset_done,
            excludeImages: current_import.excludeImages,
            parts: current_import.parts,
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

                set_step_status(data.action, result.skipped ? 'skipped' : 'done');

                if (data.action === 'hdi_install_demo') {
                    current_import.reset_done = current_import.reset;
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
