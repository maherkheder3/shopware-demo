
Ext.define('Shopware.apps.customImage.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.custom-image-list-window',
    height: 450,
    title : '{s name=window_title}customImage listing{/s}',

    configure: function() {
        return {
            listingGrid: 'Shopware.apps.customImage.view.list.List',
            listingStore: 'Shopware.apps.customImage.store.Main'
        };
    }
});