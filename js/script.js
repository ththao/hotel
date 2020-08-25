/**
 * @name Site
 * @description Define global variables and functions
 * @version 1.0
 * @Script by phpvnn
 */

window.userAccess = window.userAccess || [];
var defineVariable = defineVariable || [];

defineVariable.location = defineVariable.location || [];
defineVariable.qsYesNo = defineVariable.qsYesNo || [];

var Site = (function($, window, undefined) {
    'use strict';
    var isFunction = function(functionToCheck) {
        var getType = {};
        return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
    };
    /*in development
    description: We are using data attribute to manage all control of input which was checked validation
    user: <input type="text" name="first_name" data-validate="" data-required="required message here"
    data-pattern="^((?![/,\,<,>]).)*$" data-max-length="128" data-pattern-message="Validate message here">*/

    var isValidate = function(element) {
        var isValid = true,
            classIsValid = 'invalid',
            strMessage = null,
            strVal = element.val().trim(),
            min = element.data('minLength'),
            max = element.data('maxLength'),
            strRequired = element.data('required'),
            strPattern = element.data('pattern'),
            smsPattern = element.data('patternMessage'),
            patt = new RegExp(strPattern),
            type = element.attr('type');

            strVal = strVal.replace('"','”');
            strVal = strVal.replace('\'','’');


        if (strRequired) {
            if (!strVal) {
                isValid = false;
                strMessage = strRequired;
            } else if (type == 'radio') {
                var $radios = $('input:radio[name="' + element.attr('name') + '"]:checked');
                if (!$radios.length) {
                    isValid = false;
                    strMessage = strRequired;
                } else {
                    strVal = $radios.val();
                    console.log(strVal);
                }
            }
        }

        if (strVal) {
            if (strPattern && !strMessage) {
                isValid = patt.test(strVal);
                if (!isValid) {
                    strMessage = smsPattern;
                }
            }
            if (min && min > strVal.length) {
                isValid = false;
                strMessage = smsPattern;
            }
            if (max && max < strVal.length) {
                isValid = false;
                strMessage = smsPattern;
            }
        }

        if (!isValid) {
            if (!element.data('hiddenMessage')) {
                element.next('span').html(strMessage);
            }
            element.closest('.form-group').addClass(classIsValid);
        } else {
            if (!element.data('hiddenMessage')) {
                element.next('span').empty();
            }
            element.closest('.form-group').removeClass(classIsValid);
        }

        element.val(strVal);

        return isValid;
    };

    var formatStr = function(str) {
        var theString = str;
        for (var i = 1; i < arguments.length; i++) {
            var regEx = new RegExp("\\{" + (i - 1) + "\\}", "gm");
            theString = theString.replace(regEx, arguments[i]);
        }
        return theString;
    };

    return {
        isFunction: isFunction,
        isValidate: isValidate,
        formatStr: formatStr
    };

})(jQuery, window);

/**
 * Created by phpvnn on 5/18/15.
 */
var checkIEBrowser = (!! window.ActiveXObject && +(/msie\s(\d+)/i.exec(navigator.userAgent)[1])) || NaN;
if (checkIEBrowser < 9) {
    document.documentElement.className += ' lt-ie9' + ' ie' + checkIEBrowser;
}
//console.log('checkIEBrowser',checkIEBrowser);
/*
if (Modernizr.touch) {
    alert('Touch Screen');
} else {
    alert('No Touch Screen');
}*/

jQuery(function() {
    'use strict';
    var $datepickerEls = $('[data-date-picker]');

    if($datepickerEls.length){

        $datepickerEls.each(function(){
            var formatDay =$(this).data('dateFormat') || 'dd/mm/yyyy';
            var $linkedPickersElm = $(this).data('linkedPickers')?$($(this).data('linkedPickers')):null;

            $(this).datepicker({
                format: formatDay,
                autoclose: true
            });
            /*if($linkedPickersElm && $linkedPickersElm.length){
                $(this).click(function () {
                    if($linkedPickersElm.val()){
                        $(this).datepicker({
                            format: formatDay,
                            startDate:$linkedPickersElm.val(),
                            autoclose: true
                        });
                    }
                });
            }*/
        });
    }
});

/*

    Supersized - Fullscreen Slideshow jQuery Plugin
    Version : 3.2.7
    Theme   : Shutter 1.1

    Site    : www.buildinternet.com/project/supersized
    Author  : Sam Dunn
    Company : One Mighty Roar (www.onemightyroar.com)
    License : MIT License / GPL License

*/
/* jshint ignore:start */

(function($) {
    theme = {


        /* Initial Placement
        ----------------------------*/
        _init: function() {

            // Center Slide Links
            if (api.options.slide_links) {
                $(vars.slide_list).css('margin-left', -$(vars.slide_list).width() / 2);
            }

            // Start progressbar if autoplay enabled
            if (api.options.autoplay) {
                if (api.options.progress_bar) {
                    theme.progressBar();
                }
            } else {
                if ($(vars.play_button).attr('src')) {
                    $(vars.play_button).attr("src", vars.image_path + "play.png"); // If pause play button is image, swap src
                }

                if (api.options.progress_bar) {
                    $(vars.progress_bar).stop().css({
                        left: -$(window).width()
                    }); //  Place progress bar
                }
            }


            /* Thumbnail Tray
            ----------------------------*/
            // Hide tray off screen
            $(vars.thumb_tray).css({
                bottom: -$(vars.thumb_tray).height()
            });

            // Thumbnail Tray Toggle
            $(vars.tray_button).toggle(function() {
                $(vars.thumb_tray).stop().animate({
                    bottom: 0,
                    avoidTransforms: true
                }, 300);
                if ($(vars.tray_arrow).attr('src')) {
                    $(vars.tray_arrow).attr("src", vars.image_path + "button-tray-down.png");
                }

                return false;
            }, function() {
                $(vars.thumb_tray).stop().animate({
                    bottom: -$(vars.thumb_tray).height(),
                    avoidTransforms: true
                }, 300);
                if ($(vars.tray_arrow).attr('src')) {
                    $(vars.tray_arrow).attr("src", vars.image_path + "button-tray-up.png");
                }
                return false;
            });

            // Make thumb tray proper size
            $(vars.thumb_list).width($('> li', vars.thumb_list).length * $('> li', vars.thumb_list).outerWidth(true)); //Adjust to true width of thumb markers

            // Display total slides
            if ($(vars.slide_total).length) {
                $(vars.slide_total).html(api.options.slides.length);
            }


            /* Thumbnail Tray Navigation
            ----------------------------*/
            if (api.options.thumb_links) {
                //Hide thumb arrows if not needed
                if ($(vars.thumb_list).width() <= $(vars.thumb_tray).width()) {
                    $(vars.thumb_back + ',' + vars.thumb_forward).fadeOut(0);
                }

                // Thumb Intervals
                vars.thumb_interval = Math.floor($(vars.thumb_tray).width() / $('> li', vars.thumb_list).outerWidth(true)) * $('> li', vars.thumb_list).outerWidth(true);
                vars.thumb_page = 0;

                // Cycle thumbs forward
                $(vars.thumb_forward).click(function() {
                    if (vars.thumb_page - vars.thumb_interval <= -$(vars.thumb_list).width()) {
                        vars.thumb_page = 0;
                        $(vars.thumb_list).stop().animate({
                            'left': vars.thumb_page
                        }, {
                            duration: 500,
                            easing: 'easeOutExpo'
                        });
                    } else {
                        vars.thumb_page = vars.thumb_page - vars.thumb_interval;
                        $(vars.thumb_list).stop().animate({
                            'left': vars.thumb_page
                        }, {
                            duration: 500,
                            easing: 'easeOutExpo'
                        });
                    }
                });

                // Cycle thumbs backwards
                $(vars.thumb_back).click(function() {
                    if (vars.thumb_page + vars.thumb_interval > 0) {
                        vars.thumb_page = Math.floor($(vars.thumb_list).width() / vars.thumb_interval) * -vars.thumb_interval;
                        if ($(vars.thumb_list).width() <= -vars.thumb_page) {
                            vars.thumb_page = vars.thumb_page + vars.thumb_interval;
                        }
                        $(vars.thumb_list).stop().animate({
                            'left': vars.thumb_page
                        }, {
                            duration: 500,
                            easing: 'easeOutExpo'
                        });
                    } else {
                        vars.thumb_page = vars.thumb_page + vars.thumb_interval;
                        $(vars.thumb_list).stop().animate({
                            'left': vars.thumb_page
                        }, {
                            duration: 500,
                            easing: 'easeOutExpo'
                        });
                    }
                });

            }


            /* Navigation Items
            ----------------------------*/
            $(vars.next_slide).click(function() {
                api.nextSlide();
            });

            $(vars.prev_slide).click(function() {
                api.prevSlide();
            });

            // Full Opacity on Hover
            if (jQuery.support.opacity) {
                $(vars.prev_slide + ',' + vars.next_slide).mouseover(function() {
                    $(this).stop().animate({
                        opacity: 1
                    }, 100);
                }).mouseout(function() {
                    $(this).stop().animate({
                        opacity: 0.6
                    }, 100);
                });
            }

            if (api.options.thumbnail_navigation) {
                // Next thumbnail clicked
                $(vars.next_thumb).click(function() {
                    api.nextSlide();
                });
                // Previous thumbnail clicked
                $(vars.prev_thumb).click(function() {
                    api.prevSlide();
                });
            }

            $(vars.play_button).click(function() {
                api.playToggle();
            });


            /* Thumbnail Mouse Scrub
            ----------------------------*/
            if (api.options.mouse_scrub) {
                $(vars.thumb_tray).mousemove(function(e) {
                    var containerWidth = $(vars.thumb_tray).width(),
                        listWidth = $(vars.thumb_list).width();
                    if (listWidth > containerWidth) {
                        var mousePos = 1,
                            diff = e.pageX - mousePos;
                        if (diff > 10 || diff < -10) {
                            mousePos = e.pageX;
                            newX = (containerWidth - listWidth) * (e.pageX / containerWidth);
                            diff = parseInt(Math.abs(parseInt($(vars.thumb_list).css('left')) - newX)).toFixed(0);
                            $(vars.thumb_list).stop().animate({
                                'left': newX
                            }, {
                                duration: diff * 3,
                                easing: 'easeOutExpo'
                            });
                        }
                    }
                });
            }


            /* Window Resize
            ----------------------------*/
            $(window).resize(function() {

                // Delay progress bar on resize
                if (api.options.progress_bar && !vars.in_animation) {
                    if (vars.slideshow_interval) {
                        clearInterval(vars.slideshow_interval);
                    }
                    if (api.options.slides.length - 1 > 0) {
                        clearInterval(vars.slideshow_interval);
                    }

                    $(vars.progress_bar).stop().css({
                        left: -$(window).width()
                    });

                    if (!vars.progressDelay && api.options.slideshow) {
                        // Delay slideshow from resuming so Chrome can refocus images
                        vars.progressDelay = setTimeout(function() {
                            if (!vars.is_paused) {
                                theme.progressBar();
                                vars.slideshow_interval = setInterval(api.nextSlide, api.options.slide_interval);
                            }
                            vars.progressDelay = false;
                        }, 1000);
                    }
                }

                // Thumb Links
                if (api.options.thumb_links && vars.thumb_tray.length) {
                    // Update Thumb Interval & Page
                    vars.thumb_page = 0;
                    vars.thumb_interval = Math.floor($(vars.thumb_tray).width() / $('> li', vars.thumb_list).outerWidth(true)) * $('> li', vars.thumb_list).outerWidth(true);

                    // Adjust thumbnail markers
                    if ($(vars.thumb_list).width() > $(vars.thumb_tray).width()) {
                        $(vars.thumb_back + ',' + vars.thumb_forward).fadeIn('fast');
                        $(vars.thumb_list).stop().animate({
                            'left': 0
                        }, 200);
                    } else {
                        $(vars.thumb_back + ',' + vars.thumb_forward).fadeOut('fast');
                    }

                }
            });


        },


        /* Go To Slide
        ----------------------------*/
        goTo: function() {
            if (api.options.progress_bar && !vars.is_paused) {
                $(vars.progress_bar).stop().css({
                    left: -$(window).width()
                });
                theme.progressBar();
            }
        },

        /* Play & Pause Toggle
        ----------------------------*/
        playToggle: function(state) {

            if (state == 'play') {
                // If image, swap to pause
                if ($(vars.play_button).attr('src')) {
                    $(vars.play_button).attr("src", vars.image_path + "pause.png");
                }
                if (api.options.progress_bar && !vars.is_paused) {
                    theme.progressBar();
                }
            } else if (state == 'pause') {
                // If image, swap to play
                if ($(vars.play_button).attr('src')) {
                    $(vars.play_button).attr("src", vars.image_path + "play.png");
                }
                if (api.options.progress_bar && vars.is_paused) {
                    $(vars.progress_bar).stop().css({
                        left: -$(window).width()
                    });
                }
            }

        },


        /* Before Slide Transition
        ----------------------------*/
        beforeAnimation: function(direction) {
            if (api.options.progress_bar && !vars.is_paused) {
                $(vars.progress_bar).stop().css({
                    left: -$(window).width()
                });
            }

            /* Update Fields
            ----------------------------*/
            // Update slide caption
            if ($(vars.slide_caption).length) {
                (api.getField('title')) ? $(vars.slide_caption).html(api.getField('title')): $(vars.slide_caption).html('');
            }
            // Update slide number
            if (vars.slide_current.length) {
                $(vars.slide_current).html(vars.current_slide + 1);
            }


            // Highlight current thumbnail and adjust row position
            if (api.options.thumb_links) {

                $('.current-thumb').removeClass('current-thumb');
                $('li', vars.thumb_list).eq(vars.current_slide).addClass('current-thumb');

                // If thumb out of view
                if ($(vars.thumb_list).width() > $(vars.thumb_tray).width()) {
                    // If next slide direction
                    if (direction == 'next') {
                        if (vars.current_slide === 0) {
                            vars.thumb_page = 0;
                            $(vars.thumb_list).stop().animate({
                                'left': vars.thumb_page
                            }, {
                                duration: 500,
                                easing: 'easeOutExpo'
                            });
                        } else if ($('.current-thumb').offset().left - $(vars.thumb_tray).offset().left >= vars.thumb_interval) {
                            vars.thumb_page = vars.thumb_page - vars.thumb_interval;
                            $(vars.thumb_list).stop().animate({
                                'left': vars.thumb_page
                            }, {
                                duration: 500,
                                easing: 'easeOutExpo'
                            });
                        }
                        // If previous slide direction
                    } else if (direction == 'prev') {
                        if (vars.current_slide == api.options.slides.length - 1) {
                            vars.thumb_page = Math.floor($(vars.thumb_list).width() / vars.thumb_interval) * -vars.thumb_interval;
                            if ($(vars.thumb_list).width() <= -vars.thumb_page) {
                                vars.thumb_page = vars.thumb_page + vars.thumb_interval;
                            }

                            $(vars.thumb_list).stop().animate({
                                'left': vars.thumb_page
                            }, {
                                duration: 500,
                                easing: 'easeOutExpo'
                            });
                        } else if ($('.current-thumb').offset().left - $(vars.thumb_tray).offset().left < 0) {
                            if (vars.thumb_page + vars.thumb_interval > 0) {
                                return false;
                            }

                            vars.thumb_page = vars.thumb_page + vars.thumb_interval;
                            $(vars.thumb_list).stop().animate({
                                'left': vars.thumb_page
                            }, {
                                duration: 500,
                                easing: 'easeOutExpo'
                            });
                        }
                    }
                }


            }

        },


        /* After Slide Transition
        ----------------------------*/
        afterAnimation: function() {
            if (api.options.progress_bar && !vars.is_paused) {
                theme.progressBar(); //  Start progress bar
            }
        },


        /* Progress Bar
        ----------------------------*/
        progressBar: function() {
            $(vars.progress_bar).stop().css({
                left: -$(window).width()
            }).animate({
                left: 0
            }, api.options.slide_interval);
        }
    };


    /* Theme Specific Variables
    ----------------------------*/
    $.supersized.themeVars = {

        // Internal Variables
        progress_delay: false, // Delay after resize before resuming slideshow
        thumb_page: false, // Thumbnail page
        thumb_interval: false, // Thumbnail interval
        image_path: 'img/', // Default image path

        // General Elements
        play_button: '#pauseplay', // Play/Pause button
        next_slide: '#nextslide', // Next slide button
        prev_slide: '#prevslide', // Prev slide button
        next_thumb: '#nextthumb', // Next slide thumb button
        prev_thumb: '#prevthumb', // Prev slide thumb button

        slide_caption: '#slidecaption', // Slide caption
        slide_current: '.slidenumber', // Current slide number
        slide_total: '.totalslides', // Total Slides
        slide_list: '#slide-list', // Slide jump list

        thumb_tray: '#thumb-tray', // Thumbnail tray
        thumb_list: '#thumb-list', // Thumbnail list
        thumb_forward: '#thumb-forward', // Cycles forward through thumbnail list
        thumb_back: '#thumb-back', // Cycles backwards through thumbnail list
        tray_arrow: '#tray-arrow', // Thumbnail tray button arrow
        tray_button: '#tray-button', // Thumbnail tray button

        progress_bar: '#progress-bar' // Progress bar

    };

    /* Theme Specific Options
    ----------------------------*/
    $.supersized.themeOptions = {

        progress_bar: 1, // Timer for each slide
        mouse_scrub: 0 // Thumbnails move with mouse

    };

})(jQuery);

/* jshint ignore:end */

jQuery(function() {
    'use strict';
    var $supersized = $('[data-supersized-animate]:eq(0)');

    if ($supersized.length) {
        var slideInfo = [];
        var $items = $supersized.find('.banner-info');
        if ($items.length) {
            $items.each(function() {
                slideInfo.push({
                    image: $(this).data('image'),
                    url: $(this).data('url'),
                    //title: 'abc'
                    title: $(this).html()
                });
            });
        }

        $.supersized({
            // Functionality
            slide_interval: 3000, // Length between transitions
            pause_hover: 1,
            transition: 1, // 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
            transition_speed: 500, // Speed of transition
            // Components
            slide_links: 'false', // Individual links for each slide (Options: false, 'num', 'name', 'blank')
            slides: slideInfo
        });
    }
});

jQuery(function() {
    'use strict';
    var $btnToggleClass = $('[data-toggle-class]'),
        $btnClosetToggleClass = $('[data-closet-toggle-class]'),
        $btnClosetUpdateClass = $('[data-closet-update-class]');

    if ($btnToggleClass.length) {
        $btnToggleClass.on('click', function(e) {
            e.preventDefault();
            var objTarget = $(this).data('toggleClassObject')? $(this).data('toggleClassObject').split(','):null;
            var objClass = $(this).data('toggleClass')?$(this).data('toggleClass').split(','):null;

            var showHide = $(this).data('showHide')?$(this).data('showHide').split(','):null;

            if (objTarget && objClass) {
                for (var i = 0, l = objTarget.length; i < l; i++) {
                    $($(objTarget[i].trim())).toggleClass(objClass[i].trim());
                }
            }

            if(showHide){
                // show
                if( showHide[0] && $(showHide[0]).length ){
                    $(showHide[0].trim()).removeClass("hidden");
                }
                // hide
                if( showHide[1] && $(showHide[1]).length ){
                     $(showHide[1].trim()).addClass("hidden");
                }
            }
        });
    }

    if ($btnClosetToggleClass.length) {
        $btnClosetToggleClass.on('click', function(e) {
            e.preventDefault();
            $(this).closest($(this).data('object')).toggleClass($(this).data('closetToggleClass'));
        });
    }
    if ($btnClosetUpdateClass.length) {
        $btnClosetUpdateClass.on('click', function(e) {
            e.preventDefault();
            $(this).closest($(this).data('object')).attr('class', $(this).data('closetUpdateClass'));
        });
    }
    $('[data-toggle="tooltip"]').tooltip();
});

jQuery(function() {
    'use strict';
    var $validateInput = $('[data-validate]');
    var $formValidate = $('[data-form-validate]');
    if ($validateInput.length) {

        $validateInput.on('change', function(e) {
            Site.isValidate($(this));
        });
    }
    if ($formValidate.length) {

        $formValidate.on('submit', function(e) {

            var hasGrecaptcha = $(this).data('formHasGrecaptcha');
            var isValidForm = 0;
            var $objValidate = $(this).find('[data-validate]');
            if (hasGrecaptcha && grecaptcha) {
                if (grecaptcha.getResponse() === "") {
                    isValidForm++;
                    var $formGroup = $(this).find('.g-recaptcha').closest('.form-group');
                    $formGroup.addClass('invalid');
                    setTimeout(
                        function() {
                            $formGroup.removeClass('invalid');
                        }, 1000);
                }
            }

            $objValidate.each(function(e) {
                if (!Site.isValidate($(this))) {
                    isValidForm++;
                    $(this).blur();
                }
            });

            if (isValidForm) {
                return false;
            }
        });
    }
});
