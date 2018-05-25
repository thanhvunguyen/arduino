var IOT_ADMIN = IOT_ADMIN || {};

(function () {

    /**
     * IOT_ADMIN.Block_view Class for service page
     *
     * @type {void|*}
     */
    IOT_ADMIN.Block_item_view = Backbone.View.extend({

        el: '',

        template: _.template($('#block-item').html()),
        type: null,

        /**
         * Constructor
         */
        initialize: function (options) {
            this.type = options.type || null;

            this.line_collection = new IOT_ADMIN.Line_collection();

            this.listenTo(this.model, 'change', this.change_block_item);

            this.listenTo(this.line_collection, 'add', this.add_one_line);

            this.render();
        },

        /**
         * Render view
         *
         * @returns {IOT_ADMIN.Block_item_view}
         *
         * @author <hieunt1@nal.vn>
         */
        render: function () {
            var data = this.model.toJSON();

            this.setElement(this.template({}));

            if (this.type) {
                this.$el.find('.tb-body').addClass('text-center').addClass('last_time_' + data.device_id).text(data.number_ticket_pass.toLocaleString());

                return;
            }

            var data_line = [];

            $.each(data.lines, function (key, line) {
                data_line.push({id: key, number: line, device_id: data.device_id})
            });

            this.line_collection.push(data_line);

            return this;
        },

        /**
         * Add one line view
         *
         * @param model
         *
         * @author <hieunt1@nal.vn>
         */
        add_one_line: function (model) {
            var line_view = new IOT_ADMIN.Line_view({
                model: model
            });

            $(line_view.el).addClass('line_' + model.toJSON().id);

            this.$el.find('.tb-body').prepend(line_view.el);
        },

        /**
         * Change block item
         *
         * @param model
         *
         * @author <hieunt1@nal.vn>
         */
        change_block_item: function (model) {

            var data = model.toJSON();
            var data_line = [];

            $.each(data.lines, function (key, line) {
                data_line.push({id: key, number: line, line_change_id: data.line_change_id});
            });

            this.line_collection.set(data_line, {add: false});
        }
    })
})();