Ext.define('App.main', {
    extend: 'Ext.window.Window',

    createWindow: function () {
        var me = this;

        var win = Ext.create('Ext.window.Window', {
            closable: false,
            width: '90%',
            height: '90%',
            header: false,
            layout: 'fit',
            items: [
                // 'test'
            ]
        })
        win.show();
    },

});