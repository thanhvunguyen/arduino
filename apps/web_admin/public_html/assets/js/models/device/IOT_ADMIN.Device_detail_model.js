var IOT_ADMIN = IOT_ADMIN || {};
(function () {

    IOT_ADMIN.Device_detail_model = Backbone.Model.extend({

        /**
         * Parse data
         *
         * @param response
         *
         * @returns {*}
         */
        parse: function (response) {
            return response;
        }
    });

})();