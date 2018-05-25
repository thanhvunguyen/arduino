var IOT_ADMIN = IOT_ADMIN || {};

(function () {

    /**
     * ET.Service Class for service page
     *
     * @type {void|*}
     */
    IOT_ADMIN.List_device_view = Backbone.View.extend({

        el: '',

        num_of_device: 0,

        template: _.template($('#device-template').html()),

        /**
         * Constructor
         */
        initialize: function (options) {
            this.num_of_device = options.num_of_device;

            this.render();
        },

        /**
         * Render view
         *
         * @returns {IOT_ADMIN.List_device_view}
         *
         * @author <hieunt1@nal.vn>
         */
        render: function () {
            this.setElement(this.template({}));
            this.render_device(this.num_of_device);

            return this;
        },

        render_device: function (num_of_device) {
            for (var i = 1; i <= num_of_device; i++) {
                var device_view = new IOT_ADMIN.Device_view({});
                this.$el.find('.device-sliders').append(device_view.el);
            }
        }
    })
})();