var IOT_ADMIN = IOT_ADMIN || {};

(function () {

    /**
     * ET.Service Class for service page
     *
     * @type {void|*}
     */
    IOT_ADMIN.Time_line_view = Backbone.View.extend({

        el: '',

        num_of_block: 0,

        template: _.template($('#time_line').html()),

        list_device: {},

        type: null,

        /**
         * Constructor
         */
        initialize: function (options) {

            this.list_device = options.list_device || {};
            this.type = options.type || null;

            this.listenTo(this.model, 'change', this.change_model);

            this.render();
        },

        /**
         * Render view
         *
         * @returns {IOT_ADMIN.Time_line_view}
         *
         * @author <hieunt1@nal.vn>
         */
        render: function () {
            var data = this.model.toJSON();
            this.setElement(this.template({time: data.time_line_block}));
            this.$el.addClass('raw_time_line');

            var block_view = new IOT_ADMIN.Block_view({
                model: this.model,
                type: this.type,
                list_device: this.list_device
            });

            this.$el.addClass('line_' + data.id);

            this.$el.find('.sliders').append($($(block_view.el).html()));

            this.add_slick_time_line();

            return this;
        },

        change_model: function (model) {
            var data = model.toJSON();

            if (data.type_force) {
                $.each(data.list_device, function (key, device) {
                    $('.last_time_' + device.device_id).text(device.number_ticket_pass.toLocaleString());
                });
            }
        },

        /**
         * Add slider slick to time line
         *
         * @author <hieunt1@nal.vn>
         */
        add_slick_time_line: function () {
            // Add slick time line
            this.$el.find('.sliders').slick({
                infinite: true,
                dots: false,
                slidesToShow: 8,
                swipe: false,
                slidesToScroll: 8,
                asNavFor: '.sliders',
                accessibility: false,
                prevArrow: $('.device-prev'),
                nextArrow: $('.device-next'),
                responsive: [
                    {
                        breakpoint: 1440,
                        settings: {
                            slidesToShow: 7,
                            slidesToScroll: 7,
                            infinite: true
                        }
                    },
                    {
                        breakpoint: 1279,
                        settings: {
                            slidesToShow: 5,
                            slidesToScroll: 5,
                            infinite: true
                        }
                    },
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 4,
                            infinite: true
                        }
                    },
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                            infinite: true
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2,
                            infinite: true
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            infinite: true
                        }
                    }
                ]
            });
        }
    })
})();