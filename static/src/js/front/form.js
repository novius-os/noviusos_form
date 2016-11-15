(function(window, document, undefined) {

    // Loads the requirements if not already loaded before executing the form script
    loadRequirements([
        {
            url: 'static/apps/noviusos_form/dist/vendor/jquery.min.js',
            check: function () {
                return typeof window.jQuery !== 'undefined';
            }
        },
        {
            url: 'static/apps/noviusos_form/dist/vendor/parsley.min.js',
            check: function () {
                return typeof $.fn.parsley !== 'undefined';
            }
        },
    ], function() {
        init(jQuery);
    });

    /**
     * Initializes the form UI
     *
     * @param $
     */
    function init($) {
        $(function() {

            $('.noviusos_form form.nos-form-layout').each(function() {
                var $form = $(this);
                var $captcha = $form.find('#form_captcha');
                var good_anwser = $captcha.size() ? $captcha.data('captcha').split('-')[1] : null;

                // Sets the parsley locale
                window.Parsley.setLocale($form.data('locale') || 'en');

                $captcha.on('blur', function() {
                    if ($(this).val() != good_anwser) {
                        this.setCustomValidity($captcha.data('customValidity'));
                    } else {
                        this.setCustomValidity('');
                    }
                });

                var $sections = $form.find('.page_break');
                var $controls = $form.find('.page_break_control');

                function navigateTo(index) {
                    if (index < 0) {
                        index = 0;
                    }
                    // Mark the current section with the class 'current'
                    $sections
                        .removeClass('current')
                        .eq(index)
                        .addClass('current');
                    // Show only the navigation buttons that make sense for the current section:
                    $controls.find('.page_break_previous').toggle(index > 0);
                    var atTheEnd = index >= $sections.length - 1;
                    $controls.find('.page_break_next').toggle(!atTheEnd);
                    $controls.find('[type=submit]').toggle(atTheEnd);
                    $controls.find('#progress').val(index + 1) ;
                    $controls.find('.page_break_current').text(index + 1);
                }

                function curIndex() {
                    // Return the current index by looking at which section has the class 'current'
                    return $sections.index($sections.filter('.current'));
                }

                // Previous button is easy, just go back
                $('.page_break_control .page_break_previous').click(function(event) {
                    event.preventDefault();
                    navigateTo(curIndex() - 1);
                });

                // Next button goes forward iff current block validates
                $('.page_break_control .page_break_next').click(function() {
                    if ($form.parsley().validate({group: 'block-' + curIndex()}))
                        navigateTo(curIndex() + 1);
                });

                // Prepare sections by setting the `data-parsley-group` attribute to 'block-0', 'block-1', etc.
                $sections.each(function(index, section) {
                    $(section).find(':input').attr('data-parsley-group', 'block-' + index);
                });

                // Navigates to default page
                var defaultPage = $sections.filter('.current').index() - 1;
                navigateTo(defaultPage);
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
        (function(d,t){
            var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
            g.src = src;
            g.onload = callback;
            g.onreadystatechange = function() { // IE 6-7-8
                if (this.readyState == 'complete' || this.readyState == 'loaded') {
                    callback();
                }
            };
            s.parentNode.insertBefore(g,s)
        }(document,'script'));
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
