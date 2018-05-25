var IOT_ADMIN = IOT_ADMIN || {};

(function () {
    IOT_ADMIN.Time_line_collection = Backbone.Collection.extend({

        model: IOT_ADMIN.Time_line_model,

        url: '',

        loading : false,
        last_time_get_ticket: null,

        zone: 'Asia/Tokyo',

        /**
         * Init
         */
        initialize: function (option) {

        },

        parse: function (res) {
            this.last_time_get_ticket = res.result.last_time_get_ticket;

            var time = moment().tz(this.zone).format('YYYY-MM-DD H:mm:ss');
            var time_now = window.ticket.init_time_session(time);

            $.each(res.result.time_lines, function (key, value) {

                window.ticket.add_time_line_now(time_now);

                if (value.time_line_block == window.ticket.time_current_block) {
                    value.is_real_time = 1;
                } else {
                    value.is_real_time = 0;
                }
            });

            return res.result.time_lines;
        }
    })
})();

