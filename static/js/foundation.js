(function(window, document, undefined) {

    var add_script = function (src, callback) {
        // HTML5 Boilerplate Google Analytics loader
        (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src=src;
            g.onload = callback;
            g.onreadystatechange = function() { // IE 6-7-8
                if (this.readyState == 'complete' || this.readyState == 'loaded') {
                    callback();
                }
            }
            s.parentNode.insertBefore(g,s)}(document,'script'));
    };
    var loadPolyfiller = function() {
        add_script('static/apps/noviusos_form/vendor/webshims-1.13/minified/polyfiller.js', function() {
            webshims.polyfill('forms forms-ext');

            doPagination(jQuery);
        });
    };
    var loadModernizr = function() {
        if (window.Modernizr) {
            loadPolyfiller();
        } else {
            add_script('static/apps/noviusos_form/vendor/webshims-1.13/minified/extras/modernizr-custom.js', loadPolyfiller);
        }
    };

    // Load 3 scripts each after the other one
    if (window.jQuery) {
        loadModernizr();
    } else {
        add_script('//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', loadModernizr);
    }

    function doPagination($) {
        $(function() {
            $('form.foundation').each(function() {
                var $form = $(this);
                var $submit_button = $form.find(':submit');

                var $captcha = $form.find('#form_captcha');
                var good_anwser = $captcha.size() ? $captcha.data('captcha').split('-')[1] : null;
                $captcha.on('blur', function() {
                    if ($(this).val() != good_anwser) {
                        this.setCustomValidity($captcha.data('customValidity'));
                    } else {
                        this.setCustomValidity('');
                    }
                });

                // Deal with multi-pages forms
                var $container = $form.find('.page_break_control');
                if ($container.length > 0) {
                    var $previous_link = $container.find('.page_break_previous');
                    var $next_button   = $container.find('.page_break_next');
                    var $current_label = $container.find('.page_break_current');
                    var $total_label   = $container.find('.page_break_total');
                    var $pages = $container.siblings('.page_break');
                    var $progress_field   = $container.find('#progress');

                    var current_page  = 0;
                    var $current_page = $pages.eq(current_page);
                    var total_pages   =  $pages.length;

                    $total_label.text(total_pages);

                    var update = function(diff) {
                        diff = diff || 0;
                        current_page += diff;
                        $current_page = $pages.eq(current_page);
                        $pages.not($current_page).hide();
                        $current_page.show();
                        if (diff != 0) {
                            window.scrollTo(0, $current_page[0].offsetTop);
                        }
                        $current_label.text(current_page + 1);
                        $previous_link[current_page == 0 ? 'hide' : 'show']();
                        $next_button[current_page == total_pages - 1 ? 'hide' : 'show']();
                        $submit_button[current_page == total_pages - 1 ? 'show' : 'hide']();
                        $progress_field.val(current_page + 1) ;
                    }

                    $previous_link.on('click', function(e) {
                        update(-1);
                        e.preventDefault();
                    });

                    $next_button.on('click', function(e) {
                        var valid = true;
                        $current_page.find(':input').each(function() {
                            valid = valid && this.checkValidity();
                        })
                        if (valid) {
                            update(1);
                        }
                        e.preventDefault();
                    });

                    update();
                    $container.show();
                }

                // Deal with validation error
                $form.on('firstinvalid', function(e) {
                    // Re-focus the appropriate page when using page breaks
                    var $invalid_page = $(e.target).closest('.page_break');
                    if ($invalid_page.length > 0) {
                        current_page = $pages.index($invalid_page);
                        update();
                    }
                    webshims.validityAlert.showFor(e.target);
                    return false;
                });
            });
        });
    }

})(window, document);
