var IOT_ADMIN = IOT_ADMIN || {};

(function () {

    /**
     * IOT_ADMIN.Block_view Class for service page
     *
     * @type {void|*}
     */
    IOT_ADMIN.Seat_view = Backbone.View.extend({

        el: '',

        template: _.template($('#seat-template').html()),

        /**
         * Constructor
         */
        initialize: function (options) {
            this.device = options.device;

            this.listenTo(this.model, 'change', this.change_seat);

            this.render();
        },

        /**
         * Render view
         *
         * @returns {IOT_ADMIN.Seat_view}
         *
         * @author <hieunt1@nal.vn>
         */
        render: function () {

            var data = this.model.toJSON();

            data.total = data.total.toLocaleString();
            data.num_gatepass = data.num_gatepass.toLocaleString();

            this.setElement(this.template(data));

            return this;
        },

        /**
         * Change seat
         *
         * @param model
         *
         * @author <hieunt1@nal.vn>
         */
        change_seat: function (model) {
            var data = this.model.toJSON();

            data.total = data.total.toLocaleString();
            data.num_gatepass = data.num_gatepass.toLocaleString();

            this.$el.html(this.template(data));
        }
    })
})();