
var RealTimeModel = Backbone.Model.extend({
    link_get_credentials : function ()  {
        return "/api/ticket/get_credentials";
    },

    sub_message_handler : function(topic, payload) {
        var response = JSON.parse(payload.toString());

        if (response.device_id == 'ping') {
            return;
        }

        if (response.status >= 500 && response.status <= 600) {
            window.error++;
            return;
        }

        switch (topic) {
            case SCAN_SUB_TITLE:
                if (window.ticket) {
                    window.ticket.add_scan(response);
                }
                break;

            case GATE_KEEPER_SUB_TITLE:
                if (window.ticket) {
                    window.ticket.add_device(response);
                }
                break;

            default:
                break;
        }
    },

    on_reconnected_handler: function () {

    }
});


