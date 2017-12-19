/**
 * @author Korneliusz Kirsz <kkirsz@divante.pl>
 */
pimcore.registerNS("pimcore.plugin.divantenotifications.window");

pimcore.plugin.divantenotifications.window = Class.create({

    initialize: function (object) {
        this.object = object;
        this.getWindow().show();
    },

    getWindow: function () {

        if (!this.window) {
            var usersStore = Ext.create('Ext.data.JsonStore', {
                proxy: {
                    type: 'ajax',
                    url: '/admin/notification/users'
                }
            });
            usersStore.load();

            var actionsStore = Ext.create('Ext.data.JsonStore', {
                proxy: {
                    type: 'ajax',
                    url: '/admin/notification/actions'
                }
            });
            actionsStore.load();

            var items = [
                {
                    xtype: "combobox",
                    name: "user",
                    fieldLabel: "User",
                    width: "100%",
                    forceSelection: true,
                    queryMode: "local",
                    store: usersStore,
                    valueField: "id",
                    displayField: "text",
                    allowBlank: false
                },
                {
                    xtype: "combobox",
                    name: "action",
                    fieldLabel: "Action",
                    width: "100%",
                    forceSelection: true,
                    queryMode: "local",
                    store: actionsStore,
                    valueField: "id",
                    displayField: "text",
                    allowBlank: false
                },
                {
                    xtype: "textareafield",
                    name: "note",
                    fieldLabel: "Note",
                    width: "100%",
                    allowBlank: false
                }
            ];

            var panel = new Ext.form.FormPanel({
                border: false,
                frame: false,
                bodyStyle: 'padding:10px',
                items: items,
                defaults: {
                    labelWidth: 100
                },
                collapsible: false,
                autoScroll: true
            });

            this.window = new Ext.Window({
                width: 560,
                iconCls: "pimcore_status_notification",
                title: "Send notification",
                layout: "fit",
                closeAction: "close",
                plain: true,
                autoScroll: true,
                modal: true,
                buttons: [
                    {
                        text: "Send",
                        iconCls: "pimcore_icon_accept",
                        handler: this.send.bind(this)
                    },
                    {
                        text: "Close",
                        iconCls: "pimcore_icon_cancel",
                        handler: this.close.bind(this)
                    }
                ]
            });

            this.window.add(panel);
        }

        return this.window;
    },

    send: function () {
        this.window.hide();
        this.window.destroy();
    },

    close: function () {
        this.window.hide();
        this.window.destroy();
    }
});
