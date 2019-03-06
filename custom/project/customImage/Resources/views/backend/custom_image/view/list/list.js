
Ext.define('Shopware.apps.customImage.view.list.List', {
    extend: 'Shopware.grid.Panel',
    alias:  'widget.custom-image-listing-grid',
    region: 'center',

    configure: function() {
        return {
            detailWindow: 'Shopware.apps.customImage.view.detail.Window'
        };
    }
});
