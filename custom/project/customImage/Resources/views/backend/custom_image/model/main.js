
Ext.define('Shopware.apps.customImage.model.Main', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'customImage',
            detail: 'Shopware.apps.customImage.view.detail.Container'
        };
    },


    fields: [
        { name : 'id', type: 'int', useNull: true },
        { name : 'name', type: 'string', useNull: false }
    ]
});

