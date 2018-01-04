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

        var element = '<li id="pimcore_notification" data-menu-tooltip="' + t("Notifications") + '" class="pimcore_menu_needs_children">'
                    + '<svg id="gicsgdfk" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">'
                    + '<path style="fill:#78909C;" d="M42.3,12.8c1.1,0.7,1.7,2,1.7,3.3V37c0,2.2-1.8,4-4,4H8c-2.2,0-4-1.8-4-4V16.1c0-1.3,0.6-2.5,1.7-3.3"/>'
                    + '<path style="fill:#CFD8DC;" d="M40,41H8c-2.2,0-4-1.8-4-4V17l20,13l20-13v20C44,39.2,42.2,41,40,41z"/>'
                    + '</svg>'
                    + '<span id="notification_value" style="display:none;"></span>'
                    + '</li>';

        this.navEl = Ext.get("pimcore_menu_search").insertSibling(element, "after");
        this.menu = new Ext.menu.Menu({
            items: [
                {
                    text: t("Notifications"),
                    iconCls: "pimcore_icon_object",
                    handler: this.showNotificationTab.bind(this)
                },
                {
                    text: t("Notification actions"),
                    iconCls: "pimcore_icon_object",
                    handler: this.showActionTab.bind(this)
                }
            ],
            cls: "pimcore_navigation_flyout"
        });
        pimcore.layout.toolbar.prototype.notificationMenu = this.menu;
    },

    showNotificationTab: function () {
        try {
            pimcore.globalmanager.get("notifications").activate();
        }
        catch (e) {
            pimcore.globalmanager.add("notifications", new pimcore.plugin.divantenotifications.panel());
        }
    },

    showActionTab: function () {
        try {
            pimcore.globalmanager.get("notification_actions").activate();
        }
        catch (e) {
            pimcore.globalmanager.add("notification_actions", new pimcore.plugin.divantenotifications.actions());
        }
    },

    pimcoreReady: function (params, broker) {
        var toolbar = pimcore.globalmanager.get("layout_toolbar");
        this.navEl.on("mousedown", toolbar.showSubMenu.bind(toolbar.notificationMenu));
        pimcore.plugin.broker.fireEvent("notificationMenuReady", toolbar.notificationMenu);
        this.startConnection();
    },

    postOpenObject: function (object, type) {
        var key = 'send_notification_button_' + object.id;
        var value = new pimcore.plugin.divantenotifications.button(object);
        pimcore.globalmanager.add(key, value);
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
    }
});

var divantenotificationsPlugin = new pimcore.plugin.divantenotifications.plugin();