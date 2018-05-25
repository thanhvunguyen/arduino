
var RealTimeModel = Backbone.Model.extend({
    link_get_credentials : function ()  {
        return "/api/ticket/get_credentials";
    },

    sub_message_handler : function(topic, payload) {
        console.log(payload.toString());
        var response = JSON.parse(payload.toString());

        let canceled_id = (response.canceled_id) ? response.canceled_id.split("-") : [];

        if (response.canceled_id) {
            response.device_canceled_id = canceled_id[canceled_id.length - 1];
        }
        if (window.device_detail.device_id !== response.device_canceled_id && window.device_detail.device_id !== response.device_id) {
            return;
        }

        if (window.device_detail.load_tickets === false) {
            window.device_detail.pending_on_init.push(
                {
                    sub: topic,
                    data: response
                }
            );

            return;
        }

        window.device_detail.listen_iot(topic, response);
    },

    on_reconnected_handler: function () {
        window.device_detail.sync_newest_data_ticket();
    }
});


