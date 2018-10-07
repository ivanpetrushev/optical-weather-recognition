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
                    format: 'Y.m.d',
                    submitFormat: 'Y-m-d'
                }, {
                    xtype: 'combo',
                    fieldLabel: 'Location',
                    store: me.getLocationStore(),
                    valueField: 'id',
                    displayField: 'name',
                    listeners: {
                        change: function(cmp, id) {
                            me.getCameraStore().proxy.extraParams.location_id = id;
                            me.getCameraStore().loadPage(1);
                        }
                    }
                }, {
                    xtype: 'combo',
                    fieldLabel: 'Camera',
                    store: me.getCameraStore(),
                    valueField: 'id',
                    displayField: 'name'
                }
            ],
            items: [
                // 'test'
            ]
        })
        win.show();
    },

});