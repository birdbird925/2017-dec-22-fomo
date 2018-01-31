$(function() {
    // CK editor
    if($('#ckeditorProductDescription').get(0)) CKEDITOR.replace('ckeditorProductDescription');

    // tag input
    if($('input[name="tag"]').get(0)){
        $('input[name="tag"]').tagsInput({
            'defaultText': '',
            'width': '100%',
            'height': 'auto',
        });
    }

    // exist product guide select change
    $('select[name="guide"]').change(function() {
        var image = $(this).find(":selected").attr('data-image');
        $('#exist-guide-image').attr('src', image);
    });

    // has product guide triggle
    $('input[name="hasGuide"]').change(function() {
        $('#product-guide-wrapper').toggleClass('hidden');
    });

    // new product guide triggle
    $('#new-guide-triggle').click(function(){
        if($(this).text() == 'New')
            $(this).text('Browse exist guide');
        else
            $(this).text('New');

        var checkbox = $('input[name="newGuide"]');
        if(checkbox.val() == 1)
            checkbox.val(0);
        else
            checkbox.val(1);

        $('#exist-guide').toggleClass('hidden');
        $('#new-guide').toggleClass('hidden');
    });

    // new product guide image detected
    $('input:file[name="guide-image"]').change(function() {
        var token = $('meta[name="csrf-token"]').attr('content');
        // if uplodaed image, remove image
        var imageID = $(this).attr('image-id');
        if(typeof imageID !== typeof undefined && imageID !== false){
            $.ajax({
                data: {_token: token, id: imageID},
                url: '/image/delete',
                method: 'POST',
                dataType: 'text',
                error: function(a, b, c){
                    console.log(a.responseText);
                },
                success: function(response){
                    $('input:file[name="guide-image"]').removeAttr('image-id');
                    $('#new-guide-image').addClass('hidden');
                }
            });
        }

        // upload image
        var formData = new FormData();
        var name = $('input[name="guide-image"]')[0].files[0].name;
        formData.append('file', $('input[name="guide-image"]')[0].files[0]);
        formData.append('_token', token);
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
                showNotification('warning', errorMsg, 'bottom', 'center');
            },
            success: function(response){
                $('input[name="guide-image"]').attr('image-id', response.id);
                $('#new-guide-image').attr('src', '/images/'+response.image);
                $('#new-guide-image').removeClass('hidden');
            }
        })
    });

    // variant add / remove tag function
    function updateVariantDetail() {
        var variant = [];
        $.each($(".variant-list"),function(index){
            var variantValue = [];
            $.each($(this).find('.tag span'), function() {
                var value = $(this).text().replace(/\s/g, '');

                if(variant.length == 0)
                    variantValue.push(value);
                else
                    for (i = 0; i < variant.length; i++)
                        variantValue.push(variant[i]+','+value);
            });

            if(variantValue.length > 0)
                variant = variantValue ;
        });

        $('#variant-detail-table tbody').html('');
        if(variant.length == 0)
            $('#variant-detail-form').addClass('hidden');
        else {
            $('#variant-detail-form').removeClass('hidden');
            var variantDetailForm = $('#variant-detail-form-template').html();
            var quantity = $('input[name="quantity"]').val();
            var price = $('input[name="price"]').val();
            var sku = $('input[name="sku"]').val();
            var skuID = parseInt(sku.replace(/\D/g,''));
            for(i = 0; i < variant.length; i++) {
                if(sku != '')
                    sku = sku.substring(0, (sku.length - skuID.toString().length)) + (skuID+i);

                $('#variant-detail-table tbody').append('<tr class="variant-detail">'+variantDetailForm+'</tr>');
                $('.variant-detail').last().find('.variant-image').attr('id', 'vImage-'+i);
                $('.variant-detail').last().find('input[name="variant-price"]').val(price);
                $('.variant-detail').last().find('input[name="variant-sku"]').val(sku);
                $('.variant-detail').last().find('input[name="variant-quantity"]').val(quantity);

                var array = variant[i].split(',');
                for(x = 0; x < array.length; x++) {
                    $('.variant-detail').last().find('.variant-name').attr('data-name', array.toString());
                    $('.variant-detail').last().find('.variant-name ul').append('<li>'+array[x]+'</li>');
                }
            }
        }
    }

    // variant tag input option
    var variantTagOption = {
        'width': '100%',
        'height': 'auto',
        'defaultText': '',
        'onAddTag': function() {
            updateVariantDetail();
        },
        'onRemoveTag': function() {
            $('#variant-detail-form').removeClass('hidden');
            updateVariantDetail();
        },
    }

    // bind varint tag input plugin
    if($('input[name="variantValue"]').get(0)){
        $('input[name="variantValue"]').tagsInput(variantTagOption);
    }

    // variant-form triggle
    $('#variants-triggle').click(function() {
        $('#variant-wrapper').toggleClass('hidden');

        if($(this).html() == 'Add variant')
            $(this).html('Cancel');
        else
            $(this).html('Add variant');
    });

    // add variant-form-triggle
    $('#add-variant-triggle').click(function() {
        $('.btn-remove-variant').removeClass('hidden');
        if($('.variant-list').length == 2)
            $(this).addClass('hidden');

        var variantForm = $('.variant-list').html();
        $('#variant-form').append('<div class="row variant-list">'+variantForm+'</div>');
        console.log(variantForm);
        $('.variant-list').last().find('input[name="variantValue"]').val('');
        $('.variant-list').last().find('input[name="variantName"]').val('');
        $('.variant-list').last().find('.tagsinput').remove();
        $('.variant-list').last().find('input[name="variantValue"]').removeAttr('data-tagsinput-init');
        $('input[name="variantValue"]').tagsInput(variantTagOption);
    });

    // remove variant-form-triggle
    $('#variant-wrapper').on('click', '.btn-remove-variant', function() {
        $('#add-variant-triggle').removeClass('hidden');

        var variantList =$(this).closest('.variant-list');
        variantList.remove()
        updateVariantDetail();
        if($('.variant-list').length == 1)
            $('.btn-remove-variant').addClass('hidden');
    });

    // edit exist variant detail triggle
    $('#exist-variant-box').on('click', '.btn-edit-variant-detail', function() {
        var wrapper = $(this).closest('tr');

        if($(this).attr('data-action') == 'edit') {
            $(this).attr('data-action', 'save');
            wrapper.find('.variant-info').toggleClass('hidden');
            wrapper.find('input').toggleClass('hidden');
            $(this).find('i').toggleClass('fa-pencil');
            $(this).find('i').toggleClass('fa-floppy-o');
        }
        else {
            var emptyInput = false;
            $('input', wrapper).each(function() {
                var displayText = $(this).closest('td').find('.variant-info');
                value = $(this).val();

                if(value == '') {
                    if($(this).attr('name') == 'variant-sale'){
                        displayText.text('-');
                    }
                    else {
                        emptyInput = true;
                        $(this).val(displayText.text());
                    }
                }
                else {
                    displayText.text(value);
                }
            });

            // if variant item was new generate, remove the empty variant item
            var variantID = wrapper.attr('data-id');
            if(emptyInput && variantID == '') {
                showNotification('warning', "Please provide complete information for the new variant products", "bottom", "center");
            }
            else {
                $(this).attr('data-action', 'edit');
                wrapper.find('.variant-info').toggleClass('hidden');
                wrapper.find('input').toggleClass('hidden');
                $(this).find('i').toggleClass('fa-pencil');
                $(this).find('i').toggleClass('fa-floppy-o');
            }
        }


    });

    // remove exist variant detail triggle
    $('#exist-variant-box').on('click', '.btn-remove-variant-detail', function() {
        var wrapper = $(this).closest('tr');
        wrapper.remove();

        // if not more exist variant item, set remove all exist variant item == true
        if($('#exist-variant-box').find('tbody tr').length == 0){
            $('#exist-variant-box').toggleClass('hidden');
            $('#new-variant-box').toggleClass('hidden');
            $('#pricing-inventory-box').toggleClass('hidden');
            $('input[name="removeAllExistVariant"]').val(1);
        }
    });

    // remove all exist variant button
    $('#remove-exist-variants').click(function() {
        $('#exist-variant-box').toggleClass('hidden');
        $('#new-variant-box').toggleClass('hidden');
        $('#pricing-inventory-box').toggleClass('hidden');
        $('input[name="removeAllExistVariant"]').val(1);
    });

    // add new variant in exist variant list
    $('#new-exist-variant-category').click(function() {
        var html = $('#exist-variant-category').find('.form-group').last().html();
        $('#exist-variant-category').append('<div class="form-group">'+html+'</div>');
        var element = $('#exist-variant-category').find('.form-group').last();
        var categoryIndex = parseInt(element.find('input').attr('category-index'))+1;
        element.find('input').attr('category-index', categoryIndex);
        element.find('input').attr('default-value', '');
        element.find('input').val('');

        var count = ($('#exist-variant-category').find('.form-group').length);
        element.find('label span').text('Variant '+count);

        if(count >= 3)
            $(this).addClass('hidden');
        else
            $(this).removeClass('hidden');

        $('.remove-exist-variant-category').removeClass('hidden');
    });

    // Delete variant category
    $('#exist-variant-box').on('click', '.remove-exist-variant-category', function(){
        var wrapper = $(this).closest('.form-group');
        var index = wrapper.find('input').attr('category-index');

        wrapper.remove();
        $('#exist-variant-category').find('.form-group').each(function(index) {
            $(this).find('span').text('Variant '+(index+1));
        });

        if($('#category-'+index).get(0)) {
            $('#category-'+index).remove();
            $('.category-'+index).remove();

            // remove repeat variant after the variant category has benn removed
            var variant = [];
            $.each($(".exist-variant-item"),function(index){
                var name = [];
                $(this).find('.exist-variant-name').each(function () {
                    name.push($(this).text());
                });
                if($.inArray(name.toString(), variant) == -1){
                    variant.push(name.toString());
                }
                else
                    $(this).remove();
            });

            if(variant.length < 1) {
                $('#exist-variant-box').toggleClass('hidden');
                $('#new-variant-box').toggleClass('hidden');
                $('#pricing-inventory-box').toggleClass('hidden');
                $('input[name="removeAllExistVariant"]').val(1);
            }
        }

        if($('.remove-exist-variant-category').length < 3)
            $('#new-exist-variant-category').removeClass('hidden');
        if($('.remove-exist-variant-category').length == 1)
            $('.remove-exist-variant-category').addClass('hidden');
    });

    // once variant catgory name has modified, update it at table
    $('#exist-variant-box').on('change', 'input[name="exist-variant-category"]', function(){
        var value = $(this).val();
        var defaulValue = $(this).attr('default-value');
        var index = $(this).attr('category-index');

        if(value.trim().length == '') {
            // remove empty name variant column
            if(typeof defaultValue == 'undefined'){
                $('#category-'+index).remove();
                $('.category-'+index).remove();
            }
            else {
                $(this).val(defaulValue);
                $('#category-'+index).text(defaulValue);
            }
        }
        else {
            if($('#category-'+index).get(0)) {
                $('#category-'+index).text(value);
            }
            else {
                $('th.sku').before('<th id="category-'+index+'">'+value+'</th>');
                $('td.sku').before('<td class="category-'+index+'"><span class="variant-info exist-variant-name">-</span><input type="text" name="variant-name-'+(index+1)+'" class="form-control hidden" value=""></td>');
            }
        }
    });

    // add new variant item in exist variant list
    $('#new-exist-variant-item').click(function() {
        var template = $('#exist-variant-box tbody tr').html();
        $('#exist-variant-box tbody').append('<tr class="exist-variant-item" data-id="">'+template+'</tr>');

        $('.exist-variant-item').last().find('input').val('');
        $('.exist-variant-item').last().find('input').attr('value','');
        $('.exist-variant-item').last().find('.variant-info').text('');
        $('.exist-variant-item').last().find('.variant-image').attr('image-id','');
        $('.exist-variant-item').last().find('.variant-image').css('background-image',"url('/images/image_placeholder.png')");

        var btnEdit = $('.exist-variant-item').last().find('.btn-edit-variant-detail');
        if(btnEdit.attr('data-action') == 'edit') {
            btnEdit.attr('data-action', 'save');
            btnEdit.find('i').toggleClass('fa-pencil');
            btnEdit.find('i').toggleClass('fa-floppy-o');

            $('.exist-variant-item').last().find('input').toggleClass('hidden');
            $('.exist-variant-item').last().find('.variant-info').toggleClass('hidden');
        }
    });

    function validateVariant(){
        // edit product - product already has variant
        if($('input[name="removeAllExistVariant"]').get(0) && $('input[name="removeAllExistVariant"]').val() == "0") {
            var variant = [];
            var error = '';
            var notComplete = false;
            var notCompleteMsg = '<li>You have not provide complete information for variant products.</li>';

            // get variant category
            var variantCategory = [];
            $.each($('input[name="exist-variant-category"]'), function() {
                var category = $(this).val();
                var index = $(this).attr('category-index');
                if(category.trim() != '')
                    variantCategory.push(category);
            });

            // get variant detail
            var variantName = [];
            $.each($('.exist-variant-item'), function() {
                var name = [];
                $(this).find('[class^="category-"]').each(function() {
                    var value = $(this).find('input').val();

                    if(value.trim() == '')
                        notComplete = true;
                    else
                        name.push(value);
                });

                if(!notComplete) {
                    // variant name was repeated
                    if($.inArray(name.toString(), variantName) != -1){
                        error += '<li>The variant \'';
                        $.each(name, function(index) {
                            if(index == 0)
                                error += name[index];
                            else
                                error += ' / '+name[index];
                        });
                        error += '\' already exists. Please change at least one option value.</li>';
                    }
                    else {
                        variantName.push(name.toString());

                        var imageID = $(this).find('.variant-image').attr('image-id');
                        var sku = $(this).find('input[name="variant-sku"]').val();
                        var price = $(this).find('input[name="variant-price"]').val();
                        var quantity = $(this).find('input[name="variant-quantity"]').val();
                        var sale = $(this).find('input[name="variant-sale"]').val();

                        variant.push({
                            imageID: imageID,
                            name: name.toString(),
                            sku: sku,
                            price: price,
                            quantity: quantity,
                            sale: sale,
                        });
                    }
                }
            });

            if(notComplete) error += notCompleteMsg;

            if(error != '')
                return {error: true, message: error};
            else
                return {error: false, hasVariant: true, category: variantCategory, detail: variant};

            console.log(variant);
            return {error: false, hasVariant: false};
        }
        // create edit product - check user has added new variant
        else {
            if($('#variants-triggle').html() == 'Cancel'){
                var variantCategory = [];
                var hasVariant = false;
                var errorFound = false;
                var errorMsg = '<li>Variants option name can\'t be blank.</li>';

                $.each($(".variant-list"),function(index){
                    if($(this).find('input[name="variantValue"]').val().trim().length != 0){
                        hasVariant = true;
                        var name = $(this).find("input[name='variantName']").val();
                        if(name.trim() == '')
                            errorFound = true;
                        else
                            variantCategory.push(name);
                    }
                });
                if(errorFound)
                    return {error: true, message: errorMsg};

                if(hasVariant){
                    var variantDetail = validateVariantDetail();
                    if(variantDetail.error)
                        return {error: true, message: variantDetail.message};
                    else
                        return {error: false, hasVariant: true, category: variantCategory, detail: variantDetail.variant};
                }
            }
            return {error: false, hasVariant: false};
        }
    }
    function validateVariantDetail(){
        var errorFound = false;
        var errorMsg = '<li>You have not provide complete information for variant products</li>';
        var variant = [];
        $.each($('.variant-detail'), function(index) {
            var imageID = $(this).find('.variant-image').attr('image-id');
            var name = $(this).find('.variant-name').attr('data-name');
            var sku = $(this).find('input[name="variant-sku"]').val();
            var price = $(this).find('input[name="variant-price"]').val();
            var quantity = $(this).find('input[name="variant-quantity"]').val();

            if(sku.trim() == '' || price == '' || quantity == '')
                errorFound = true;
            else
                variant.push({
                    imageID: imageID,
                    name: name,
                    sku: sku,
                    price: price,
                    quantity: quantity
                });
        });

        if(errorFound)
            return {error: true, message: errorMsg};
        else
            return {error: false, variant: variant};
    }

    // save product button clicked
    $('#saveProductBtn').click(function() {
        var formData = {};
        var errorMsg = '';
        // title validate
        var title = $('input[name="title"]').val();
        if(title.trim() == '') errorMsg += '<li>Product title can\'t be blank</li>';
        // product image validate
        var productImage = $('input[name="productImage"]').val();
        if(productImage == '') errorMsg += '<li>At least upload one photo as product image</li>';
        // product guide
        var productGuide = {};
        if($('input[name="hasGuide"]').is(':checked')) {
            // new guide
            if($('input[name="newGuide"]').val() == "1"){
                guideName = $('input[name="guide-name"]').val(),
                guideImage = $('input[name="guide-image"]').attr('image-id');

                if(guideName.trim() == '')
                    errorMsg += '<li>New product guide name can\'t be blank</li>';

                if(guideImage === 'undefined' || guideImage == '')
                    errorMsg += '<li>Please upload an image as product guide</li>';

                formData.guide = {newGuide: 1, name: guideName, image: guideImage };
            }
            else {
                formData.guide = {newGuide: 0, id: $('select[name="guide"]').val()};
            }
        }
        // variant validation
        var variant = validateVariant()
        if(variant.error) {
            errorMsg += variant.message;
        }
        else {
            if(variant.hasVariant){
                formData.variant = variant.category;
                formData.variantDetail = variant.detail;
            }
            else {
                var price = $('input[name="price"]').val();
                var sale = $('input[name="sale"]').val();
                var sku = $('input[name="sku"]').val();
                var quantity = $('input[name="quantity"]').val();

                if(price == '') errorMsg += '<li>Product price can\'t be blank</li>';
                if(sku.trim() == '') errorMsg += '<li>Product SKU can\'t be blank</li>';
                if(quantity == '') errorMsg += '<li>Product quantity can\'t be blank</li>';

                formData.price = price;
                formData.sale = sale;
                formData.sku = sku;
                formData.quantity = quantity;
            }
        }
        if(errorMsg != '') {
            $('#productAlert').removeClass('hidden');
            $('#productAlert').find('.alert-description').html(errorMsg);
            scrollToAlert('productAlert');
        }
        else {
            var token = $('meta[name="csrf-token"]').attr('content');
            formData['_token'] =  token;
            formData.title = title;
            formData.description = CKEDITOR.instances.ckeditorProductDescription.getData();
            formData.images = productImage;
            formData.visible = (($('input[name="visibility"]').is(':checked')) ? 1 : 0);
            formData.type = $('input[name="type"]').val();
            formData.vendor = $('input[name="vendor"]').val();
            formData.tag = $('input[name="tag"]').val();
            // edit product mode
            if($('input[name="deleteImage"]').get(0)) {
                var id = $("#saveProductBtn").attr('data-id');
                formData.deleteImages = $('input[name="deleteImage"]').val();
                console.log(formData);
                $.ajax({
                    data: formData,
                    url: '/admin/product/'+id,
                    method: 'post',
                    dataType: 'json',
                    error: function(a, b, c){
                        $('#productAlert').removeClass('hidden');
                        console.log(a.responseText);
                        console.log(b);
                        console.log(c);
                        $('#productAlert').find('.alert-description').html('JSON.parse(a.responseText).message');
                        scrollToAlert('productAlert');
                    },
                    success: function(response){
                        needToConfirm = false;
                        window.location.href = "/admin/product";
                    }
                });
            }
            else {
                $.ajax({
                    data: formData,
                    url: '/admin/product',
                    method: 'POST',
                    dataType: 'json',
                    error: function(a, b, c){
                        $('#productAlert').removeClass('hidden');
                        $('#productAlert').find('.alert-description').html(JSON.parse(a.responseText).message);
                        scrollToAlert('productAlert');
                    },
                    success: function(response){
                        needToConfirm = false;
                        window.location.href = "/admin/product";
                    }
                });
            }
        }
    });

    // delete product button clicked
    $('#deleteProductBtn').click(function() {
        var id = $(this).attr('data-id');
        var token = $('meta[name="csrf-token"]').attr('content');

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this imaginary file!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        },
        function(){
            $.ajax({
                data: {_token: token, _method: 'delete'},
                url: '/admin/product/'+id,
                method: 'post',
                error: function(a, b, c){
                    console.log(a.responseText);
                },
                success: function(response){
                    window.location.href = '/admin/product';
                }
            });
        });
    });

    // product table Datatable plugin
    $('#productTable').DataTable({
        "paging":    false,
        "info":      false,
        "aaSorting": [],
        columnDefs: [
            {
                "targets": [ 0, 1, 2, 3 ],
                "className": 'mdl-data-table__cell--non-numeric'
            },
            {
                "targets": [0,1],
                "orderable": false,
            }
        ],
    });

    $('#productTable').on( 'click', 'tbody tr', function () {
        window.location.href = $(this).attr('href');
    });

    $('#inventoryTable').DataTable({
        "paging": false,
        "info": false,
        "aaSorting": [],
        columnDefs: [
            {
                "targets": [ 0, 1, 2, 3 ],
                "className": 'mdl-data-table__cell--non-numeric'
            },
            {
                "targets": [0,1,6],
                "orderable": false,
            }
        ]
    });

    $('#inventoryTable').on('focus keyup keypress change', 'input[name="variant-quantity"]', function() {
        var wrapper = $(this).closest('tr');
        var newQuantity = parseInt($(this).val());
        var currentQuantity = parseInt(wrapper.find('.default-quantity').text());
        if(!isNaN(newQuantity) && newQuantity != currentQuantity) {
            wrapper.find('.new-quantity').text(newQuantity);
            wrapper.find('.new-quantity').removeClass('hidden');
            wrapper.find('.fa-long-arrow-right').removeClass('hidden');
        }
        else {
            wrapper.find('.new-quantity').addClass('hidden');
            wrapper.find('.fa-long-arrow-right').addClass('hidden');
        }
    });

    $('#inventoryTable').on('click', '.updateInventoryBtn', function() {
        var wrapper = $(this).closest('tr');
        var newQuantity = parseInt(wrapper.find('input[name="variant-quantity"]').val());
        var currentQuantity = parseInt(wrapper.find('.default-quantity').text());

        if(isNaN(newQuantity))
            showNotification('warning', 'New quantity can\'t be blank', 'bottom', 'center');
        else if(newQuantity != currentQuantity) {
            var id = $(this).attr('data-id');
            var token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                data: {_token: token, quantity: newQuantity},
                url: '/admin/product/'+id+'/inventory',
                method: 'post',
                error: function(a, b, c){
                    console.log(a.responseText);
                },
                success: function(response){
                    showNotification('info', 'Variants product\'s quantity updated', 'top', 'right');

                    wrapper.find('.default-quantity').text(newQuantity);
                    wrapper.find('.new-quantity').addClass('hidden');
                    wrapper.find('.fa-long-arrow-right').addClass('hidden');
                    if(newQuantity > 0)
                        wrapper.find('.default-quantity').css({'color': '#636b6f'});
                }
            });
        }
    });

    //Table checkbox toggle
    // function test(source, className) {
    //     checkboxes = document.getElementsByClassName(className);
    //     for(var i=0, n=checkboxes.length;i<n;i++) {
    //         checkboxes[i].checked = source.checked;
    //     }
    // }

    // error found scroll to alert
    function scrollToAlert(elementID) {
        $('.main-panel').animate({
            scrollTop: ($("#"+elementID).offset().top)
        }, 1200);
    }

    // Leave page confirmation
    // $.fn.only = function (events, callback) {
    //     //The handler is executed at most once for all elements for all event types.
    //     var $this = $(this).on(events, myCallback);
    //     function myCallback(e) {
    //         $this.off(events, myCallback);
    //         callback.call(this, e);
    //     }
    //     return this
    // };
    //
    // $(":input").only('change', function() {
    //     if($.contains('.form-content', 'body'))
    //         needToConfirm = true;
    // });
    //
    // function leavePageValidation(e) {
    //     if (needToConfirm){
    //         return "You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?";
    //     }
    // }
    // function leavePageExecution(e) {
    //     // if there had already uploaded some photo, remove it before unload
    //     var imageArray = [];
    //     var uploadedProductImage = $('input[name="productImage"]').val();
    //     var uploadedGuideImage = $('input[name="guide-image"]').attr('image-id');
    //
    //     if(typeof uploadedProductImage !== 'undefined' && uploadedProductImage != '')
    //         imageArray = uploadedProductImage.split(',');
    //
    //     if(typeof uploadedGuideImage !== 'undefined' && uploadedGuideImage != '')
    //         imageArray.push(uploadedGuideImage);
    //
    //     if(imageArray.length > 0) {
    //         var token = $('meta[name="csrf-token"]').attr('content');
    //         $.each(imageArray, function(index, value) {
    //             $.ajax({
    //                 data: {_token: token, id: imageID},
    //                 url: '/image/delete',
    //                 method: 'POST'
    //             });
    //         });
    //     }
    // }
    // window.onbeforeunload = leavePageValidation;
    // window.unload = leavePageExecution;
    // end leave page confirmation

    /*******************/
    /* customize main js
    /*******************/
    var stageWidth = $('#customize-canvas').width();
    var stageHeight = $(window).height();
    var stage = new Konva.Stage({
        container: 'customize-canvas',
        width: stageWidth,
        height: stageHeight
    });
    // update stage width and height as input hidden value
    $('input[name="stage_width"]').val(stageWidth);
    $('input[name="stage_height"]').val(stageHeight);

    // hide resize anchor
    stage.on('mousedown touchstart', function() {
        stage.find('.resize-group').opacity(0);
        stage.find('Layer')[stage.find('Layer').length-1].draw();
    });
    function loadCustomizeImage(konvaImg, stageWidth, stageHeight, img = null) {
        loadCustomizeImage.size = (typeof loadCustomizeImage.size === undefined) ? 0.9 : loadCustomizeImage.size;
        var imgWidth = (img) ? img.width : konvaImg.getAttr('defaultWidth');
        var imgHeight = (img) ? img.height : konvaImg.getAttr('defaultHeight');
        var ration = (stageWidth/imgWidth) * loadCustomizeImage.size;
        var width = imgWidth * ration;
        var height = imgHeight * ration;
        var x = stageWidth/2 - width/2;
        var y = stageHeight/2 - height/2;

        if(img) konvaImg.image(img);
        konvaImg.width(width);
        konvaImg.height(height);
        konvaImg.x(x);
        konvaImg.y(y);
        konvaImg.setAttr('defaultWidth', imgWidth);
        konvaImg.setAttr('defaultHeight', imgHeight);
        konvaImg.getLayer().draw();
    }
    function customizeUpdateDescription(element) {
        var description = element.attr('description');
        var descriptionWrapper = element.attr('description-wrapper');
        var wrapper = element.closest('.customize-step-wrapper');;
        wrapper.find('.'+descriptionWrapper).html(description);
    }
    function loadCustomize() {
        // get selected customize type
        loadCustomize.customizeRadio = $('input[name="customize_type"]:checked');
        loadCustomize.customizeID = loadCustomize.customizeRadio.val();
        baseLayer = new Konva.Layer({id: 'base-layer'});
        stage.add(baseLayer);
        var imageAttr = {};
        imageAttr['front'] = 'f-image';
        imageAttr['back'] = 'b-image';
        // loop array to add both front and back image
        $.each(imageAttr, function(direction, attribute) {
            var index = direction+'-base';
            if(stage.find('#'+index).length == 0){
                var konvaImage = new Konva.Image({name: direction, id: index});
                baseLayer.add(konvaImage);
            }
            else{
                var konvaImage = stage.find('#'+index)[0];
            }
            // add image into canvas
            var img = new Image();
            img.onload = function() {loadCustomizeImage(konvaImage, stage.width(), stage.height(), img);};
            img.src = loadCustomize.customizeRadio.attr(attribute);
        });

        // update customize type description and hide and show customize stuff
        customizeUpdateDescription(loadCustomize.customizeRadio);
        $('.customize-element').addClass('hidden');
        $('.'+loadCustomize.customizeRadio.attr('target-element')).removeClass('hidden');

        // show all customize element
        stage.find('Image').show();
        stage.find('Rect').show();
        stage.find('Text').show();

        // loop through all step
        $('.customize-step-wrapper').each(function() {
            var stepWrapper = $(this);
            stepWrapper.removeClass('hidden');
            var unavailable = $(this).attr('unavailable');
            // convernt php array string to js array
            unavailable = unavailable.replace(/"/g, '');
            unavailable = unavailable.replace('[', '');
            unavailable = unavailable.replace(']', '');
            array = unavailable.split(",");

            if($.inArray(loadCustomize.customizeID, array) < 0) {
                var stepID      = $(this).attr('step-id');
                var direction   = $(this).attr('direction');
                var personalize = $(this).attr('personalize');
                var primaryStep = $(this).attr('primary');
                var layer       = '';
                var layerIndex  = $(this).attr('layer');
                var fixed       = $(this).attr('fixed');
                var inputName   = (fixed=='1') ? 'step_'+stepID : 'customize_'+loadCustomize.customizeID+'_step_'+stepID ;
                // based on layerIndex create and add layer into stage
                if(layerIndex != ''){
                    layerID = '#layer'+layerIndex;
                    if(stage.find(layerID).length == 0) {
                        layer = new Konva.Layer({id: 'layer'+layerIndex});
                        layer.setAttr('index', layerIndex);
                        stage.add(layer);
                        reorderLayer();
                    }
                    else {
                        layer = stage.find(layerID)[0];
                    }
                }

                if(personalize != '') {
                    var productStageWidth = $('input[name="stage_width"]').attr('data-width');
                    var productStageHeight = $('input[name="stage_height"]').attr('data-height');
                    var currentStageWidth = $('input[name="stage_width"]').val();
                    var currentStageHeight = $('input[name="stage_height"]').val();

                    // add personalize element (image) into layer
                    if(stepWrapper.has('input[type=file]').length) {
                        var personalizeImage = ''
                        if(layer.find('#step'+stepID+'_image').length == 0) {
                            personalizeImage = new Konva.Image({
                                id: 'step'+stepID+'_image',
                                name: direction,
                                x: stage.getWidth()/2,
                                y: stage.getHeight()/2,
                            });
                            personalizeImage.addName('step'+stepID);
                            layer.add(personalizeImage);
                        }
                        else {
                            personalizeImage = layer.find('#step'+stepID+'_image')[0];
                        }

                        if(stepWrapper.find('input[name="image-id"]').val() != '') {
                            var id = stepWrapper.find('input[name="image-id"]').val();
                            var src = stepWrapper.find('input[name="image-src"]').val();
                            var width = stepWrapper.find('input[name="image-width"]').val();
                            var height = stepWrapper.find('input[name="image-height"]').val();
                            var x = stepWrapper.find('input[name="image-position-x"]').val();
                            var y = stepWrapper.find('input[name="image-position-y"]').val();
                            var rotation = stepWrapper.find('input[name="image-rotation"]').val();

                            var pImg = new Image();
                            pImg.onload = function() {
                                personalizeImage.image(pImg);
                                personalizeImage.width(Number(width));
                                personalizeImage.height(Number(height));
                                personalizeImage.offsetX(Number(width)/2);
                                personalizeImage.offsetY(Number(height)/2);
                                personalizeImage.x(Number(x));
                                personalizeImage.y(Number(y));
                                personalizeImage.rotation(Number(rotation));
                                layer.draw();
                                addAnchor(personalizeImage, direction, stepWrapper);

                                // move text control to top
                                if(stage.find('#step'+stepID+'_text_control').length > 0) {
                                    stage.find('#step'+stepID+'_text_control')[0].moveToTop();
                                    stage.find('#step'+stepID+'_text_control')[0].getLayer().draw();
                                }
                            };
                            pImg.src = '/images/'+src;
                        }
                        stepWrapper.find('input[name="image-position-x"]').val(personalizeImage.x());
                        stepWrapper.find('input[name="image-position-y"]').val(personalizeImage.y());
                    }

                    // add personalize element (text) into layer
                    if(stepWrapper.has('input[type=text]').length) {
                        var personalize = '';
                        if(layer.find('#step'+stepID+'_text').length == 0) {
                            personalizeText = new Konva.Text({
                                id: 'step'+stepID+'_text',
                                name: direction,
                                x: stage.getWidth()/2,
                                y: stage.getHeight()/2,
                                fontFamily: 'monospace',
                                fill: 'gold',
                            });
                            // update personalize position into input hidden
                            personalizeText.addName('step'+stepID);
                            layer.add(personalizeText);
                        }
                        if(stepWrapper.find('input[type=text]').val() != '') {
                            var value = stepWrapper.find('input[type=text]').val();
                            var x = stepWrapper.find('input[name="text-position-x"]').val();
                            var y = stepWrapper.find('input[name="text-position-y"]').val();
                            personalizeText = stage.find('#step'+stepID+'_text')[0];
                            personalizeText.text(value);
                            personalizeText.x(productStageWidth != currentStageWidth ? (x/productStageWidth)*currentStageWidth : x);
                            personalizeText.y(productStageHeight != currentStageHeight ? (y/productStageHeight)*currentStageHeight : y);

                            console.log(personalizeText.y());
                            addControlForText(personalizeText, stepWrapper, stepID, direction);
                        }

                        stepWrapper.find('input[name="text-position-x"]').val(personalizeText.x());
                        stepWrapper.find('input[name="text-position-y"]').val(personalizeText.y());
                    }

                    // check layer has customize personalize area to prevent personalize overflow happens
                    var personalizeArea = '';
                    if(layer.find('.personalize-area').length == 0) {
                        personalizeArea = new Konva.Image({name: 'personalize-area'});
                        personalizeArea.addName('step'+stepID);
                        personalizeArea.addName(direction);
                        layer.add(personalizeArea);
                    }
                    else {
                        personalizeArea = layer.find('.personalize-area')[0];
                    }

                    var img = new Image();
                    img.onload = function() {loadCustomizeImage(personalizeArea, stage.width(), stage.height(), img);};
                    img.src = loadCustomize.customizeRadio.attr(direction+'-personalize');
                }
                else if(primaryStep == '0'){
                    var component = stepWrapper.find('input[name="'+inputName+'"]:checked');
                    // update the description
                    customizeUpdateDescription(component);

                    // is step was triggle to control the size
                    var size = component.attr('size-triggle');
                    if(typeof size !== typeof undefined && size !== '') {
                        loadCustomizeImage.size = (size == 'bigger') ? 1 : ((size == 'smaller') ? 0.8 : 0.9);
                    }
                    else {
                        // check and select extral
                        if(component.attr('extral-option') == '1') {
                            var wrapperClass = component.attr('target-element');
                            var wrapper = stepWrapper.find('.'+wrapperClass);
                            var component = wrapper.find('input[name="'+inputName+'_extral"]:checked');
                            customizeUpdateDescription(component);
                        }

                        // loop array to add both front and back image
                        $.each(imageAttr, function(direction, attribute) {
                            var index = 'step'+stepID+'_'+direction;
                            // check step image was exist or not
                            if(stage.find('#'+index).length == 0){
                                var konvaImage = new Konva.Image({id: index});
                                konvaImage.addName(direction);
                                konvaImage.addName('step'+stepID);
                                layer.add(konvaImage);
                            }
                            else {
                                var konvaImage = stage.find('#'+index)[0];
                            }

                            // add image into canvas
                            var img = new Image();
                            img.onload = function() {loadCustomizeImage(konvaImage, stage.width(), stage.height(), img);};
                            img.src = component.attr(attribute);
                        });
                    }
                }
            }
            else {
                stepWrapper.addClass('hidden');
                // hide step image
                stage.find('.step'+stepID).hide();
            }
        });

        hideImageInDirection('back');
    }
    function reorderLayer() {
        var topIndex = 0;
        $.each(stage.find('Layer'), function(index, layer) {
            var index = layer.getAttr('index');
            if(typeof index !== undefined) {
                if(index > topIndex) {
                    topIndex = index;
                    layer.moveToTop();
                }
            }
        });
    }
    function hideImageInDirection(direction){
        stage.find('.'+direction).hide();
    }
    function resize(activeAnchor, konvaImg, stepWrapper) {
        var imageLayer = konvaImg.getLayer();
        var group = activeAnchor.getParent();
        var width = group.getWidth();
        var height = group.getHeight();
        var x = group.getX();
        var y = group.getY();
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
        imageLayer.draw();

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

        // update image info into input hidden
        stepWrapper.find('input[name="image-width"]').val(konvaImg.width());
        stepWrapper.find('input[name="image-height"]').val(konvaImg.height());
        stepWrapper.find('input[name="image-rotation"]').val(konvaImg.rotation());
    }
    function addAnchor(konvaImg, direction, stepWrapper){
        var imgLayer = konvaImg.getLayer();
        var width = konvaImg.getWidth();
        var height = konvaImg.getHeight();
        var stage = konvaImg.getStage();
        var layer = stage.find('Layer')[stage.find('Layer').length-1];

        var groudID = konvaImg.id()+'_control';
        if(stage.find('#'+groudID).length > 0) {stage.find('#'+groudID)[0].destroy();}

        // group option
        var group = new Konva.Group({
            name: 'resize-group',
            id: groudID,
            x: konvaImg.getX(),
            y: konvaImg.getY(),
            width: width,
            height: height,
            offset: {
                x: width/2,
                y: height/2
            },
            draggable: true,
        });
        group.addName(direction);
        var control = new Konva.Rect({
            x: konvaImg.getOffsetX(),
            y: konvaImg.getOffsetY(),
            width: width,
            height: height,
            offset: {
                x: width/2,
                y: height/2
            }
        });
        group.add(control);
        console.log(group.getX());
        group.on('dragmove', function() {
            console.log(group.getX());
            console.log(konvaImg.getX());
            konvaImg.x(group.x());
            konvaImg.y(group.y());
            imgLayer.draw();
            // update image position into input hidden
            stepWrapper.find('input[name="image-position-x"]').val(konvaImg.x());
            stepWrapper.find('input[name="image-position-y"]').val(konvaImg.y());
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
            group.opacity(1);
            layer.draw();
            event.stopPropagation();
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
                fill: '#ddd',
                draggable: true,
                dragBoundFunc: function(pos) {
                    return {
                        x: this.getAbsolutePosition().x,
                        y: this.getAbsolutePosition().y
                    }
                }
            });
            anchor.on('dragmove', function() {
                resize(this, konvaImg, stepWrapper);
                layer.draw();
            });
            // anchor.on('mousedown touchstart', function(event) {
            //     group.opacity(1);
            //     group.setDraggable(false);
            //     this.moveToTop();
            //     layer.draw();
            //     event.stopPropagation();
            // });
            // anchor.on('dragend', function() {
            //     group.setDraggable(true);
            //     layer.draw();
            // });
            anchor.on('mouseover', function() { document.body.style.cursor = option.cursor; });
            anchor.on('mouseout', function() { document.body.style.cursor = 'default'; });
            group.add(anchor);
        });
        layer.add(group);
        layer.draw();
    }
    function addControlForText(konvaText, stepWrapper, stepID, direction) {
        var stage = konvaText.getStage();
        var controlLayer = stage.find('Layer')[stage.find('Layer').length-1];
        var control = new Konva.Rect({
            draggable: true,
            id: "step"+stepID+'_text_control',
            name: "step"+stepID,
            width: konvaText.width() + 10,
            height: konvaText.height() + 10,
            x: konvaText.x()-5,
            y: konvaText.y()-5,
            stroke: 'black',
        });
        control.addName(direction);

        control.on('mouseover', function() {
            document.body.style.cursor = 'move';
            control.stroke('gray');
            control.getLayer().draw();
        });
        control.on('mouseout', function() {
            document.body.style.cursor = 'default';
            control.stroke('');
            control.getLayer().draw();
        });
        control.on('dragmove', function() {
            var text = stage.find('#step'+stepID+'_text')[0]
            text.x(this.x()+5);
            text.y(this.y()+5);
            // update personalize position into input hidden
            stepWrapper.find('input[name="text-position-x"]').val(text.x());
            stepWrapper.find('input[name="text-position-y"]').val(text.y());
            konvaText.getLayer().draw();
        });

        controlLayer.add(control);
        controlLayer.draw();
    }
    $('#customize-wrapper').on('change', 'input[type=radio]', function() {
        customizeUpdateDescription($(this));
        var stepWrapper = $(this).closest('.customize-step-wrapper');
        var stepID = stepWrapper.attr('step-id');
        var direction = stepWrapper.attr('direction');
        var personalize = stepWrapper.attr('personalize');

        if(personalize != '') {
            stepWrapper.find('.personalize-element').addClass('hidden');
            $('.'+$(this).attr('target-element')).removeClass('hidden');
        }
        else {
            var size = $(this).attr('size-triggle');
            if($(this).attr('name') == 'customize_type'){
                loadCustomize();
            }
            else if(typeof size !== typeof undefined && size !== '') {
                loadCustomizeImage.size = (size == 'bigger') ? 1 : ((size == 'smaller') ? 0.8 : 0.9);
                // update exits image, skip personalize image
                $.each(stage.find('Image'), function(index, image) {
                    if(!image.hasName('personalize')) {
                        loadCustomizeImage(image, stage.width(), stage.height());
                    }
                });
            }
            else {
                var fixed = stepWrapper.attr('fixed');
                var inputName = (fixed == '1') ? 'step_'+stepID : 'customize_'+loadCustomize.customizeID+"_step_"+stepID;

                // extral option was not checked, hide component element
                if($(this).attr('name') == inputName) stepWrapper.find('.component-element').addClass('hidden');

                // blank was selected
                if($(this).val() == 0) {
                    stage.find('#step'+stepID+'_'+direction)[0].hide();
                    stage.find('#step'+stepID+'_'+direction)[0].getLayer().draw();
                    stepWrapper.find('.customize-description span').html();
                }
                else {
                    stage.find('#step'+stepID+'_'+direction)[0].show();
                    stage.find('#step'+stepID+'_'+direction)[0].getLayer().draw();

                    var component = $(this);
                    // check radio button has extral option
                    if($(this).attr('extral-option') == 1) {
                        // find the first extral component and marks it as checked
                        var wrapperName = $(this).attr('target-element');
                        var wrapper = $('.'+wrapperName);
                        wrapper.removeClass('hidden');
                        component = wrapper.find('input[type=radio]').first();
                        component.prop('checked', true);
                        customizeUpdateDescription(component);
                    }

                    var imageAttr = {};
                    imageAttr['front'] = 'f-image';
                    imageAttr['back'] = 'b-image';
                    $.each(imageAttr, function(direction, attribute){
                        var index = 'step'+stepID+'_'+direction;
                        var konvaImage = stage.find('#'+index)[0];
                        var img = new Image();
                        img.onload = function() {
                            loadCustomizeImage(konvaImage, stage.width(), stage.height(), img);
                        };
                        img.src = component.attr(attribute);
                    });
                }
            }
        }
    });
    $('.customize-step-wrapper').on('keyup', 'input[name="personalize-text"]', function() {
        var stepWrapper = $(this).closest('.customize-step-wrapper');
        var direction = $(this).attr('target-direction');
        var stepID = stepWrapper.attr('step-id');
        var konvaText = stage.find('#step'+stepID+'_text')[0];
        var layer = konvaText.getLayer();
        var value = $(this).val();
        var controlLayer = stage.find('Layer')[stage.find('Layer').length-1];

        konvaText.text(value);
        konvaText.fontFamily('monospace');
        konvaText.fill('gold');
        layer.find('.personalize-area')[0].moveToTop();
        layer.draw();

        // personalize text control
        if(stage.find('#step'+stepID+'_text_control').length == 0) {
            addControlForText(konvaText, stepWrapper, stepID, direction);
        }
        else {
            var control = stage.find('#step'+stepID+'_text_control')[0];
            control.width(konvaText.width() + 10);
            control.height(konvaText.height() + 10);
        }

        // if value = empty destroy text control
        if(value == '') stage.find('#step'+stepID+'_text_control')[0].destroy();
        controlLayer.draw();
    });
    $('input:file[name="personalize-image"]').change(function() {
        var stepWrapper = $(this).closest('.customize-step-wrapper')
        var token = $('meta[name="csrf-token"]').attr('content');

        // upload image
        var formData = new FormData();
        var name = stepWrapper.find('input[name="personalize-image"]')[0].files[0].name;
        formData.append('file', stepWrapper.find('input[name="personalize-image"]')[0].files[0]);
        formData.append('_token', token);
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
                showNotification('warning', errorMsg, 'bottom', 'center');
            },
            success: function(response){
                // add image attribute into input
                var input = stepWrapper.find('input[name="personalize-image"]');
                input.attr('data-id', response.id);
                input.attr('data-image', response.image);
                var stepID = stepWrapper.attr('step-id');
                var direction = stepWrapper.attr('target-direction');
                var konvaImage = stage.find('#step'+stepID+'_image')[0];
                var layer = konvaImage.getLayer();

                var img = new Image();
                img.onload = function() {
                    var ration = img.width / 50;
                    var height = img.height / ration;

                    konvaImage.image(img);
                    konvaImage.width(50);
                    konvaImage.height(height);
                    konvaImage.offsetX(25);
                    konvaImage.offsetY(height/2);
                    layer.find('.personalize-area')[0].moveToTop();
                    layer.draw();
                    addAnchor(konvaImage, direction, stepWrapper);
                    // update image info into input hidden
                    stepWrapper.find('input[name="image-id"]').val(response.id);
                    stepWrapper.find('input[name="image-src"]').val(response.image);
                    stepWrapper.find('input[name="image-width"]').val(50);
                    stepWrapper.find('input[name="image-height"]').val(height);
                    stepWrapper.find('input[name="image-position-x"]').val(konvaImage.x());
                    stepWrapper.find('input[name="image-position-y"]').val(konvaImage.y());
                    stepWrapper.find('input[name="image-rotation"]').val(konvaImage.rotation());
                };
                img.src = '/images/'+response.image;
            }
        })
    });
    loadCustomize();
    $('#saveCustomizeBtn').click(function() {
        swal({
            title: "Watch Name",
          text: "Name for your creative",
          type: "input",
          showCancelButton: true,
          animation: "slide-from-top",
          inputPlaceholder: "Rosielee",
          inputValue: "Untitled Watch",
        },
        function(inputValue){
          if (inputValue === false) return false;

          if (inputValue === "") {
            swal.showInputError("You need to write something!");
            return false
          }
          saveCustomizeWatch(inputValue);
        });

        function saveCustomizeWatch(name) {
            var token = $('meta[name="csrf-token"]').attr('content');
            var customizeID = $('input[name="customize_type"]').val();
            var customizeDetail = {};
            customizeDetail['stage_width'] = $('input[name="stage_width"]').val();
            customizeDetail['stage_height'] = $('input[name="stage_height"]').val();

            $('.customize-step-wrapper').each(function() {
                var stepWrapper = $(this);
                var unavailable = $(this).attr('unavailable');
                // convernt php array string to js array
                unavailable = unavailable.replace(/"/g, '');
                unavailable = unavailable.replace('[', '');
                unavailable = unavailable.replace(']', '');
                array = unavailable.split(",");
                if($.inArray(customizeID, array) < 0) {
                    var stepID      = $(this).attr('step-id');
                    var personalize = $(this).attr('personalize');
                    var primaryStep = $(this).attr('primary');
                    var fixed       = $(this).attr('fixed');
                    var inputName   = (fixed=='1') ? 'step_'+stepID : 'customize_'+customizeID+'_step_'+stepID ;

                    if(personalize != '') {
                        customizeDetail[stepID] = {image: 0, text: 0};
                        if(stepWrapper.has('input[type=file]').length) {
                            customizeDetail[stepID]['image'] = {
                                'id': stepWrapper.find('input[name="image-id"]').val(),
                                'src': stepWrapper.find('input[name="image-src"]').val(),
                                'x': stepWrapper.find('input[name="image-position-x"]').val(),
                                'y': stepWrapper.find('input[name="image-position-y"]').val(),
                                'width': stepWrapper.find('input[name="image-width"]').val(),
                                'height': stepWrapper.find('input[name="image-height"]').val(),
                                'rotation': stepWrapper.find('input[name="image-rotation"]').val(),
                            };
                        }

                        // add personalize element (text) into layer
                        if(stepWrapper.has('input[type=text]').length) {
                            customizeDetail[stepID]['text'] = {
                                'value': stepWrapper.find('input[name="personalize-text"]').val(),
                                'x': stepWrapper.find('input[name="text-position-x"]').val(),
                                'y': stepWrapper.find('input[name="text-position-y"]').val(),
                            };
                        }
                    }
                    else if(primaryStep == '0'){
                        var component = stepWrapper.find('input[name="'+inputName+'"]:checked');
                        var wrapperClass = component.attr('target-element');
                        var wrapper = stepWrapper.find('.'+wrapperClass);
                        customizeDetail[stepID] = {
                            main: component.val(),
                            extral: (component.attr('extral-option') == '1') ? wrapper.find('input[name="'+inputName+'_extral"]:checked').val() : 0,
                        };
                    }
                }
            });

            var formData = new FormData();
            formData.append('_token', token);
            formData.append('type', customizeID);
            formData.append('name', name);
            formData.append('components', JSON.stringify(customizeDetail));
            $.ajax({
                url: '/admin/customize',
                data: formData,
                type: 'POST',
                contentType: false,
                processData: false,
                dataType: 'json',
                error: function(a, b, c){
                    console.log(a.responseText);
                    // var response = $.parseJSON(a.responseText);
                    // var errorMsg = name + ' ' + response.message;
                    // console.log(errorMsg);
                    // showNotification('warning', errorMsg, 'bottom', 'center');
                },
                success: function(response){
                    console.log('12');
                }
            });
        }
    });
});
