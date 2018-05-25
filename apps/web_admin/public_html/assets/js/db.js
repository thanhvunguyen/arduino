window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;

window.IDBTransaction = window.IDBTransaction || window.webkitIDBTransaction || window.msIDBTransaction || {READ_WRITE: "readwrite"};
window.IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange || window.msIDBKeyRange;

if (!window.indexedDB) {
    window.alert("Your browser doesn't support a stable version of IndexedDB. Such and such feature will not be available.");
}
window.process = null;
var db, data_event;
var db_name = "event_" + event_code;

// Delete database
localStorage.removeItem([window.kogyo_code, window.kogyo_sub_code, window.koen_code].join('-'));
window.indexedDB.deleteDatabase(db_name);

var connect = window.indexedDB.open(db_name);
var first_time = false;
data_event = {
    kogyo_code: window.kogyo_code || '999999',
    kogyo_sub_code: window.kogyo_sub_code || '0001',
    koen_code: window.koen_code || '001',
    app_id: window.app_id || '999999-0001-001'
};

connect.onerror = function (event) {
    alert("Why didn't you allow my web app to use IndexedDB?!");
};

connect.onsuccess = function (event) {
    db = event.target.result;

    alasql.promise('ATTACH INDEXEDDB DATABASE ' + db_name + ';' + 'USE ' + db_name).then(function (data) {
        (first_time) ? insert_data_seats(data_event) : init_view_ticket();
    });
};

connect.onupgradeneeded = function (event) {

    show_process_sync();
    var result = event.target.result;
    first_time = true;

    create_device_table(result);
    create_scan_table(result);
    create_seat_ticket(result);
    create_ticket_table(result);
};

/**
 * Insert list seat of ticket
 */
function insert_data_seats(data) {
    $.ajax({
        url: '/api/ticket/get_total_ticket_by_seat',
        method: 'POST',
        data: {
            kogyo_code: data.kogyo_code,
            app_id: data.app_id,
            kogyo_sub_code: data.kogyo_sub_code,
            koen_code: data.koen_code,
            page: data.page
        },
        success: function (output) {
            alasql.promise('SELECT * INTO seats FROM ?', [output.result.items])
                .then(function (data) {
                    get_list_device();
                });
        }
    });

}

/**
 * Get list device of event
 */
function get_list_device() {
    $.ajax({
        url: "/api/device/get_list_device",
        method: 'POST',
        data: {
            kogyo_code: window.kogyo_code,
            kogyo_sub_code: window.kogyo_sub_code,
            koen_code: window.koen_code,
            app_id: window.app_id
        },
        success: function (output) {
            insert_data_device(output.result.items);
        }
    });
}

/**
 * Insert list device
 *
 * @param data
 * @returns {boolean}
 */
function insert_data_device(data) {
    if (!data.length) {
        return init_view_ticket();
    }

    alasql.promise('SELECT * INTO devices FROM ?', [data])
        .then(function (res) {
            insert_data_scan_by_csv();
        });
}

function insert_data_scan_by_csv() {
    // TODO : Get list scan to csv
    $.ajax({
        url: '/api/ticket/get_csv_link',
        method: 'POST',
        data: {app_id: window.app_id},
        success: function (output) {
            if (!output.result.csv_link) {
                get_last_time_scan();
                return;
            }

            alasql.promise('SELECT * INTO scan FROM CSV("' + output.result.csv_link + '", {header:true})').then(function (data) {
                get_last_time_scan();
            }).catch(function (err) {

            })
        }
    })
}

function get_last_time_scan() {
    alasql.promise('SELECT * scan FROM scan ORDER BY dynamo_created_at DESC LIMIT 1').then(function (data) {
        init_view_ticket();
    })
}

function init_view_ticket() {
    alasql.promise('SELECT * FROM scan ORDER BY created_at DESC LIMIT 1').then(function (data) {
        window.ticket = new IOT_ADMIN.Ticket({
            el: $('.x-load-popup'),

            app_id: window.app_id,
            kogyo_code: window.kogyo_code,
            kogyo_sub_code: window.kogyo_sub_code,
            koen_code: window.koen_code
        });

        if (!window.ticket.get_last_scan() && data[0]) {
            window.ticket.set_last_scan(data[0]);
        }

        window.ticket.get_data_scan();
        window.ticket.get_list_seat();
        window.ticket.get_list_device();

        var check_process = setInterval(function () {
            if (window.ticket.process == window.ticket.process_done) {
                hide_process_sync();

                clearInterval(check_process);
            }
        }, 500);

    });
}

function show_process_sync() {
    $('#modal_sync_scan').modal('show');

    var i = 1;
    window.process = setInterval(function () {
        if (i == 4) {
            i = 1;
        }

        var str = '...';
        $('.process-sync').text(str.substring(0, i));

        i++;
    }, 1000);
}

function hide_process_sync() {
    clearInterval(window.process);

    $('#modal_sync_scan').modal('hide');
}

/**
 * Create ticket table
 *
 * @param db
 */
function create_ticket_table(db) {
    var object_store = db.createObjectStore("tickets", {keyPath: 'tx_barcode_num'});

    object_store.createIndex("sptx_id", "sptx_id", {unique: false});
    object_store.createIndex("first_name_kana", "first_name_kana", {unique: false});
    object_store.createIndex("last_name_kana", "last_name_kana", {unique: false});
}

/**
 * Create device table
 *
 * @param db
 */
function create_device_table(db) {
    var object_store = db.createObjectStore("devices", {keyPath: 'device_id'});

    object_store.createIndex("device_id", "device_id", {unique: false});
    object_store.createIndex("gate_name", "gate_name", {unique: false});
    object_store.createIndex("gate_keeper_name", "gate_keeper_name", {unique: false});
    object_store.createIndex("status", "status", {unique: false});
    object_store.createIndex("created_at", "created_at", {unique: false});
    object_store.createIndex("updated_at", "updated_at", {unique: false});
}

/**
 * Create scan table
 *
 * @param db
 */
function create_scan_table(db) {
    var object_store = db.createObjectStore("scan", {keyPath: 'scan_id'});

    object_store.createIndex("scan_id", "scan_id", {unique: false});
    object_store.createIndex("tx_barcode_num", "tx_barcode_num", {unique: false});
    object_store.createIndex("device_id", "device_id", {unique: false});
    object_store.createIndex("scanned_at", "scanned_at", {unique: false});
    object_store.createIndex("status", "status", {unique: false});
    object_store.createIndex("canceled_id", "canceled_id", {unique: false});
    object_store.createIndex("tx_type_text", "tx_type_text", {unique: false});
    object_store.createIndex("gatepassed_at", "gatepassed_at", {unique: false});
    object_store.createIndex("first_name_kana", "first_name_kana", {unique: false});
    object_store.createIndex("last_name_kana", "last_name_kana", {unique: false});
    object_store.createIndex("created_at", "created_at", {unique: false});
}

function create_seat_ticket(db) {
    var object_store = db.createObjectStore("seats", {keyPath: 'tx_type_text'});

    object_store.createIndex("total", "total", {unique: false});
}

function insert_ticket_by_csv() {

    alasql.promise('SELECT * INTO tickets FROM CSV("http://nexgen_ticket.local/uploads/tickets.csv")').then(function (data) {

    });
}

// GET MINUTE ALASQL
alasql.fn.MINUTE = function (time) {
    return parseInt(moment(time).format('mm'));
};