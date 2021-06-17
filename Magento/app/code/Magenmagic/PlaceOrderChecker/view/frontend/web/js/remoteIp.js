define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery'
], function (Component, customerData, $) {
    'use strict';

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();

            if(JSON.parse(localStorage['mage-cache-storage']).mmremoteip === undefined
                || JSON.parse(localStorage['mage-cache-storage']).mmremoteip.ip === undefined) {
                customerData.reload(['mmremoteip'], false).done(
                    function (data){
                        if(data.mmremoteip !== undefined && data.mmremoteip.ip !== undefined) {
                            window.remoteIp = data.mmremoteip.ip;
                        }
                    })
            } else {
                window.remoteIp = JSON.parse(localStorage['mage-cache-storage']).mmremoteip.ip;
            }
        }
    });


});
