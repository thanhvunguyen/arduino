AppCore = window.AppCore || {};
var IOT_ADMIN = IOT_ADMIN || {};

(function () {

    /**
     * ETYPING.Base Class for all pages
     *
     * @type {void|*}
     */
    IOT_ADMIN.Base = AppCore.Base.extend({

        el: 'body',

        events: {

        },

        /**
         * Constructor
         */
        initialize: function (config) {
            this.loader();
        }
    });

})();
