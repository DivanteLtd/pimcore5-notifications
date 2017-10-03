/**
 * @author Kamil Karkus <kkarkus@divante.pl>
 * @author Korneliusz Kirsz <kkirsz@divante.pl>
 */
pimcore.registerNS("pimcore.plugin.divantenotifications.plugin");

pimcore.plugin.divantenotifications.plugin = Class.create(pimcore.plugin.admin, {

    getClassName: function () {
        return "pimcore.plugin.divantenotifications.plugin";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        this.addIcon();
        this.startConnection();
    },

    addIcon: function () {
        var html = '<div id="pimcore_status_notification" data-menu-tooltip="'
                 + t("pimcore_status_notification") + '" style="display:none; cursor:pointer;">'
                 + '<span id="notification_value" style="display:none;"></span></div>';

        var statusbar = Ext.get("pimcore_status");
        statusbar.insertHtml('afterBegin', html);

        Ext.get("pimcore_status_notification").show();
        Ext.get("pimcore_status_notification").on("click", this.showNotificationTab.bind());
    },

    startConnection: function () {
        Ext.Ajax.request({
            url: "/admin/notification/token",
            success: function (response) {
                var data = Ext.decode(response.responseText);

                var websocketProtocol = "ws:";
                if (location.protocol === "https:") {
                    websocketProtocol = "wss:";
                }

                var url = websocketProtocol + "//" + location.host + ':8080/'
                        + "?token=" + data['token'] + "&user=" + data['user'];
                this.socket = new WebSocket(url);

                this.socket.onopen = function (event) {
                };

                this.socket.onclose = function (event) {
                };

                this.socket.onerror = function (error) {
                    //cannot start websocket so start ajax
                    this.startAjaxConnection();
                }.bind(this);

                this.socket.onmessage = function (event) {
                    var msg = event.data;
                    var data = Ext.decode(msg);
                    var unreadCount = data['unread'];
                    var newNotifications = data['notifications'];
                    pimcore.plugin.divantenotifications.helper.updateCount(unreadCount);
                    pimcore.plugin.divantenotifications.helper.showNotifications(newNotifications);
                };
            }.bind(this)
        });
    },

    startAjaxConnection: function () {
        function runAjaxConnection () {
            Ext.Ajax.request({
                url: "/admin/notification/find-last-unread?interval=" + 30,
                success: function (response) {
                    var data = Ext.decode(response.responseText);
                    pimcore.plugin.divantenotifications.helper.updateCount(data.unread);
                    pimcore.plugin.divantenotifications.helper.showNotifications(data.data);
                }
            });
        }

        pimcore["intervals"]["checkNewNotification"] = window.setInterval(function (elt) {
            runAjaxConnection();
        }, 30000);
        runAjaxConnection(); // run at the Pimcore login
    },

    showNotificationTab: function () {
        try {
            pimcore.globalmanager.get("notifications").activate();
        }
        catch (e) {
            pimcore.globalmanager.add("notifications", new pimcore.plugin.divantenotifications.panel());
        }
    }
});

var divantenotificationsPlugin = new pimcore.plugin.divantenotifications.plugin();