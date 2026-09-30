/*
 * Total home sections: show a section's page controls or its repeater as soon as its
 * "Content From" switch changes. The preview reload that follows sets the same state
 * from the controls' active callbacks.
 */
(function (api, _) {
    api.bind('ready', function () {
        _.each(window.hdiTotalHomeSections || {}, function (section, typeId) {
            // The page control ids come as a PHP pattern: /.../
            var pages = new RegExp(section.pages.slice(1, -1));

            api(typeId, function (setting) {
                setting.bind(function (value) {
                    var useRepeater = 'repeater' === value;

                    api.control.each(function (control) {
                        if (pages.test(control.id)) {
                            control.active.set(!useRepeater);
                        }
                    });

                    api.control(section.repeater, function (control) {
                        control.active.set(useRepeater);
                    });
                });
            });
        });
    });
})(wp.customize, _);
