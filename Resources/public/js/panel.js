/**
 * @author Piotr Ćwięcek <pcwiecek@divante.pl>
 * @author Kamil Karkus <kkarkus@divante.pl>
 * @author Korneliusz Kirsz <kkirsz@divante.pl>
 */
pimcore.registerNS("pimcore.plugin.divantenotifications.panel");

pimcore.plugin.divantenotifications.panel = Class.create({

    initialize: function () {
        this.getTabPanel();
    },

    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.setActiveItem("pimcore_notification_panel");
    },

    getTabPanel: function () {

        this.panel = new Ext.Panel({
            id: 'pimcore_notification_panel',
            title: t("notifications"),
            iconCls: "pimcore_icon_email",
            layout: 'vbox',
            items: [
                {
                    xtype: 'panel',
                    layout: 'fit',
                    flex: 2,
                    items: [
                        this.getGrid()
                    ]
                },
                {
                    xtype: 'panel',
                    flex: 1,
                    html: 'Some wonderful information',
                }
            ]
        });

        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.add(this.panel);
        tabPanel.setActiveItem("pimcore_notification_panel");

        this.panel.on("destroy", function () {
            pimcore.globalmanager.remove("notifications");
        }.bind(this));

        pimcore.layout.refresh();

        return this.panel;

        if (!this.panel) {
            var gridPanel = new Ext.Panel({
                id: 'gridPanel',
                region: 'center',
                layout: "fit",
                items: [
                    this.getGrid()
                ]
            });

            this.panel = new Ext.Panel({
                id: "pimcore_notification_panel",
                title: t("notifications"),
                iconCls: "pimcore_icon_email",
                border: false,
                layout: 'vbox',
                closable: true,
                items: [
                    gridPanel
                ],
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem("pimcore_notification_panel");


            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("notifications");
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    getGrid: function () {

        this.grid = new Ext.grid.Panel({
            width: '100%',
            columns: [
                {
                    text: 'ID'
                },
                {
                    text: t('title')
                },
                {
                    text: t('from')
                },
                {
                    text: t('date')
                }
            ]
        });

        return this.grid;

        var itemsPerPage = pimcore.helpers.grid.getDefaultPageSize();
        this.store = pimcore.helpers.grid.buildDefaultStore(
            '/admin/notification/find-all?',
            ["id", "title", "from", "date", "unread"],
            itemsPerPage
        );

        var typesColumns = [
            {header: "ID", flex: 1, sortable: false, hidden: true, dataIndex: 'id'},
            {
                header: t("title"),
                flex: 10,
                sortable: true,
                filter: 'string',
                dataIndex: 'title',
                renderer: function (val, metaData, record, rowIndex, colIndex, store) {
                    var unread = parseInt(store.getAt(rowIndex).get("unread"));
                    if (unread) {
                        return '<strong style="font-weight: bold;">' + val + '</strong>'; // css style need to be added
                    }
                    return val;
                }
            },
            {header: t("from"), flex: 2, sortable: false, dataIndex: 'from'},
            {header: t("date"), flex: 3, sortable: true, filter: 'date', dataIndex: 'date'},
            {
                header: t("element"),
                xtype: 'actioncolumn',
                flex: 1,
                items: [
                    {
                        tooltip: t('open_linked_element'),
                        icon: "/pimcore/static6/img/flat-color-icons/cursor.svg",
                        handler: function (grid, rowIndex) {
                            pimcore.plugin.divantenotifications.helper.openLinkedElement(grid.getStore().getAt(rowIndex).data);
                        }.bind(this),
                        isDisabled: function (grid, rowIndex) {
                            return !parseInt(grid.getStore().getAt(rowIndex).data['linkedElementId']);
                        }.bind(this)
                    }
                ]
            },
            {
                xtype: 'actioncolumn',
                flex: 1,
                items: [
                    {
                        tooltip: t('open'),
                        icon: "/pimcore/static6/img/flat-color-icons/right.svg",
                        handler: function (grid, rowIndex) {
                            pimcore.plugin.divantenotifications.helper.openDetails(grid.getStore().getAt(rowIndex).get("id"), function() {
                                this.reload();
                            }.bind(this));
                        }.bind(this)
                    },
                    {
                        tooltip: t('mark_as_read'),
                        icon: '/pimcore/static6/img/flat-color-icons/checkmark.svg',
                        handler: function (grid, rowIndex) {
                            pimcore.plugin.divantenotifications.helper.markAsRead(grid.getStore().getAt(rowIndex).get("id"), function () {
                                this.reload();
                            }.bind(this));
                        }.bind(this),
                        isDisabled: function (grid, rowIndex) {
                            return !parseInt(grid.getStore().getAt(rowIndex).get("unread"));
                        }.bind(this)
                    },
                    {
                        tooltip: t('delete'),
                        icon: '/pimcore/static6/img/flat-color-icons/delete.svg',
                        handler: function (grid, rowIndex) {
                            pimcore.plugin.divantenotifications.helper.delete(grid.getStore().getAt(rowIndex).get("id"), function () {
                                this.reload();
                            }.bind(this));
                        }.bind(this)
                    }

                ]
            }
        ];

        this.pagingtoolbar = pimcore.helpers.grid.buildDefaultPagingToolbar(this.store);

        var toolbar = Ext.create('Ext.Toolbar', {
            cls: 'main-toolbar',
            items: [
                {
                    text: t("delete_all"),
                    iconCls: "pimcore_icon_delete",
                    handler: function() {
                        Ext.MessageBox.confirm(t("are_you_sure"), t("all_content_will_be_lost"),
                            function (buttonValue) {
                                if (buttonValue == "yes") {
                                    pimcore.plugin.divantenotifications.helper.deleteAll(function () {
                                        this.reload();
                                    }.bind(this));
                                }
                            }.bind(this));
                    }.bind(this)
                }
            ]
        });

        this.grid = new Ext.grid.GridPanel({
            frame: false,
            autoScroll: true,
            store: this.store,
            plugins: ['pimcore.gridfilters'],
            columns: typesColumns,
            trackMouseOver: true,
            bbar: this.pagingtoolbar,
            columnLines: true,
            stripeRows: true,
            listeners: {
                "itemdblclick": function (grid, record, tr, rowIndex, e, eOpts) {
                    pimcore.plugin.divantenotifications.helper.openDetails(record.data.id, function() {
                        this.reload();
                    }.bind(this));
                }.bind(this)

            },
            viewConfig: {
                forceFit: true
            },
            tbar: toolbar
        });

        return this.grid;
    },

    reload: function () {
        this.store.reload();
    }
});
