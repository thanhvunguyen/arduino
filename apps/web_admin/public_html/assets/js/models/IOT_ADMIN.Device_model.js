var IOT_ADMIN = IOT_ADMIN || {};
(function () {

    IOT_ADMIN.Device_model = Backbone.Model.extend({

        /**
         * Init
         */
        initialize: function () {

        },

        /**
         * Parse data
         *
         * @param response
         *
         * @returns {*}
         */
        parse: function (response) {
            response.id = response.device_id;
            response.num_gate_pass = (response.num_gate_pass) ? eval(response.num_gate_pass) : 0;
            response.num_error = (response.num_error) ? eval(response.num_error) : 0;
            response.status = (response.status) ? eval(response.status) : 0;

            return response;
        }
    });

})();