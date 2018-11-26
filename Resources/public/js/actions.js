/**
 * @author Korneliusz Kirsz <kkirsz@divante.pl>
 */
pimcore.registerNS("pimcore.plugin.divantenotifications.actions");

pimcore.plugin.divantenotifications.actions = Class.create({

    initialize: function () {
        this.getTabPanel();
    },

    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.setActiveItem("notification_actions");
    },

    getTabPanel: function () {

        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: "notification_actions",
                title: t("Notification actions"),
                iconCls: "pimcore_icon_object",
                border: false,
                layout: "fit",
                closable: true,
                items: [this.getRowEditor()]
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem("notification_actions");

            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("notification_actions");
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    getRowEditor: function () {

        var url = '/admin/notification-action/index?';

        this.store = pimcore.helpers.grid.buildDefaultStore(
            url,
            [
                {name: 'id'},
                {name: 'text', allowBlank: false}
            ],
            null,
            {
                remoteSort: false,
                remoteFilter: false
            }
        );
        this.store.setAutoSync(true);

        var propertiesColumns = [
            {
                header: t("Text"),
                flex: 100,
                sortable: true,
                dataIndex: 'text',
                editor: new Ext.form.TextField({})
            },
            {
                xtype: 'actioncolumn',
                width: 30,
                items: [
                    {
                        tooltip: t('delete'),
                        icon: "/bundles/pimcoreadmin/img/flat-color-icons/delete.svg",
                        handler: function (grid, rowIndex) {
                            grid.getStore().removeAt(rowIndex);
                        }.bind(this)
                    }
                ]
            }
        ];

        this.cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1
        });

        this.grid = Ext.create('Ext.grid.Panel', {
            frame: false,
            autoScroll: true,
            store: this.store,
            columnLines: true,
            bodyCls: "pimcore_editable_grid",
            stripeRows: true,
            trackMouseOver: true,
            columns : propertiesColumns,
            selModel: Ext.create('Ext.selection.RowModel', {}),
            plugins: [
                this.cellEditing
            ],
            tbar: [
                {
                    text: t('add'),
                    handler: this.onAdd.bind(this),
                    iconCls: "pimcore_icon_add"
                }
            ],
            viewConfig: {
                forceFit: true
            }
        });

        return this.grid;
    },

    onAdd: function (btn, ev) {
        this.grid.store.insert(0, {
            text: t("New notification action")
        });
    }
});
