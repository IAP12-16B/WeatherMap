/**
 * Forecaster
 * Provides a class which is responsible for controlling the Forecaster site
 */
var Forecaster = (function () {
    "use strict"; // enable strict mode

    return new Class({
        Implements: [Events, Options],

        // options
        options: {
            'selectorNavi': '.main-nav',
            'selectorContentList': '.inner-wrapper .maps',
            'attrMapType': 'data-map-type',
            'selectorImagesDatalist': '.image-datalist'
        },

        navi: null,
        contentList: null,

        keyboardEvents: null,

        /**
         * @param array options
         */
        initialize: function (options) {
            this.setOptions(options);

            // set properties
            this.navi = $$(this.options.selectorNavi)[0];
            this.contentList = $$(this.options.selectorContentList)[0];

            // add keyboard events
            this.keyboardEvents = new Keyboard({
                defaultEventType: 'keydown',
                events: {
                    'left': this.previous.bind(this),
                    'p': this.previous.bind(this),
                    'right': this.next.bind(this),
                    'n': this.next.bind(this),
                }
            });

            // ... and activate them
            this.keyboardEvents.activate();

            // init several things
            this.initNavi();
            this.initImageSlider();

            // preaload images on window:load event
            window.addEvent('load', this.preloadImages.bind(this));
        },

        /**
         * Preloads all images in the datalists.
         * This method simply requests all images from the server, so they should be in cache.
         * This avoid annoying loading, when the images are changed.
         */
        preloadImages: function () {
            // foreach datalist...
            $$(this.options.selectorImagesDatalist).each(function (datalist) {
                var srcs = [];

                // get options
                datalist.getElements('option').each(function (option) {
                    // collect image src's
                    srcs.push(option.get('data-src'))
                });

                // request all images for that datalist at once (better performance)
                Asset.images(srcs);
            });
        },

        /**
         * Initializes the navigation
         */
        initNavi: function () {
            // add a delegated click event listener on every li in the navi
            this.navi.addEvent('click:relay(li)', this.eventNaviClick.bind(this));

            // manually fire a click event on the first li
            this.navi.fireEvent('click', {'target': this.navi.getElement('li')});
        },

        /**
         * Initializes the image slider.
         */
        initImageSlider: function () {
            // add a delegated change event to the slider input
            this.contentList.getElements('li').addEvent('change:relay(.date-slider)', this.eventSliderChange.bind(this));

            // add captions to the slider
            this.addCaptionsToSlider();
        },

        /**
         * Adds the captions to the input[type="range"] elements.
         * They're placed above the slider. The data is read from the datalists
         */
        addCaptionsToSlider: function () {
            $$('.date-slider').each(function (slider) {
                // first, create a container for the slider and the captions
                var slider_container = new Element('div', {'class': 'slider-container'});

                // we need to position it relative so we can position the caption absolutely
                slider_container.setStyle('position', 'relative');

                // add the container after the slider
                slider_container.inject(slider, 'after');
                // and move the slider inside the container
                slider_container.adopt(slider);

                // get the options from the datalist
                var opts = $(slider.get('list')).getElements('option');

                // calculate the width for the captions. We do all stuff in %. This is more flexible
                var captionWidth = 100 / (opts.length - 1);

                opts.each(function (opt, index) {
                    // make a new element for the caption
                    var caption = new Element('label', {'class': 'slider-caption index-' + index});

                    // set text
                    caption.set('text', opt.get('text'));

                    // add it at the top of the container
                    caption.inject(slider_container, 'top');

                    // set some styles
                    caption.setStyles({
                        'position': 'absolute',
                        // current index * the with of the caption should give the position of the caption
                        'left': ((index * captionWidth)) + "%",
                        'width': captionWidth + '%',
                        // in order to have the captions more or less centered above ths marks on the range slider,
                        // we need to pull them to the left by half of their width
                        'margin-left': captionWidth / -2 + '%'
                    });
                });
            });
        },

        /**
         * Event handler for the click event on an naviagtion item
         * @param Event event
         * @param Element target
         */
        eventNaviClick: function (event, target) {
            // if this is a DOM event, stop it from propagation etc...
            if ('preventDefault' in event) {
                event.preventDefault();
            }

            // remove the current class from all elements
            this.navi.getElements('li').removeClass('current');
            this.contentList.getElements('li').removeClass('current');

            // and add it tho the new urrent element
            this.navi.getElements(
                '[' + this.options.attrMapType + '="' + target.get(this.options.attrMapType) + '"]'
            ).addClass('current');
            this.contentList.getElements(
                '[' + this.options.attrMapType + '="' + target.get(this.options.attrMapType) + '"]'
            ).addClass('current');
        },

        /**
         * Event handler for the change event on the date slider.
         * @param Event event
         * @param Element target
         */
        eventSliderChange: function (event, target) {
            // get the parent list item
            var mapListItem = target.getParent('[' + this.options.attrMapType + ']');

            var val = target.value;
            // get the datalist
            var datalist = $(target.get('list'));
            var datalist_el = datalist.getElement('[value="' + val + '"]');

            // get the src attribute from the datalist option
            var img = datalist_el.get('data-src');

            // set the new src
            mapListItem.getElement('.map-area .map').set('src', img);
        },

        /**
         * Next. This method is used by the keyboard events
         */
        next: function () {
            var slider = $$('.current .date-slider')[0];
            // get current value
            var current_val = slider.get('value');
            // increase it by 1
            current_val++;

            // check if the new value would be valid
            if (current_val <= slider.get('max')) {
                // if true, set the new value
                slider.value = current_val;
                // manually fire the change event, so the map, etc.. gets updated
                slider.getParent('li').fireEvent('change', {'target': slider});
            }
        },

        /**
         * Previous. This method is used by the keyboard events
         */
        previous: function () {
            var slider = $$('.current .date-slider')[0];
            // get current value
            var current_val = slider.get('value');
            // decrease it by 1
            current_val--;

            // check if the new value would be valid
            if (current_val >= slider.get('min')) {
                // if true, set the new value
                slider.value = current_val;
                // manually fire the change event, so the map, etc.. gets updated
                slider.getParent('li').fireEvent('change', {'target': slider}); // manually fire the change event
            }
        }
    });
})();


// when DOM is ready...
document.addEvent('domready', function () {
    "use strict";

    // init the Forecaster class
    document.forecaster = new Forecaster();
});
