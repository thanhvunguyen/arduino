var IOT_ADMIN = IOT_ADMIN || {};

(function () {
    IOT_ADMIN.Time_line_current_collection = Backbone.Collection.extend({

        model: IOT_ADMIN.Time_line_model,

        url: '',

        /**
         * Init
         */
        initialize: function () {

        },

        parse: function (res) {
            return res.result;
        }
    })
})();

