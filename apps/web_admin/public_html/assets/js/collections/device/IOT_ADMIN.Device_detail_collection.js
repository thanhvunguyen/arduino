var IOT_ADMIN = IOT_ADMIN || {};

(function () {
        IOT_ADMIN.Device_detail_collection = Backbone.Collection.extend({

            model: IOT_ADMIN.Device_detail_model,

            url: '/api/event/get_list_scans_by_time',

            zone: 'Asia/Tokyo',

            max_time: null,
            loading: false,
            
            /**
             * Init
             */
            initialize: function (config) {
                config = config || {};
            },

            comparator: function (a, b) {
                return a.get('id') > b.get('id') ? -1 : 1;
            },

            parse: function (output) {
                if (! output.success || ! output.submit) {
                    return [];
                }
                this.max_time = output.result.next_time;

                for (var i = 0; i < output.result.items.length; i++) {
                    output.result.items[i]._update_at = moment().valueOf();
                    output.result.items[i].add_by_real_time = output.result.add_by_real_time;
                }
                return output.result.items;
            },
        })
})();

