var IOT_ADMIN = IOT_ADMIN || {};

(function () {
    const TICKET_STATUS_NOT_YET = '1';
    const TICKET_STATUS_PASS = '2';
    /**
     * ET.Service Class for service page
     *
     * @type {void|*}
     */
    IOT_ADMIN.Common = AppCore.Base.extend({

        face_recognition_mode : null,

        events: {
            'click #csv-submit': 'uploadCsv',
            'click .x-btn-get-list-promoter': 'getListPromoter',
            'click .x-btn-display-promoter' : 'displayDeletePromoterModal',
            'click .x-btn-display-unlink-event' : 'displayUnlinkEventModal',
            'click .x-btn-force-off' : 'forceOffDevice',
            'click .x-btn-return-connect' : 'returnConnectDevice',
            'click #button-update-note' : 'changeNoteTicket',
            'change .selectpicker' : 'changeStatus',
            'click .x-btn-change-to-passed' : 'changeStatus',
            'click .x-change-status-dynamo-db' : 'change_dynamo_db_status',
            'click .x-btn-get-list-event': 'getListEvent',
            'click .face_recognition': 'change_status_face_recognition'
        },

        /**
         * Constructor
         */
        initialize: function (config) {
            config = config || {};
            this.face_recognition_mode = config.face_recognition_mode;
            this.loader();
            this.render(config);
        },

        /**
         * Upload csv
         *
         * @param e
         *
         * @author <hieunt1@nal.vn>
         */
        uploadCsv: function (e) {
            var btn_submit = $(e.currentTarget);
            btn_submit.attr('disabled', 'disabled');

            var currentTags = btn_submit.html();
            var tagsNew = '<span class="btn-text"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i> アップロード中...</span>';

            var body = $('body');

            body.css('position', 'relative');
            body.append('<div id="layer" style="z-index: 9999; position: absolute; top: 0; left: 0; right: 0; bottom: 0; cursor: no-drop;"></div>');

            btn_submit.html(tagsNew);

            var formData = new FormData();
            var csv = $('#csvUpload').prop('files');
            var me = this;

            formData.append('file_upload', csv[0]);
            formData.append('kogyo_code', kogyo_code);
            formData.append('kogyo_sub_code', kogyo_sub_code);
            formData.append('koen_code', koen_code);

            var with_errors = $('#form-csv .with-errors');
            var message = $('#modalAlert #message');

            Backbone.ajax({
                method: 'POST',
                url: "/api/ticket/upload",
                cache: false,
                dataType: 'json',
                processData: false,
                contentType: false,
                data: formData,
                success: function (val) {
                    btn_submit.removeAttr('disabled');
                    btn_submit.html(currentTags);

                    $('body').css('position', 'static');
                    $('#layer').remove();

                    if (val.success && val.submit) {
                        me.init_file_upload();
                        with_errors.text('');

                        $('#modalCsvUpload').modal('hide');
                        $('#form-csv').removeClass('has-error');

                        message.html('<span>CSVがアップロードされました。<br />データの読み込みには少し時間がかかります。<br />このポップアップ閉じて、しばらくお待ちください。</span>');
                        $('#modalAlert').modal('show');

                        csv_history_id = val.result.id;
                        me.checkImportCsv();
                    } else {
                        $('#form-csv').addClass('has-error');
                        me.init_file_upload();

                        if (val.invalid_fields) {
                            $.each(val.invalid_fields, function(index, value) {
                                with_errors.text(value);
                            });

                            return;
                        }

                        var errormgs = val.errmsg;
                        with_errors.text(errormgs);
                    }
                },
                error: function (a, b) {
                    btn_submit.removeAttr('disabled');
                    btn_submit.html(currentTags);

                    $('body').css('position', 'static');
                    $('#layer').remove();

                    $('#form-csv').addClass('has-error');
                    me.init_file_upload();
                    with_errors.text('ファイルを開くことができませんでした。');
                }
            });
        },

        init_file_upload: function () {
            $('.file-status').text('ファイルが選択されていません。');
            $('#csvUpload').val('');
        },

        load_ticket_number_info: function (el, callback) {


            $.ajax({
                url: '/api/ticket/get_ticket_number_info',
                method: 'POST',
                data: {
                    app_id: app_id,
                },
                success: function (output) {
                    if (!output.success || !output.submit) {
                        return;
                    }

                    let res = output.result;
                    let html = '';
                    let ticket_number = 0;
                    html += `<div class="col-xs-6 col-sm-6 box-left">`;
                    if (res.renkeimotos.length > 0) {
                        for (let i = 0; i < res.renkeimotos.length; i++) {
                            html += `<label class="col-xs-8 col-sm-8 col-md-8 pa-0" style="padding: 5px !important;color: #353535 !important">${res.renkeimotos[i].renkeimoto_name}</label>
                                 <span class="col-xs-4 col-sm-4 col-md-4 pr-0"
                                 style="text-align: right;padding: 5px !important;color: #353535 !important">${parseInt(res.renkeimotos[i].total).toLocaleString()}</span>`
                        }
                    }

                    html += `</div>
                        <div class="col-xs-6 col-sm-6 box-righ"t style="border-left: 1px solid rgb(104, 203, 84);">`;

                    if (res.seats.length > 0) {
                        for (let i = 0; i < res.seats.length; i++) {
                            ticket_number += parseInt(res.seats[i].total);
                            html += `<label class="col-xs-8 col-sm-8 col-md-8 pa-0" style="padding: 5px !important;color: #353535 !important"> ${res.seats[i].tx_type_text}</label>
                                 <span class="col-xs-4 col-sm-4 col-md-4  pr-0"
                                    style="text-align: right;padding: 5px !important;color: #353535 !important">${parseInt(res.seats[i].total).toLocaleString()}</span>
                                 <div class="clearfix"></div>`
                        }
                    }

                    html += `</div>
                         <div class="clearfix"></div>`;

                    if (res.renkeimotos.length > 0) {
                        html += `<hr class="mb-10">`;
                    }
                    html += `<label class="col-xs-4 col-sm-4 col-md-4 pa-0" style="font-size: 14px;color: #353535 !important;font-weight: 700;padding: 5px !important">合計チケット数</label>
                         <span class="col-xs-4 col-sm-4 col-md-4 pr-0" style="font-size: 14px;color: #353535 !important;font-weight: 700;text-align: center;padding: 5px !important">
                            ${ticket_number.toLocaleString()}
                         </span>
                        <span class="col-xs-4 col-sm-4 col-md-4"></span>`;

                    $(el).html(html);
                    if (typeof callback === "function") {
                        callback(ticket_number);
                    }
                }
            });
        },

        /**
         * Get List Promoter
         *
         * @param e
         *
         * @author <hoangnq@nal.vn>
         */
        getListPromoter: function (e) {
            $('#modalSearch tbody').html('<tr><td colspan="8" class="pt-10 text-center"><i class="fa fa-spin fa-4x fa-spinner"></i></td></tr>');
            $('.remove-on-reload').remove();

            var target = $(e.target);

            if (target.prop("tagName") == "I") {
                target = target.closest("a");
            }
            var page = target.data("p");

            var isSubmit = target.data("button-submit");

            var promoter_code, promoter_name, promoter_name_kana;

            if ((typeof isSubmit !== 'undefined') && isSubmit == true) {
                var form = target.closest('form');

                page = 1;
                promoter_code = form.find("#promoter_code").val();
                promoter_name = form.find("#promoter_name").val();
                promoter_name_kana = form.find("#promoter_name_kana").val();
            } else {
                page = (typeof page !== 'undefined') ? page : 1;

                promoter_code = target.data("promoter_code");
                promoter_code = (typeof promoter_code !== 'undefined') ? promoter_code : '';

                promoter_name = target.data("promoter_name");
                promoter_name = (typeof promoter_name !== 'undefined') ? promoter_name : '';

                promoter_name_kana = target.data("promoter_name_kana");
                promoter_name_kana = (typeof promoter_name_kana !== 'undefined') ? promoter_name_kana : '';
            }

            var params = {
                p: page,
                promoter_code: promoter_code,
                promoter_name: promoter_name,
                promoter_name_kana: promoter_name_kana
            };

            Backbone.ajax({
                method: 'GET',
                url: "/api/account/get_list_promoter?" + build_query(params),
                cache: false,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (res) {
                    if (!res.success || !res.submit) {
                        $("#modalError").modal("show");
                        return;
                    }

                    var items = res.result.items;
                    var html = '';
                    if (items.length > 0) {
                        for (var i = 0; i < items.length; i++) {
                            html += '<tr>';
                            html += '<td class="text-center">';
                            var result = $.grep(
                                promoters, function(e){
                                    return e.client_code == items[i].promoter_code;
                                });

                            if( result.length == 0 ){
                                html += '<span class="radio ml-10 radio-inline radio-success">';
                                html += '<input type="radio" name="radio_add" id="radio_' + i + '" value="' + items[i].promoter_code + '" data-client_name="' + items[i].promoter_name + '"><label for="radio_' + i + '"></label>';
                                html += '</span>';
                            } else {
                                html += '<span>';
                                html += '<i class="zmdi zmdi-check-circle" style="font-size: 20px; color: #01c853;"></i>';
                                html += '</span>';
                            }
                            html += '</td>';
                            html += '<td>' + items[i].promoter_code + '</td>';
                            html += '<td>' + items[i].promoter_name + '</td>';
                            html += '</tr>';
                        }
                    } else {
                        html += '<div class="clearfix text-center remove-on-reload">';
                        html += '<p><i class="zmdi zmdi-hc-4x zmdi-mood-bad"></i></p>';
                        html += '<div class="text-center">該当する項目が見つかりませんでした</div>';
                        html += '</div>';
                        $('#modalSearch table').after(html);
                        html = '';

                    }


                    // Display pagination
                    var html_pagination = '';

                     var $pagination = res.result.pagination;
                    if ((typeof $pagination !== 'undefined') && $pagination.total != 0) {
                        var $default_display = $pagination.const.NUMBER_PAGE_SHOW;
                        var $total_page = Math.ceil($pagination.total / $pagination.limit);
                        var $min_display, $max_display, key;
                        if ($total_page < $default_display) {
                            $min_display = 1;
                            $max_display = $total_page
                        }
                        else {
                            if ($pagination.p < $default_display / 2) {
                                $min_display = 1;
                                $max_display = $default_display;
                            } else if ($pagination.p > $total_page - ($default_display - 1) / 2) {
                                $min_display = $total_page - ($default_display - 1);
                                $max_display = $total_page;
                            } else {
                                $min_display = $pagination.p - ($default_display - 1) / 2;
                                $max_display = $pagination.p + ($default_display - 1) / 2;
                            }
                        }

                        if ($total_page > 1) {
                            html_pagination += '<ul class="pagination pagination-sm pull-left remove-on-reload">';

                            if ($pagination.p > 1) {
                                $pagination.params.p = ($pagination.p <= $default_display) ? 1 : $pagination.p - ($pagination.const.GO_TO_10);
                                html_pagination += '<li>';
                                html_pagination += '<a  class="x-btn-get-list-promoter" href="javascript:void(0)"';

                                for (key in $pagination.params) {
                                    html_pagination += ' data-' + key + '="' + $pagination.params[key] + '"';
                                }

                                html_pagination += '>';
                                html_pagination += '<i class="fa fa-angle-double-left"></i>';
                                html_pagination += '</a>';
                                html_pagination += '</li>';
                                $pagination.params.p = $pagination.p - 1;
                                html_pagination += '<li>';
                                html_pagination += '<a  class="x-btn-get-list-promoter paginate_button previous" href="javascript:void(0)"';

                                for (key in $pagination.params) {
                                    html_pagination += ' data-' + key + '="' + $pagination.params[key] + '"';
                                }

                                html_pagination += '>';
                                html_pagination += '<i class="fa fa-angle-left"></i>';
                                html_pagination += '</a>';
                                html_pagination += '</li>';
                            }

                            for (var $p = $min_display; $p <= $max_display; ++$p) {
                                $pagination.params.p = $p;
                                html_pagination += '<li ' + ($pagination.p == $p ? 'class="active"' : '') + '>';
                                html_pagination += '<a  class="x-btn-get-list-promoter" href="javascript:void(0)"';

                                for (key in $pagination.params) {
                                    html_pagination += ' data-' + key + '="' + $pagination.params[key] + '"';
                                }

                                html_pagination += '>' + $p + '</a>';
                                html_pagination += '</li>';
                            }

                            if ($pagination.p < $total_page) {
                                $pagination.params.p = $pagination.p + 1;
                                html_pagination += '<li>';
                                html_pagination += '<a  class="x-btn-get-list-promoter" href="javascript:void(0)"';

                                for (key in $pagination.params) {
                                    html_pagination += ' data-' + key + '="' + $pagination.params[key] + '"';
                                }

                                html_pagination += '>';
                                html_pagination += '<i class="fa fa-angle-right"></i>';
                                html_pagination += '</a>';
                                html_pagination += '</li>';
                                $pagination.params.p = ($total_page < $pagination.p + (($pagination.const.GO_TO_10))) ? $total_page : $pagination.p + (($pagination.const.GO_TO_10));
                                html_pagination += '<li>';
                                html_pagination += '<a  class="x-btn-get-list-promoter" href="javascript:void(0)"';

                                for (key in $pagination.params) {
                                    html_pagination += ' data-' + key + '="' + $pagination.params[key] + '"';
                                }

                                html_pagination += '>';
                                html_pagination += '<i class="fa fa-angle-double-right"></i>';
                                html_pagination += '</a>';
                                html_pagination += '</li>';
                            }
                            html_pagination += '</ul>';
                        }

                        html_pagination += '<div class="pull-right remove-on-reload">';
                        html_pagination += '<div class="total-pages text-right mt-10 mb-10">';

                        if ($pagination.p < $total_page) {
                            html_pagination += '<span class="count">' + (($pagination.p - 1)*$pagination.limit + 1) + '~' + ($pagination.limit * $pagination.p) + '</span> / <span class="total">' + $pagination.total + '</span>';
                        } else {
                            html_pagination += '<span class="count">' + (($pagination.p - 1)*$pagination.limit + 1) + '~' + ($pagination.total) + '</span> / <span class="total">' + $pagination.total + '</span>';
                        }

                        html_pagination += '</div>';
                        html_pagination += '</div>';
                        html_pagination += '<div class="clearfix"></div>';
                    }

                    if ((typeof isSubmit !== 'undefined') && isSubmit == true) {
                        var form = target.closest('form');
                        form.find("#promoter_code").val((typeof $pagination.params.promoter_code !== 'undefined') ? $pagination.params.promoter_code : "");
                        form.find("#promoter_name").val((typeof $pagination.params.promoter_name !== 'undefined') ? $pagination.params.promoter_name : "");
                        form.find("#promoter_name_kana").val((typeof $pagination.params.promoter_name_kana !== 'undefined') ? $pagination.params.promoter_name_kana : "");
                    }

                    $(html_pagination).prependTo($('#modalSearch .pagination-modal'));
                    $(html_pagination).appendTo($('#modalSearch .pagination-modal'));
                    $('#modalSearch tbody').html(html);
                    $('#add-promoter button[type="submit"]').prop('disabled', true);
                }
            });
        },


        /**
         * Get List Event
         *
         * @param e
         *
         * @author <hoangnq@nal.vn>
         */
        getListEvent: function (e) {
            $('#modalSearchEvent tbody').html('<tr><td colspan="8" class="pt-10 text-center"><i class="fa fa-spin fa-4x fa-spinner"></i></td></tr>');
            $('.remove-on-reload').remove();

            var target = $(e.target);

            if (target.prop("tagName") == "I") {
                target = target.closest("a");
            }
            var page = target.data("p");

            var isSubmit = target.data("button-submit");

            var koen_code, kogyo_code, kogyo_sub_code, event_name;

            if ((typeof isSubmit !== 'undefined') && isSubmit == true) {
                var form = target.closest('form');

                page = 1;
                koen_code = form.find("#koen_code").val();
                kogyo_code = form.find("#kogyo_code").val();
                kogyo_sub_code = form.find("#kogyo_sub_code").val();
                event_name = form.find("#event_name").val();
            } else {
                page = (typeof page !== 'undefined') ? page : 1;

                event_name = target.data("event_name");
                event_name = (typeof event_name !== 'undefined') ? event_name : '';

                koen_code = target.data("koen_code");
                koen_code = (typeof koen_code !== 'undefined') ? koen_code : '';

                kogyo_code = target.data("kogyo_code");
                kogyo_code = (typeof kogyo_code !== 'undefined') ? kogyo_code : '';

                kogyo_sub_code = target.data("kogyo_sub_code");
                kogyo_sub_code = (typeof kogyo_sub_code !== 'undefined') ? kogyo_sub_code : '';
            }

            var params = {
                p: page,
                app_id: app_id,
                koen_code: koen_code,
                kogyo_code: kogyo_code,
                kogyo_sub_code: kogyo_sub_code,
                event_name: event_name
            };

            Backbone.ajax({
                method: 'GET',
                url: "/api/tour/get_list_event_link_app_id?" + build_query(params),
                cache: false,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (res) {

                    if (!res.success || !res.submit) {
                        $("#modalError").modal("show");
                        return;
                    }

                    var items = res.result.items;
                    var html = '';
                    if (items.length > 0) {
                        for (var i = 0; i < items.length; i++) {
                            html += '<tr>';
                            html += '<td class="text-center">';

                            if (items[i].is_primary_event == '1') {
                                html += '<span>';
                                html += '<i class="zmdi zmdi-check-circle" style="font-size: 20px; color: #01c853;"></i>';
                                html += '</span>';
                            } else {
                                html += '<span class="radio ml-10 radio-inline radio-success">';
                                html += '<input type="radio" name="radio_add" id="radio_' + i + '" value="' + items[i].event_code + '" data-event_code="' + items[i].event_code + '"><label for="radio_' + i + '"></label>';
                                html += '</span>';
                            }
                            html += '</td>';
                            html += '<td>' + items[i].event_code + '</td>';
                            html += '<td>' + items[i].event_name + '</td>';
                            html += '<td>' + items[i].venue_name + '</td>';
                            html += '</tr>';
                        }
                    } else {
                        html += '<div class="clearfix text-center remove-on-reload">';
                        html += '<p><i class="zmdi zmdi-hc-4x zmdi-mood-bad"></i></p>';
                        html += '<div class="text-center">該当する項目が見つかりませんでした</div>';
                        html += '</div>';
                        $('#modalSearchEvent table').after(html);
                        html = '';

                    }

                    // Display pagination
                    var html_pagination = '';

                     var $pagination = res.result.pagination;
                    if ((typeof $pagination !== 'undefined') && $pagination.total != 0) {
                        var $default_display = $pagination.const.NUMBER_PAGE_SHOW;
                        var $total_page = Math.ceil($pagination.total / $pagination.limit);
                        var $min_display, $max_display, key;
                        if ($total_page < $default_display) {
                            $min_display = 1;
                            $max_display = $total_page
                        }
                        else {
                            if ($pagination.p < $default_display / 2) {
                                $min_display = 1;
                                $max_display = $default_display;
                            } else if ($pagination.p > $total_page - ($default_display - 1) / 2) {
                                $min_display = $total_page - ($default_display - 1);
                                $max_display = $total_page;
                            } else {
                                $min_display = $pagination.p - ($default_display - 1) / 2;
                                $max_display = $pagination.p + ($default_display - 1) / 2;
                            }
                        }

                        if ($total_page > 1) {
                            html_pagination += '<ul class="pagination pagination-sm pull-left remove-on-reload">';

                            if ($pagination.p > 1) {
                                $pagination.params.p = ($pagination.p <= $default_display) ? 1 : $pagination.p - ($pagination.const.GO_TO_10);
                                html_pagination += '<li>';
                                html_pagination += '<a  class="x-btn-get-list-event" href="javascript:void(0)"';

                                for (key in $pagination.params) {
                                    html_pagination += ' data-' + key + '="' + $pagination.params[key] + '"';
                                }

                                html_pagination += '>';
                                html_pagination += '<i class="fa fa-angle-double-left"></i>';
                                html_pagination += '</a>';
                                html_pagination += '</li>';
                                $pagination.params.p = $pagination.p - 1;
                                html_pagination += '<li>';
                                html_pagination += '<a  class="x-btn-get-list-event paginate_button previous" href="javascript:void(0)"';

                                for (key in $pagination.params) {
                                    html_pagination += ' data-' + key + '="' + $pagination.params[key] + '"';
                                }

                                html_pagination += '>';
                                html_pagination += '<i class="fa fa-angle-left"></i>';
                                html_pagination += '</a>';
                                html_pagination += '</li>';
                            }

                            for (var $p = $min_display; $p <= $max_display; ++$p) {
                                $pagination.params.p = $p;
                                html_pagination += '<li ' + ($pagination.p == $p ? 'class="active"' : '') + '>';
                                html_pagination += '<a  class="x-btn-get-list-event" href="javascript:void(0)"';

                                for (key in $pagination.params) {
                                    html_pagination += ' data-' + key + '="' + $pagination.params[key] + '"';
                                }

                                html_pagination += '>' + $p + '</a>';
                                html_pagination += '</li>';
                            }

                            if ($pagination.p < $total_page) {
                                $pagination.params.p = $pagination.p + 1;
                                html_pagination += '<li>';
                                html_pagination += '<a  class="x-btn-get-list-event" href="javascript:void(0)"';

                                for (key in $pagination.params) {
                                    html_pagination += ' data-' + key + '="' + $pagination.params[key] + '"';
                                }

                                html_pagination += '>';
                                html_pagination += '<i class="fa fa-angle-right"></i>';
                                html_pagination += '</a>';
                                html_pagination += '</li>';
                                $pagination.params.p = ($total_page < $pagination.p + (($pagination.const.GO_TO_10))) ? $total_page : $pagination.p + (($pagination.const.GO_TO_10));
                                html_pagination += '<li>';
                                html_pagination += '<a  class="x-btn-get-list-event" href="javascript:void(0)"';

                                for (key in $pagination.params) {
                                    html_pagination += ' data-' + key + '="' + $pagination.params[key] + '"';
                                }

                                html_pagination += '>';
                                html_pagination += '<i class="fa fa-angle-double-right"></i>';
                                html_pagination += '</a>';
                                html_pagination += '</li>';
                            }
                            html_pagination += '</ul>';
                        }

                        html_pagination += '<div class="pull-right remove-on-reload">';
                        html_pagination += '<div class="total-pages text-right mt-10 mb-10">';

                        if ($pagination.p < $total_page) {
                            html_pagination += '<span class="count">' + (($pagination.p - 1)*$pagination.limit + 1) + '~' + ($pagination.limit * $pagination.p) + '</span> / <span class="total">' + $pagination.total + '</span>';
                        } else {
                            html_pagination += '<span class="count">' + (($pagination.p - 1)*$pagination.limit + 1) + '~' + ($pagination.total) + '</span> / <span class="total">' + $pagination.total + '</span>';
                        }

                        html_pagination += '</div>';
                        html_pagination += '</div>';
                        html_pagination += '<div class="clearfix"></div>';
                    }

                    if ((typeof isSubmit !== 'undefined') && isSubmit == true) {
                        var form = target.closest('form');
                        form.find("#koen_code").val((typeof $pagination.params.koen_code !== 'undefined') ? $pagination.params.koen_code : "");
                        form.find("#kogyo_code").val((typeof $pagination.params.kogyo_code !== 'undefined') ? $pagination.params.kogyo_code : "");
                        form.find("#kogyo_sub_code").val((typeof $pagination.params.kogyo_sub_code !== 'undefined') ? $pagination.params.kogyo_sub_code : "");
                        form.find("#event_name").val((typeof $pagination.params.event_name !== 'undefined') ? $pagination.params.event_name : "");
                    }

                    $(html_pagination).prependTo($('#modalSearchEvent .pagination-modal'));
                    $(html_pagination).appendTo($('#modalSearchEvent .pagination-modal'));
                    $('#modalSearchEvent tbody').html(html);
                    $('#link_event button[type="submit"]').prop('disabled', true);
                }
            });
        },

        /**
         * Display delete promoter modal
         *
         * @param e
         *
         * @author <hoangnq@nal.vn>
         */
        displayDeletePromoterModal: function (e) {
            $('#modalDelete tbody').html('<tr><td colspan="8" class="pt-10 text-center"><i class="fa fa-spin fa-4x fa-spinner"></i></td></tr>');

            var target = $(e.target);

            var html = '';

            for (var item in promoters) {
                if((typeof promoters[item] !== 'undefined')) {
                    html += '<tr>';
                    html += '<td class="text-center">';
                    html += ' <div class="checkbox-inline pl-10">';
                    html += '<span class="checkbox checkbox-success">';
                    html += '<input type="checkbox" name="checkbox_detele" id="checkbox_' + item + '" value="' + promoters[item].client_code + '" data-client_name="' + promoters[item].client_name + '"><label for="checkbox_' + item + '"></label>';
                    html += '</span>';
                    html += '</div>';
                    html += '</td>';
                    html += '<td>' + promoters[item].client_code + '</td>';
                    html += '<td>' + promoters[item].client_name + '</td>';
                    html += '</tr>';
                }
            }
            $('#modalDelete tbody').html(html);
            $('#delete-promoter button[type="submit"]').prop('disabled', true);
        },

        /**
         * Unlink event
         * 
         * @param e
         */
        displayUnlinkEventModal: function (e) {
            $('#modalUnlink tbody').html('<tr><td colspan="8" class="pt-10 text-center"><i class="fa fa-spin fa-4x fa-spinner"></i></td></tr>');

            var target = $(e.target);

            var html = '';

           
            for (var item in link_event) {
                html += '<tr>';
                html += '<td class="text-center">';
                html += ' <div class="checkbox-inline pl-10">';
                html += '<span class="checkbox checkbox-success">';
                html += '<input type="checkbox" name="checkbox_detele" id="checkbox_' + item + '" value="' + link_event[item].event_code + '"><label for="checkbox_' + item + '"></label>';
                html += '</span>';
                html += '</div>';
                html += '</td>';
                html += '<td>' + link_event[item].event_code + '</td>';
                html += '<td>' + link_event[item].event_name + '</td>';
                html += '<td>' + link_event[item].venue_name + '</td>';
                html += '</tr>';
            }
            $('#modalUnlink tbody').html(html);
            $('#unlink_event button[type="submit"]').prop('disabled', true); 
        },
        /**
         * Force off Status Device
         *
         * @param e
         *
         * @author <dungpt@nal.vn>
         */
        forceOffDevice: function (e) {
            var  deviceId = $(e.currentTarget).data("id");
            var  kogyoCode = kogyo_code;
            var  kogyoSubCode = kogyo_sub_code;
            var  koenCode = koen_code;
            var btnForceOff = $('.btn-force-off[data-id="' + deviceId + '"]');
            btnForceOff.addClass("disabled");
            btnForceOff.text("切断");// Cutting

                // Force off status device
                $.ajax({
                    'type': 'post',
                    'url': "/api/event/force_off",
                    data: {
                      device_id: deviceId,
                      kogyo_code : kogyoCode,
                      kogyo_sub_code : kogyoSubCode,
                      koen_code : koenCode
                     },
                    /**
                     *
                     * @param {object} res
                     * @property {res} errmsg
                     * @property {res} success
                     * @property {res} submit
                     */
                    success: function (res) {
                        if (!res.success || !res.submit) {
                            $("#modalError .modal-header h5").text('注意');
                            $("#modalError .modal-body p").text(res.errmsg);
                            $("#modalError").modal("show");
                            return;
                        }
                        location.reload();
                    },
                    error: function (res) {
                        $("#modalError").modal("show");
                        $("#modalError .modal-body p").text('問題が発生し、操作が完了できませんでした。問題が続く場合は、管理者にお問い合わせください。');
                    }
                });
            },

        /**
         * Return Connect Device
         *
         * @param e
         *
         * @author <dungpt@nal.vn>
         */
        returnConnectDevice: function (e) {
            // return connect status device
            var  deviceId = $(e.currentTarget).data("id");
            var  kogyoCode = kogyo_code;
            var  kogyoSubCode = kogyo_sub_code;
            var  koenCode = koen_code;

            var btnReturn = $('.btn-return[data-id="' + deviceId + '"]');
            btnReturn.addClass("disabled");
            btnReturn.text("復帰中");// In Return

            // Force off status device
            $.ajax({
                'type': 'post',
                'url': "/api/event/return_connect",
                data: {
                    device_id: deviceId,
                    kogyo_code : kogyoCode,
                    kogyo_sub_code : kogyoSubCode,
                    koen_code : koenCode
                },
                /**
                 * @param {object} res
                 * @property {res} errmsg
                 * @property {res} success
                 * @property {res} submit
                 */
                success: function (res) {
                    if (!res.success || !res.submit) {
                        $("#modalError .modal-header h5").text('注意');
                        $("#modalError .modal-body p").text(res.errmsg);
                        $("#modalError").modal("show");
                        return
                    }
                    location.reload();
                },
                error: function (res) {
                    $("#modalError .modal-body p").text('問題が発生し、操作が完了できませんでした。問題が続く場合は、管理者にお問い合わせください。');
                    $("#modalError").modal("show");
                }
            });

        },


        /**
         * Change status face recognition
         *
         * @param e
         *
         * @author <tinhvd@nal.vn>
         */
        change_status_face_recognition: function (e) {

            // Change status device
            $.ajax({
                'type': 'post',
                'url': "/api/tour/change_status_face_recognition",
                data: {
                    app_id: app_id,
                    face_recognition_mode : face_recognition_mode
                },
                success: function (res) {
                    if (!res.success || !res.submit) {
                        return
                    }
                    location.reload();
                },
                error: function (res) {
                    $("#modalError").modal("show");
                }
            });

        },

        changeNoteTicket: function (e) {
            var target = $(e.currentTarget);

            if (target.attr("disabled") == "disabled") {
                return;
            }

            var id = target.attr("data-id");
            var content = $('#content-note').val();
            target.attr("disabled", true).html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>  保存');

            Backbone.ajax({
                method: 'POST',
                url: "/api/ticket/update_note",
                data: {
                    sptx_id: id,
                    note: content,
                    app_id: app_id,
                    kogyo_code: kogyo_code,
                    kogyo_sub_code: kogyo_sub_code,
                    koen_code: koen_code
                },
                success: function (data) {
                    $('#tr_' + id + ' .show-note').attr('data-note', content);
                    $('#modalManual').modal('hide');
                    target.attr("disabled", false).html('<i class="zmdi zmdi-check-circle"></i>  保存');
                }
            });
        },

        /**
         * Change status ticket
         *
         * @param e
         *
         * @author <hieunt1@nal.vn>
         */
        changeStatus: (e) => {
            let id = $(e.currentTarget).data("id");
            let status = $(e.currentTarget).val();
            let htmlProgress = '<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>';

            if (! $(e.currentTarget).hasClass('x-btn-change-to-passed')) {
                var old_status = $(e.currentTarget).data('old-status');
                $('select.selectpicker[data-id="'+ id +'"]').val(old_status);
                $('select.selectpicker[data-id="'+ id +'"]').selectpicker('refresh');
            }

            if (status == TICKET_STATUS_NOT_YET) {
                $('#modalTicketCancelApproval').find('.x-btn-change-to-passed').data('id', id);
                $('#modalTicketCancelApproval').modal('show');
            } else {
                $('#tr_' + id + ' .gate_pass_at').text('').append(htmlProgress);
                $('#tr_' + id + ' .last_login_name').text('').append(htmlProgress);
                $('#tr_' + id + ' .x-load-popup button').attr('disabled', 'disabled');
                if ($(e.currentTarget).hasClass('x-btn-change-to-passed')) {
                    status = TICKET_STATUS_NOT_YET;
                }
                Backbone.ajax({
                    method: 'POST',
                    url: "/api/ticket/change_status",
                    data: {
                        sptx_id: id,
                        status: status,
                        app_id: app_id,
                        kogyo_code: kogyo_code,
                        kogyo_sub_code: kogyo_sub_code,
                        koen_code: koen_code
                    },
                    success: function (data) {
                        if (!data.submit) {
                            $('#modalAlert #message').text(data.errmsg || "ステータスが変更できませんでした。");
                            $('#modalAlert').modal('show');
                        }

                        $('#tr_' + id).find('select.selectpicker').data('old-status', status);
                        $('select.selectpicker[data-id="'+ id +'"]').val(status);
                        $('select.selectpicker[data-id="'+ id +'"]').selectpicker('refresh');

                        if (status == TICKET_STATUS_PASS) {
                            $('#tr_' + id + ' .gate_pass_at').text(data.result.time);
                            $('#tr_' + id + ' .last_login_name').text(data.result.gate_name);
                        } else {
                            $('#tr_' + id + ' .gate_pass_at').text('-');
                            $('#tr_' + id + ' .last_login_name').text('-');
                        }

                        $('#tr_' + id + ' .x-load-popup button').removeAttr('disabled');
                    }
                });
            }

        },

        /**
         * Check csv import status
         *
         * @author <hieunt1@nal.vn>
         */
        checkImportCsv: function () {
            var inter_val = setInterval(function () {
                Backbone.ajax({
                    method: 'POST',
                    url: "/api/event/csv_import_status",
                    data: {
                        id: csv_history_id
                    },
                    success: function (data) {
                        switch (eval(data.result.status)) {
                            case 0:
                            case 1:
                                break;
                            case 2:
                                $('#btn-upload-csv i').attr('class', '').attr('class', 'zmdi zmdi-upload');

                                $('#modalAlert #message').html('データの読み込みが完了しました。');
                                $('#modalAlert').modal('show');

                                clearInterval(inter_val);
                                break;
                            default:
                                $('#btn-upload-csv i').attr('class', '').attr('class', 'zmdi zmdi-upload');

                                $('#modalAlert #message').css('color', 'red').text(data.result.message);
                                $('#modalAlert').modal('show');

                                clearInterval(inter_val);
                                break;
                        }
                    }
                });
            }, 5000);
        },

        change_dynamo_db_status: function (e) {
            var me = this;

            var target = $(e.currentTarget);
            target.text('').html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>作成中');
            url = '/api/event/create_data_dynamo';
            $('#btn-upload-csv').hide();

            Backbone.ajax({
                method: 'POST',
                url: url,
                data: {
                    app_id: app_id
                },
                success: function (data) {
                    me.checkCreateDynamoData(app_id);
                }, 
                error: function (data) {
                    target.text('登録');
                    $("#modalError").modal("show");
                    return;
                }
            })

        },

        /**
         *
         *
         * @param app_id
         */
        checkCreateDynamoData: function (app_id) {
            var dynamo_status_val = setInterval(function () {
                $('.btn-show-on-open').hide();
                $('.show-on-creating').show();
                Backbone.ajax({
                    method: 'POST',
                    url: "/api/event/get_dynamo_status",
                    data: {
                        app_id: app_id
                    },
                    success: function (data) {
                        if (data.result.status == 1) {
                            clearInterval(dynamo_status_val);

                            $('#modalAlert #message').html('DynamoDBの作成が完了しました。');
                            $('#modalAlert').modal('show');
                        }
                    }
                });
            }, 10000);
        },
    })

})();
