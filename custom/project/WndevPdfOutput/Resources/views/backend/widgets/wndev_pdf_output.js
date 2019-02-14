
//{block name="backend/index/application" append}
Ext.define('Shopware.apps.WndevPdfOutput.widgets.WndevPdfOutput', {
    extend: 'Shopware.apps.Index.view.widgets.Base',

    alias: 'widget.wndev_pdf_output',

    layout: 'fit',

    initComponent: function () {
        var me = this;

        me.items = me.getItems();

        me.callParent(arguments);
    },

    getItems: function () {
        var me = this;

        return [
            {
                xtype: 'grid',
                store: me.getWidgetStore(),
                viewConfig: {
                    hideLoadingMsg: true
                },
                border: 0,
                columns: [
                    {
                        dataIndex: 'id',
                        header: 'ID',
                        flex: 1
                    },
                    {
                        dataIndex: 'name',
                        header: 'Name',
                        flex: 1
                    }
                ]
            }
        ];
    },

    getWidgetStore: function () {
        var me = this;

        return Ext.create('Ext.data.Store', {
            fields: [
                { name: 'id', type: 'integer' },
                { name: 'name', type: 'string' }
            ],
            proxy: {
                type: 'ajax',
                url: '{url controller=WndevPdfOutputWidget action=loadBackendWidget}',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            autoLoad: true
        });
    }



});
//{/block}