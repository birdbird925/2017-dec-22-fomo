$(function() {
    var token = $('meta[name="csrf-token"]').attr('content');
    var locked = false;
    var currentPosition = 0;
    function unlock() {locked = false;}
    // load canvs thumb
    function loadThumb(thumbWrapper){
        var thumb = thumbWrapper;
        var konvaObj = JSON.parse(thumbWrapper.attr('data-thumb'));
        var stageWidth = thumbWrapper.width();
        var ratio = stageWidth / konvaObj.stage.width;
        var stageHeight = konvaObj.stage.height * ratio;
        var ratio = stageHeight / konvaObj.stage.height;
        var deferreds = [];

        var stage = new Konva.Stage({
            container: thumbWrapper.attr('id'),
            width: stageWidth,
            height: stageHeight
        });

        $.each(konvaObj.layer, function(index, layers){
            layer = new Konva.Layer();
            stage.add(layer);
            $.each(layers, function(index, konvaNode){
                if(konvaNode.type == 'image' && typeof konvaNode.src !== typeof undefined) {
                    var konvaImg = new Konva.Image({
                        x: (konvaNode.x != 0) ? konvaNode.x * ratio : 0,
                        y: (konvaNode.y != 0) ? konvaNode.y * ratio : 0,
                        offsetX: (konvaNode.x != 0) ? konvaNode.width * ratio / 2 : 0,
                        offsetY: (konvaNode.y != 0) ? konvaNode.height * ratio / 2 : 0,
                        width: konvaNode.width * ratio,
                        height: konvaNode.height * ratio,
                        rotation: konvaNode.rotation,
                    });
                    layer.add(konvaImg);
                    deferreds.push(loadThumbCanvasImg(konvaNode.src, konvaImg));
                }
                if(konvaNode.type == 'text') {
                    var konvaTxt = new Konva.Text({
                        text: konvaNode.text,
                        fontFamily: 'Museo_Slab',
                        fill: '#b7b7b7',
                        x: (konvaNode.x != 0) ? konvaNode.x * ratio : 0,
                        y: (konvaNode.y != 0) ? konvaNode.y * ratio : 0,
                        fontSize: konvaNode['font-size'] * ratio
                    });
                    layer.add(konvaTxt);
                    layer.draw();
                }
            });
        });

        return $.when.apply(null, deferreds).done(function() {
            thumbWrapper.animate({'opacity': '1'}, 444);
            customizeProductThumb.stage.push(stage);
        });
    }
    function customizeProductThumb(){
        customizeProductThumb.stage = [];
        $.each($('body').find('.konvas-thumb'), function(index, thumb){
            if($(window).width() > 768 || !$(this).hasClass('mobile-hide')) {
                loadThumb($(this));
            }
        });
    }
    function loadThumbCanvasImg(imgSrc, konvaImg) {
        var deferred = $.Deferred();
        var imgObj = new Image();
        imgObj.onload = function() {
            konvaImg.image(imgObj);
            konvaImg.getLayer().draw();
            deferred.resolve();
        };
        imgObj.src = imgSrc;
        return deferred.promise();
    }
    customizeProductThumb();

    /***************
    ** home
    ***************/
    // initial the animation for home page
    if(!$('#logo').hasClass('fixed') && !$('#logo').hasClass('animate-start')) {
        var scrollElement = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor) ? 'html' : 'body';
        var initial = true;
        var position = 2;
        var vh = $(window).height();
        var width = $(window).width();

        // reset page to top
        $(window).on('beforeunload', function() {$(scrollElement).scrollTop(0);});

        // function scroll
        function scroll(e) {
            currentVh = $(window).scrollTop();
            var scrollTo = 0;
            var wheelData = (e.type == 'mousewheel' ? e.originalEvent.wheelDelta : e.originalEvent.detail);
            // scroll down
            if(wheelData < 0) {
                scrollTo = currentVh + vh;
            }
            // scroll up
            else {
                last = currentVh % vh;
                scrollTo = (last != 0 ? currentVh - last : currentVh - vh);
            }

            $(scrollElement).animate({
                scrollTop: scrollTo
            }, 666, function(){
                setTimeout(function() {
                    $(window).one('mousewheel DOMMouseScroll', function(e) {
                        scroll(e);
                    });
                }, 333);
            });
            return false;
        }

        $(window).one('mousewheel DOMMouseScroll touchmove', function(e) {
            var mousewheel = e;
            if(initial) {
                initial = false;
                $('#logo').addClass('animate-start');
                setTimeout(function() {
                    $(scrollElement).animate({
                        scrollTop: (width > 769 ? vh :$('#featured').position().top)
                    }, 666, function(){
                        if(width < 769){
                            $('body').removeClass('initial');
                        }
                        else{
                            setTimeout(function() {
                                $(window).one('mousewheel DOMMouseScroll', function(e) {
                                    scroll(e);
                                });
                            }, 666);
                        }
                    });
                }, 666);
                return false;
            }
        });
    }
    // nav triggle
    $('.menu-tab').on('click', function() {
        if(!locked) {
            locked = true;
            $('body').toggleClass('reveal-nav');
            setTimeout(unlock, 777);
        }
    });
    // light slider in home page
    $(".hero-slider").lightSlider({
        item: 3,
        slideMove:3,
        easing: 'cubic-bezier(0.25, 0, 0.25, 1)',
        speed:600,
        slideMove: 3,
        pager: false,
        enableDrag: false,
        auto: true,
        pause: 5000,
        // loop: true,
        slideMargin: 0,
        controls: false,
        responsive : [
            {
                breakpoint: 768,
                settings: {
                    item:1,
                    slideMove:1,
                }
            },
        ]
    });
    //instagram feed
    if($('#instafeed').get(0)){
        var feed = new Instafeed({
            target: 'instafeed',
            get: 'user',
            userId: '4805556198',
            limit: '4',
            sortBy: 'most-recent',
            resolution: 'standard_resolution',
            accessToken: '4805556198.1677ed0.070374edd74b4e0a8c0c74588f07147c'
        });
        feed.run();
    }
    // feature product
    $('.featured-products').one('click', '.box', function() {
        var url = $(this).attr('product-link');
        if($(window).width() > 768)
            location.href = url;
    });

    // account
    $('#account-wrapper').find('.in').slideDown();
    $('#account-wrapper').on('click', '.section-title', function() {
        if(!locked && $(window).width() < 769) {
            locked = true;
            var target = $(this).attr('href');
            var width = $(window).width();
            if(width < 769) {
                var hideSection = $('#account-wrapper').find(target).hasClass('in');
                $.each($('#account-wrapper').find('.section'), function(){
                    if('#'+$(this).attr('id') == target){
                        if(!hideSection){
                            $(this).addClass('in');
                            $(this).slideDown();
                        }
                        else {
                            $(this).removeClass('in');
                            $(this).slideUp();
                        }
                    }
                    else {
                        $(this).removeClass('in');
                        $(this).slideUp();
                    }
                });
                setTimeout(unlock, 400);
            }
        }
    });
    $('#account-wrapper').on('submit', 'form', function(e) {
        e.preventDefault();
        var form = $(this);
        var hasError = false;
        var url = form.attr('action');
        var action = form.find('input[type=submit]').attr('data-action');

        if(action == 'email') {
            var emailRE = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            var email = form.find('input[name=email]').val();

            if(email == '') {
                hasError = true;
                msgPopup('Erm', 'Don\'t fill up the form is not cool!');
            }
            else if(!emailRE.test(email)){
                hasError = true;
                msgPopup('Erm', 'Are you sure it is email?');
            }
            else {
                formData = {'_token': token, 'email': email, 'submitBy': 'js'};
            }
        }
        if(action == 'password') {
            var oldPwd = form.find('input[name="old-password"]').val();
            var newPwd = form.find('input[name="new-password"]').val();

            if(oldPwd == '' || newPwd == '') {
                hasError = true;
                msgPopup('Erm', 'Don\'t fill up the form is not cool!');
            }
            else {
                formData = {'_token': token, 'Old Password': oldPwd, 'New Password': newPwd, 'submitBy': 'js'};
            }
        }

        if(!hasError) {
            $.ajax({
                url: url,
                data: formData,
                type: 'POST',
                error: function(a, b, c){
                    unlock();
                    msgPopup('Oh - No!', JSON.parse(a.responseText).message);
                },
                success: function(response){
                    msgPopup('ALL Good', 'New update is saved');
                    setTimeout(function() {location.reload();}, 666)

                }
            });
        }

    });
    $('#account-wrapper').on('click', '.show-password', function(e) {
        if($(this).text() == 'show') {$(this).text('hide')}
        else if($(this).text() == 'hide') {$(this).text('show')}
        var input = $(this).closest('.form-group').find('input');
        if(input.attr('type') == 'password') {input.attr('type', 'text');}
        else if(input.attr('type') == 'text') {input.attr('type', 'password');}
    });
    $('.loadmore').click(function() {
        var hideCount = $('.loadmore').closest('#saved').find('.mobile-hide').length;
        var count = 0;
        $.each($('.loadmore').closest('#saved').find('.mobile-hide'), function() {
            if(count < 2)
                $(this).toggleClass('mobile-hide').css({'opacity': '0'});
            count++;
        });
        if(count > 0) customizeProductThumb();
        if($('.loadmore').closest('#saved').find('.mobile-hide').length == 0) $(this).css({'display': 'none'});
    });
    $('.editEmail').click(function(){
        $('#account-info').fadeOut();
        $('#email-form').fadeIn();
    });
    $('.editPassword').click(function(){
        $('#account-info').fadeOut();
        $('#password-form').fadeIn();
    });
    $('.cancelEdit').click(function() {
        $('#account-wrapper input[name="email"], #account-wrapper input[name="old-password"], #account-wrapper input[name="new-password"]').val('');
        $('#account-wrapper input[name="old-password"], #account-wrapper input[name="new-password"]').attr('type', 'password');
        $('#account-wrapper a.show-password').text('show');
        $('#account-info').fadeIn();
        $('#email-form').fadeOut();
        $('#password-form').fadeOut();
    });

    // forget password
    $('#forget-password-wrapper').on('submit', 'form', function(e) {
        locked = true;
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var email = form.find('input[name=email]').val();
        var password = form.find('input[name=password]').val();
        var confirm = form.find('input[name="password_confirmation"]').val();
        var resetToken = form.find('input[name="token"]').val();
        var formData = {'_token': token, 'email' : email, 'password' : password, 'password_confirmation' : confirm, 'token': resetToken};

        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            error: function(a, b, c){
                unlock();
                msgPopup('Oh - No!', JSON.parse(a.responseText).message);
            },
            success: function(response){
                unlock();
                msgPopup('ALL Good', response.message);
                if(url == '/password/reset') {setTimeout(function() {location.href = '/account';}, 666)}
            }
        });
    });

    // login popup
    function mobileLogin() {
        if($('.login-popup').hasClass('popup')) {
            mobileLogin.y = $(document).scrollTop();
            $(document).scrollTop(0);
            $('html, body').css({
                'max-height': $('.login-popup').height(),
                'overflow': 'hidden'
            });
            $('footer').css('display', 'none');
        }
        else {
            $('html, body').css({
                'max-height': '',
                'overflow': ''
            });
            $(document).scrollTop(mobileLogin.y);
        }
    }
    $('.login-tab').click(function() {
        // toggle login popup
        $('.login-popup').toggleClass('popup');
        // hide nav menu
        if($('body').hasClass('reveal-nav')) $('body').removeClass('reveal-nav');
        // show login form as first in mobile
        $('.login-popup .login, .login-popup .register').removeClass('mobile');
        $('.login-popup .login').addClass('mobile');
        $('.login-popup').find('.action-switcher').find('label').html("Don't have account?");
        $('.login-popup').find('.switch').html('Sign Up Now');
        $('.login-popup').find('.forget-password').show();
        // clean form and error
        $('.login-popup input[type=email], .login-popup input[type=password]').val('');
        $('.login-popup .error').text('');

        // mobile login-popup
        if($(window).width() < 769) {mobileLogin();}
        unlock();

        // if user checkout with login
        var action = $(this).attr('data-action');
        if(typeof action !== typeof undefined && action != '') {
            if(action == 'checkout'){$( "#checkout-form" ).submit();}
            $('.login-popup input[type=submit]').attr('data-action', '');
            $('.login-popup .login-tab').attr('data-action', '');
        }
    });
    $('.login-popup .switch').on('click', function(){
        $('.login-popup .popup-inner').fadeOut('fast').fadeIn('slow');
        $('.login-popup .login, .login-popup .register').toggleClass('mobile');
        var login = $('.login-popup .login').hasClass('mobile');
        $(this).closest('.action-switcher').find('label').html((login) ? "Don't have account?" : "Already have account?");
        $(this).html((login) ? "Sign Up Now" : "Sign In Now");
        var forget = $(this).closest('.popup-footer').find('.forget-password');
        login ? forget.show() : forget.hide();
    });
    $('.login-popup').on('click', 'input[type=submit]', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var hasError = false;
        var url = form.attr('action');
        var email = form.find('input[type=email]').val();
        var emailRE = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        var password = form.find('input[type=password]').val();
        var action = $(this).attr('data-action');

        if(email == '' || password == '') {
            msgPopup('Erm', 'Don\'t fill up the form is not cool!');
        }
        else if(!emailRE.test(email)) {
            msgPopup('Erm', 'Are you sure it is email?');
        }
        else {
            locked = true;
            $.ajax({
                url: url,
                data: {
                    '_token': token,
                    'email': email,
                    'password': password
                },
                type: 'POST',
                error: function(a, b, c){
                    unlock();
                    // console.log(JSON.parse(a.responseText).message);
                    msgPopup('Oh - No!', JSON.parse(a.responseText).message);
                },
                success: function(response){
                    unlock();
                    if(typeof response =='object') {
                        if(typeof action !== typeof undefined && action != '') {
                            if(action == 'checkout') {$('#checkout-form').submit();}
                            if(action == 'save') {
                                unlock();
                                $('.login-popup').toggleClass('popup');
                                $('.nav-menu ul li:last-child').remove();
                                $('.nav-menu ul').append('<li><a href="/account">Account</a></li><li><a href="/logout">Logout</a></li>');
                                setTimeout(function(){$('.login-popup').remove()}, 666);
                                saveProduct();
                            }
                        }
                        else {
                            if(url == '/login'){msgPopup('Oh Yeah!', 'Good to see you again.');}
                            if(url == '/register'){msgPopup('OH Yeah!', 'Thanks for the registration.');}
                            setTimeout(function(){location.reload()}, 666);
                        }
                    }
                    else {
                        msgPopup('Oh - Oh!', 'You had been logged in.');
                        setTimeout(function(){location.href = '/';}, 666);
                    }

                }
            });
        }
    });
    function loginPopup(action) {
        if(!locked ) {
            locked = true;
            if($('body').hasClass('reveal-nav')) { $('body').toggleClass('reveal-nav'); }
            $('.login-popup').toggleClass('popup');
            $('.login-popup input[type=submit]').attr('data-action', action);
            $('.login-popup .login-tab').attr('data-action', action);
        }
    }
    // msg popup
    function msgPopup(title, msg) {
        $('.msg-popup').find('.title').html(title);
        $('.msg-popup').find('.caption').html(msg);
        $('.msg-popup').toggleClass('popup');
        setTimeout(function(){ $('.msg-popup').toggleClass('popup'); }, 2000);
    }
    $('.msg-popup').on('click', '.close-nav', function() {
        $(this).toggleClass('popup');
        $('.msg-popup').toggleClass('popup');
    });

    /****************
    ** currency switch
    *****************/
    $('#currency-selection').on('change', function(e) {
      $.get('/currency/'+$(this).val()+'/update',function(data, status) {
        if(window.location.href.includes("/cart") || window.location.href.includes("/checkout")) {window.location.reload();}
      });
    });

    /*****************
    ** customize
    /*****************/
    var canvasSlider = $('.canvas-slider').lightSlider({
        item: 1,
        slideMove: 1,
        slideMargin: 0,
        controls: false,
        enableTouch: false,
        enableDrag: false,
        adaptiveHeight: true,
        onSliderLoad: function (el) {
            $('#front-canvas, #back-canvas').css({'height': $('.customize-canvas').height()-30});
            canvasSlider.refresh()
        }
    });
    var optionSlider = $(".option-slider").lightSlider({
        item:1,
        vertical: ($(window).width() > 768 ? true : false ),
        verticalHeight: 275,
        slideMargin:0,
        slideMove: 1,
        pager: false,
        controls: false,
        enableTouch: false,
        enableDrag: false,
        responsive : [
            {
                breakpoint: 768,
                settings: {
                    vertical: false,
                }
            },
        ],
        onSliderLoad: function (el) {
            $('.option-slider').find('.lslide').css({'min-height': $('.customize-option').height()});
            $('.option-slider').find('.customize-element, .component-element, .personalize-text, .personalize-image').addClass('fadeOut').fadeOut()
            var product = $('input[name="customize-product"]').val();
            initialCustomize(product);
        }
    });
    $('.customize-option').on('click', '.next, .prev', function(){
        if($(window).width() > 768 && !$(this).hasClass('desktop-control')) return false
        var stepCount = $(this).attr('total-step');
        var action = $(this).hasClass('next') ? 'next' : 'prev';
        var reserveAction = $(this).hasClass('next') ? 'prev' : 'next';
        if(action == 'next') optionSlider.goToNextSlide();
        if(action == 'prev') optionSlider.goToPrevSlide();

        $('.customize-option').find('.desktop-control.'+reserveAction).removeClass('hide');
        if(action == 'next' && optionSlider.getCurrentSlideCount() == stepCount) {
            $('.customize-option').find('.desktop-control.next').addClass('hide');
            canvasSlider.goToNextSlide();
        }
        else if(action == 'prev' && optionSlider.getCurrentSlideCount() == 1) {
            $('.customize-option').find('.desktop-control.prev').addClass('hide');
        }
        else if((action == 'prev' && canvasSlider.getCurrentSlideCount() != '1') || (canvasSlider.getCurrentSlideCount() != '1' && action == 'next') ) {
            canvasSlider.goToPrevSlide();
        }

        if($('.lslide.active').find('.control.fadeOut').length > 0) {
            if($('.lslide.active').find('.control.fadeOut').hasClass('next'))
                $('.desktop-control.next').addClass('hide');
            else
                $('.desktop-control.prev').addClass('hide');
        }
    });
    function initialCustomizeOption(product) {
        if(product == '') {
            var checkedRadio = [];
            $.each($('.option-slider').find('input[type=radio]'), function() {
                var radio = $(this);
                if($.inArray(radio.attr('name'), checkedRadio) == -1) {
                    checkedRadio.push(radio.attr('name'));
                    if(!radio.attr('required-component')){
                        radio.prop("checked", true);
                        checkedLabel(radio);
                        updateDesc(radio);
                        displayOption(radio, true);
                        if(radio.attr('size-component') == '1')
                            deferreds = updateSizeImage(radio.val());
                        optionSlider.refresh();
                    }
                }
            });
        }
        else {
            product = JSON.parse(product);
            for (var inputName in product) {
                var attritube = product[inputName];
                var input = $('.option-slider').find('input[name='+inputName+']');
                for (var attrName in attritube) {
                    if(input.attr('type') == 'radio') {
                        var radio = $('.option-slider').find('input[name='+inputName+']['+attrName+'='+attritube[attrName]+']');
                        radio.prop("checked", true);
                        checkedLabel(radio);
                        updateDesc(radio);
                        if(radio.attr('size-component') == '1')
                            deferreds = updateSizeImage(radio.val());
                        displayOption(radio);
                        optionSlider.refresh();
                    }

                    if(input.attr('type') == 'text' || input.attr('type') == 'file')
                        input.attr(attrName, attritube[attrName]);
                }
            }
        }

        return $.when.apply(null, deferreds).done(function() {
            updateNextPreviousTitle();
            console.log('update size done');
        }).promise();
    }
    function initialCustomize(product) {
        initialCustomizeOption(product).done(function(){
            loadCustomizeCanvas().done(function() {
                updateLabelBorder();
                updateNextPreviousTitle();
            });
        });
    }
    function checkedLabel(radio){
        var id = radio.attr('id');
        $('.'+radio.attr('name')).removeClass('checked');
        $('label[for='+id+']').addClass('checked');
    }
    function updateDesc(radio){
        var description = radio.attr('description');
        var descClass = radio.attr('desc-class');
        radio.closest('.step').find(descClass).fadeOut(function(){
            $(this).html(description);
        }).fadeIn();
    }
    function displayOption(radio, updateRadio = false){
        var checkPersonalize = radio.attr('personalize');
        var showClass = radio.attr( (typeof checkPersonalize !== typeof undefined && checkPersonalize != '0') ? 'show-personalize' : 'show-class' );
        var hideClass = radio.attr( (typeof checkPersonalize !== typeof undefined && checkPersonalize != '0') ? 'hide-personalize' : 'hide-class' );
        var hideStep = radio.attr('hide-step');
        var checkedArray = [];
        var nameArray = [];
        var inputArray = [];
        var hideName = [];
        if(typeof hideStep !== typeof undefined && hideStep != '.step-'){
            // console.log('h'+hideStep+' - '+radio.attr('name'));
            $.each($(hideStep).find('input[type=radio]:checked'), function() {
                $(this).prop('checked', false);
            });
            $.each($(hideStep).find('.form-group'), function() {
                $(this).find('label').removeClass('checked');
                $(this).addClass('fadeOut').fadeOut();
            });

            $(hideStep).css('display', 'none');
        }
        else if(radio.attr('color-option') == 1 && radio.val() != 130){
            var showStep = radio.attr('show-step');
            var id = radio.val();
            var hasChecked = false;
            $(showStep).css('display', 'block');
            $.each($(showStep).find('.form-group'), function() {
                if($(this).hasClass('colorOption'+id)){
                    $(this).removeClass('fadeOut');
                    $(this).addClass('fadeIn').fadeIn();
                    if($(this).find('input[type=radio]').is(':checked')){
                        hasChecked = true;
                    }
                }else {
                    $(this).removeClass('checked');
                    $(this).removeClass('fadeIn');
                    $(this).addClass('fadeOut').fadeOut();
                }
            });

            if(!hasChecked) {
                $(showStep+' .colorOption'+id+' label').first().addClass('checked');
                $(showStep+' .colorOption'+id+' input[type=radio]').first().prop('checked', true);
            }
        }
        $.each($('.option-slider').find(hideClass), function() {
            if(!$(this).hasClass(showClass.substring(1))) {
                if($(this).hasClass('fadeIn'))
                    $(this).removeClass('fadeIn')
                $(this).addClass('fadeOut').fadeOut();

                if(updateRadio && $(this).hasClass('form-group') && $(this).find('input[type=radio]:checked').length > 0) {
                    hideName.push($(this).find('input[type=radio]:checked').attr('name'));
                    $(this).find('input[type=radio]:checked').prop('checked', false);
                    $(this).find('label').removeClass('checked');
                }
            }
        });
        $.each($('.option-slider').find(showClass), function() {
            if($(this).hasClass('fadeOut'))
                $(this).removeClass('fadeOut')
            $(this).addClass('fadeIn').fadeIn();

            if(updateRadio && $(this).hasClass('form-group') && $(this).find('input[type=radio]').length > 0) {
                var input = $(this).find('input[type=radio]:first');
                var inputName = input.attr('name');
                var checked = $(this).find('input[type=radio]:first:checked').length;
                if($.inArray(inputName, nameArray) == -1) {
                    nameArray.push(inputName);
                    checkedArray.push(checked);
                    inputArray.push(input)
                }
                else {
                    var index = $.inArray(inputName, nameArray);
                    if(checkedArray[index] == 0 && checked == 1)
                    checkedArray[index] = 1;
                }
            }
        });

        if(updateRadio) {
            var deferreds = [];

            $.each(nameArray, function(index, name) {
                if(!checkedArray[index])
                    // check current checked element is fix component or not
                    var update = true;
                    if($('input[name='+name+']:checked').length > 0) update = !($('input[name='+name+']:checked').parent().hasClass('fixed-element'));
                    if(update) {
                        inputArray[index].prop('checked', true);
                        checkedLabel(inputArray[index]);
                        updateDesc(inputArray[index]);
                        displayOption(inputArray[index], true);
                        if(inputArray[index].attr('size-component') == '1')
                            deferreds = updateSizeImage(inputArray[index].val());
                    }
            });
            $.each(hideName, function(index, name) {
                if($('input[name='+name+']:checked').length <= 0) {
                    var target = ($(showClass).find('input[name='+name+']').length > 0) ? showClass : (($('.fixed-element').find('input[name='+name+']').length > 0) ? '.fixed-element' : '#none' );
                    if($(target).find('input[name='+name+']').length > 0) {
                        $.each($(target).find('input[name='+name+']'), function() {
                            $(this).prop('checked', true);
                            checkedLabel($(this));
                            updateDesc($(this));
                            displayOption($(this), true);
                            if($(this).attr('size-component') == '1')
                                deferreds = updateSizeImage($(this).val());
                            return false;
                        });
                    }
                }
            });

            return deferreds;
        }

    }
    function updateLabelBorder(){
        console.log('update label border');
        $.each($('.option-slider').find('.main-option'), function() {
            $i=0;
            $.each($(this).find('.form-group.fadeIn'), function() {
                $(this).find('label').css({'border': '1px solid #fba200', 'border-top' : 'none'});
                if($i<2) $(this).find('label').css({'border-top': '1px solid #fba200'});
                $i++;
            });
            $(this).find('.form-group.fadeIn:even').find('label').css({'border-left': '1px solid #fba200'});
            $(this).find('.form-group.fadeIn:odd').find('label').css({'border-left': 'none'});
        });
    }
    function updateSizeImage(sizeRadioID){
        var deferreds = [];
        $.each($('.option-slider').find('input[type=radio]'), function(){
            if($(this).attr('size-image') != '') {
                var input = $(this);
                $.each(JSON.parse(input.attr('size-image')), function(key, obj) {
                    if(sizeRadioID == key) {
                        $.each(obj, function(attr, imageID) {
                            deferreds.push($.get( "/image/"+imageID+"/src", function(imgSrc) {
                                if(imgSrc != 0) input.attr(attr, imgSrc);
                            }));
                        });
                    }
                });
            }
        });

        return deferreds;
    }
    function updateNextPreviousTitle(){
        console.log('next previous title');
        var title = [];
        var step = [];
        $.each($('.step'), function() {
            if($(this).css('display') != 'none'){
                step.push($(this).attr('step'));
                title.push($(this).attr('data-title'));
            }
        });
        $.each(step, function(index, step) {
            if($('.step'+step).css('display') != 'none') {
                $('.step'+step).find('.next').text(title[index+1]);
                $('.step'+step).find('.prev').text(title[index-1]);
            }
        });
        $('.customize-option .next').attr('total-step', step.length);
    }
    function loadCanvasImage(imgSrc, konvaImg, konvaLayer) {
        var deferred = $.Deferred();
        konvaLayer.add(konvaImg);
        var imgObj = new Image();
        imgObj.onload = function() {
            if(konvaImg.hasName('personalize')){
                loadPersonalizeImg(konvaImg, imgObj, konvaLayer);
                konvaLayer.find('.personalize-area')[0].moveToTop();
                konvaLayer.draw();
            }
            else if(konvaImg.hasName('personalize-area')) {
                konvaLayer.add(konvaImg);
                resizeImg(konvaImg, imgObj);
            }
            else
                resizeImg(konvaImg, imgObj);
            deferred.resolve();
        };
        imgObj.src = imgSrc;
        return deferred.promise();
    }
    function loadPersonalizeImg(konvaImg, imgObj, layer) {
        console.log('personalize img');
        konvaImg.image(imgObj);
        konvaImg.offsetX(konvaImg.width()/2);
        konvaImg.offsetY(konvaImg.height()/2);
        layer.find('.personalize-area')[0].moveToTop();
        layer.draw();
        addAnchor(konvaImg);
    }
    function resizeImg(konvaImg, imgObj) {
        var scaleRatio = konvaImg.getStage().height()/imgObj.height;
        var width = imgObj.width * scaleRatio;
        var height = imgObj.height * scaleRatio;

        konvaImg.image(imgObj);
        konvaImg.width(width);
        konvaImg.height(height);
        konvaImg.getLayer().draw();

        // if(konvaImg.getStage().width() > width) konvaImg.getStage().width(width);
    }
    function loadCustomizeCanvas(triggerChange){
        console.log('load customize canvas');
        var deferreds = [];
        var inputJson = {};
        var dArray = ['front', 'back'];
        var sArray = {};
        $.each(dArray, function(index, value) {
            var canvasHeight = $('#'+value+'-canvas').height();
            var canvasWidth = $('#'+value+'-canvas').width()
            var size =  canvasHeight > canvasWidth ? canvasWidth : canvasHeight;
            var stage = new Konva.Stage({
                width: size,
                height: size,
                container: value+'-canvas',

            });
            sArray[value] = stage;
            if(canvasHeight > canvasWidth) {
                $('.konvajs-content').css('margin-top', (canvasHeight - canvasWidth) / 2);
            }
        });
        $.each($('.option-slider').find('input[type=radio]:checked'), function(index) {
            inputJson[$(this).attr('name')] = {'value': $(this).val()};
            var input = $(this);
            var layerID = '.layer'+input.attr('layer');
            var step = input.attr('name');

            // if radio button = Quartz 36mm
            if(input.val() == 131) {
                // zindex
                // $('#component150').parent().addClass('disabled');
                // $('#component150').prop('disabled', true);
                // $('#component215').parent().removeClass('disabled');
                // $('#component215').prop('disabled', false);
                $('#component150').parent().removeClass('fadeIn').addClass('fadeOut').fadeOut();
                $('#component215').parent().removeClass('fadeOut').addClass('fadeIn').fadeIn();

                if($('#component150').is(':checked')) {
                    $('.step6').find('label').removeClass('checked');
                   $('.step6 input[type=radio]').first().prop('checked', true);
                   $('.step6 label').first().addClass('checked');
                }

                updateLabelBorder();
            }
            // if radio button = Quartz 40mm
            if(input.val() == 130 || input.val() == 128) {
                // $('#component150').parent().removeClass('disabled');
                // $('#component150').prop('disabled', false);
                $('#component150').parent().removeClass('fadeOut').addClass('fadeIn').fadeIn();
                $('#component215').parent().removeClass('fadeIn').addClass('fadeOut').fadeOut();
                // $('#component215').prop('disabled', true);

                if($('#component215').is(':checked')) {
                    $('.step6').find('label').removeClass('checked');
                   $('.step6 input[type=radio]').first().prop('checked', true);
                   $('.step6 label').first().addClass('checked');
                }

                updateLabelBorder();
            }

            $.each(dArray, function(index, direction) {
                // check stage has layer or not
                if(sArray[direction].find(layerID).length == 0){
                    layer =  new Konva.Layer({name: layerID.substring(1)});
                    layer.setAttr('index', layerID.substring(6));
                    sArray[direction].add(layer);
                }
                else
                    layer = sArray[direction].find(layerID)[0];

                image = input.attr(direction+'_image');
                // outer blank
                if(input.val() == '182'){
                    var dial = $('.step5').find('input[type=radio]:checked').val();
                    var color = (dial == 138 || dial == 139 || dial == 142) ? "W" : "B" ;
                    var image = '';
                    switch($('.step2').find('input[type=radio]:checked').val()) {
                        case '128':
                            image = '/images/FOMO_watch_parts/FOMO_MecaQuarz_40mm/FOMO_MecaQuartz40_Exterior6'+color+'.png';
                            break;

                        case '130':
                            image = '/images/FOMO_watch_parts/FOMO_Quartz_40mm/FOMO_Quartz40_Exterior6'+color+'.png';
                            break;

                        case '131':
                            image = '/images/FOMO_watch_parts/FOMO_Quartz_36mm/FOMO_Quartz36_Exterior6'+color+'.png';
                            break;
                    }
                    deferreds.push(loadCanvasImage(image, new Konva.Image({'id': step}), layer));
                }
                else if(image != 0)
                    deferreds.push(loadCanvasImage(image, new Konva.Image({'id': step}), layer));
            });

        });

        var customizeType = $('input[name=customize_type]:checked').val();
        var personalizeArea = [];
        $.each($('.fixed-element').find('input[type=text], input[type=file]'), function(index, value) {
            var direction = $(this).closest('.step').attr('direction');
            var layerID = '.layer'+$(this).attr('layer');
            // dial - black(138, 139, 142, 145), white(140, 141, 144, 147)
            var dial = $('.step5').find('input[type=radio]:checked').val();
            var color = direction == 'back' ? 'white' : ((dial == 138 || dial == 139 || dial == 142 || dial == 145) ? 'white' : 'black');

            if(sArray[direction].find(layerID).length == 0){
                layer =  new Konva.Layer({name: layerID.substring(1)});
                layer.setAttr('index', layerID.substring(6));
                sArray[direction].add(layer);
            }
            else layer = sArray[direction].find(layerID)[0];
            loadPersonalizeOuterImg(layer, direction);

            if($(this).attr('type') == 'text' && $(this).val() != '') {
                var position = getPersonalizeTextPosition(direction, layer.getStage(), $(this));
                inputJson[$(this).attr('name')] = {
                    'value': $(this).val(),
                    'font-size': $(this).attr('font-size'),
                    'stage-width': $(this).attr('stage-width'),
                    'stage-height': $(this).attr('stage-height')
                };
                text = new Konva.Text({
                    id: $(this).attr('name'),
                    name: 'personalize',
                    text: $(this).val(),
                    fontFamily: 'Museo_Slab',
                    x: position.x,
                    y: position.y,
                    fill: color,
                    fontSize: $(this).attr('font-size'),
                });
                if(position.textCenter) {
                    text.x((layer.getStage().width()/2) - (text.width()/2));
                }
                else {
                    var newX = text.x() - text.width() / 2;
                    text.x(newX);
                }
                layer.add(text);
            }
            if($(this).attr('type') == 'file' && typeof $(this).attr('image-src') !== typeof undefined && $(this).attr('image-src')) {
                inputJson[$(this).attr('name')] = {
                    "image-id": $(this).attr('image-id'),
                    "image-src": $(this).attr('image-src'),
                    "width": $(this).attr('width'),
                    "height": $(this).attr('height'),
                    "rotation": $(this).attr('rotation'),
                    "x": $(this).attr('x'),
                    "y": $(this).attr('y'),
                    'stage-width': $(this).attr('stage-width'),
                    'stage-height': $(this).attr('stage-height')
                }

                var input = $(this);
                var src = '/images/'+color+"-"+input.attr('image-src');

                konvaImg = new Konva.Image({
                    id: input.attr('name'),
                    name: 'personalize',
                    x: input.attr('x') ? input.attr('x') : stage.width()/2,
                    y: input.attr('y') ? input.attr('y') : stage.height()/2,
                    width: input.attr('width'),
                    height: input.attr('height'),
                    rotation: input.attr('rotation'),
                });
                deferreds.push(loadCanvasImage(src, konvaImg, layer));
            }
        });

        return $.when.apply(null, deferreds).done(function() {
            console.log('done load');
            loadCustomizeCanvas.canvas = sArray;
            reorderCanvasLayer();
            scalePersonalize();
            $('input[name="customize-product"]').val(JSON.stringify(inputJson));
            if(triggerChange) $('input[name="customize-product"]').trigger('change');

            setTimeout(function(){
                $('#front-canvas, #back-canvas').toggleClass('initial');
                $('.loader-wrapper').toggleClass('done');
                $('.customize-option').toggleClass('lock');
            }, 333);
        }).promise();
    }
    function reorderCanvasLayer(){
        console.log('reorder layer');
        var layers = {};
        $.each(loadCustomizeCanvas.canvas, function(direction, stage) {
            $.each(stage.find('Layer'), function(num, layer) {
                var index = parseInt(layer.getAttr('index'));
                layers[index] = layer;
            });

            $.each(layers, function(index, node) {node.moveToTop();});
        });
    }
    function updatePersonalizeZIndex(layer){
        console.log('update personalize zindex');
        var inputJson = JSON.parse($('input[name="customize-product"]').val());
        $.each(layer.find('.personalize'), function(index, node) {
            inputJson[node.id()]['z-index'] = node.getZIndex();
            $('input[name='+node.id()+']').attr('z-index', node.getZIndex());
        });
        $('input[name="customize-product"]').val(JSON.stringify(inputJson)).trigger('change');
    }
    function resize(activeAnchor, konvaImg) {
        var stage = konvaImg.getStage();
        var imageLayer = konvaImg.getLayer();
        var group = activeAnchor.getParent();
        var width = parseInt(group.getWidth());
        var height = parseInt(group.getHeight());
        var x = parseInt(group.getX());
        var y = parseInt(group.getY());
        var left = x - width/2;
        var right = x + width/2;
        var top = y - height/2;
        var bottom = y + height/2;
        var anchorName = activeAnchor.getName();
        var pointerX = stage.getPointerPosition().x;
        var pointerY = stage.getPointerPosition().y;
        var rect = group.find('Rect')[0];
        var layer = group.getLayer();
        var degreeX = 360/width;
        var degreeY = 360/height;

        switch(anchorName) {
            case 'leftTop':
                if(pointerX < x-10) var moveX = left-pointerX;
                if(pointerX > x+10) var moveX = pointerX-right;
                if(pointerY < y-10) var moveY = top-pointerY;
                if(pointerY > y+10) var moveY = pointerY-bottom;
                var scale = (moveX+moveY)/2;
                break;
            case 'rightTop':
                if(pointerX < x-10) var moveX = left-pointerX;
                if(pointerX > x+10) var moveX = pointerX-right;
                if(pointerY < y-10) var moveY = top-pointerY;
                if(pointerY > y+10) var moveY = pointerY-bottom;
                var scale = (moveX+moveY)/2;
                break;
            case 'leftBottom':
                if(pointerX < x-10) var moveX = left-pointerX;
                if(pointerX > x+10) var moveX = pointerX-right;
                if(pointerY < y-10) var moveY = top-pointerY;
                if(pointerY > y+10) var moveY = pointerY-bottom;
                var scale = (moveX+moveY)/2;
                break;
            case 'rightBottom':
                if(pointerX < x-10) var moveX = left-pointerX;
                if(pointerX > x+10) var moveX = pointerX-right;
                if(pointerY < y-10) var moveY = top-pointerY;
                if(pointerY > y+10) var moveY = pointerY-bottom;
                var scale = (moveX+moveY)/2;
                break;
            case 'centerTop':
                if(pointerY < y-10) var scaleY = top-pointerY;
                if(pointerY > y+10) var scaleY = pointerY-bottom;
                break;
            case 'centerBottom':
                if(pointerY < y-10) var scaleY = top-pointerY;
                if(pointerY > y+10) var scaleY = pointerY-bottom;
                break;
            case 'leftCenter':
                if(pointerX < x-10) var scaleX = left-pointerX;
                if(pointerX > x+10) var scaleX = pointerX-right;
                break;
            case 'rightCenter':
                if(pointerX < x-10) var scaleX = left-pointerX;
                if(pointerX > x+10) var scaleX = pointerX-right;
                break;
            case 'rotation':
                var moveX = pointerX-x;
                var moveY = pointerY-y;
                var degreeX = degreeX * moveX;
                var degreeY = degreeY * moveY;
                var degree = (degreeX + degreeY) / 2
                konvaImg.rotation(degree);
                group.rotation(degree);
                break;
        }

        if(!isNaN(scale)) {
            var ration = (width+scale)/width;
            rect.width(width+scale);
            rect.height(height*ration);
            rect.offsetX(rect.width()/2);
            rect.offsetY(rect.height()/2);
        }

        if(!isNaN(scaleX)) {
            rect.width(width+scaleX);
            rect.offsetX(rect.width()/2);
        }

        if(!isNaN(scaleY)) {
            rect.height(height+scaleY);
            rect.offsetY(rect.height()/2);
        }

        rect.x(rect.width()/2);
        rect.y(rect.height()/2);
        group.width(rect.width());
        group.height(rect.height());
        group.offsetX(rect.width()/2);
        group.offsetY(rect.height()/2);
        konvaImg.width(rect.width());
        konvaImg.height(rect.height());
        konvaImg.offsetX(rect.width()/2);
        konvaImg.offsetY(rect.height()/2);
        imageLayer.find('.personalize-area')[0].moveToTop();
        // imageLayer.draw();
        imageLayer.batchDraw();

        // move anchor
        group.find('.centerTop')[0].x(group.width()/2);
        group.find('.rightTop')[0].x(group.width());
        group.find('.rightCenter')[0].x(group.width());
        group.find('.centerBottom')[0].x(group.width()/2);
        group.find('.rightBottom')[0].x(group.width());
        group.find('.rotation')[0].x(group.width()/2);
        group.find('.leftCenter')[0].y(group.height()/2);
        group.find('.rightCenter')[0].y(group.height()/2);
        group.find('.leftBottom')[0].y(group.height());
        group.find('.rightBottom')[0].y(group.height());
        group.find('.centerBottom')[0].y(group.height());
        layer.draw();
    }
    function addAnchor(konvaImg){
        var imgLayer = konvaImg.getLayer();
        var width = konvaImg.getWidth();
        var height = konvaImg.getHeight();
        var stage = konvaImg.getStage();
        var layer = stage.find('.layer10')[0];
        if(!layer) {
            layer = new Konva.Layer({name: 'layer10'});
            layer.setAttr('index', 10);
            stage.add(layer);
        }

        var groudID = konvaImg.id()+'-control';
        if(stage.find('#'+groudID).length > 0) {stage.find('#'+groudID)[0].destroy();}
        // group option
        var group = new Konva.Group({
            name: 'personalize-control',
            id: groudID,
            x: parseInt(konvaImg.x()),
            y: parseInt(konvaImg.y()),
            width: width,
            height: height,
            offset: {
                x: width/2,
                y: height/2
            },
            rotation: parseInt(konvaImg.rotation()),
            draggable: true,
            opacity: 0,
        });
        var control = new Konva.Rect({
            x: parseInt(konvaImg.getOffsetX()),
            y: parseInt(konvaImg.getOffsetY()),
            width: width,
            height: height,
            offset: {
                x: width/2,
                y: height/2
            }
        });
        group.add(control);
        group.on('dragmove', function() {
            konvaImg.x(parseInt(group.x()));
            konvaImg.y(parseInt(group.y()));
            imgLayer.draw();
            // update image position into input hidden
            $('input[name='+konvaImg.id()+']').attr('x', konvaImg.x());
            $('input[name='+konvaImg.id()+']').attr('y', konvaImg.y());
        });
        group.on('dragend', function() {
            var inputJson = JSON.parse($('input[name="customize-product"]').val());
            var json = inputJson[konvaImg.id()];
            json.x = konvaImg.x();
            json.y = konvaImg.y();
            $('input[name="customize-product"]').val(JSON.stringify(inputJson)).trigger('change');
        });
        group.on('mouseover', function() {
            document.body.style.cursor = 'move';
            if(group.opacity() == 0) {
                group.opacity(0.5);
                layer.draw();
            }
        });
        group.on('mouseout', function() {
            document.body.style.cursor = 'default';
            if(group.opacity() == 0.5) {
                group.opacity(0);
                layer.draw();
            }
        });
        group.on('mousedown touchstart', function(event) {
            konvaImg.moveToTop();
            imgLayer.find('.personalize-area')[0].moveToTop();
            imgLayer.draw();
            updatePersonalizeZIndex(imgLayer);
            group.opacity(1);
            layer.draw();
            event.evt.stopPropagation();
        });
        var anchors = {
            leftTop: {cursor: 'nw-resize', x: 0, y: 0},
            centerTop: {cursor: 'n-resize', x: width/2, y: 0},
            rightTop: {cursor: 'ne-resize', x: width, y: 0},
            rightCenter: {cursor: 'e-resize', x: width, y: height/2},
            rightBottom: {cursor: 'se-resize', x: width, y: height},
            centerBottom: {cursor: 's-resize', x: width/2, y: height},
            leftBottom: {cursor: 'sw-resize', x: 0, y: height},
            leftCenter: {cursor: 'w-resize', x: 0, y: height/2},
            rotation: {cursor: 'crosshair', x: width/2, y: -15},
        }
        $.each(anchors, function(name, option) {
            anchor = new Konva.Circle({
                radius: 5,
                x: option.x,
                y: option.y,
                name: name,
                fill: '#b7b7b7',
                draggable: true,
                dragBoundFunc: function(pos) {
                    return {
                        x: this.getAbsolutePosition().x,
                        y: this.getAbsolutePosition().y
                    }
                }
            });
            anchor.on('dragmove', function() {
                resize(this, konvaImg);
                layer.draw();
            });
            anchor.on('dragend', function() {
                group.setDraggable(true);
                layer.draw();
                $('input[name='+konvaImg.id()+']').attr('width', konvaImg.width());
                $('input[name='+konvaImg.id()+']').attr('height', konvaImg.height());
                $('input[name='+konvaImg.id()+']').attr('rotation', konvaImg.rotation());

                var inputJson = JSON.parse($('input[name="customize-product"]').val());
                var json = inputJson[konvaImg.id()];
                json.width = konvaImg.width();
                json.height = konvaImg.height();
                json.rotation = konvaImg.rotation();
                $('input[name="customize-product"]').val(JSON.stringify(inputJson)).trigger('change');
            });
            anchor.on('mouseover', function() { document.body.style.cursor = option.cursor; });
            anchor.on('mouseout', function() { document.body.style.cursor = 'default'; });
            group.add(anchor);
        });
        layer.add(group);
        if(layer.find('Rect').length > 1) layer.find('Rect')[0].moveToTop();
        layer.draw();
    }
    function loadPersonalizeOuterImg(layer, direction){
        var type = $('input[name=customize_type]:checked').val();
        var size = $('input[name=step2]:checked').val();
        var watchCase = $('input[name=step3]:checked').val();
        var dial = $('input[name=step5]:checked').val();
        var name = '';
        if(direction == 'front') {
            switch(dial) {
                case '138':
                    name = 'FOMO_MecaQuartz40mm_Dial_White2_Personalisation.png';
                    break;

                case '139':
                    name = 'FOMO_MecaQuartz40mm_Dial_Black1_Personalisation.png';
                    break;

                case '140':
                    name = 'FOMO_MecaQuartz40mm_Dial_White1_Personalisation.png';
                    break;

                case '141':
                    name = 'FOMO_MecaQuartz40mm_Dial_Black2_Personalisation.png';
                    break;

                case '142':
                    name = size == 130 ? 'FOMO_Quartz40mm_Dial_Personalisation.png' : 'FOMO_Quartz36mm_Dial_Personalisation.png';
                    break;

                case '144':
                    name = size == 130 ? 'FOMO_Quartz40mm_Dial_Personalisation.png' : 'FOMO_Quartz36mm_Dial_Personalisation.png';
                    break;
            }
        }
        // back
        else {
            switch(watchCase) {
                case '132':
                    name = (type == 1 ? 'FOMO_MecaQuartz40mm_BackCase_Silver_Personalisation.png' : (size == 130 ? 'FOMO_Quartz40mm_BackCase_Silver_Personalisation.png' : 'FOMO_Quartz36mm_BackCase_Silver_Personalisation.png'));
                    break;
                case '133':
                    name = (type == 1 ? 'FOMO_MecaQuartz40mm_BackCase_RoseGold_Personalisation.png' : (size == 130 ? 'FOMO_Quartz40mm_BackCase_RoseGold_Personalisation.png' : 'FOMO_Quartz36mm_BackCase_RoseGold_Personalisation.png'));
                    break;
                case '134':
                    name = (type == 1 ? 'FOMO_MecaQuartz40mm_BackCase_Black_Personalisation.png' : (size == 130 ? 'FOMO_Quartz40mm_BackCase_Black_Personalisation.png' : 'FOMO_Quartz36mm_BackCase_Black_Personalisation.png'));
                    break;

            }
        }

        var reloadPersonalizeOuter = true;
        var image = '/images/FOMO_watch_parts/FOMO_Personalisation/'+name;
        if(layer.find('.personalize-area').length != 0) {
            if(layer.find('.personalize-area')[0].id() == image) {
                reloadPersonalizeOuter = false;
            }
        }

        if(reloadPersonalizeOuter)
            deferreds.push(loadCanvasImage(image, new Konva.Image({'name': 'personalize-area', 'id': image}), layer));
    }
    function getPersonalizeTextPosition(direction, stage, input){
        console.log('get personalize text position');
        var textCenter = true;
        if(direction == 'front') {
            var customizeType = $('input[name=customize_type]:checked').val();
            // meca-quartz
            if(customizeType == 1) {
                x = stage.width() / 2 + ((stage.width() / 2 * 0.5) / 2);
                y = stage.height() / 2 - (stage.height() * 0.01074);
                textCenter = false;
            }
            // quartz
            else {
                x = stage.width() / 2;
                y = stage.height() / 2 + (stage.height() * 0.1);
            }
        }
        else {
            if(input.attr('line') == 1) {
                x = stage.width() / 2;
                y = stage.height() / 2 + (stage.height() * 0.115);
            }
            else {
                x = stage.width() / 2;
                y = stage.height() / 2 + (stage.height() * 0.155);
            }
        }

        return {x: x, y: y, textCenter: textCenter};
    }
    function scalePersonalize() {
        console.log('start scale Personalize');
        $.each(loadCustomizeCanvas.canvas, function(index, stage) {
            var topIndex = 0;
            var direction = index;
            $.each(stage.find('.personalize'), function(index, node) {
                var node = stage.find('.personalize')[index];
                var control = stage.find('#'+node.id()+'-control')[0];
                var input = $('input[name='+node.id()+']');

                // update personalize z-index
                if(input.attr('z-index') > topIndex) {
                    topIndex = input.attr('z-index');
                    node.moveToTop();
                    node.getLayer().find('.personalize-area')[0].moveToTop();
                    node.getLayer().draw();
                }

                if(input.attr('stage-height') != stage.height()) {
                    var inputJson = JSON.parse($('input[name="customize-product"]').val());
                    var json = inputJson[node.id()];
                    var scale = stage.height() / input.attr('stage-height');

                    if(node.getClassName() == 'Text') {
                        var position = getPersonalizeTextPosition(direction, stage, input);
                        var size = parseInt(json['font-size']) * scale;
                        node.fontSize(size);
                        (position.textCenter) ? node.x((stage.width()/2) - (node.width()/2)) : node.x(position.x);
                        node.y(position.y);

                        input.attr('font-size', size);
                        json['font-size'] = node.fontSize();;
                    }
                    else {
                        var x = input.attr('x') * scale;
                        var y = input.attr('y') * scale;
                        var width = input.attr('width') * scale;
                        var height = input.attr('height') * scale;
                        node.x(x);
                        node.y(y);
                        node.width(width);
                        node.height(height);
                        node.offsetX(width/2);
                        node.offsetY(height/2);
                        if(typeof control !== typeof undefined) {
                            control.find('Rect')[0].width(width);
                            control.find('Rect')[0].height(height);
                            control.find('Rect')[0].offsetX(width/2);
                            control.find('Rect')[0].offsetY(height/2);
                            control.width(width);
                            control.height(height);
                            control.offsetX(width/2);
                            control.offsetY(height/2);
                            control.x(x);
                            control.y(y);
                            control.find('.centerTop')[0].x(control.width()/2);
                            control.find('.rightTop')[0].x(control.width());
                            control.find('.rightCenter')[0].x(control.width());
                            control.find('.centerBottom')[0].x(control.width()/2);
                            control.find('.rightBottom')[0].x(control.width());
                            control.find('.rotation')[0].x(control.width()/2);
                            control.find('.leftCenter')[0].y(control.height()/2);
                            control.find('.rightCenter')[0].y(control.height()/2);
                            control.find('.leftBottom')[0].y(control.height());
                            control.find('.rightBottom')[0].y(control.height());
                            control.find('.centerBottom')[0].y(control.height());
                            control.getLayer().draw();
                        }

                        input.attr('x', x);
                        input.attr('y', y);
                        input.attr('height', height);
                        input.attr('width', width);
                        json.height = height;
                        json.width = width;
                        json.x = x;
                        json.y = y;
                    }

                    input.attr('stage-height', stage.height());
                    input.attr('stage-width', stage.width());
                    node.getLayer().draw();

                    json['stage-height'] = stage.height();
                    json['stage-width'] = stage.width();
                    $('input[name="customize-product"]').val(JSON.stringify(inputJson));
                }
            });
        });
    }
    function getStageInfo() {
        var imageList = [];
        var thumb = {};
        $.each(loadCustomizeCanvas.canvas, function(direction, stage) {
            $.each(stage.find('Image'), function(index, konvaImg) {
                if(!konvaImg.hasName('personalize-area')) {
                    var imgSrc = $(konvaImg.image()).attr('src')
                    imageList.push(imgSrc);
                }
            });

            thumb[direction] = {};
            thumb[direction]['stage'] = {};
            thumb[direction]['layer'] = {};
            thumb[direction]['stage']['width'] = stage.width();
            thumb[direction]['stage']['height'] = stage.height();

            $.each(stage.find('Layer'), function(layer, konvaLayer) {
                if(konvaLayer.find('Image, Text').length > 0) {
                    thumb[direction]['layer'][layer] = {};
                    $.each(konvaLayer.find('Image, Text'), function(index, konvaNode){
                        if(konvaNode.getClassName() == 'Image') {
                            var detail = {
                                'type': 'image',
                                'src': $(konvaNode.image()).attr('src'),
                                'x': konvaNode.x(),
                                'y': konvaNode.y(),
                                'width': konvaNode.width(),
                                'height': konvaNode.height(),
                                'rotation': konvaNode.rotation(),
                            }
                            thumb[direction]['layer'][layer][konvaNode.getZIndex()] = detail;
                        }
                        if(konvaNode.getClassName() == 'Text') {
                            var detail = {
                                'type': 'text',
                                'text': konvaNode.text(),
                                'font-size': konvaNode.fontSize(),
                                'x': konvaNode.x(),
                                'y': konvaNode.y(),
                            }
                            thumb[direction]['layer'][layer][konvaNode.getZIndex()] = detail;
                        }
                    });
                }
            });
        });

        return {'thumb': JSON.stringify(thumb['front']), 'back': JSON.stringify(thumb['back']), 'images': JSON.stringify(imageList)};
    }
    function saveProduct(popup = true) {
        var stageDetail = getStageInfo();
        // var form = $('#test-save-form');
        // form.find('input#product').val($('input[name="customize-product"]').val());
        // form.find('input#name').val($('input[name="customize-name"]').val());
        // form.find('input#images').val(stageDetail.images);
        // form.find('input#thumb').val(stageDetail.thumb);
        // form.find('input#back').val(stageDetail.back);
        // form.submit();
        $.ajax({
            url: '/product/save',
            data: {
                '_token': token,
                'product': $('input[name="customize-product"]').val(),
                'name': $('input[name="customize-name"]').val(),
                'images': stageDetail.images,
                'thumb': stageDetail.thumb,
                'back' : stageDetail.back
            },
            type: 'POST',
            error: function(a, b, c){
                console.log(a.responseText);
                msgPopup('Uh - oh!', 'SOMETHING WENT WRONG.');
            },
            success: function(id){
                console.log(id);
                if(popup){msgPopup('Sweet!', 'YOUR WATCH WAS SAVED.');}
                var saveBtn = $('.save');
                saveBtn.removeClass('save')
                saveBtn.addClass('saved');
                saveBtn.attr('data-id', id);
            }
        });
    }
    $(document).on('mousedown touchstart', function(event) {
        if($(document).find('.canvas-slider').length == 1)
            $.each(loadCustomizeCanvas.canvas, function(direction, stage) {
                stage.find('.personalize-control').opacity(0);
                if(stage.find('.layer10').length > 0) stage.find('.layer10')[0].draw();
            });
    });
    $('.option-slider').on('change', 'input[type=radio]', function(){
        var input = $(this);
        // personalization image and text switched
        if(input.val() >= 211 && input.val() <= 214){
            var wrapper = input.closest('.step');
            var fadedInItem = wrapper.find('.fadeIn.personalization');
            var fadedOutItem = wrapper.find('.fadeOut.personalization');
            fadedInItem.removeClass('fadeIn').addClass('fadeOut').fadeOut();
            setTimeout(function(){
                fadedOutItem.removeClass('fadeOut').addClass('fadeIn').fadeIn();
            }, 333);
            checkedLabel(input);
        }
        else {
            $('#front-canvas, #back-canvas').toggleClass('initial');
            $('.loader-wrapper').toggleClass('done');
            $('.customize-option').toggleClass('lock');
            setTimeout(function(){
                checkedLabel(input);
                updateDesc(input);
                optionSlider.refresh();

                var deferreds = input.attr('size-component') != '1' ? displayOption(input, true) : updateSizeImage(input.val());
                $.when.apply(null, deferreds).done(function() {
                    updateLabelBorder();
                    updateNextPreviousTitle();
                    loadCustomizeCanvas(true);
                });
            }, 333);
        }
    });
    $('.option-slider').on('keyup', 'input[type=text]', function() {
        var step = $(this).closest('.step');
        var direction = step.attr('direction');
        var stage = loadCustomizeCanvas.canvas[direction];
        var layer = stage.find('.layer'+$(this).attr('layer'))[0];
        var value = $(this).val();

        var position = getPersonalizeTextPosition(direction, stage, $(this))

        if(stage.find('#'+$(this).attr('name')).length != 0) text = stage.find('#'+$(this).attr('name'))[0];
        else {
            var dial = $('.step5').find('input[type=radio]:checked').val();
            var color = (dial == 138 || dial == 139 || dial == 142) ? "#ffffff" : "#000000" ;
            text = new Konva.Text({
                id: $(this).attr('name'),
                name: 'personalize',
                fontFamily: 'Museo_Slab',
                fill: direction == 'back' ? '#ffffff' : color,
                fontSize: $(this).attr('font-size') ? $(this).attr('font-size') :'10',
            });
            layer.add(text)
        }

        // change text location
        text.text(value);
        text.x(position.x);
        text.y(position.y);
        if(position.textCenter) {
            text.x((stage.width()/2) - (text.width()/2));
        }
        else {
            var newX = text.x() - text.width() / 2;
            text.x(newX);
        }
        text.moveToTop();
        layer.find('.personalize-area')[0].moveToTop();
        layer.draw();

        var inputJson = JSON.parse($('input[name="customize-product"]').val());
        if(typeof inputJson[text.id()] === typeof undefined) {
            inputJson[text.id()] = {};
            inputJson[text.id()]['font-size'] = text.fontSize();
            inputJson[text.id()]['stage-width'] = text.getStage().width();
            inputJson[text.id()]['stage-height'] = text.getStage().height();
            $(this).attr('font-size', text.fontSize());
            $(this).attr('stage-width', text.getStage().width());
            $(this).attr('stage-height', text.getStage().height());
        }
        inputJson[text.id()].value = value;
        $('input[name="customize-product"]').val(JSON.stringify(inputJson)).trigger('change');
        // updatePersonalizeZIndex(layer);
    });
    $('.option-slider').on('change', 'input[type=file]', function() {
        // upload image
        var formData = new FormData();
        var input = $('input[name="'+$(this).attr('name')+'"]');
        var name = input[0].files[0].name;
        formData.append('file', input[0].files[0]);
        formData.append('_token', token);
        formData.append('personalizeImg', 1);
        $.ajax({
            url: '/image/upload',
            data: formData,
            type: 'POST',
            contentType: false,
            processData: false,
            dataType: 'json',
            error: function(a, b, c){
                var response = $.parseJSON(a.responseText);
                var errorMsg = name + ' ' + response.message;
                console.log(errorMsg);
                // showNotification('warning', errorMsg, 'bottom', 'center');
            },
            success: function(response){
                var direction = input.closest('.step').attr('direction');
                var stage = loadCustomizeCanvas.canvas[direction];
                var layer = stage.find('.layer'+input.attr('layer'))[0];

                if(stage.find('#'+input.attr('name')).length != 0) {
                    image = stage.find('#'+input.attr('name'))[0];
                }
                else {
                    image = new Konva.Image({
                        id: input.attr('name'),
                        name: 'personalize',
                        x: input.attr('x') ? input.attr('x') : stage.width()/2,
                        y: input.attr('y') ? input.attr('y') : stage.height()/2,
                        width: input.attr('width') ? input.attr('width') : 50,
                        height: input.attr('height') ? input.attr('height') : 50,
                        rotation: input.attr('rotation') ? input.attr('rotation') : 0,
                    });
                    layer.add(image)
                }


                var imgObj = new Image();
                imgObj.onload = function() {
                    var ration = imgObj.width / 50;
                    var height = imgObj.height / ration;

                    image.image(imgObj);
                    image.width(50);
                    image.height(height);
                    image.offsetX(25);
                    image.offsetY(height/2);
                    layer.find('.personalize-area')[0].moveToTop();
                    layer.draw();
                    addAnchor(image);
                    // update image info into input hidden
                    input.attr('image-id', response.id);
                    input.attr('image-src', response.image);
                    input.attr('width', 50);
                    input.attr('height', height);
                    input.attr('x', image.x());
                    input.attr('y', image.y());
                    input.attr('rotation', image.rotation());
                    input.attr('stage-height', image.getStage().height());
                    input.attr('stage-width', image.getStage().width());

                    var inputJson = JSON.parse($('input[name="customize-product"]').val());
                    if(typeof inputJson[input.attr('name')] === typeof undefined) {
                        inputJson[input.attr('name')] = {};
                    }
                    var json = inputJson[input.attr('name')];
                    json['image-id'] = response.id;
                    json['image-src'] = response.image;
                    json['width'] = 50;
                    json['height'] = height;
                    json['x'] = image.x();
                    json['y'] = image.y();
                    json['rotation'] = image.rotation();
                    json['stage-height'] = image.getStage().height();
                    json['stage-width'] = image.getStage().width();
                    $('input[name="customize-product"]').val(JSON.stringify(inputJson)).trigger('change');
                    updatePersonalizeZIndex(layer);
                };
                // dial - black(138, 139, 142, 145), white(140, 141, 144, 147)
                var dial = $('.step5').find('input[type=radio]:checked').val();
                var prefix = direction == 'back' ? 'white-' : ((dial == 138 || dial == 139 || dial == 142 || dial == 145) ? 'white-' : 'black-');
                imgObj.src = '/images/'+prefix+response.image;
            }
        });
    });
    $('.customize-canvas').on('click', '.addCart', function() {
        var cartBtn = $(this);
        var stageDetail = getStageInfo();
        $.ajax({
            url: '/cart/add',
            data: {
                '_token': token,
                'product': $('input[name="customize-product"]').val(),
                'name': $('input[name="customize-name"]').val(),
                'images': stageDetail.images,
                'thumb': stageDetail.thumb,
                'back' : stageDetail.back
            },
            type: 'POST',
            error: function(a, b, c){
                console.log(a.responseText);
                msgPopup('Uh - oh!', 'SOMETHING WENT WRONG.');
            },
            success: function(cartCode){
                msgPopup('Sweet!', 'YOUR WATCH WAS ADDED.');
                cartBtn.removeClass('addCart');
                cartBtn.addClass('addedCart');
                cartBtn.attr('data-id', cartCode);
                var cartCount = parseInt($('.cart > span').text());
                $('.cart > span').text(cartCount+1);
            }
        });
    });
    $('.customize-canvas').on('click', '.save', function() {
        if($('.login-popup').get(0)) {loginPopup('save');}
        else {saveProduct();}
    });
    $('.customize-canvas').on('click', '.admin-control', function() {
        var action = $(this).attr('data-action');
        var id = $(this).attr('data-id');
        var name = $()
        swal({
            title: "Watch name",
            text: "Name your creative !!:",
            type: "input",
            inputValue: $('input[name="customize-name"]').val(),
            showCancelButton: true,
            closeOnConfirm: false,
            animation: "slide-from-top",
            inputPlaceholder: "Write something"
        },
        function(inputValue){
            if (inputValue === false) return false;

            if (inputValue.trim() === "") {
                swal.showInputError("You need to write something!");
                return false
            }

            locked = true;
            var stageDetail = getStageInfo();
            $.each(loadCustomizeCanvas.canvas.front.find('.personalize-area'), function(index, konva){
                konva.cache();
                konva.filters([Konva.Filters.Brighten]);
                konva.brightness(2);
                konva.getLayer().draw();
            });

            $.ajax({
                url: '/admin/customize/product'+(action=="save" ? '' : '/'+id),
                type: 'POST',
                data: {
                    '_token': token,
                    'product': $('input[name="customize-product"]').val(),
                    'name': inputValue,
                    'image': loadCustomizeCanvas.canvas.front.toDataURL(),
                    'images': stageDetail.images,
                    'thumb': stageDetail.thumb,
                    'back' : stageDetail.back
                },
                error: function(a, b, c){
                    console.log(a);
                    console.log(a.responseText);
                },
                success: function(){
                    location.href = "/admin/customize/product"+(action=='save' ? '' : '/'+id);
                }
            });
        });
    });
    $('input[name="customize-product"]').on('change', function() {
        var stageDetail = getStageInfo();
        if($('.addedCart').get(0)) {
            var cartBtn = $('.addedCart');
            cartBtn.addClass('addCart');
            cartBtn.removeClass("addedCart");
            cartBtn.removeAttr('data-id');
        }
        if($('.saved').get(0)) {
            var saveBtn = $('.saved');
            saveBtn.addClass('save');
            saveBtn.removeClass('saved')
            saveBtn.removeAttr('data-id');
        }
        // if($('.addedCart').get(0)) {
        //     $.ajax({
        //         url: '/cart/'+$('.addedCart').attr('data-id')+'/update/',
        //         data: {
        //             '_token': token,
        //             'product': $('input[name="customize-product"]').val(),
        //             'name': $('input[name="customize-name"]').val(),
        //             'images': stageDetail.images,
        //             'thumb': stageDetail.thumb,
        //             'back' : stageDetail.back
        //         },
        //         type: 'POST',
        //         error: function(a, b, c){
        //             console.log(a.responseText);
        //         },
        //         success: function(response){
        //             console.log('updated session');
        //         }
        //     });
        // }
        // if($('.saved').get(0)) {
        //     $.ajax({
        //         url: '/product/'+$('.saved').attr('data-id')+'/update/',
        //         data: {
        //             '_token': token,
        //             'product': $('input[name="customize-product"]').val(),
        //             'name': $('input[name="customize-name"]').val(),
        //             'images': stageDetail.images,
        //             'thumb': stageDetail.thumb,
        //             'back' : stageDetail.back
        //         },
        //         type: 'POST',
        //         error: function(a, b, c){
        //             console.log(a.responseText);
        //         },
        //         success: function(response){
        //             console.log('updated session');
        //         }
        //     });
        // }
    });

    /***************
    ** cart
    /**************/
    $('select[name="shipping-country"]').on('change', function() {
        if($(this).val() != '') {
            var location = $(this).val();
            var cost = $(this).find('option:selected').attr('data-price');
            $('.shipping-table td.price').text('$ '+cost);
            $.ajax({
                url: '/cart/shipping/update/',
                data: {
                    '_token': token,
                    'location': location,
                    'cost': cost,
                },
                type: 'POST',
                error: function(a, b, c){
                    console.log(a.responseText);
                },
                success: function(total){
                    $('.cart-footer .total').text('$ '+total);
                }
            });
        }

    });
    // $('#checkout-button').on('click', function() {
    //     var productImg = [];
    //     $.each(customizeProductThumb.stage, function(index, stage) {
    //         productImg.push(stage.toDataURL());
    //     });
    //     productImg = JSON.stringify(productImg);
    //     $.ajax({
    //         url: '/checkout/validation',
    //         data: {'_token': token, 'image': productImg},
    //         type: 'POST',
    //         error: function(a, b, c){
    //             console.log(a.responseText);
    //         },
    //         success: function(response){
    //             if(response == 'empty') {
    //                 msgPopup('Uh - oh!', 'You cart was empty.');
    //                 setTimeout(function() {
    //                     location.reload();
    //                 }, 1800);
    //             }
    //             else if(response == 'shipping'){
    //                 msgPopup('Uh - oh!', 'Where should we send you this awesome watch ?');
    //             }
    //             else {
    //                 $('#checkout-button').off();
    //                 if($('.login-popup').get(0)) {loginPopup('checkout');}
    //                 else {$( "#checkout-form" ).submit();}
    //                 // else {window.location.replace = "/checkout/customer-info"}
    //             }
    //         }
    //     });
    // });
    $('.cart-body .edit').on('click', function(e) {
      e.preventDefault();
      var row = $(this).closest('tr');
      row.toggleClass('edit');

      if($(this).text() == 'Edit') {
        $(this).text('Save');
      }
      else {
        var quantity = row.find('.quantity-dropdown').val();
        var id = row.find('.quantity-dropdown').attr('data-id');
        $.get("/cart/"+id+"/quantity/"+quantity+"/update", function() {
          row.find('span.quantity').text(quantity+' piece');
          window.location.reload();
        });
      }
    })

    /**************
    ** admin cms
    /*************/
    if($(".cms-slider").get(0)) {
        $(".cms-slider").lightSlider({
            item: 1,
            enableDrag: false,
            pause: 5000,
            slideMargin: 0,
            controls: false,
            adaptiveHeight: true
        });
    }
    if($(".components-slider").get(0)) {
        var componentSlider = $(".components-slider").lightSlider({
            item: 1,
            enableDrag: false,
            pause: 5000,
            slideMargin: 0,
            controls: false,
            pager: false,
            loop: true,
            adaptiveHeight: true
        });

        $('.component-control').on('click', '.next', function() {componentSlider.goToNextSlide();});
        $('.component-control').on('click', '.prev', function() {componentSlider.goToPrevSlide();});
    }
    $('select#product-dropdown').on('change', function() {
        var image = $(this).find(':selected').attr('data-image');
        $('.product-image').attr('src', image);
    });
    if($('#ckeditor').get(0)) CKEDITOR.replace('ckeditor');
    if($('#data-table').get(0)) {
        // click row to redirect
        $('#data-table tbody').on('click', 'tr', function() {
            var url = $(this).attr('href');
            var mailto = $(this).attr('mailto');
            if(url != '') {
                if(typeof mailto !== typeof undefined && mailto != '')
                    window.open(mailto);

                location.href = url;
            }
        });

        $('#data-table').DataTable({
            // "paging":    false,
            // "info":      false,
            "aaSorting": [],
            columnDefs: [
                {
                    "targets": [ 0, 1, 2, 3 ],
                    "className": 'mdl-data-table__cell--non-numeric'
                }
            ],
        });
    }
    $('.edit-shipment-tab').on('click', function() {
        $($(this).attr('data-target')).toggleClass('hide');
    });
    $('.required-confirm').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');

        swal({
            title: "Are you sure?",
            text: "Take this action may affect user shopping experience",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel pls!",
            closeOnConfirm: false
        },
        function(isConfirm){
            if (isConfirm) {form.submit();}
        });
    });
});
