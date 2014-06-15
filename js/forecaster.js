var Forecaster = (function () {
    "use strict";

    return new Class({
        Implements: [Events, Options],

        options: {},

        initialize: function (options) {
            this.setOptions(options);
        }
    });
})();

document.addEvent('domready', function () {
    "use strict";

    document.forecaster = new Forecaster();
});