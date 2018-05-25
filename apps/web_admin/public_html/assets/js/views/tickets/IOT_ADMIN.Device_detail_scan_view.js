var IOT_ADMIN = IOT_ADMIN || {};

(function () {

    /**
     * IOT_ADMIN.Block_view Class for service page
     *
     * @type {void|*}
     */
    IOT_ADMIN.Device_detail_scan_view = Backbone.View.extend({

        el: '',

        template: _.template($('#device-detail-scan').html()),

        /**
         * Constructor
         */
        initialize: function (options) {
            this.listenTo(this.model, 'change', this.change_device);
            this.render();
        },

        /**
         * Render view
         *
         * @returns {IOT_ADMIN.Device_detail_view}
         *
         * @author <hieunt1@nal.vn>
         */
        render: function () {
            var data = this.model.toJSON();
            this.setElement(this.template(data));

            return this;
        }
    })
})();