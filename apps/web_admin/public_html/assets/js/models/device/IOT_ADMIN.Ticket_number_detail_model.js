var IOT_ADMIN = IOT_ADMIN || {};
(function () {

    IOT_ADMIN.Ticket_number_detail_model = Backbone.Model.extend({

        /**
         * Parse data
         *
         * @param response
         *
         * @returns {*}
         */
        parse: function (response) {
            response.num_gate_pass = (response.num_gate_pass) ? eval(response.num_gate_pass) : 0;
            response.num_error = (response.num_error) ? eval(response.num_error) : 0;
            response.status = (response.status) ? eval(response.status) : 0;
            response.total = (response.total) ? eval(response.total) : 0;

            return response;
        }
    });

})();