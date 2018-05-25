var IOT_ADMIN = IOT_ADMIN || {};
(function () {

    IOT_ADMIN.Time_line_model = Backbone.Model.extend({
        /**
         * Parse response data
         * @param {object} response
         * @return {object}
         */
        parse: function (response) {
            return response.data ? response.data : response;
        }
    });

})();