Ext.define('App.main', {
    extend: 'Ext.window.Window',

    getLocationStore: function() {
        var me = this;
        if (! me.locationStore) {
            me.locationStore = Ext.create('Ext.data.Store', {
                fields: ['id', 'name'],
                proxy: {
                    type: 'ajax',
                    url: '/location/list',
                    reader: {
                        type: 'json',
                        rootProperty: 'data'
                    }
                }
            });
        }
        return me.locationStore;
    },

    getCameraStore: function() {
        var me = this;
        if (! me.cameraStore) {
            me.cameraStore = Ext.create('Ext.data.Store', {
                fields: ['id', 'name'],
                proxy: {
                    type: 'ajax',
                    url: '/camera/list',
                    reader: {
                        type: 'json',
                        rootProperty: 'data'
                    },
                    extraParams: {
                        location_id: null
                    }
                }
            });
        }
        return me.cameraStore;
    },

    getImageStore: function() {
        var me = this;
        if (! me.imageStore) {
            me.imageStore = Ext.create('Ext.data.Store', {
                fields: ['id', 'name'],
                proxy: {
                    type: 'ajax',
                    url: '/image/list',
                    reader: {
                        type: 'json',
                        rootProperty: 'data'
                    },
                    extraParams: {
                        taken_date: null,
                        location_id: null,
                        camera_id: null
                    }
                }
            });
        }
        return me.imageStore;
    },

    renderImage: function(val, meta, rec) {
        return '<img src="/image/display/' + rec.data.id + '" width=200/>';
    },

    getImageGrid: function() {
        var me = this;
        if (! me.imageGrid) {
            me.imageGrid = Ext.create('Ext.grid.Panel', {
                region: 'center',
                store: me.getImageStore(),
                columns: [
                    {text: 'Image', dataIndex: 'filename', width: 200},
                    {dataIndex: 'dummy', flex: 1, renderer: me.renderImage, flex: 1}
                    // {text: 'Date', dataIndex: 'taken_date', flex: 1},
                    // {text: 'Time', dataIndex: 'taken_time', flex: 1},
                ],
                bbar: {
                    xtype: 'pagingtoolbar',
                    displayInfo: true
                }
            });
        }
        return me.imageGrid;
    },

    createWindow: function () {
        var me = this;

        var win = Ext.create('Ext.window.Window', {
            closable: false,
            width: '90%',
            height: '90%',
            header: false,
            layout: 'fit',
            tbar: [
                {
                    xtype: 'datefield',
                    fieldLabel: 'Date',
                    labelWidth: 50,
                    width: 180,
                    format: 'Y.m.d',
                    submitFormat: 'Y-m-d',
                    listeners: {
                        change: function(cmp, val) {
                            me.getImageStore().proxy.extraParams.taken_date = val;
                            me.getImageStore().loadPage(1);
                        }
                    }
                }, {
                    xtype: 'combo',
                    fieldLabel: 'Location',
                    labelWidth: 50,
                    store: me.getLocationStore(),
                    valueField: 'id',
                    displayField: 'name',
                    editable: false,
                    listeners: {
                        change: function(cmp, id) {
                            me.getImageStore().proxy.extraParams.location_id = id;
                            me.getCameraStore().proxy.extraParams.location_id = id;
                            me.getCameraStore().loadPage(1);
                        }
                    }
                }, {
                    xtype: 'combo',
                    fieldLabel: 'Camera',
                    labelWidth: 50,
                    store: me.getCameraStore(),
                    valueField: 'id',
                    displayField: 'name',
                    editable: false,
                    listeners: {
                        change: function(cmp, id) {
                            me.getImageStore().proxy.extraParams.camera_id = id;
                            me.getImageStore().loadPage(1);
                        }
                    }
                }
            ],
            items: [
                me.getImageGrid()
            ]
        })
        win.show();
    },

});