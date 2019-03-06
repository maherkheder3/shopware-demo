
Ext.define('Shopware.apps.customImage.store.Main', {
    extend:'Shopware.store.Listing',

    configure: function() {
        return {
            controller: 'customImage'
        };
    },
    model: 'Shopware.apps.customImage.model.Main'
});