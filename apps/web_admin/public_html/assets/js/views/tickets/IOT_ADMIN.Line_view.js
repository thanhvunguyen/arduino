var IOT_ADMIN = IOT_ADMIN || {};

(function () {

    /**
     * ET.Service Class for service page
     *
     * @type {void|*}
     */
    IOT_ADMIN.Line_view = Backbone.View.extend({

        el: '',

        template: _.template($('#line').html()),

        /**
         * Constructor
         */
        initialize: function (options) {

            this.listenTo(this.model, 'change', this.change_line);

            this.render();
        },

        render: function () {
            var data = this.model.toJSON();
            this.setElement(this.template({data: data.number}));

            return this;
        },

        /**
         * Change line
         *
         * @param model
         *
         * @author <hieunt1@nal.vn>
         */
        change_line: function (model) {
            var data = model.toJSON();
            var current_line = $(document).find('.line_' + data.line_change_id + ' .block_' + data.device_id + ' .line_' + data.id);
            var data_tp = current_line.attr('data-tp');
            var number = (data.number <= 0) ? 0 : data.number;
            current_line.removeClass('tp-' + data_tp);
            current_line.addClass('tp-' + number);
            current_line.attr('data-tp', number);
        }
    })
})();