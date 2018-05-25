var IOT_ADMIN = IOT_ADMIN || {};

(function () {

    /**
     * IOT_ADMIN.Block_view Class for service page
     *
     * @type {void|*}
     */
    IOT_ADMIN.Block_view = Backbone.View.extend({

        el: '',

        template: _.template($('#block').html()),

        list_device: {},
        type: null,

        /**
         * Constructor
         */
        initialize: function (options) {

            this.list_device = options.list_device || {};
            this.type = options.type || null;

            this.block_collection = new IOT_ADMIN.Block_collection();

            this.listenTo(this.block_collection, 'add', this.add_one_block);
            this.listenTo(this.model, 'change', this.switch_change);

            this.render();
        },

        /**
         * Render view
         *
         * @returns {IOT_ADMIN.Block_view}
         *
         * @author <hieunt1@nal.vn>
         */
        render: function () {
            var data = this.model.toJSON();

            this.setElement(this.template({}));
            var block_collection = new Backbone.Collection(data.list_device);
            var data_change = [];

            $.each(block_collection.toJSON(), function (key, device) {
                var block = block_collection.findWhere({id: device.device_id});

                data_change.push(block.toJSON());
            });

            this.block_collection.push(data_change);

            return this;
        },

        /**
         * Add one block view
         *
         * @param block_model
         *
         * @author <hieunt1@nal.vn>
         */
        add_one_block: function (block_model) {
            var block_view = new IOT_ADMIN.Block_item_view({
                model: block_model,
                type: this.type
            });

            $(block_view.el).addClass('block_' + block_model.toJSON().device_id);

            var data = block_model.toJSON();

            switch (data.type) {
                case 'add_new_device':
                    $('.line_' + data.line_id + ' .sliders').slick('slickAdd', block_view.el);
                    break;

                default:
                    this.$el.append(block_view.el);
                    break;
            }
        },

        /**
         * Switch change block
         *
         * @param model
         *
         * @author <hieunt1@nal.vn>
         */
        switch_change: function (model) {
            var data = model.toJSON();
            var me = this;

            $.each(data.list_device, function (key, value) {
                if (!me.block_collection.findWhere({device_id: value.device_id})) {
                    value.type = 'add_new_device';
                    value.line_id = data.id;
                    value.id = value.device_id;

                    me.list_device = data.list_device;
                    me.block_collection.push(value, {add: true, remove: false});
                }
            });

            switch (data.status) {
                case 'add':
                    break;

                default:
                    this.list_device = data.new_list_device || this.list_device;
                    this.change_block(model);
                    break;
            }
        },

        /**
         * Change block
         *
         * @param model
         *
         * @author <hieunt1@nal.vn>
         */
        change_block: function (model) {
            var me = this;
            var data = model.toJSON();
            var block_collection = new Backbone.Collection(data.list_device);

            var data_change = [];

            $.each(this.list_device, function (key, device) {
                var block = block_collection.findWhere({device_id : device.device_id});

                block.set({_token: me.make_id()});
                block.set({id: device.device_id});
                data_change.push(block.toJSON());
            });

            this.block_collection.set(data_change, {add: false, remove: false});
        },

        /**
         * Random string
         *
         * @returns {string}
         *
         * @author <hieunt1@nal.vn>
         */
        make_id: function () {
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

            for (var i = 0; i < 15; i++)
                text += possible.charAt(Math.floor(Math.random() * possible.length));

            return text;
        }
    })
})();