var Forecaster = (function () {
    "use strict";

    return new Class({
        Implements: [Events, Options],

        options: {
            'selectorNavi': '.main-nav',
            'selectorContentList': '.inner-wrapper .maps',
            'attrMapType': 'data-map-type'
        },

        navi: null,
        contentList: null,

        initialize: function (options) {
            this.setOptions(options);

            this.navi = $$(this.options.selectorNavi)[0];
            this.contentList = $$(this.options.selectorContentList)[0];

            this.navi.addEvent('click:relay(li)', this.eventNaviClick.bind(this));

            this.contentList.getElements('li').addEvent('change:relay(.date-slider)', this.eventSliderChange.bind(this));
        },

        eventNaviClick: function (event, target) {
            event.preventDefault();


            this.navi.getElements('li').removeClass('current');
            this.contentList.getElements('li').removeClass('current');

            this.navi.getElements(
                '[' + this.options.attrMapType + '="' + target.get(this.options.attrMapType) + '"]'
            ).addClass('current');
            this.contentList.getElements(
                '[' + this.options.attrMapType + '="' + target.get(this.options.attrMapType) + '"]'
            ).addClass('current');
        },

        eventSliderChange: function (event, target) {
            var mapListItem = target.getParent('[' + this.options.attrMapType + ']');
            var val = target.value;
            var datalist = $(target.get('list'));
            console.log(datalist);
            console.log(datalist.getElement('[data-index="' + val + '"]'));
            var datalist_el = datalist.getElement('[data-index="' + val + '"]');

            var img = datalist_el.get('value');
            console.log(img);

            mapListItem.getElement('.map-area .map').set('src', img);
        }
    });
})();

document.addEvent('domready', function () {
    "use strict";

    document.forecaster = new Forecaster();
});

//$$('input[type="range"]').addEvent('change', function (event) { var val = event.target.value; var datalist = $(event.target.get('list')); console.log(datalist); console.log(datalist.getElement('[data-index="'+val+'"]')); });