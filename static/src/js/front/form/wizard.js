/**
 * The form wizard
 *
 * Uses Parsley to validate groups of fields (http://parsleyjs.org/)
 */
(function() {

    /**
     * Wizard constructor
     */
    function NosFormWizard($form, options) {
        this.$form = $form;
        this.options = $.extend(true, {
            currentPageClassName: 'current',
            selectors: {
                sections: '.form-fields-group',
                controls: '.wizard-controls',
                controlPrevious: '.wizard-control-previous',
                controlNext: '.wizard-control-next',
                controlCurrentPage: '.wizard-control-current-page',
                controlProgress: '.wizard-control-progress',
                controlSubmit: '.wizard-control-submit[type=submit]',
            },
        }, options);
    }

    /**
     * Initializes the wizard
     */
    NosFormWizard.prototype.init = function() {
        var self = this;

        // Sets the parsley locale
        window.Parsley.setLocale(this.$form.data('locale') || 'en');

        // Initializes the sections
        this.getSections().each(function(index, section) {
            // Sets the block index on the section
            $(section).find(':input').attr('data-parsley-group', 'block-'+index);
        });

        // Initializes the controls
        var $controls = this.getControls();
        if (this.options.selectors.controlPrevious) {
            // Previous button is easy, just go back
            $controls.find(this.options.selectors.controlPrevious).click(function(event) {
                event.preventDefault();
                var currentPage = self.getCurrentPage();
                if (currentPage > 0) {
                    self.navigateTo(currentPage - 1);
                }
            });
        }
        if (this.options.selectors.controlNext) {
            // Next button goes forward if current block validates
            $controls.find(this.options.selectors.controlNext).click(function(event) {
                event.preventDefault();
                var currentPage = self.getCurrentPage();
                if (self.$form.parsley().validate({group: 'block-' + currentPage})) {
                    self.navigateTo(currentPage + 1);
                }
            });
        }

        // Navigates to the default page
        this.navigateTo(this.getCurrentPage());

        return true;
    };

    /**
     * Navigates to the specified page
     *
     * @param page
     */
    NosFormWizard.prototype.navigateTo = function(page) {
        page = page > 0 ? parseInt(page) : 0;

        var $sections = this.getSections();
        var $controls = this.getControls();
        var atTheEnd = page >= $sections.length - 1;

        // Marks the current section with the class 'current'
        $sections
            .removeClass(this.options.currentPageClassName)
            .eq(page)
            .addClass(this.options.currentPageClassName);

        // Shows only the navigation buttons that make sense for the current section
        if (this.options.selectors.controlPrevious) {
            $controls.find(this.options.selectors.controlPrevious).toggle(page > 0);
        }
        if (this.options.selectors.controlNext) {
            $controls.find(this.options.selectors.controlNext).toggle(!atTheEnd);
        }
        if (this.options.selectors.controlSubmit) {
            $controls.find(this.options.selectors.controlSubmit).toggle(atTheEnd);
        }
        if (this.options.selectors.controlProgress) {
            $controls.find(this.options.selectors.controlProgress).val(page + 1);
        }
        if (this.options.selectors.controlCurrentPage) {
            $controls.find(this.options.selectors.controlCurrentPage).text(page + 1);
        }
    };

    /**
     * Gets the current page
     *
     * @returns {*}
     */
    NosFormWizard.prototype.getCurrentPage = function() {
        var $sections = this.getSections();
        // Return the current page by looking at which section has the current page class
        return $sections.index($sections.filter('.'+this.options.currentPageClassName));
    };

    /**
     * Gets the sections
     *
     * @returns {*}
     */
    NosFormWizard.prototype.getSections = function() {
        return this.$form.find(this.options.selectors.sections);
    };

    /**
     * Gets the controls
     *
     * @returns {*}
     */
    NosFormWizard.prototype.getControls = function() {
        return this.$form.find(this.options.selectors.controls);
    };

    // Global register
    window.NosFormWizard = NosFormWizard;
})();
