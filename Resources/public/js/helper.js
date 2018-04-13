/**
 * @author Piotr Ćwięcek <pcwiecek@divante.pl>
 * @author Kamil Karkus <kkarkus@divante.pl>
 * @author Korneliusz Kirsz <kkirsz@divante.pl>
 */
pimcore.registerNS("pimcore.plugin.divantenotifications.helper.x");


pimcore.plugin.divantenotifications.helper.updateCount = function (count) {
    if (count > 0) {
        Ext.get("notification_value").show();
        Ext.fly('notification_value').update(count);
    } else {
        Ext.get("notification_value").hide();
    }
};

pimcore.plugin.divantenotifications.helper.showNotifications = function (notifications) {
    for (var i = 0; i < notifications.length; i++) {
        var row = notifications[i];
        var tools = [];
        tools.push({
            type: 'save',
            tooltip: t('mark_as_read'),
            handler: (function (row) {
                return function () {
                    this.up('window').close();
                    pimcore.plugin.divantenotifications.helper.markAsRead(row.id);
                }
            }(row))
        });
        if (row.linkedElementId) {
            tools.push({
                type: 'right',
                tooltip: t('open_linked_element'),
                handler: (function (row) {
                    return function () {
                        this.up('window').close();
                        pimcore.plugin.divantenotifications.helper.openLinkedElement(row);
                    }
                }(row))
            });
        }
        tools.push({
            type: 'maximize',
            tooltip: t('open'),
            handler: (function (row) {
                return function () {
                    this.up('window').close();
                    pimcore.plugin.divantenotifications.helper.openDetails(row.id);
                }
            }(row))
        });
        var notification = Ext.create('Ext.window.Toast', {
            iconCls: 'pimcore_icon_' + row.type,
            title: row.title,
            html: row.message,
            autoShow: true,
            width: 400,            
            height: 150,            
            closable: true,
            autoClose: false,
            tools: tools
        });
        notification.show();
    }
};

pimcore.plugin.divantenotifications.helper.markAsRead = function (id, callback) {
    Ext.Ajax.request({
        url: "/admin/notification/mark-as-read?id=" + id,
        success: function (response) {
            if (callback) {
                callback();
            }
        }
    });
};

pimcore.plugin.divantenotifications.helper.openLinkedElement = function (row) {
    if ('document' == row['linkedElementType']) {
        pimcore.helpers.openElement(row['linkedElementId'], 'document');
    } else if ('asset' == row['linkedElementType']) {
        pimcore.helpers.openElement(row['linkedElementId'], 'asset');
    } else if ('object' == row['linkedElementType']) {
        pimcore.helpers.openElement(row['linkedElementId'], 'object');
    }
};

pimcore.plugin.divantenotifications.helper.openDetails = function (id, callback) {
    Ext.Ajax.request({
        url: "/admin/notification/find?id=" + id,
        success: function (response) {
            response = Ext.decode(response.responseText);
            if (!response.success) {
                return;
            }
            pimcore.plugin.divantenotifications.helper.openDetailsWindow(
                response.data.id,
                response.data.title,
                response.data.message,
                response.data.type,
                callback
            );
        }
    });
};

pimcore.plugin.divantenotifications.helper.openDetailsWindow = function (id, title, message, type, callback) {
    var notification = new Ext.Window({
        modal: true,
        iconCls: 'pimcore_icon_' + type,
        title: title,
        html: message,
        autoShow: true,
        width: 700,
        height: 350,
        scrollable: true,
        closable: true,
        maximizable: true,
        bodyStyle: "background:#fff;",
        bodyPadding: "10px",
        autoClose: false,
        listeners: {
            focusleave: function () {
                this.close();
            },
            afterrender: function () {
                pimcore.plugin.divantenotifications.helper.markAsRead(id, callback);
            }
        }
    });
    notification.show(document);
    notification.focus();
};

pimcore.plugin.divantenotifications.helper.delete = function (id, callback) {
    Ext.Ajax.request({
        url: "/admin/notification/delete?id=" + id,
        success: function (response) {
            if (callback) {
                callback();
            }
        }
    });
};

pimcore.plugin.divantenotifications.helper.deleteAll = function (callback) {
    Ext.Ajax.request({
        url: "/admin/notification/delete-all",
        success: function (response) {
            if (callback) {
                callback();
            }
        }
    });
};
