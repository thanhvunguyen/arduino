var IOT_ADMIN = IOT_ADMIN || {};

(function () {
        IOT_ADMIN.Device_collection = Backbone.Collection.extend({

        model: IOT_ADMIN.Device_model,

        url: '/api/event/get_list_device',

        /**
         * Init
         */
        initialize: function () {

        },

        parse: function (res) {
            return res.result.items;
        }
    })
})();

