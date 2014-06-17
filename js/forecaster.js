var Forecaster = (function () {
    "use strict";

    return new Class({
        Implements: [Events, Options],

        options: {
            'selectorNavi': '.main-nav',
            'selectorContentList': '.inner-wrapper .maps',
            'attrMapType': 'data-map-type',
            'selectorImagesDatalist': '.image-datalist'
        },

        navi: null,
        contentList: null,

        keyboardEvents: null,

        initialize: function (options) {
            this.setOptions(options);

            this.navi = $$(this.options.selectorNavi)[0];
            this.contentList = $$(this.options.selectorContentList)[0];

            this.keyboardEvents = new Keyboard({
                defaultEventType: 'keydown',
                events: {
                    'left': this.previous.bind(this),
                    'p': this.previous.bind(this),
                    'right': this.next.bind(this),
                    'n': this.next.bind(this),
                }
            });

            this.keyboardEvents.activate();

            this.initNavi();
            this.initImageSlider();

            window.addEvent('load', this.preloadImages.bind(this));
        },

        preloadImages: function () {
            $$(this.options.selectorImagesDatalist).each(function (datalist) {
                var srcs = [];

                datalist.getElements('option').each(function (option) {
                    srcs.push(option.get('data-src'))
                });

                Asset.images(srcs); // request image, so they're in cache
            });
        },

        initNavi: function () {
            this.navi.addEvent('click:relay(li)', this.eventNaviClick.bind(this));
            this.navi.fireEvent('click', {'target': this.navi.getElement('li')});
        },

        initImageSlider: function () {
            this.contentList.getElements('li').addEvent('change:relay(.date-slider)', this.eventSliderChange.bind(this));
            this.addLabelsToSlider();
        },

        addLabelsToSlider: function () {
            $$('.date-slider').each(function (slider) {
                var slider_container = new Element('div', {'class': 'slider-container'});
                slider_container.setStyle('position', 'relative');

                slider_container.inject(slider, 'after');
                slider_container.adopt(slider);

                var opts = $(slider.get('list')).getElements('option');
                var captionWidth = 100 / (opts.length - 1);

                opts.each(function (opt, index) {
                    var caption = new Element('label', {'class': 'slider-caption index-' + index});

                    caption.set('text', opt.get('text'));
                    caption.inject(slider, 'before');
                    caption.setStyle('display', 'block');


                    caption.setStyles({
                        'position': 'absolute',
                        'left': ((index * captionWidth)) + "%",
                        'width': captionWidth + '%',
                        'margin-left': captionWidth / -2 + '%'
                    });
                });
            });
        },

        eventNaviClick: function (event, target) {
            if ('preventDefault' in event) {
                event.preventDefault();
            }

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
            var datalist_el = datalist.getElement('[value="' + val + '"]');

            var img = datalist_el.get('data-src');
            console.log(img);

            mapListItem.getElement('.map-area .map').set('src', img);
        },

        next: function () {
            var slider = $$('.current .date-slider')[0];
            var current_val = slider.get('value');
            current_val++;
            if (current_val <= slider.get('max')) {
                slider.value = current_val;
                slider.getParent('li').fireEvent('change', {'target': slider}); // manually fire the change event
            }
        },

        previous: function () {
            var slider = $$('.current .date-slider')[0];
            var current_val = slider.get('value');
            current_val--;
            if (current_val >= slider.get('min')) {
                slider.value = current_val;
                slider.getParent('li').fireEvent('change', {'target': slider}); // manually fire the change event
            }
        }
    });
})();

document.addEvent('domready', function () {
    "use strict";

    document.forecaster = new Forecaster();
});
