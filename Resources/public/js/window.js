/**
 * @author Korneliusz Kirsz <kkirsz@divante.pl>
 */
pimcore.registerNS("pimcore.plugin.divantenotifications.window");

pimcore.plugin.divantenotifications.window = Class.create({

    initialize: function (object) {        
        this.object = object;
        this.tab = Ext.getCmp("object_" + this.object.id);
        this.getWindow().show();
    },

    getWindow: function () {

        if (!this.window) {
            var usersStore = Ext.create("Ext.data.JsonStore", {
                proxy: {
                    type: "ajax",
                    url: "/admin/notification/users"
                }
            });
            usersStore.load();

            var actionsStore = Ext.create("Ext.data.JsonStore", {
                proxy: {
                    type: "ajax",
                    url: "/admin/notification/actions"
                }
            });
            actionsStore.load();

            var items = [
                {
                    xtype: "combobox",
                    name: "user",
                    fieldLabel: t("User"),
                    width: "100%",
                    forceSelection: true,
                    queryMode: "local",
                    anyMatch: true,
                    store: usersStore,
                    valueField: "id",
                    displayField: "text",
                    allowBlank: false,
                    blankText: t("This field is required"),
                    msgTarget: "under"
                },
                {
                    xtype: "combobox",
                    name: "action",
                    fieldLabel: t("Action"),
                    width: "100%",
                    forceSelection: true,
                    queryMode: "local",
                    anyMatch: true,
                    store: actionsStore,
                    valueField: "id",
                    displayField: "text",
                    allowBlank: false,
                    blankText: t("This field is required"),
                    msgTarget: "under"
                },
                {
                    xtype: "textareafield",
                    name: "note",
                    fieldLabel: t("Note"),
                    width: "100%",
                    allowBlank: false,
                    blankText: t("This field is required"),
                    msgTarget: "under"
                }
            ];

            var panel = new Ext.form.FormPanel({
                border: false,
                frame: false,
                bodyStyle: "padding:10px",
                url: "/admin/notification/send",
                items: items,
                defaults: {
                    labelWidth: 100
                },
                collapsible: false,
                autoScroll: true,
                buttons: [
                    {
                        text: t("Send"),
                        iconCls: "pimcore_icon_accept",
                        formBind: true,
                        handler: this.send.bind(this)
                    },
                    {
                        text: t("Close"),
                        iconCls: "pimcore_icon_cancel",
                        handler: this.close.bind(this)
                    }
                ]                
            });

            this.window = new Ext.Window({
                width: 560,
                iconCls: "pimcore_status_notification",
                title: t("Send notification"),
                layout: "fit",
                closeAction: "close",
                plain: true,
                autoScroll: true,
                modal: true
            });

            this.window.add(panel);
        }

        return this.window;
    },

    send: function () {
        var form = this.getWindow().down("form").getForm();
        if (form.isValid()) {
            this.getWindow().hide();                        
            this.tab.mask();
            form.submit({
                params: {objectId: this.object.id},
                success: this.onSuccess.bind(this),
                failure: this.onFailure.bind(this)
            });
        }
    },

    close: function () {
        this.getWindow().hide();
        this.getWindow().destroy();
    },
    
    onSuccess: function (form, action) {
        this.tab.unmask();
        pimcore.helpers.showNotification(t("success"), t("Notification has been sent"), "success");
        this.getWindow().destroy();
    },
    
    onFailure: function (form, action) {
        this.tab.unmask();
        this.getWindow().destroy();
    }
});
