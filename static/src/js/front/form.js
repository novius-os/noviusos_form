(function (window, document, undefined) {

    // Loads the requirements if not already loaded before executing the form script
    loadRequirements([
        {
            url  : 'static/apps/noviusos_form/dist/vendor/jquery.min.js',
            check: function () {
                return typeof window.jQuery !== 'undefined';
            }
        },
        {
            url  : 'static/apps/noviusos_form/dist/vendor/parsley.min.js',
            check: function () {
                return typeof $.fn.parsley !== 'undefined';
            }
        }
    ], function () {
        init(jQuery);
    });

    /**
     * Initializes the form UI
     *
     * @param $
     */
    function init($) {
        $(function () {

            $('.noviusos_form form.nos-form-layout').each(function () {
                var $form = $(this);

                // @todo make it work again... or not
                // // Initializes the captcha field
                // var $captcha = $form.find('#form_captcha');
                // var good_anwser = $captcha.size() ? $captcha.data('captcha').split('-')[1] : null;
                // $captcha.on('blur', function() {
                //     if ($(this).val() != good_anwser) {
                //         this.setCustomValidity($captcha.data('customValidity'));
                //     } else {
                //         this.setCustomValidity('');
                //     }
                // });

                // Initializes the wizard if more than one group of fields
                if ($form.find('.form-fields-group').length > 1) {
                    // Checks if the Wizard module is loaded
                    if (typeof window.NosFormWizard !== 'undefined') {
                        var wizard = new NosFormWizard($form);
                        wizard.init();
                    } else if (console) {
                        console.warn("Can't initialize the form wizard: Module NosFormWizard not found.");
                    }
                }

                // Initializes the conditional fields
                if (typeof window.NosFormCondition !== 'undefined') {
                    var params = {};
                    if ($form.attr('data-conditions') !== '') {
                        params = {
                            fields: JSON.parse($form.attr('data-conditions'))
                        };
                    }
                    var conditions = new NosFormCondition($form, params);
                    conditions.init();
                } else if (console) {
                    console.warn("Can't initialize the form conditions: Module NosFormCondition not found.");
                }
            });
        });
    }

    /**
     * Adds the script and then execute the callback
     *
     * @param src
     * @param callback
     */
    function addScript(src, callback) {
        // HTML5 Boilerplate Google Analytics loader
        (function (d, t) {
            var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
            g.src = src;
            g.onload = callback;
            g.onreadystatechange = function () { // IE 6-7-8
                if (this.readyState == 'complete' || this.readyState == 'loaded') {
                    callback();
                }
            };
            s.parentNode.insertBefore(g, s)
        }(document, 'script'));
    }

    /**
     * Loads the requirements and then execute the callback
     *
     * @param requirements
     * @param callback
     */
    function loadRequirements(requirements, callback) {
        if (!requirements.length) {
            callback();
        } else {
            var requirement = requirements.shift();
            if (requirement.check()) {
                loadRequirements(requirements, callback);
            } else {
                addScript(requirement.url, function () {
                    loadRequirements(requirements, callback);
                });
            }
        }
    }

})(window, document);
