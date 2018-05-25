var IOT_ADMIN = IOT_ADMIN || {};

(function () {

    /**
     * IOT_ADMIN.Block_view Class for service page
     *
     * @type {void|*}
     */
    IOT_ADMIN.Device_view = Backbone.View.extend({

        el: '',

        template: _.template($('#device-item').html()),

        /**
         * Constructor
         */
        initialize: function (options) {
            this.device = options.device;
            
            this.listenTo(this.model, 'change', this.change_device);
            this.listenTo(this.model, 'add', this.add_device);

            this.render();
        },

        /**
         * Render view
         *
         * @returns {IOT_ADMIN.Device_view}
         *
         * @author <hieunt1@nal.vn>
         */
        render: function () {
            var data = this.model.toJSON();
            this.setElement(this.template(data));

            return this;
        },

        /**
         * Change device
         *
         * @param model
         *
         * @author <hieunt1@nal.vn>
         */
        change_device: function (model) {
            var data = model.toJSON();

            $('.box_device_' + data.device_id + ' .device_num_gate_pass').text(data.num_pass.toLocaleString());
            $('.box_device_' + data.device_id + ' .device_num_error').text(data.num_error.toLocaleString());
        }
    })
})();