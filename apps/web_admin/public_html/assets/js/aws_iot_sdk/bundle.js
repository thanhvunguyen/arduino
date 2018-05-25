(function e(t, n, r) {
    function s(o, u) {
        if (!n[o]) {
            if (!t[o]) {
                var a = typeof require == "function" && require;
                if (!u && a) return a(o, !0);
                if (i) return i(o, !0);
                var f = new Error("Cannot find module '" + o + "'");
                throw f.code = "MODULE_NOT_FOUND", f
            }
            var l = n[o] = {
                exports: {}
            };
            t[o][0].call(l.exports, function(e) {
                var n = t[o][1][e];
                return s(n ? n : e)
            }, l, l.exports, e, t, n, r)
        }
        return n[o].exports
    }
    var i = typeof require == "function" && require;
    for (var o = 0; o < r.length; o++) s(r[o]);
    return s
})({
    1: [function(require, module, exports) {
        /*
         * Copyright 2015-2016 Amazon.com, Inc. or its affiliates. All Rights Reserved.
         *
         * Licensed under the Apache License, Version 2.0 (the "License").
         * You may not use this file except in compliance with the License.
         * A copy of the License is located at
         *
         *  http://aws.amazon.com/apache2.0
         *
         * or in the "license" file accompanying this file. This file is distributed
         * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
         * express or implied. See the License for the specific language governing
         * permissions and limitations under the License.
         */

        /*
         * NOTE: You must set the following string constants prior to running this
         * example application.
         */

        var awsConfiguration = {
            poolId: AWS_COGNITO_IDENTITY_POOL_ID,
            host: AWS_IOT_ENDPOINT,
            region: AWS_REGION,
            accessKeyId: AWS_CREDENTIALS_ACCESS_KEY_ID,
            secretKey: AWS_CREDENTIALS_SECRET_KEY,
            sessionToken: AWS_CREDENTIALS_SESSION_TOKEN
        };

        module.exports = awsConfiguration;


    }, {}],
    2: [function(require, module, exports) {
        /*
         * Copyright 2015-2016 Amazon.com, Inc. or its affiliates. All Rights Reserved.
         *
         * Licensed under the Apache License, Version 2.0 (the "License").
         * You may not use this file except in compliance with the License.
         * A copy of the License is located at
         *
         *  http://aws.amazon.com/apache2.0
         *
         * or in the "license" file accompanying this file. This file is distributed
         * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
         * express or implied. See the License for the specific language governing
         * permissions and limitations under the License.
         */

        //
        // Instantiate the AWS SDK and configuration objects.  The AWS SDK for
        // JavaScript (aws-sdk) is used for Cognito Identity/Authentication, and
        // the AWS IoT SDK for JavaScript (aws-iot-device-sdk) is used for the
        // WebSocket connection to AWS IoT and device shadow APIs.
        //
        var AWS = require('aws-sdk');
        var AWSIoTData = require('aws-iot-device-sdk');
        var AWSConfiguration = require('./aws-configuration.js');

        var messageHistory = '';

        //
        // Create a client id to use when connecting to AWS IoT.
        //
        var clientId = 'iot-admin-' + SCAN_SUB_TITLE + (Math.floor((Math.random() * 1000000) + 1));

        //
        // Initialize our configuration.
        //
        AWS.config.region = AWSConfiguration.region;

        AWS.config.credentials = new AWS.CognitoIdentityCredentials({
            IdentityPoolId: AWSConfiguration.poolId
        });

        //
        // Create the AWS IoT device object.  Note that the credentials must be
        // initialized with empty strings; when we successfully authenticate to
        // the Cognito Identity Pool, the credentials will be dynamically updated.
        //
        const mqttClient = AWSIoTData.device({
            //
            // Set the AWS region we will operate in.
            //
            region: AWS.config.region,
            //
            ////Set the AWS IoT Host Endpoint
            host: AWSConfiguration.host,
            //
            // Use the clientId created earlier.
            //
            clientId: clientId,
            //
            // Connect via secure WebSocket
            //
            protocol: 'wss',
            //
            // Set the maximum reconnect time to 8 seconds; this is a browser application
            // so we don't want to leave the user waiting too long for reconnection after
            // re-connecting to the network/re-opening their laptop/etc...
            //
            maximumReconnectTimeMs: 8000,
            //
            // Enable console debugging information (optional)
            //
            debug: false,
            //
            // IMPORTANT: the AWS access key ID, secret key, and sesion token must be
            // initialized with empty strings.
            //
            accessKeyId: AWSConfiguration.accessKeyId,
            secretKey: AWSConfiguration.secretKey,
            sessionToken: AWSConfiguration.sessionToken
        });

        //
        // Attempt to authenticate to the Cognito Identity Pool.  Note that this
        // example only supports use of a pool which allows unauthenticated
        // identities.
        //
        var cognitoIdentity = new AWS.CognitoIdentity();
        AWS.config.credentials.get(function(err, data) {
            if (!err) {
                var params = {
                    IdentityId: AWS.config.credentials.identityId
                };
                cognitoIdentity.getCredentialsForIdentity(params, function(err, data) {
                    if (!err) {
                        //
                        // Update our latest AWS credentials; the MQTT client will use these
                        // during its next reconnect attempt.
                        //
                        mqttClient.updateWebSocketCredentials(
                            data.Credentials.AccessKeyId,
                            data.Credentials.SecretKey,
                            data.Credentials.SessionToken);
                    } else {

                    }
                });
            } else {

            }
        });

        //
        // Connect handler; update div visibility and fetch latest shadow documents.
        // Subscribe to lifecycle events on the first connect event.
        //
        window.mqttClientConnectHandler = function() {

            showConnectedStatus();
            // Subscribe to our current topic.
            //
            mqttClient.subscribe(SCAN_SUB_TITLE);
            mqttClient.subscribe(GATE_KEEPER_SUB_TITLE);

            var real_time_model = new RealTimeModel();
            real_time_model.on_reconnected_handler();
        };

        //
        // Reconnect handler; update div visibility.
        //
        window.mqttClientReconnectHandler = function(data) {

            showReconnectStatus();
        };

        //
        // Utility function to determine if a value has been defined.
        //
        window.isUndefined = function(value) {
            return typeof value === 'undefined' || typeof value === null;
        };

        //
        // Message handler for lifecycle events; create/destroy divs as clients
        // connect/disconnect.
        //
        window.mqttClientMessageHandler = function(topic, payload) {
            var real_time_model = new RealTimeModel();
            real_time_model.sub_message_handler(topic, payload);
        };

        window.mqttClientCloseHandler = function() {
            showCloseStatus();

            var xmlhttp;
            // compatible with IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    var data = JSON.parse(xmlhttp.responseText);

                    if (data.errcode == 401) {
                        location.reload();
                    }

                    mqttClient.updateWebSocketCredentials(data.result.AccessKeyId,
                        data.result.SecretAccessKey,
                        data.result.SessionToken);
                }
            };

            var real_time_model = new RealTimeModel();
            xmlhttp.open("GET", real_time_model.link_get_credentials(), true);
            xmlhttp.send();

        };

        window.mqttClientOffline = function () {

        };

        //
        // Install connect/reconnect event handlers.
        //
        mqttClient.on('close', window.mqttClientCloseHandler);
        mqttClient.on('offline', window.mqttClientOffline);
        mqttClient.on('connect', window.mqttClientConnectHandler);
        mqttClient.on('reconnect', window.mqttClientReconnectHandler);
        mqttClient.on('message', window.mqttClientMessageHandler);

    }, {
        "./aws-configuration.js": 1,
        "aws-iot-device-sdk": "aws-iot-device-sdk",
        "aws-sdk": "aws-sdk"
    }]
}, {}, [2]);