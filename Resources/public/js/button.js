/**
 * @author Korneliusz Kirsz <kkirsz@divante.pl>
 */
pimcore.registerNS("pimcore.plugin.divantenotifications.button");

pimcore.plugin.divantenotifications.button = Class.create({

    initialize: function (object) {
        this.object = object;
        this.getButton();
    },

    getButton: function () {

        if (!this.button) {
            this.button = new Ext.Button({
                tooltip: "Send notification",
                iconCls: "pimcore_status_notification",
                scale: "medium",
                handler: function () {
                    new pimcore.plugin.divantenotifications.window(this.object);
                }.bind(this)
            });

            this.button.on('destroy', function () {
                pimcore.globalmanager.remove('send_notification_button_' + this.object.id);
            }.bind(this));

            var toolbar = Ext.getCmp('object_toolbar_' + this.object.id);
            var items = toolbar.items.items;

            for (var i = 1; i < items.length; i++) {
                if (items[i - 1].xtype === 'tbseparator') {
                    toolbar.insert(i, this.button);
                    break;
                }
            }
        }

        return this.button;
    }
});
