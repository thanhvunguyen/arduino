AppCore = window.AppCore || {};
AppCore.Form = AppCore.Form || {};

(function () {

    AppCore.Form = AppCore.Base.extend({

        requireJavaScript: [
            '/third_party/ladda-bootstrap/dist/spin.min.js',
            '/third_party/ladda-bootstrap/dist/ladda.min.js'
        ],

        requireCSS: [
            '/third_party/ladda-bootstrap/dist/ladda-themeless.min.css'
        ],

        errorTemplate: _.template([
            '<div class="col-sm-offset-4 col-sm-8">',
                '<p class="text-danger help-text help-block"><%= error %></p>',
            '</div>'
        ].join('')),

        /**
         * Cancel Message and handle function
         */
        cancelMessage: 'このページを離れると、入力したデータが削除されます。\nよろしいですか？',
        cancelFunc: null,

        /**
         * Initialize
         * @param {Object} config
         */
        initialize: function (config) {
            config = config || {};

            this.loader();
            this.render(config);
        },

        /**
         * Destroy form
         */
        destroy: function () {
            if (this.cancelFunc) {
                $(window).un('beforeunload', this.cancelFunc);
            }
        },

        /**
         * Render
         *
         * @param {Object} config
         */
        render: function (config) {
            config = config || {};

            /**
             * Configure submit button for ladda
             */

            _.each(this.$('input[type=submit],button[type=submit]', this.$el), function(v) {
                var button = $(v);
                button.addClass('ladda-button');
                button.attr('data-style', 'expand-right');

                var span = $('<span />').addClass('ladda-label').html(button.html());
                button.empty().append(span);
            });

            /**
             * @type {boolean} Handling Cancel button is used right now
             */
            var isCanceling = true;

            /**
             * @type {Object} Handle cancel jquery object
             */
            var cancelLink = $('.form-cancel', this.$el);
            if (cancelLink) {
                var href = cancelLink.attr('href');
                cancelLink.attr('href', 'javascript:;').on('click', _.bind(function () {
                    this.confirm($.nl2br(this.cancelMessage), function (result) {
                        if (result === false) {
                            return;
                        }

                        isCanceling = true;
                        document.location.href = href;
                    });
                }, this));
            }

            this.cancelFunc = _.bind(function () {
                if (isCanceling === true) {
                    return this.cancelMessage;
                }
            }, this);

            // Run when reload page and move to other page (like history back)
            $(window).on('beforeunload', this.cancelFunc);

            // Tell form is dirty
            this.$('input, select, textarea').on('change', function () {
                isCanceling = false;
            });

            /** @namespace config.errorObject Error Object from CI */
            _.each(config.errorObject || {}, function (v, k) {
                var el = this.$([
                    'input[name='+k+']',
                    'textarea[name='+k+']',
                    'select[name='+k+']'
                ].join(','));

                var errorEl = $(this.errorTemplate({
                    error: v
                }));

                el.closest('.form-group')
                    .addClass('has-error').append(errorEl)
                    .on('change', function () {
                        $(this).removeClass('has-error');
                        $(errorEl).remove();
                    });

                isCanceling = false;
            }, this);

            this.$el.on('submit', _.bind(function () {

                if (!window.Ladda) {
                    return;
                }

                // if (button[0]) {
                //     //var l = Ladda.create(button[0]);
                //     //l.start();
                // }
            }, this));

            if(_.isFunction(config.ready)) {
                config.ready.call(this, error);
            }
        }
    });

})();

