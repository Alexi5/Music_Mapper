/***************************************************************************
 *
 * 	----------------------------------------------------------------------
 * 						DO NOT EDIT THIS FILE
 *	----------------------------------------------------------------------
 *
 *  				     Copyright (C) Themify
 *
 *	----------------------------------------------------------------------
 *
 ***************************************************************************/

/**
 * Common shared code between backend and frontend Builder.
 */

;var ThemifyBuilderCommon, themifyBuilder;
(function($, window, document, undefined) {

    'use strict';

    ThemifyBuilderCommon = {

        /**
         * Function that detects whether localStorage is both supported and available.
         *
         * From MDN.
         *
         * @param type Type of the tested storage. E.g 'localStorage'.
         * @returns {boolean} True on browser having both support and availability, otherwise false.
         */
        storageAvailable: function(type) {
            try {
                var storage = window[type],
                    x = '__storage_test__';
                storage.setItem(x, x);
                storage.removeItem(x);
                return true;
            }
            catch(e) {
                return false;
            }
        },

        isFrontend: function() {
            return themifyBuilder.isFrontend === 'true';
        },

        showLoader: function(stats) {
            var $previewBtn = $('.builder_preview_lightbox'),
                $previewLoaderImage = $('.builder_preview_loader_image');

            if(stats == 'show'){
                $('#themify_builder_alert').addClass('busy').show();
            }
            else if(stats == 'spinhide'){
                $("#themify_builder_alert").delay(800).fadeOut(800, function() {
                    $(this).removeClass('busy');
                });
            } else if (stats === 'lightbox-preview') {
                $previewBtn.hide();

                if ($previewLoaderImage.length) {
                    $previewLoaderImage.show();
                } else {
                    $('<div>', {class: 'builder_preview_loader_image'}).insertBefore($previewBtn);
                }
            } else if (stats === 'lightbox-preview-hide') {
                $previewBtn.show();
                $previewLoaderImage.hide();
            } else if (stats === 'error') {
                $("#themify_builder_alert").removeClass("busy").addClass('error').delay(800).fadeOut(800, function() {
                    $(this).removeClass('error');
                });
            }
            else{
                $("#themify_builder_alert").removeClass("busy").addClass('done').delay(800).fadeOut(800, function() {
                    $(this).removeClass('done');
                });
            }
        },

        getDocHeight: function(){
            var D = document;
            return Math.max(
                Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
                Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
                Math.max(D.body.clientHeight, D.documentElement.clientHeight)
            );
        },

        highlightColumn: function($column) {
            $('.themify_builder_col').removeClass('current_selected_column');
            $column.addClass('current_selected_column');
        },

        highlightRow: function($row) {
            $('.themify_builder_row').removeClass('current_selected_row');
            $row.addClass('current_selected_row');
        },

        highlightSubRow: function($subRow) {
            $('.themify_builder_sub_row').removeClass('current_selected_sub_row');
            $subRow.addClass('current_selected_sub_row');
        },

        /**
         * Returns the closest parent Builder row.
         *
         * @param $elmt {jQuery}
         * @returns {jQuery}
         */
        getClosestRow: function($elmt) {
            return $elmt.parent().closest('.themify_builder_row');
        },

        /**
         * Returns the closest parent Builder col.
         *
         * @param $elmt {jQuery}
         * @returns {jQuery}
         */
        getClosestCol: function($elmt) {
            return $elmt.parent().closest('.themify_builder_col');
        },

        /**
         * Returns the closest parent Builder sub-row.
         *
         * @param $elmt {jQuery}
         * @returns {jQuery}
         */
        getClosestSubRow: function($elmt) {
            return $elmt.parent().closest('.themify_builder_sub_row');
        },

        /**
         * Returns the order (index) of a row/col/sub-col.
         *
         * @param $component {jQuery} The component we want to find the order of.
         */
        getComponentOrder: function($component) {
            var self = ThemifyBuilderCommon;
            var subColIndex, subRowIndex, colIndex, rowIndex;

            var componentType = self.getComponentType($component);

            if (componentType == 'sub-col') {
                subColIndex = $component.index();
                subRowIndex = self.getClosestSubRow($component).index();
                colIndex = self.getClosestCol($component).index();
                rowIndex = self.getClosestRow($component).index();

                return rowIndex + '-' + colIndex + '-' + subRowIndex + '-' + subColIndex;

            } else if (componentType == 'col') {
                colIndex = $component.index();
                rowIndex = self.getClosestRow($component).index();

                return rowIndex + '-' + colIndex;

            } else if (componentType == 'row') {
                rowIndex = $component.index();

                return rowIndex;
            }
        },

        /**
         * Returns the type of a component. Can be 'row', 'col', 'sub-col', 'module-holder', 'module', 'module-inner'.
         *
         * 'module-holder' contains 'module' + '.empty_holder_text'.
         * 'module' contains module's data such as mod name, mod contents and styling.
         * 'module-inner' contains module's contents such as title, text or any other.
         *
         * @param $component {jQuery} The component we want to detect the type of.
         */
        getComponentType: function($component) {
            if ($component.hasClass('themify_builder_col')) {
               if ($component.closest('.themify_builder_sub_row_content').length) {
                   return 'sub-col';
               } else {
                   return 'col';
               }
            } else if ($component.hasClass('themify_builder_row')) {
                return 'row';
            } else if ($component.hasClass('themify_builder_module_front')
                || $component.hasClass('themify_builder_module') /* backend */) {
                return 'module';
            } else if ($component.hasClass('themify_module_holder')) {
                return 'module-holder';
            } else if ($component.hasClass('module')) {
                return 'module-inner';
            }
        },

        /**
         * Returns component's Builder's ID.
         *
         * @param {jQuery} $component
         * @returns {Number}
         */
        getComponentBuilderId: function($component) {
            var id = $component.closest('.themify_builder_content').data('postid');

            if (typeof id === 'undefined' || isNaN(id)) {
                return -1;
            }

            return id;
        },

        /**
         * Clipboard-like functionality. Wraps localStorage with a Themify key. Supports one copied item per time.
         */
        Clipboard: {
            key : 'themify_builder_clipboard_',
            set : function( type, content ) {
                if ( ThemifyBuilderCommon.storageAvailable('localStorage') ) {
                    localStorage.setItem( this.key + 'type', type );
                    localStorage.setItem( this.key + 'content', content );
                } else {
                    alert( themifyBuilderCommon.text_no_localStorage );
                }
            },
            get : function( type ) {
                if ( ThemifyBuilderCommon.storageAvailable('localStorage') ) {
                    var savedType =  localStorage.getItem( this.key + 'type' ),
                        savedContent = localStorage.getItem( this.key + 'content' );

                    if ( typeof type === undefined || type === savedType ) {
                        return savedContent;
                    } else {
                        return false;
                    }
                } else {
                    alert( themifyBuilderCommon.text_no_localStorage );
                }
            }
        },

        confirmDataPaste: function() {
            return confirm(themifyBuilderCommon.text_confirm_data_paste);
        },

        alertWrongPaste: function() {
            alert(themifyBuilderCommon.text_alert_wrong_paste);
        },

        detectBuilderComponent: function($component) {
            if (!$component.attr('data-component')) {
                return false;
            }

            return $component.data('component');
        },

        /**
         * Returns module's settings in as {Object} if there are any, otherwise empty string.
         *
         * @param $moduleWrapper {jQuery} div.themify_builder_module_front wrapper around module.
         * @returns {string|Object}
         */
        getModuleSettings: function($moduleWrapper) {
            var settingsInText = $moduleWrapper
                .find('.front_mod_settings')
                .children('script[type="text/json"]')
                .text()
                .trim();

            if (settingsInText.length > 2 && settingsInText != 'null') {
                return JSON.parse(settingsInText);
            }

            return '';
        },

        /**
         * Returns row's styling settings as {Object} if there any, otherwise {false}.
         *
         * @param $row {jQuery} Row element, which styling settings we want to get.
         * @returns {Object|false}
         */
        getRowStylingSettings: function($row) {
            var settings = $row.find('.row_inner').children('.row-data-styling').data('styling');

            if (typeof settings === "object") {
                return settings;
            }

            return false;
        },

        /**
         * Returns column's styling settings as {Object} if there any, otherwise {false}.
         *
         * @param $column {jQuery} Column element, which styling settings we want to get.
         * @returns {Object|false}
         */
        getColumnStylingSettings: function($column) {
            var settings = $column.children('.column-data-styling').data('styling');

            if (typeof settings === "object") {
                return settings;
            }

            return false;
        },

        findColumnInNewRow: function($newRow, colLocationObj) {
            var $currentCol = null;
            var colIndex, subColIndex = 0;

            if (colLocationObj.hasOwnProperty('sub-col_index')) {
                subColIndex = colLocationObj['sub-col_index'];
                colIndex = colLocationObj['col_index'];

                var $parentCol = $newRow.find(
                    '.themify_builder_row_content > .themify_builder_col:nth-child(' + (colIndex + 1) + ')'
                );
                $currentCol = $parentCol.find(
                    '.themify_builder_sub_row_content > .themify_builder_col:nth-child(' + (subColIndex + 1) + ')'
                );
            } else if (colLocationObj.hasOwnProperty('col_index')) {
                colIndex = colLocationObj['col_index'];

                $currentCol = $newRow.find(
                    '.themify_builder_row_content > .themify_builder_col:nth-child(' + (colIndex + 1) + ')'
                );
            }

            return $currentCol;
        },

        /**
         * Returns the checked radio in a named group.
         *
         * @param {jQuery} $radioInGroup A radio in a named group.
         * @param {jQuery|undefined} $context Where should the radio group be searched in.
         * @returns {jQuery}
         */
        getCheckedRadioInGroup: function($radioInGroup, $context) {
            if (typeof $context === 'undefined') {
                $context = null;
            }

            var radioGroupName = $radioInGroup.attr('name');

            return $('input:radio[name="' + radioGroupName + '"]:checked', $context);
        },

        /**
         * Loads Google Web Fonts.
         *
         * @param {Array} fontFamilies Array containing font family declarations with character sets and styles.
         *                             E.g., [ 'Open+Sans:400,700:latin,latin-ext' ]
         */
        loadGoogleFonts: function(fontFamilies) {
            var fontConfig = {
                google: { families: fontFamilies }
            };

            WebFont.load(fontConfig);
        },

        Lightbox: {
            $lightbox: null,

            setup: function() {
                var self = ThemifyBuilderCommon.Lightbox,
                    isThemifyTheme = 'true' == themifyBuilder.isThemifyTheme? 'is-themify-theme' : 'is-not-themify-theme',
                    lightbox_func = wp.template( 'builder_lightbox'),
                    markup = lightbox_func( { is_themify_theme: isThemifyTheme } );

                self.bindEvents();

                $(markup).hide().find('#themify_builder_lightbox_parent').hide().end().appendTo('body');

                self.$lightbox = $('#themify_builder_lightbox_parent');

                function makeLightboxResizable() {
                    self.$lightbox.resizable({
                        minWidth: 540,
                        maxWidth: 880,
                        minHeight: 400, /* corresponds to min-height in themify-builder-admin-ui.css .themify_builder.builder-lightbox  */
                        maxHeight: 800,

                        create: function(_, ui) {
                            var $resizingIcon = self.$lightbox.find('.ui-resizable-handle.ui-icon-gripsmall-diagonal-se');

                            // change resizing icon to an arrow
                            $resizingIcon
                                .removeClass('ui-icon-gripsmall-diagonal-se')
                                .addClass('ui-icon-arrow-1-se');
                        },

                        resize: function(event, ui) {
                            self.adjustHeight(ui.size.height);
							setupLightboxSizeClass();
                        },

                        stop: function() {
                            self.fixContainment();
                        }
                    });
					$( 'body' ).on('editing_module_option', setupLightboxSizeClass )
                }

                function makeLightboxDraggable() {
                    var topBarElem = document.querySelector('#themify_builder_lightbox_parent .themify_builder_lightbox_top_bar');

                    self.$lightbox.draggable({
                        handle: topBarElem,
                        scroll: false,
                        start: ThemifyBuilderCommon.Lightbox.fixContainment,
                        stop: ThemifyBuilderCommon.Lightbox.fixContainment
                    });
                }

                function rememberLightboxPositionSize() {
                    self.$lightbox.on('dragstop resizestop', function(event, ui) {
                        var posSizeObj = {
                            top: ui.position.top,
                            left: ui.position.left,
                            width: self.$lightbox.outerWidth(),
                            height: self.$lightbox.outerHeight()
                        };

                        var key = 'themify_builder_lightbox_frontend_pos_size';

                        if (!ThemifyBuilderCommon.isFrontend()) {
                            key = 'themify_builder_lightbox_backend_pos_size';
                        }

                        localStorage.setItem(key, JSON.stringify(posSizeObj));
                    });
                }

				function setupLightboxSizeClass() {
					if( parseInt( self.$lightbox.width() ) > 750 ) {
						self.$lightbox.addClass( 'larger-lightbox' );
					} else {
						self.$lightbox.removeClass( 'larger-lightbox' );
					}
				}

				
                makeLightboxResizable();
                makeLightboxDraggable();
                rememberLightboxPositionSize();
            },

            bindEvents: function() {
                var self = ThemifyBuilderCommon.Lightbox;
                var $body = $('body'),
                    actionEvent = 'true' == themifyBuilder.isTouch ? 'touchend' : 'click';

                $body
                    // Top bar actions
                    .on('editing_row_option editing_column_option editing_module_option', self.selectDefaultTab)
                    .on(actionEvent, '.themify_builder_options_tab li', self.switchTabs)
                    .on(actionEvent, '.builder_cancel_lightbox', self.cancel)
                    .on(actionEvent, '#builder_submit_row_settings', ThemifyPageBuilder.rowSaving)
                    .on(actionEvent, '#builder_submit_column_settings', ThemifyPageBuilder.columnSaving)
                    .on(actionEvent, '#builder_submit_module_settings, #builder_preview_module', ThemifyPageBuilder.moduleSave)

                    // Content actions
                    .on('change click', '.tf-option-checkbox-js', self.clickCheckBoxOption)
                    .on(actionEvent, '#tfb_module_settings input[type="text"], #tfb_module_settings textarea', self.focusInput)
                    .on('change', '#tfb_module_settings .query_category_single', function(){
                        $(this).closest('.themify_builder_input').find('.query_category_multiple').val($(this).val());
                    })
                    .on(actionEvent, '.reset-styling', self.resetStyling);

                if (!themifyBuilder.disableShortcuts) {
                    self.saveByKeyInput();
                }
            },

            adjustHeight: function(newLightboxHeight)  {
                var self = ThemifyBuilderCommon.Lightbox;

                var $lightboxContent = self.$lightbox.find('.themify_builder_options_tab_wrapper'),
                    heightOfLightboxWithoutContent = 65;

                $lightboxContent.css('height', newLightboxHeight - heightOfLightboxWithoutContent);
            },

            fixContainment: function(){
                var self = ThemifyBuilderCommon.Lightbox,
                    el_w = self.$lightbox.outerWidth(),
                    container_w = $(window).width(),
                    container_h = $(window).height(),
                    containment = [-el_w + 20, 0, container_w - 20, container_h - 30],
                    positions = self.$lightbox.position(),
                    new_positions = {};
                positions.left < containment[0] && (new_positions.left = containment[0]), 0 > positions.top && (new_positions.top = 0), positions.left > containment[2] && (new_positions.left = containment[2]), positions.top > containment[3] && (new_positions.top = containment[3]), self.$lightbox.css(new_positions)
            },

            open: function( options, callback) {
                var self = ThemifyBuilderCommon.Lightbox,
                    $lightboxContainer = $('#themify_builder_lightbox_container');

                if (ThemifyBuilderCommon.isFrontend()) {
                    ThemifyPageBuilder.hideSlidingPanel();

                    ThemifyPageBuilder.hideModulesControl();
                    ThemifyPageBuilder.hideColumnsBorder();
                }

                $lightboxContainer.empty();
                self.$lightbox.addClass( 'tfb-lightbox-open' );
                $('#themify_builder_overlay').show();
                ThemifyBuilderCommon.showLoader('show');

                if ( options.loadMethod && 'inline' == options.loadMethod ) {
                    var response = document.getElementById( 'tmpl-' + options.templateID ).innerHTML;
                    self.openCallback( response, $lightboxContainer, callback );
                } else {
                    options.data = _.extend( options.data || {}, { nonce : themifyBuilder.tfb_load_nonce });
                    options = _.defaults( options || {}, {
                        type:    'POST',
                        url:     themifyBuilder.ajaxurl
                    });

                    var jqxhr = $.ajax(options);
                    jqxhr.done(function(response){
                        self.openCallback( response, $lightboxContainer, callback );
                    });
                }
            },

            openCallback: function( response, $lightboxContainer, callback ) {
                var self = ThemifyBuilderCommon.Lightbox;

                // Load last position and size of lightbox
                var key = 'themify_builder_lightbox_frontend_pos_size';

                if (!ThemifyBuilderCommon.isFrontend()) {
                    key = 'themify_builder_lightbox_backend_pos_size';
                }

                var posSizeJSON = localStorage.getItem(key),
                    posSizeObj = {
                        top: 100,
                        left: Math.max(0, ( ( $(window).width() / 2 ) - 300 ) ),
                        width: 600,
                        height: 500
                    };

                if (posSizeJSON !== null) {
                    posSizeObj = JSON.parse(posSizeJSON);
                }

                self.$lightbox
                    .show()
                    .css({
                        'top': posSizeObj.top,
                        'left': posSizeObj.left,
                        'width': posSizeObj.width,
                        'height': posSizeObj.height
                    })
                    .addClass('animated fadeInUp')
                    .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(e) {
                        self.fixContainment();
                        $(this).removeClass('animated fadeInUp');
                        console.log('transition end');
                    });

                $( document ).on( 'keyup', self.cancelKeyListener );

                ThemifyBuilderCommon.showLoader('spinhide');

                var $response = $(response);
                
                var $optionsTabItemsContainer = $response.find('#themify_builder_lightbox_options_tab_items');
                if ($optionsTabItemsContainer.length) {
                    $('.themify_builder_options_tab').append($optionsTabItemsContainer.children());
                    $optionsTabItemsContainer.remove();
                }

                var $actionButtonsContainer = $response.find('#themify_builder_lightbox_actions_items');
                if ($actionButtonsContainer.length) {
                    $('.themify_builder_lightbox_actions').append($actionButtonsContainer.children());
                    $actionButtonsContainer.remove();
                }

                $lightboxContainer.append($response);

                self.adjustHeight(posSizeObj.height);

                self.$lightbox.show();

                // Get content height
                var h = $('#themify_builder_lightbox_container').height(),
                    windowH = $(window).height();

                $('#themify_builder_lightbox_container').find('select').wrap('<div class="selectwrapper"></div>');
                $('.selectwrapper').click(function(){
                    $(this).toggleClass('clicked');
                });

                if( $.isFunction(callback) ){
                    callback.call(this, $response[0]);
                }
            },

            close: function() {
                var self = ThemifyBuilderCommon.Lightbox,
                    $tfb_dialog_form = $('form#tfb_module_settings');

                $( document ).off( 'keyup', self.cancelKeyListener );


                if ( typeof tinyMCE !== 'undefined' ) {
                    $tfb_dialog_form.find('.tfb_lb_wp_editor').each( function(){
                        var $id = $(this).prop('id');
                        switchEditors.go($id, 'tmce');
                    });
                }

                if (ThemifyBuilderCommon.isFrontend()) {
                    self.forgetRow();

                    if (ThemifyPageBuilder.liveStylingInstance !== null) {
                        ThemifyPageBuilder.liveStylingInstance.remove();
                    }

                    ThemifyPageBuilder.showSlidingPanel();

                    ThemifyPageBuilder.showColumnsBorder();
                }

                self.$lightbox.addClass('animated fadeOutDown')
                .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(e) {
                    $(this).removeClass('animated fadeOutDown');
                    console.log('transition closed');
                    // Animation complete.
                    $('#themify_builder_lightbox_container').empty();
                    self.$lightbox.find('.themify_builder_options_tab').empty();
                    self.$lightbox.find('.themify_builder_lightbox_actions')
                        .children()
                        .not('.builder_cancel_lightbox')
                        .remove();

                    $( "#themify_builder_lightbox_parent" ).removeAttr('style');

                    ThemifyPageBuilder.deleteEmptyModule(); // clear empty module

                    $('#themify_builder_overlay, #themify_builder_lightbox_parent').hide();
                    self.$lightbox.removeClass( 'tfb-lightbox-open' );
                });
            },

            saveByKeyInput: function () {
                $(document).on('keydown', function (event) {
                    // Ctrl + s | Cmd + s
                    if (83 == event.which && (true == event.ctrlKey || true == event.metaKey)) {
                        var currentElement = document.activeElement.tagName.toLowerCase();

                        if (currentElement != 'input' && currentElement != 'textarea') {
                            event.preventDefault();
                            var $moduleSettings = $('#builder_submit_module_settings'),
                                $rowSetting = $('#builder_submit_row_settings'),
                                $columnSettings = $('#builder_submit_column_settings'),
                                $panelSave = $('.themify_builder_active').find('.themify-builder-front-save');
                            if ($moduleSettings.length > 0) {
                                $moduleSettings.trigger('click');
                            } else if ($rowSetting.length > 0) {
                                $rowSetting.trigger('click');
                            } else if ($columnSettings.length > 0) {
                                $columnSettings.trigger('click');
                            } else if ($panelSave.length > 0) {
                                $panelSave.trigger('click');
                            }
                        }
                    }
                });
            },

            // If clicked on the "Preview" button in lightbox, save (remember) the original (unmodified) row
            // and put it back on "Cancel" lightbox click.
            rememberedRow: null,

            rememberRow: function() {
                var self = ThemifyBuilderCommon.Lightbox;

                self.rememberedRow = $('.current_selected_row').get(0).outerHTML;
            },

            revertToRememberedRow: function() {
                var self = ThemifyBuilderCommon.Lightbox,
                    $currentSelectedRow = $('.current_selected_row');

                if ($currentSelectedRow.length) {
                    $currentSelectedRow.get(0).outerHTML = self.rememberedRow;

                    $currentSelectedRow = $('.current_selected_row');

                    // For some reason, additional "animated" gets appended to the classes list. Remove it.
                    $currentSelectedRow.removeClass('animated');

                    ThemifyPageBuilder.moduleEvents();
                }

                self.rememberedRow = null;
            },

            forgetRow: function() {
                var self = ThemifyBuilderCommon.Lightbox;

                self.rememberedRow = null;
            },

            previewButtonClicked: function($link) {
                return $link.hasClass('builder_preview_lightbox');
            },

            showPreviewBtn: function() {
                $('.builder_button.builder_preview_lightbox').show();
            },

            hidePreviewBtn: function() {
                $('.builder_button.builder_preview_lightbox').hide();
            },

            clickCheckBoxOption: function(e) {
                var selected_group = ThemifyBuilderCommon.getCheckedRadioInGroup($(this), ThemifyBuilderCommon.Lightbox.$lightbox).data('selected');
                $('.tf-group-element').hide();
                $('.'+selected_group).show();
                $('.thumb_preview').each(function(){
                    if($(this).find('img').length == 0) {
                        $(this).hide();
                    }
                });
            },

            cancelKeyListener : function(e){
                var self = ThemifyBuilderCommon.Lightbox;

                if( e.keyCode == 27 ) {
                    e.preventDefault();
                    self.cancel(e);
                }
            },

            cancel: function(e) {
                e.preventDefault();

                var self = ThemifyBuilderCommon.Lightbox;

                if (self.rememberedRow !== null) {
                    self.revertToRememberedRow();
                }

                // clear undo/redo start value
                ThemifyBuilderCommon.undoManager.startValue = null;

                self.close();
            },

            focusInput: function(e) {
                $(this).focus();
            },

            resetStyling: function(e) {
                e.preventDefault();

                var dataReset = $(this).data('reset');
                var $context = null;

                if (dataReset === 'module') {
                    $context = $('#themify_builder_options_styling');
                } else if (dataReset === 'row') {
                    $context = $('#tfb_row_settings');
                } else if (dataReset === 'column') {
                    $context = $('#tfb_column_settings');
                }

                var radioGroupNames = [];

                $('.tfb_lb_option:not(.exclude-from-reset-field)', $context).each(function(){
                    var $this = $(this);

                    if ($this.attr('type') === 'radio') {
                        var radioGroupName = $this.attr('name');

                        if (radioGroupNames.indexOf(radioGroupName) === -1) {
                            radioGroupNames.push(radioGroupName);
                        }
                    } else {
                        $this.val('').prop('selected', false);
                    }

                    if( $this.hasClass('themify-builder-uploader-input') ) {
                        $this.parent().find('.img-placeholder').html('').parent().hide();
                    } else if ( $this.hasClass('font-family-select') ) {
                        $this.val('default');
                    } else if( $this.hasClass('builderColorSelectInput') ) {
                        $this.parent().find('.colordisplay').val('').trigger('blur');
                    }
                });

                radioGroupNames.forEach(function(radioGroupName) {
                    $('input:radio[name="' + radioGroupName + '"]:first', $context)
                        .attr('checked', true)
                        .trigger('change');
                });

                if (ThemifyBuilderCommon.isFrontend()) {
                    ThemifyPageBuilder.liveStylingInstance.resetStyling();
                }
            },

            switchTabs: function(e) {
                var self = ThemifyBuilderCommon.Lightbox;

                e.preventDefault();

                $(this).addClass('current').siblings().removeClass('current');

                var activeTabId = $(this).find('a').attr('href');
                $(activeTabId).show().siblings('.themify_builder_options_tab_wrapper').hide();

                if (ThemifyBuilderCommon.isFrontend()) {
                    self.handleDisplayOfPreviewBtn(activeTabId);
                }
            },

            selectDefaultTab: function(e) {
                var self = ThemifyBuilderCommon.Lightbox;

                $('.themify_builder_options_tab_wrapper').hide().first().show();

                var $tabItem = $('ul.themify_builder_options_tab li:first');

                $tabItem.addClass('current');

                if (ThemifyBuilderCommon.isFrontend()) {
                    var activeTabId = $tabItem.find('a').attr('href');
                    self.handleDisplayOfPreviewBtn(activeTabId);
                }
            },

            handleDisplayOfPreviewBtn: function(activeTabId) {
                var self = ThemifyBuilderCommon.Lightbox;

                if (activeTabId === '#themify_builder_options_setting') {
                    self.showPreviewBtn();
                } else {
                    self.hidePreviewBtn();
                }
            }
        },
        undoManager: {
            instance: new UndoManager(),
            events: $({}), // create empty object
            startValue: null,
            set: function( container, startValue, newValue ) {
                var newStartValue = startValue,
                    newNewValue = newValue;
                ThemifyBuilderCommon.undoManager.instance.add({
                    undo: function () {
                        container.innerHTML = newStartValue;
                    },
                    redo: function () {
                        container.innerHTML = newNewValue;
                    }
                });
                ThemifyBuilderCommon.undoManager.startValue = null;
            },
            setStartValue: function( value ) {
                ThemifyBuilderCommon.undoManager.startValue = value;
            },
            getStartValue: function() {
                return ThemifyBuilderCommon.undoManager.startValue;
            }
        },
        LiteLightbox: {
            modal: new wp.media.view.Modal({
                controller: { trigger: function() {} }
            }),
            confirmView: wp.Backbone.View.extend({
                template: wp.template('builder_lite_lightbox_confirm'),
                className: 'themify_builder_lite_lightbox_content',
                initialize: function(options) {
                    this.options = options || {};
                },
                render: function() {
                    this.$el.html( this.template( { message: this.options.message, buttons: this.options.buttons } ) );
                },
                events: {
                    'click button': 'buttonClick'
                },
                buttonClick: function( event ) {
                    event.preventDefault();
                    var type = $(event.currentTarget).data('type');
                    this.trigger('litelightbox:confirm', type);
                }
            }),
            promptView: wp.Backbone.View.extend({
                template: wp.template('builder_lite_lightbox_prompt'),
                className: 'themify_builder_lite_lightbox_content',
                initialize: function(options) {
                    this.options = options || {};
                },
                render: function() {
                    this.$el.html( this.template( this.options ) );
                },
                events: {
                    'click button': 'buttonClick',
                    'keypress .themify_builder_litelightbox_prompt_input': 'keyPress'
                },
                buttonClick: function( event ) {
                    event.preventDefault();
                    var type = $(event.currentTarget).data('type'),
                        value = this.$el.find('.themify_builder_litelightbox_prompt_input').val();
                    this.trigger('litelightbox:prompt', type, value);
                },
                keyPress: function( event ) {
                    if ( event.which === 13 ) { // on enter
                        var value = $(event.currentTarget).val();
                        this.trigger('litelightbox:prompt', 'ok', value);
                    }
                }
            }),
            confirm: function( message, callback, options ) {
                options = _.defaults( options || {}, {
                    buttons: {
                        no: {
                            label: 'No'
                        },
                        yes: {
                            label: 'Yes'
                        }
                    }
                });
                options.message = message;
                var contentView = new ThemifyBuilderCommon.LiteLightbox.confirmView( options );
                ThemifyBuilderCommon.LiteLightbox.modal.content( contentView );
                ThemifyBuilderCommon.LiteLightbox.modal.open();
                contentView.on('litelightbox:confirm', function( type ){
                    ThemifyBuilderCommon.LiteLightbox.modal.close();
                    // load callback
                    if ($.isFunction(callback)) {
                        callback.call(this, type);
                    }
                });
            },
            prompt: function( message, callback, options ) {
                options = _.defaults( options || {}, {
                    buttons: {
                        cancel: {
                            label: 'Cancel'
                        },
                        ok: {
                            label: 'OK'
                        }
                    }
                });
                options.message = message;
                var promptView = new ThemifyBuilderCommon.LiteLightbox.promptView( options );
                ThemifyBuilderCommon.LiteLightbox.modal.content( promptView );
                ThemifyBuilderCommon.LiteLightbox.modal.open();
                promptView.on('litelightbox:prompt', function( type, value ){
                    ThemifyBuilderCommon.LiteLightbox.modal.close();
                    // load callback
                    if ($.isFunction(callback)) {
                        value = 'cancel' == type ? null : value;
                        callback.call(this, value);
                    }
                });
            }
        }

    };

}(jQuery, window, document));
