/**
 *------------------------------------------------------------------------------
 * @package       Plazart Framework for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2012-2014 TemPlaza.com. All Rights Reserved.
 * @license       http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * @authors       TemPlaza
 * @Link:         http://templaza.com
 *------------------------------------------------------------------------------
 */
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
jQuery(function($){

    var reArrangePopOvers = function(){

        $('#layout-options .row-fluid').each(function(){

            $(this).find('>.column').find('.columntools > .rowcolumnspop')
                .attr('data-placement','bottom').data('placement', 'bottom');

            $(this).find('>.column').first().find('.columntools > .rowcolumnspop')
                .attr('data-placement','right').data('placement', 'right');

            $(this).find('>.column').last().find('.columntools > .rowcolumnspop')
                .attr('data-placement', 'left').data('placement', 'left');
        });
    }

    reArrangePopOvers();


    var columnInputs = function(element, name){

        $(element).find('>.widthinput-xs').attr('name', name+'[col-xs]');
        $(element).find('>.widthinput-sm').attr('name', name+'[col-sm]');
        $(element).find('>.widthinput-md').attr('name', name+'[col-md]');
        $(element).find('>.widthinput-lg').attr('name', name+'[col-lg]');
        $(element).find('>.offsetinput-xs').attr('name', name+'[col-xs-offset]');
        $(element).find('>.offsetinput-sm').attr('name', name+'[col-sm-offset]');
        $(element).find('>.offsetinput-md').attr('name', name+'[col-md-offset]');
        $(element).find('>.offsetinput-lg').attr('name', name+'[col-lg-offset]');
        $(element).find('>.typeinput').attr('name', name+'[type]');
        $(element).find('>.positioninput').attr('name', name+'[position]');
        $(element).find('>.styleinput').attr('name', name+'[style]');
        $(element).find('>.customclassinput').attr('name', name+'[customclass]');
        $(element).find('>.responsiveclassinput').attr('name', name+'[responsiveclass]');
        $(element).find('>.animationType').attr('name', name+'[animationType]');
        $(element).find('>.animationSpeed').attr('name', name+'[animationSpeed]');
        $(element).find('>.animationDelay').attr('name', name+'[animationDelay]');
        $(element).find('>.animationOffset').attr('name', name+'[animationOffset]');
        $(element).find('>.animationEasing').attr('name', name+'[animationEasing]');

    }
    var rowInputs = function(element, name){

        $(element).find('>div>.rowpropperties .rownameinput').attr('name', name+'[name]');
        $(element).find('>div>.rowpropperties .rowcustomclassinput').attr('name', name+'[class]');
        $(element).find('>div>.rowpropperties .rowresponsiveinput').attr('name', name+'[responsive]');

        $(element).find('>div>.rowpropperties .rowbackgroundcolorinput').attr('name', name+'[backgroundcolor]');
        $(element).find('>div>.rowpropperties .rowtextcolorinput').attr('name', name+'[textcolor]');
        $(element).find('>div>.rowpropperties .rowlinkcolorinput').attr('name', name+'[linkcolor]');
        $(element).find('>div>.rowpropperties .rowlinkhovercolorinput').attr('name', name+'[linkhovercolor]');
        $(element).find('>div>.rowpropperties .rowmargininput').attr('name', name+'[margin]');
        $(element).find('>div>.rowpropperties .rowpaddinginput').attr('name', name+'[padding]');
        $(element).find('>div>.row-container .containertype').attr('name', name+'[containertype]');

    }

    var columnRowInputs = function(element,name){
        $(element).find('>.row-fluid').each(function(rowl4){
            var r4name = name+'[children]['+rowl4+']';
            rowInputs(this, r4name);
            $(this).find('> div >.row-fluid >.column').each(function(columnl4){
                var c4name = r4name+'[children]['+columnl4+']';
                columnInputs(this, c4name);
                if($(this).find('>.row-fluid').length){
                    columnRowInputs(this,c4name);
                }
            });
            columnRowInputs($(this).next(),r4name);
        });
    }

    $.tzTemplateSubmit  = function(){
        $('#content .generator >.row-fluid, #element-box .generator >.row-fluid').each(function(rowl0){
            var r0name = fieldName+'['+rowl0+']';
            rowInputs(this, r0name);
            $(this).find('> div >.row-fluid >.column').each(function(columnl0){
                var c0name = r0name+'[children]['+columnl0+']';
                columnInputs(this, c0name);
                // main rows
                if($(this).find('>.row-fluid').length){
                    columnRowInputs(this,c0name);
                }else{
                    columnRowInputs($(this).next(),c0name);
                }

            });

        });
        $('.toolbox-saveConfig').trigger('click');
    };

    ;(function(textOnly) {

        jQuery.fn.textOnly = function( selector ) {


            return $.trim($(selector)
                .clone()
                .children()
                .remove()
                .end()
                .text());

        }

    })(jQuery.fn.textOnly);




    /**
     * jQuery alterClass plugin
     *
     * Remove element classes with wildcard matching. Optionally add classes:
     *   $( '#foo' ).alterClass( 'foo-* bar-*', 'foobar' )
     *
     * Copyright (c) 2011 Pete Boere (the-echoplex.net)
     * Free under terms of the MIT license: http://www.opensource.org/licenses/mit-license.php
     *
     */
    ;(function ( $ ) {

        $.fn.alterClass = function ( removals, additions ) {

            var self = this;

            if ( removals.indexOf( '*' ) === -1 ) {
                // Use native jQuery methods if there is no wildcard matching
                self.removeClass( removals );
                return !additions ? self : self.addClass( additions );
            }

            var patt = new RegExp( '\\s' +
                removals.
                    replace( /\*/g, '[A-Za-z0-9-_]+' ).
                    split( ' ' ).
                    join( '\\s|\\s' ) +
                '\\s', 'g' );

            self.each( function ( i, it ) {
                var cn = ' ' + it.className + ' ';
                while ( patt.test( cn ) ) {
                    cn = cn.replace( patt, ' ' );
                }
                it.className = $.trim( cn );
            });

            return !additions ? self : self.addClass( additions );
        };

    })( jQuery );



    /**
     * Get class using regular expression
     */
    ;(function(getClass) {

        jQuery.fn.getClass = function( classname ) {

            if ( classname && typeof(classname) === "object" ) {
                var classes = $(this).attr('class').split( /\s+/ );
                var re = new RegExp(classname);
                var m = re.exec(classes);
                if(m!=null) {
                    return m[m.length-1];
                }
            }

            if( typeof(classname)=== "boolean" ){

                return $(this).attr('class').split( /\s+/ );
            }


            return '';
        }
    })(jQuery.fn.getClass);




    var columnMaping = {
        2 : [2, 3, 4, 5, 6, 7, 8, 9, 10],   //  possible spans
        3 : [2, 3, 4, 5, 6, 7, 8],
        4 : [2, 3, 4, 5, 6],
        5 : [2, 3],
        6 : [2]
    };


    $('#content').delegate('.columntools > a:not(.accordion-toggle), .row-tools > a:not(.accordion-toggle)', 'click', function(){

        return false;
    });




    /**
     * Open Bootstrap popover
     *
     */



    var popover = function(){

        $('a[rel="popover"]').popover({
            html:true,
            content:function(){


                var id = $(this).attr('href');


                var currentSpan = $(this).closest('.column').getClass(/\bspan([0-9]{1,2})\b/);

                //$(id).find('#spanwidth option').removeAttr('selected');
                //$(id).find('#spanwidth option[value="'+currentSpan+'"]').attr('selected', true);
                //$(id).find('#spanwidth select').val(currentSpan);

                setTimeout(function(value, $this){
                    $this.next().find('#spanwidth select').val(value);
                }, 300, currentSpan, $(this));


                $("#content,#element-box").delegate(".popover select.possiblewidths",'change', function(event){

                    event.stopImmediatePropagation();
                    var newSpan = $(this).val();
                    switch ($("button[class*='tz-admin-dv'].active").attr('data-device')) {
                        case 'xs':
                            $(this).parents('.popover').eq(0).parent().parent().find('>.widthinput-xs').val(newSpan);
                            break;
                        case 'sm':
                            $(this).parents('.popover').eq(0).parent().parent().find('>.widthinput-sm').val(newSpan);
                            break;
                        case 'md':
                        default :
                            $(this).parents('.popover').eq(0).parent().parent().find('>.widthinput-md').val(newSpan);
                            break;
                        case 'lg':
                            $(this).parents('.popover').eq(0).parent().parent().find('>.widthinput-lg').val(newSpan);
                            break;
                    }

                    $(this).parents('.popover').eq(0).parent().parent().removeClass().addClass('column span'+newSpan);
                });




                var currentOffset = $(this).closest('.column').getClass(/\boffset([0-9]{1,2})\b/);


                //$(id).find('#spanoffset option').removeAttr('selected');
                //$(id).find('#spanoffset option[value="'+currentOffset+'"]').attr('selected', true);

                setTimeout(function(value, $this){
                    $this.next().find('#spanoffset select').val(value);
                }, 300, currentOffset, $(this));

                $("#content,#element-box").delegate(".popover select.possibleoffsets",'change', function(event){

                    event.stopImmediatePropagation();
                    var newOffset = $(this).val();
                    //$(this).parents('.popover').parent().parent().removeClass(/\boffset\S+/g).addClass('offset'+newOffset);

                    var newClass = $(this).parents('.popover').parent().parent().attr('class').replace(/\boffset\S+/g, '');
                    $(this).parents('.popover').parent().parent().attr('class', newClass).addClass('offset'+newOffset);

                    switch ($("button[class*='tz-admin-dv'].active").attr('data-device')) {
                        case 'xs':
                            $(this).parents('.popover').parent().parent().find('>.offsetinput-xs').val(newOffset);
                            break;
                        case 'sm':
                            $(this).parents('.popover').parent().parent().find('>.offsetinput-sm').val(newOffset);
                            break;
                        case 'md':
                        default :
                            $(this).parents('.popover').parent().parent().find('>.offsetinput-md').val(newOffset);
                            break;
                        case 'lg':
                            $(this).parents('.popover').parent().parent().find('>.offsetinput-lg').val(newOffset);
                            break;
                    }

                    if( newOffset=='0' ){
                        $(this).parents('.popover').parent().parent().removeClass('offset0');
                        switch ($("button[class*='tz-admin-dv'].active").attr('data-device')) {
                            case 'xs':
                                $(this).parents('.popover').parent().parent().find('>.offsetinput-xs').val('');
                                break;
                            case 'sm':
                                $(this).parents('.popover').parent().parent().find('>.offsetinput-sm').val('');
                                break;
                            case 'md':
                            default :
                                $(this).parents('.popover').parent().parent().find('>.offsetinput-md').val('');
                                break;
                            case 'lg':
                                $(this).parents('.popover').parent().parent().find('>.offsetinput-lg').val('');
                                break;
                        }
                    }
                });




                var currentIncludetype = $(this).closest('.column').find('.typeinput').val();
                //$(id).find('#includetypes option').removeAttr('selected');
                //$(id).find('#includetypes option[value="'+currentIncludetype+'"]').attr('selected', true);



                setTimeout(function(value, $this){
                    $this.next().find('#includetypes select').val(value);
                }, 300, currentIncludetype, $(this));

                $("#content,#element-box").delegate(".popover select.includetypes",'change', function(event){

                    event.stopImmediatePropagation();
                    var newIncludetype = $(this).val();
                    $(this).parents('.popover').parent().parent().find('>.typeinput').val(newIncludetype);

                    $(this).parents('.popover').parent().parent().removeClass('type-component type-message');
                    $(this).parents('.popover').parent().parent().addClass('type-'+newIncludetype);

//                    if( newIncludetype==='modules' ){
//                        $(this).closest('.tab-pane').find('#positions').show();
//                        $(this).closest('.tab-pane').find('#positions option[value=""]').attr('selected', true);
//                        $(this).parents('.popover').parent().parent().find('>.position-name').text('(none)');
//                    }
//                    else{
                        $(this).closest('.tab-pane').find('#positions').hide();
                        $(this).parents('.popover').parent().parent().find('>.position-name').text(newIncludetype.toLowerCase());
//                    }
                });


                var currentPosition = $(this).closest('.column').find('.positioninput').val();

                setTimeout(function(value, $this){
                    $this.next().find('#positions select').val(value);
                }, 300, currentPosition, $(this));

                $("#content,#element-box").delegate(".popover select.positions",'change', function(event){

                    event.stopImmediatePropagation();
                    var newPosition = $(this).val();
                    if( newPosition=='' ) newPosition='(none)';
                    $(this).parents('.popover').parent().parent().find('>.positioninput').val(newPosition);
                    $(this).parents('.popover').parent().parent().find('>.position-name').text(newPosition);
                });



                var currentStyle = $(this).closest('.column').find('.styleinput').val();
                //$(id).find('#modchrome option').removeAttr('selected');
                //$(id).find('#modchrome option[value="'+currentStyle+'"]').attr('selected', true);

                setTimeout(function(value, $this){
                    $this.next().find('#modchrome select').val(value);
                }, 300, currentStyle, $(this));


                $("#content,#element-box").delegate(".popover select.modchrome",'change', function(event){

                    event.stopImmediatePropagation();
                    var newStyle = $(this).val();
                    $(this).parents('.popover').parent().parent().find('>.styleinput').val(newStyle);
                });

                var currentCustomClass = $(this).closest('.column').find('.customclassinput').val();

                setTimeout(function(value, $this){
                    $this.next().find('#inputcustomclass').val(value);
                }, 300, currentCustomClass, $(this));

                //$(this).parents('.column').find('>.columntools >.popover .customclass').val(currentCustomClass);
                $("#content,#element-box").delegate(".popover input.customclass",'blur', function(event){

                    event.stopImmediatePropagation();
                    var newCustomClass = $(this).val();
                    $(this).parents('.popover').parent().parent().find('>.customclassinput').val(newCustomClass);
                });

                $("#content,#element-box").delegate(".popover #columnsettings a",'click', function(event){

                    var id = $(this).attr('href');

                    if( id=='' || id=='#' ){  return;}

                    $(this).parents('ul').find('li').removeClass('active');
                    $(this).parent().addClass('active');
                    $(this).parents('ul').next().find('.active').removeClass('active');
                    $('.popover '+id).addClass('active');

                    $(this).parents('.dropdown-menu').parents('li.dropdown').addClass('active');

                });
                ///
                //var currentResponsive = $(this).parents('.column').getClass(true);

                var currentResponsive = $(this).closest('.column').find('.responsiveclassinput').val().split(/\s+/);

                $(id).find('#responsive input:checkbox').removeAttr('checked');


                $.each(currentResponsive, function(index, item) {
                    $(id).find('#responsive input[value="'+item+'"]').attr('checked', true);
                });


                $("#content,#element-box").delegate(".popover input:checkbox",'click', function(event){

                    event.stopImmediatePropagation();

                    var newResponsive = $(this).val();

                    var currentResponsive = $(this).parents('.popover').parent().parent().find('>.responsiveclassinput').val();

                    if( typeof(currentResponsive)==='undefined' ){
                        currentResponsive = '';
                    }

                    if( $(this).is(':checked') ){
                        var value = currentResponsive+' '+newResponsive;
                        value = $.unique( value.split(/\s+/) );
                        value = value.join(' ');
                    } else  {
                        var value = currentResponsive.replace(newResponsive, '');
                    }

                    $(this).parents('.popover').parent().parent().find('>.responsiveclassinput').val($.trim(value));

                });

                var currentAnimation = $(this).closest('.column').find('.animationType').val();

                setTimeout(function(value, $this){
                    $this.next().find('select.animationType').val(value);
                }, 300, currentAnimation, $(this));

                $("#content,#element-box").delegate(".popover select.animationType",'change', function(event){
                    event.stopImmediatePropagation();
                    var newAnimation = $(this).val();
                    $(this).parents('.popover').parent().parent().find('>.animationType').val(newAnimation);
                });

                var currentAnimationSpeed = $(this).closest('.column').find('.animationSpeed').val();

                setTimeout(function(value, $this){
                    $this.next().find('input.animationSpeed').val(value);
                }, 300, currentAnimationSpeed, $(this));

                $("#content,#element-box").delegate(".popover input.animationSpeed",'blur', function(event){
                    event.stopImmediatePropagation();
                    var newAnimationSpeed = $(this).val();
                    $(this).parents('.popover').parent().parent().find('>.animationSpeed').val(newAnimationSpeed);
                });

                var currentAnimationDelay = $(this).closest('.column').find('.animationDelay').val();

                setTimeout(function(value, $this){
                    $this.next().find('input.animationDelay').val(value);
                }, 300, currentAnimationDelay, $(this));

                $("#content,#element-box").delegate(".popover input.animationDelay",'blur', function(event){
                    event.stopImmediatePropagation();
                    var newAnimationDelay = $(this).val();
                    $(this).parents('.popover').parent().parent().find('>.animationDelay').val(newAnimationDelay);
                });

                var currentAnimationOffset = $(this).closest('.column').find('.animationOffset').val();

                setTimeout(function(value, $this){
                    $this.next().find('input.animationOffset').val(value);
                }, 300, currentAnimationOffset, $(this));

                $("#content,#element-box").delegate(".popover input.animationOffset",'blur', function(event){
                    event.stopImmediatePropagation();
                    var newAnimationOffset = $(this).val();
                    $(this).parents('.popover').parent().parent().find('>.animationOffset').val(newAnimationOffset);
                });

                var currentAnimationEasing = $(this).closest('.column').find('.animationEasing').val();

                setTimeout(function(value, $this){
                    $this.next().find('select.animationEasing').val(value);
                }, 300, currentAnimationEasing, $(this));

                $("#content,#element-box").delegate(".popover select.animationEasing",'change', function(event){
                    event.stopImmediatePropagation();
                    var newAnimationEasing = $(this).val();
                    $(this).parents('.popover').parent().parent().find('>.animationEasing').val(newAnimationEasing);
                });

                $(this).closest('.columntools').addClass('open');

                return $(id).html();
            },

            template: '<div class="popover"><div class="arrow"></div><div class="popover-inner"><div class="popover-content"><p></p></div> <p>   <a class="btn btn-primary sp-popover-apply" onclick="jQuery(this).closest(\'.popover\').prev().popover(\'hide\');  jQuery(this).closest(\'.popover\').prev().show();"><i class="icon-ok"></i> Apply</a> <a class="btn btn-danger sp-popover-close" onclick="jQuery(this).closest(\'.popover\').prev().popover(\'hide\'); jQuery(this).closest(\'.popover\').prev().show();"><i class="icon-remove"></i> Close</a>   </p> </div></div>'
        }).click(function(){  $(this).show();   return false;});

        $("#layout-options").delegate(".popover .sp-popover-apply, .popover .sp-popover-close",'click', function(event){
            $(this).closest('.columntools').removeClass('open');
            $('#columnsettings').find('li').first().addClass('active');

        });

        $('a[rel="rowpopover"]').popover({
            html:true,
            placement:'left',
            //container:'body',
            content:function(){

                var id = $(this).attr('href');


                //var currentPosition = $(this).parent('.column').find('>.positioninput').val();
                var currentName = $(this).parent().prev().find('>span.rowdocs>.rownameinput');

                setTimeout(function($this, value){
                    $this.parent().find('>.popover .rowname').val(value);
                }, 300, $(this), currentName.val() );

                $("#content,#element-box").delegate(".popover input.rowname",'blur', function(event){

                    event.stopImmediatePropagation();
                    var newName = $(this).val();
                    $(this).parents('.popover').parent().prev().find('>span.rowdocs>.rownameinput').val(newName);
                    $(this).parents('.popover').parent().prev().find('>.rowname').text(newName);
                });

                // background color
                var currentBackgroundColor = $(this).parent().prev().find('>span.rowdocs>.rowbackgroundcolorinput');

                setTimeout(function($this, value){
                    $this.parent().find('>.popover .rowbackgroundcolor').val(value);

                    $this.parent().find('>.popover .rowbackgroundcolor').spectrum({
                        flat:false,
                        showInput:true,
                        preferredFormat: "rgb",
                        showButtons:true,
                        showAlpha:true,
                        showPalette:true,
                        clickoutFiresChange:true,
                        cancelText:"cancel",
                        chooseText:"Choose",
                        palette : [ ['rgba(255, 255, 255, 0)'] ],
                        change: function(color) {
                            var currentcolor = color.toRgbString();
                            $(this).parents('.popover').parent().prev().find('>span.rowdocs>.rowbackgroundcolorinput').val(currentcolor);
                        }
                    });

                    // $this.parent().find('>.popover .rowtextcolor').show();

                }, 300, $(this), currentBackgroundColor.val());

                // text color
                var currentTextColor = $(this).parent().prev().find('>span.rowdocs>.rowtextcolorinput');

                setTimeout(function($this, value){
                    $this.parent().find('>.popover .rowtextcolor').val(value);

                    $this.parent().find('>.popover .rowtextcolor').spectrum({
                        flat:false,
                        showInput:true,
                        preferredFormat: "rgb",
                        showButtons:true,
                        showAlpha:true,
                        showPalette:true,
                        clickoutFiresChange:true,
                        cancelText:"cancel",
                        chooseText:"Choose",
                        palette : [ ['rgba(255, 255, 255, 0)'] ],
                        change: function(color) {
                            var currentcolor = color.toRgbString();
                            $(this).parents('.popover').parent().prev().find('>span.rowdocs>.rowtextcolorinput').val(currentcolor);
                        }
                    });

                }, 300, $(this), currentTextColor.val());


                // link color
                var currentLinkColor = $(this).parent().prev().find('>span.rowdocs>.rowlinkcolorinput');

                setTimeout(function($this, value){
                    $this.parent().find('>.popover .rowlinkcolor').val(value);

                    $this.parent().find('>.popover .rowlinkcolor').spectrum({
                        flat:false,
                        showInput:true,
                        preferredFormat: "rgb",
                        showButtons:true,
                        showAlpha:true,
                        showPalette:true,
                        clickoutFiresChange:true,
                        cancelText:"cancel",
                        chooseText:"Choose",
                        palette : [ ['rgba(255, 255, 255, 0)'] ],
                        change: function(color) {
                            var currentcolor = color.toRgbString();
                            $(this).parents('.popover').parent().prev().find('>span.rowdocs>.rowlinkcolorinput').val(currentcolor);
                        }
                    });

                }, 300, $(this), currentLinkColor.val());


                // link hover color
                var currentLinkHoverColor = $(this).parent().prev().find('>span.rowdocs>.rowlinkhovercolorinput');

                setTimeout(function($this, value){
                    $this.parent().find('>.popover .rowlinkhovercolor').val(value);

                    $this.parent().find('>.popover .rowlinkhovercolor').spectrum({
                        flat:false,
                        showInput:true,
                        preferredFormat: "rgb",
                        showButtons:true,
                        showAlpha:true,
                        showPalette:true,
                        clickoutFiresChange:true,
                        cancelText:"cancel",
                        chooseText:"Choose",
                        palette : [ ['rgba(255, 255, 255, 0)'] ],
                        change: function(color) {
                            var currentcolor = color.toRgbString();
                            $(this).parents('.popover').parent().prev().find('>span.rowdocs>.rowlinkhovercolorinput').val(currentcolor);
                        }
                    });

                }, 300, $(this), currentLinkHoverColor.val());


                // css margin
                var currentMargin = $(this).parent().prev().find('>span.rowdocs>.rowmargininput');

                setTimeout(function($this, value){
                    $this.parent().find('>.popover .rowmargin').val(value);
                }, 300, $(this), currentMargin.val());

                $("#content,#element-box").delegate(".popover input.rowmargin",'blur', function(event){

                    event.stopImmediatePropagation();
                    var newName = $(this).val();
                    $(this).parents('.popover').parent().prev().find('>span.rowdocs>.rowmargininput').val(newName);
                });

                // css padding
                var currentPadding = $(this).parent().prev().find('>span.rowdocs>.rowpaddinginput');

                setTimeout(function($this, value){
                    $this.parent().find('>.popover .rowpadding').val(value);
                }, 300, $(this), currentPadding.val());

                $("#content,#element-box").delegate(".popover input.rowpadding",'blur', function(event){

                    event.stopImmediatePropagation();
                    var newName = $(this).val();
                    $(this).parents('.popover').parent().prev().find('>span.rowdocs>.rowpaddinginput').val(newName);
                });

                // css class
                var currentCss = $(this).parent().prev().find('>span.rowdocs>.rowcustomclassinput');

                setTimeout(function($this, value){
                    $this.parent().find('>.popover .rowcustomclass').val(value);
                }, 300, $(this), currentCss.val());

                $("#content,#element-box").delegate(".popover input.rowcustomclass",'blur', function(event){

                    event.stopImmediatePropagation();
                    var newName = $(this).val();
                    $(this).parents('.popover').parent().prev().find('>span.rowdocs>.rowcustomclassinput').val(newName);

                });

                var currentResponsive = $(this).parent().prev().find('>span.rowdocs>.rowresponsiveinput').val().split(/\s+/);

                $(id).find('#rowresponsiveinputs input:checkbox').removeAttr('checked');
                $.each(currentResponsive, function(index, item) {
                    $(id).find('#rowresponsiveinputs input[value="'+item+'"]').attr('checked', true);
                });

                $("#content,#element-box").delegate(".popover input:checkbox",'click', function(event){

                    event.stopImmediatePropagation();

                    var newResponsive = $(this).val();
                    var currentResponsive = $(this).parents('.popover').parent().prev().find('span.rowdocs>.rowresponsiveinput').val();

                    if( typeof(currentResponsive)==='undefined' ){
                        currentResponsive = '';
                    }

                    if( $(this).is(':checked') ){
                        var value = currentResponsive+' '+newResponsive;
                        value = $.unique( value.split(/\s+/) );
                        value = value.join(' ');

                    } else  {
                        var value = currentResponsive.replace(newResponsive, '');
                    }

                    $(this).parents('.popover').parent().prev().find('span.rowdocs>input.rowresponsiveinput').val($.trim(value));

                });

                return $(id).html();
            },

            template: '<div class="popover"><div class="arrow"></div><div class="popover-inner"><div class="popover-content"><p></p></div><a class="btn btn-primary" onclick="jQuery(this).closest(\'.popover\').prev().popover(\'hide\'); jQuery(this).closest(\'.popover\').prev().show(); "><i class="icon-ok"></i> Apply</a></div></div>'
        }).click(function(){  $(this).show();   return false;});

    }


    popover();



    /**
     * Row delete
     *
     */
    $("#content,#element-box").delegate("a.rowdelete",'click', function(){


        if( confirm('Are you sure to delete this row?') ){

            $(this).parent().parent().parent().slideUp('slow', function(){
                $(this).remove();
            });
        }
        return false;
    });

    /**
     * Column delete
     *
     */
    $("#content,#element-box").delegate("a.columndelete",'click', function(){

        if( confirm('Are you sure to delete this column?') ){
            $parent2 = $(this).parent().parent().parent();
            $(this).parent().parent().fadeOut('fast').remove();
            var totalSpan =  $parent2.find('>.column').length;
            //resetColumns($parent2);
        }
        return false;
    });

    /**
     * row Column Sortable
     *
     */
    var rowColumnSortable  = function(){

        $('div.row-fluid').sortable({
            //placeholder: "highlight",
            forcePlaceholderSize: true,
            axis: "x,y",
            items: ">div.column",
            tolerance: "pointer",
            handle: ".columnmove",
            containment: "parent",
            placeholder: 'tz-state-highlight',
            start: function(event,ui){
                ui.placeholder.width(ui.item.width());
            },
            'update' : function(event, ui){
                setTimeout(function(){

                    $('a[rel="popover"]').popover('destroy');
                    $('a[rel="popover"]').show();
                    reArrangePopOvers();
                    popover();
                },300);

            }
        });

        $('.generator').sortable({
            axis: "y",
            forcePlaceholderSize: true,
            containment: "parent",
            handle: ".rowmove",
            placeholder: 'tz-state-highlight-row',
            items:'>div.row-fluid'
        });

        $( ".generator" ).sortable( "refreshPositions" );

        $('.generator > .row-fluid .row-fluid .column').sortable({
            axis: "y",
            forcePlaceholderSize: true,
            containment: "parent",
            handle: ".row-move-in-column",
            placeholder: 'tz-state-highlight',
            items:'>div'
        });


    }

    rowColumnSortable();

    /**
     * Add new row
     *
     */
    $("#content,#element-box").delegate("a.add-row",'click', function(){
        var $item = $(this);
        $.get(pluginPath+'/new-row.php?time='+$.now(),function($row){
            $($row).hide().insertAfter( $item.closest('.row-fluid') ).slideDown('slow');
            $('a[rel="popover"]').popover('destroy');
            $('a[rel="popover"]').show();
            reArrangePopOvers();
            popover();
        });
        return false;
    });

    $("#content,#element-box").delegate("a.columnmove, a.icon-asterisk",'click', function(){

        return false;
    });




    /**
     * Reset Columns on Update or delete
     *
     * @param $selector
     */
    var resetColumns = function($selector){

        var totalSpan =  $selector.find('>.column').length;
        var tzdevice = $("button[class*='tz-admin-dv'].active").attr('data-device');
        var spanClass;
        if( totalSpan==5 ) {
            spanClass = 12/4;
            //$selector.find('>.column').alterClass('span* offset*').addClass('span3').find('>.widthinput').val('3');
            $selector.find('>.column').alterClass('span*').addClass('span3').find('>.widthinput-'+tzdevice).val('3');
            $selector.find('>.column').not(':first-child, :last-child').alterClass('span*').addClass('column span2').find('>.widthinput-'+tzdevice).val('2');
        } else {
            spanClass = 12/totalSpan;
            //$selector.find('>.column').alterClass('span* offset*').addClass('span'+spanClass).find('>.widthinput').val(spanClass);
            $selector.find('>.column').alterClass('span*').addClass('span'+spanClass).find('>.widthinput-'+tzdevice).val(spanClass);
        }



        // data-original-title
        //toolTip('[data-original-title]');
        $('a[rel="popover"]').popover('destroy');
        $('a[rel="popover"]').show();
        reArrangePopOvers();
        popover();

        // alert('column reset');
        //reArrangePopOvers();
    }


    /**
     * Add new column
     *
     * @param $selector
     */
    var addColumn = function($selector){
        $.get(pluginPath+'/new-column.php?time='+ $.now(), function($column){
            $($column).hide().appendTo($selector).fadeIn(1000);
            //resetColumns($selector);
            rowColumnSortable();
            $('a[rel="popover"]').popover('destroy');
            $('a[rel="popover"]').show();
            reArrangePopOvers();
            popover();
        });
    }

    $("#content,#element-box").delegate("a.add-column",'click', function(){

        var totalSpan =  $(this).parent().next().next().find('>.column').length;
//        if( totalSpan >= 6 ){
//            alert('Maximum 6 module positions is possible in a row');
//            return false;
//        }

        addColumn( $(this).parent().next().next() );
        $('a[rel="popover"]').popover('destroy');
        $('a[rel="popover"]').show();
        reArrangePopOvers();
        popover();
        return false;
    });




    /**
     * Add Row in column
     *
     */
    var addRowInColumn = function(){
        $("#content,#element-box").delegate("a.add-rowin-column",'click', function(){
            var $this = $(this);
            $.get(pluginPath+'/new-row-in-column.php?time='+$.now(), function($row){
                $($row).hide().appendTo( $this.parent().parent() ).slideDown('slow');
                $('a[rel="popover"]').popover('destroy');
                $('a[rel="popover"]').show();
                reArrangePopOvers();
                popover();
            });
            return false;
        });
    }

    addRowInColumn();

    var $getnextdevice = new Array();
    $getnextdevice['lg'] =  'md';
    $getnextdevice['md'] =  'sm';
    $getnextdevice['sm'] =  'xs';

    /**
     *  Update column
     */
    var updateLayoutColumn = function(datadevice) {
        $('#plazart_layout_builder .generator .column').each(function(i,el){
            var $widthinput = $(this).find('.widthinput-'+datadevice).val();
            var $offsetinput = $(this).find('.offsetinput-'+datadevice).val();
            var $nextdevice = datadevice;
            while ($widthinput == '') {
                if ($nextdevice == 'xs') {
                    //if xs is null so it will be span12
                    $widthinput =   '12';
                } else {
                    $nextdevice =   $getnextdevice[$nextdevice];
                    $widthinput =   $(this).find('.widthinput-'+$nextdevice).val();
                }
            }
            $offsetinput = $offsetinput != '' ? ' offset'+$offsetinput : '';
            $(this).removeClass().addClass('ui-sortable column span'+$widthinput+$offsetinput);
        });
    }

    $(document).ready(function(){
        $("button[class*='tz-admin-dv']").click(function() {
            $("button[class*='tz-admin-dv']").removeClass('active');
            $(this).addClass('active');
            switch ($(this).attr('data-device')) {
                case 'xs':
                    $('#plazart_layout_builder .generator').css('width',450);
                    updateLayoutColumn('xs');
                    break;
                case 'sm':
                    $('#plazart_layout_builder .generator').css('width',650);
                    updateLayoutColumn('sm');
                    break;
                case 'md':
                    $('#plazart_layout_builder .generator').css('width',800);
                    updateLayoutColumn('md');
                    break;
                case 'lg':
                default :
                    $('#plazart_layout_builder .generator').css('width',950);
                    updateLayoutColumn('lg');
                    break;
            }
        });

        $("button[class*='tz-admin-dv']").hover(function() {
            if(typeof tooltip != 'undefined'){
                $(this).tooltip('show');
            }
        });
    });
});