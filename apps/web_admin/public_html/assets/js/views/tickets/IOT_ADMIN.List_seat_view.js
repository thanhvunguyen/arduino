var IOT_ADMIN = IOT_ADMIN || {};

(function () {

    /**
     * ET.Service Class for service page
     *
     * @type {void|*}
     */
    IOT_ADMIN.List_seat_view = Backbone.View.extend({

        el: '',

        num_of_device: 0,

        kogyo_code: null,
        kogyo_sub_code: null,
        koen_code: null,

        /**
         * Constructor
         */
        initialize: function (options) {

            this.kogyo_code = options.kogyo_code;
            this.kogyo_sub_code = options.kogyo_sub_code;
            this.koen_code = options.koen_code;

            this.list_seat_el = $('#list-seat');

            this.render();
        },

        /**
         * Render view
         *
         * @returns {IOT_ADMIN.List_seat_view}
         *
         * @author <hieunt1@nal.vn>
         */
        render: function () {
            var me = this;

            this.seat_collection = new IOT_ADMIN.Seat_collection();

            this.listenTo(this.seat_collection, 'add', this.add_seat);

            return this;
        },

        /**
         * Add seat
         *
         * @param model
         *
         * @author <hieunt1@nal.vn>
         */
        add_seat: function (model) {
            model.set({id: this.hash_code(model.get('seat'))});

            var seat_view = new IOT_ADMIN.Seat_view({
                model: model
            });

            this.list_seat_el.slick('slickAdd', seat_view.el);
        },

        /**
         * Set seat
         *
         * @param seat_id
         * @param data
         *
         * @author <hieunt1@nal.vn>
         */
        set_seat: function (seat_id, data) {
            var seat = this.seat_collection.findWhere({id: seat_id});

            if (typeof (seat) == 'undefined') {
                return;
            }

            var num_gate_pass = seat.get('num_gatepass') + data.num_gatepass;
            var percent = num_gate_pass / seat.get('total') * 100;

            seat.set({
                num_gatepass: num_gate_pass,
                percent: percent
            });
        },

        /**
         * @param string
         *
         * @returns {number}
         */
        hash_code: function(string) {
            var hash = 0, i, chr;
            if (string.length === 0) return hash;
            for (i = 0; i < string.length; i++) {
                chr   = string.charCodeAt(i);
                hash  = ((hash << 5) - hash) + chr;
                hash |= 0; // Convert to 32bit integer
            }
            return hash;
        }
    })
})();