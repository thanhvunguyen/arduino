var IOT_ADMIN = IOT_ADMIN || {};

(function () {
    IOT_ADMIN.Seat_collection = Backbone.Collection.extend({

        model: IOT_ADMIN.Seat_model,

        url: '/api/ticket/get_list_seat',

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

