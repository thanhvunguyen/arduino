var IOT_ADMIN = IOT_ADMIN || {};
(function () {

    IOT_ADMIN.Seat_model = Backbone.Model.extend({

        /**
         * Parse data
         *
         * @param response
         *
         * @returns {*}
         */
        parse: function (response) {
            if (response.result !== undefined) {
                return response.result;
            }

            return response;
        }
    });

})();