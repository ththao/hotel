(function ($, window, undefined) {
    'use strict';
    var pluginName = 'ui-tabs';
    var privateVar = null;
    var generateNavs = function (contents) {
        var navsText = '';
        contents.each(function () {
            navsText += '<li><a href="#" title="' + $(this).find('>h3').text() + '">' + $(this).find('>h3').text() + '</a></li>';
        });
        return navsText;
    };

    function Plugin(element, options) {
        this.element = $(element);
        this.options = $.extend({}, $.fn[pluginName].defaults, options);
        this.init();
    }

    Plugin.prototype = {
        init: function () {
            var that = this;
            this.contentEls = this.element.children().children();
            var navWrapText = '<nav class="{0}"><ul>{1}</ul></nav>';
            if (!this.contentEls.length) {
                this.element.remove();
                return;
            }

            this.options.tabClass = this.element.data('tab-class') ? this.element.data('tab-class') : this.options.tabClass;
            this.options.mobileTitle = this.element.data('mobile-title') ? this.element.data('mobile-title') : this.options.mobileTitle;

            var navWrapEl = $(Site.formatStr(navWrapText, this.options.tabClass, generateNavs(this.contentEls)));
            this.element.prepend(navWrapEl);
            navWrapEl.on('click.' + pluginName, 'li > a', function (e) {
                e.preventDefault();
                $('ul > li', navWrapEl).removeClass(that.options.activeCls);
                that.showContent($(this).parent().addClass(that.options.activeCls).index());
            });

            $('.' + this.options.mobileTitle, this.element).on('click.' + pluginName, function () {
                $(this).toggleClass(that.options.activeCls).next().slideToggle();
            });

            $('li:first > a', navWrapEl).trigger('click');
        },
        showContent: function (idx) {
            this.contentEls.removeClass(this.options.activeCls);
            this.contentEls.eq(idx).addClass(this.options.activeCls);
        }
    };

    $.fn[pluginName] = function (options, params) {
        return this.each(function () {
            var instance = $.data(this, pluginName);
            if (!instance) {
                $.data(this, pluginName, new Plugin(this, options));
            } else if (instance[options]) {
                instance[options](params);
            } else {
                console.warn(options ? options + ' method is not exists in ' + pluginName : pluginName + ' plugin has been initialized');
            }
        });
    };

    $.fn[pluginName].defaults = {
        tabClass: 'tabs',
        mobileTitle: 'tab-title',
        activeCls: 'current'
    };

    $(function ($) {
        $('[data-' + pluginName + ']')[pluginName]();
    });

}(jQuery, window));
