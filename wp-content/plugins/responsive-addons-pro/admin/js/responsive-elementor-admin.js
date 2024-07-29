
/**
 * AJAX Request Queue
 *
 * - add()
 * - remove()
 * - run()
 * - stop()
 *
 * @since 1.0.0
 */
 var ResponsiveSitesAjaxQueue = (function () {

	var requests = [];

	return {

		/**
		 * Add AJAX request
		 *
		 * @since 1.0.0
		 */
		add: function (opt) {
			requests.push(opt);
		},

		/**
		 * Remove AJAX request
		 *
		 * @since 1.0.0
		 */
		remove: function (opt) {
			if (jQuery.inArray(opt, requests) > -1)
				requests.splice($.inArray(opt, requests), 1);
		},

		/**
		 * Run / Process AJAX request
		 *
		 * @since 1.0.0
		 */
		run: function () {
			var self = this,
				oriSuc;

			if (requests.length) {
				oriSuc = requests[0].complete;

				requests[0].complete = function () {
					if (typeof (oriSuc) === 'function') oriSuc();
					requests.shift();
					self.run.apply(self, []);
				};

				jQuery.ajax(requests[0]);

			} else {

				self.tid = setTimeout(function () {
					self.run.apply(self, []);
				}, 1000);
			}
		},

		/**
		 * Stop AJAX request
		 *
		 * @since 1.0.0
		 */
		stop: function () {

			requests = [];
			clearTimeout(this.tid);
		}
	};

}());

(function ($) {

	$rst_elscope = {};

	$.fn.isInViewport = function () {

		// If not have the element then return false!
		if (!$(this).length) {
			return false;
		}

		var elementTop = $(this).offset().top;
		var elementBottom = elementTop + $(this).outerHeight();
		var viewportTop = $(window).scrollTop();
		var viewportBottom = viewportTop + $(window).height();

		return elementBottom > viewportTop && elementTop < viewportBottom;
	};

	ResponsiveElementorSitesAdmin = {

		visited_pages: [],
		reset_remaining_posts: 0,
		site_imported_data: null,
		backup_taken: false,
		templateData: {},
		insertData: {},
		log_file: '',
		pages_list: '',
		insertActionFlag: false,
		page_id: 0,
		site_id: 0,
		actual_site_id: 0,
		wpforms_path: '',
		step: 0,
		site_url: '',
		block_id: 0,
		requiredPlugins: [],
		requiredPagePlugins: [],
		requiredBlockPlugins: [],
		requiredProBlockPlugins: [],
		requiredProPlugins: [],
		canImport: false,
		canInsert: false,
		type: 'pages',
		action: '',
		masonryObj: [],
		index: 0,
		blockCategory: '',
		blockColor: '',
		processing: false,
		siteType: '',
		templateType: '',
		blockType: '',
		page: 1,
		demo_type: '',
		per_page: 20,
		syncBlocks: false,
		license_key: '',

		init: function () {
			this._bind();
		},

		/**
		 * Binds events for the Responsive Sites.
		 *
		 * @since 1.0.0
		 * @access private
		 * @method _bind
		 */
		_bind: function () {

			if (elementorCommon) {

				let add_section_tmpl = $("#tmpl-elementor-add-section");

				if (add_section_tmpl.length > 0) {
					let action_for_add_section = add_section_tmpl.text();

					action_for_add_section = action_for_add_section.replace('<div class="elementor-add-section-drag-title', '<div class="elementor-add-section-area-button elementor-add-rst-site-button" title="'+ responsiveElementorProSites.plugin_name +'"> <i class="eicon-folder"></i> </div><div class="elementor-add-section-drag-title');

					add_section_tmpl.text(action_for_add_section);

					elementor.on("preview:loaded", function () {
						
						let base_skeleton = wp.template('rst-template-base-skeleton');

						let header_template = $('#tmpl-rst-template-modal__header').text();

						if ($('#rst-sites-modal').length == 0) {
							
							$('body').append(base_skeleton());
							$rst_elscope = $('#rst-sites-modal');
							$rst_elscope.find('.responsive-sites-content-wrap').before(header_template);

						}

						ResponsiveElementorSitesAdmin._populate_block_color_filters();

						$rst_elscope.find('.responsive-blocks-category').select2();

						$rst_elscope.find('.responsive-blocks-category').on('select2:select', ResponsiveElementorSitesAdmin._categoryChange);

						$rst_elscope.find('#elementor-template-block-color-filter select').on('change', ResponsiveElementorSitesAdmin._blockColorChange);

						$(elementor.$previewContents[0].body).on("click", ".elementor-add-rst-site-button", ResponsiveElementorSitesAdmin._open);

						// Click events.
						$('body').on("click", ".rst-sites-modal__header__close", ResponsiveElementorSitesAdmin._close);

						$('body').on("click", "#rst-sites-modal .theme-screenshot", ResponsiveElementorSitesAdmin._preview);

						$('body').on("click", "#rst-sites-modal .back-to-layout", ResponsiveElementorSitesAdmin._goBack);

						$(document).on("click", "#rst-sites-modal .rst-library-template-insert", ResponsiveElementorSitesAdmin._insert);

						$(document).on("click", ".rst-import-elementor-template", ResponsiveElementorSitesAdmin._importTemplate);

						$('body').on("click", "#rst-sites-modal .responsive-sites-tooltip-icon", ResponsiveElementorSitesAdmin._toggleTooltip);

						$(document).on("click", ".elementor-template-library-menu-item", ResponsiveElementorSitesAdmin._toggle);

						$(document).on("click", ".rst-sites-modal__header__sync", ResponsiveElementorSitesAdmin._sync);

						$(document).on("rst-sync-blocks", ResponsiveElementorSitesAdmin._sync);

						$(document).on('click', '#rst-sites-modal .rst-sites-modal__header__logo, #rst-sites-modal .back-to-layout-button', ResponsiveElementorSitesAdmin._home);

						$(document).on('click', '#rst-sites-modal .notice-dismiss', ResponsiveElementorSitesAdmin._dismiss);

						// Other events.
						$rst_elscope.find('.responsive-sites-content-wrap').scroll(ResponsiveElementorSitesAdmin._loadLargeImages);

						$(document).on('change', '#rst-sites-modal .elementor-template-library-order-input', ResponsiveElementorSitesAdmin._changeType);

						$(document).on('keyup input', '#rst-sites-modal #wp-filter-search-input', ResponsiveElementorSitesAdmin._search);

						// Triggers.
						$(document).on("responsive-sites__elementor-open-after", ResponsiveElementorSitesAdmin._initSites);
						$(document).on("responsive-sites__elementor-open-before", ResponsiveElementorSitesAdmin._beforeOpen);
						$(document).on("responsive-sites__elementor-plugin-check", ResponsiveElementorSitesAdmin._pluginCheck);
						$(document).on('responsive-sites__elementor-close-before', ResponsiveElementorSitesAdmin._beforeClose);
						
						$(document).on('responsive-sites__elementor-do-step-1', ResponsiveElementorSitesAdmin._step1);
						$(document).on('responsive-sites__elementor-do-step-2', ResponsiveElementorSitesAdmin._step2);

						$(document).on('responsive-sites__elementor-goback-step-1', ResponsiveElementorSitesAdmin._goStep1);
						$(document).on('responsive-sites__elementor-goback-step-2', ResponsiveElementorSitesAdmin._goStep2);

						// Plugin install & activate.
						$(document).on('wp-plugin-installing', ResponsiveElementorSitesAdmin._pluginInstalling);
						$(document).on('wp-plugin-install-error', ResponsiveElementorSitesAdmin._installError);
						$(document).on('wp-plugin-install-success', ResponsiveElementorSitesAdmin._installSuccess);

					})
				}
			}

		},

		_close: function (e) {
			console.groupEnd('Process Done.');
			$(document).trigger('responsive-sites__elementor-close-before');
			setTimeout(function () {
				$rst_elscope.fadeOut();
				$('body').removeClass('responsive-sites__elementor-open');
			}, 300);
		},

		_beforeClose: function () {
			if (ResponsiveElementorSitesAdmin.action == 'insert') {
				$rst_elscope.find('.rst-library-template-insert').removeClass('installing');
				$rst_elscope.find('.rst-library-template-insert').text('Imported');
				$rst_elscope.find('.rst-library-template-insert').addClass('action-done');

				if ($rst_elscope.find('.rst-sites-floating-notice-wrap').hasClass('slide-in')) {

					$rst_elscope.find('.rst-sites-floating-notice-wrap').removeClass('slide-in');
					$rst_elscope.find('.rst-sites-floating-notice-wrap').addClass('slide-out');

					setTimeout(function () {
						$rst_elscope.find('.rst-sites-floating-notice-wrap').removeClass('slide-out');
					}, 200);
				}
			}
		},

		_open: function (e) {
			$(document).trigger('responsive-sites__elementor-open-before');

			$('body').addClass('responsive-sites__elementor-open');

			let add_section = $(this).closest('.elementor-add-section');

			if (add_section.hasClass('elementor-add-section-inline')) {
				ResponsiveElementorSitesAdmin.index = add_section.prevAll().length;
			} else {
				ResponsiveElementorSitesAdmin.index = add_section.prev().children().length;
			}
			ResponsiveElementorSitesAdmin._home();
			$rst_elscope.fadeIn();
			if ($('.refreshed-notice').length == 1) {
				setTimeout(
					function () {
						$('.refreshed-notice').find('.notice-dismiss').click();
					},
					2500
				);
			}
			$(document).trigger('responsive-sites__elementor-open-after');
		},
		
		_beforeOpen: function (e) {

			let userPrefersDark = matchMedia('(prefers-color-scheme: dark)').matches;
			let uiTheme = elementor.settings.editorPreferences.model.get('ui_theme');

			if ('dark' === uiTheme || ('auto' === uiTheme && userPrefersDark)) {
				$('body').addClass('rst-sites-dark-mode');
			} else {
				$('body').removeClass('rst-sites-dark-mode');
			}

			// Hide preview page.
			$rst_elscope.find('.theme-preview').hide();
			$rst_elscope.find('.theme-preview').html('');

			// Show site listing page.
			$rst_elscope.find('.dialog-lightbox-content').show();

			// Hide Back button.
			$rst_elscope.find('.back-to-layout').css('visibility', 'hidden');
			$rst_elscope.find('.back-to-layout').css('opacity', '0');
		},

		_home: function () {
			if (ResponsiveElementorSitesAdmin.processing) {
				return;
			}
			$rst_elscope.find('#wp-filter-search-input').val('');
			// Hide Back button.
			$rst_elscope.find('.back-to-layout').css('visibility', 'hidden');
			$rst_elscope.find('.back-to-layout').css('opacity', '0');
			$rst_elscope.find('.elementor-template-library-menu-item:first-child').trigger('click');
		},

		_initSites: function (e) {
			ResponsiveElementorSitesAdmin._appendSites(responsiveElementorProSites.default_page_builder_sites);
			ResponsiveElementorSitesAdmin._goBack();
		},

		_initBlocks: function (e) {
			ResponsiveElementorSitesAdmin._appendBlocks(responsiveElementorProSites.default_page_builder_blocks);
			ResponsiveElementorSitesAdmin._goBack();
		},

		_appendSites: function (data) {

			let single_template = wp.template('responsive-sites-list');
			pages_list = single_template(data);
			$rst_elscope.find('.dialog-lightbox-message-block').hide();
			$rst_elscope.find('.dialog-lightbox-message').show();
			$rst_elscope.find('.dialog-lightbox-content').html(pages_list);
			ResponsiveElementorSitesAdmin._loadLargeImages();
		},

		_appendBlocks: function (data) {
			let single_template = wp.template('responsive-blocks-list');
			let blocks_list = single_template(data);
			$rst_elscope.find('.dialog-lightbox-message').hide();
			$rst_elscope.find('.dialog-lightbox-message-block').show();
			$rst_elscope.find('.dialog-lightbox-content-block').html(blocks_list);
			ResponsiveElementorSitesAdmin._masonry();
		},

		_masonry: function () {
			var masonryObj;
			var container = document.querySelector('.dialog-lightbox-content-block');
			// initialize Masonry after all images have loaded
			imagesLoaded(container, function () {
				masonryObj = new Masonry(container, {
					itemSelector: '.responsive-sites-library-template'
				});
			});
		},

		_loadLargeImage: function (el) {

			if (el.hasClass('loaded')) {
				return;
			}

			if (el.parents('.responsive-theme').isInViewport()) {
				var large_img_url = el.data('src') || '';
				var imgLarge = new Image();
				imgLarge.src = large_img_url;
				imgLarge.onload = function () {
					el.removeClass('loading');
					el.addClass('loaded');
					el.css('background-image', 'url(\'' + imgLarge.src + '\'');
				};
			}
		},

		_loadLargeImages: function () {
			$rst_elscope.find('.theme-screenshot').each(function (key, el) {
				ResponsiveElementorSitesAdmin._loadLargeImage($(el));
			});
		},

		_goBack: function (e) {

			if (ResponsiveElementorSitesAdmin.processing) {
				return;
			}

			$('#rst-pro-template').removeClass('rst-pro-template')
			$('#rst-pro-template').text('')

			let step = $(this).attr('data-step');

			$rst_elscope.find('#rst-sites-floating-notice-wrap-id.error').hide();

			$rst_elscope.find('.responsive-sites-step-1-wrap').show();
			$rst_elscope.find('.responsive-preview-actions-wrap').remove();

			$rst_elscope.find('.rst-template-library-toolbar').show();
			$rst_elscope.find('.rst-sites-modal__header').removeClass('rst-preview-mode');

			if ('pages' == ResponsiveElementorSitesAdmin.type) {

				if (3 == step) {
					$(this).attr('data-step', 2);
					$(document).trigger('responsive-sites__elementor-goback-step-2');
				} else if (2 == step) {
					$(this).attr('data-step', 1);
					$(document).trigger('responsive-sites__elementor-goback-step-1');
				}
			} else {
				$(this).attr('data-step', 1);
				$(document).trigger('responsive-sites__elementor-goback-step-1');
			}

			$rst_elscope.find('.responsive-sites-content-wrap').trigger('scroll');
		},

		_goStep1: function (e) {

			// Reset site and page ids to null.
			ResponsiveElementorSitesAdmin.site_id = '';
			ResponsiveElementorSitesAdmin.page_id = '';
			ResponsiveElementorSitesAdmin.block_id = '';
			ResponsiveElementorSitesAdmin.requiredPlugins = [];
			ResponsiveElementorSitesAdmin.templateData = {};
			ResponsiveElementorSitesAdmin.canImport = false;
			ResponsiveElementorSitesAdmin.canInsert = false;

			// Hide Back button.
			$rst_elscope.find('.back-to-layout').css('visibility', 'hidden');
			$rst_elscope.find('.back-to-layout').css('opacity', '0');

			// Hide Preview Page.
			$rst_elscope.find('.theme-preview').hide();
			$rst_elscope.find('.theme-preview').html('');
			$rst_elscope.find('.theme-preview-block').hide();
			$rst_elscope.find('.theme-preview-block').html('');
			$rst_elscope.find('.rst-template-library-toolbar').show();

			// Show listing page.
			if (ResponsiveElementorSitesAdmin.type == 'pages') {

				$rst_elscope.find('.dialog-lightbox-content').show();
				$rst_elscope.find('.dialog-lightbox-content-block').hide();

				// Set listing HTML.
				ResponsiveElementorSitesAdmin._appendSites(responsiveElementorProSites.default_page_builder_sites);
			} else {

				// Set listing HTML.
				ResponsiveElementorSitesAdmin._appendBlocks(responsiveElementorProSites.default_page_builder_blocks);

				$rst_elscope.find('.dialog-lightbox-content-block').show();
				$rst_elscope.find('.dialog-lightbox-content').hide();

				if ('' !== $rst_elscope.find('#wp-filter-search-input').val()) {
					$rst_elscope.find('#wp-filter-search-input').trigger('keyup');
				}
			}
		},

		_goStep2: function (e) {

			// Set page and site ids.
			ResponsiveElementorSitesAdmin.site_id = $rst_elscope.find('#responsive-blocks').data('site-id');
			ResponsiveElementorSitesAdmin.page_id = '';

			if (undefined === ResponsiveElementorSitesAdmin.site_id) {
				return;
			}

			// Single Preview template.
			let single_template = wp.template('responsive-sites-list-search');
			let passing_data = responsiveElementorProSites.default_page_builder_sites[ResponsiveElementorSitesAdmin.site_id]['pages'];
			passing_data['site_id'] = ResponsiveElementorSitesAdmin.site_id;
			pages_list = single_template(passing_data);
			$rst_elscope.find('.dialog-lightbox-content').html(pages_list);

			// Hide Preview page.
			$rst_elscope.find('.theme-preview').hide();
			$rst_elscope.find('.theme-preview').html('');
			$rst_elscope.find('.rst-template-library-toolbar').show();
			$rst_elscope.find('.theme-preview-block').hide();
			$rst_elscope.find('.theme-preview-block').html('');

			// Show listing page.
			$rst_elscope.find('.dialog-lightbox-content').show();
			$rst_elscope.find('.dialog-lightbox-content-block').hide();

			ResponsiveElementorSitesAdmin._loadLargeImages();

			if ('' !== $rst_elscope.find('#wp-filter-search-input').val()) {
				$rst_elscope.find('#wp-filter-search-input').trigger('keyup');
			}
		},

		_toggleTooltip: function (e) {

			var wrap = $rst_elscope.find('.rst-tooltip-wrap');

			if (wrap.hasClass('rst-show-tooltip')) {
				$rst_elscope.find('.rst-tooltip-wrap').removeClass('rst-show-tooltip');
			} else {
				$rst_elscope.find('.rst-tooltip-wrap').addClass('rst-show-tooltip');
			}
		},

		_toggle: function (e) {
			$rst_elscope.find('.elementor-template-library-menu-item').removeClass('elementor-active');

			$rst_elscope.find('.dialog-lightbox-content').hide();

			$rst_elscope.find('.theme-preview').hide();
			$rst_elscope.find('.theme-preview').html('');
			$rst_elscope.find('.theme-preview-block').hide();
			$rst_elscope.find('.theme-preview-block').html('');
			$rst_elscope.find('.rst-template-library-toolbar').show();

			$rst_elscope.find('.dialog-lightbox-content').hide();
			$rst_elscope.find('.dialog-lightbox-content-block').hide();

			$(this).addClass('elementor-active');
			let data_type = $(this).data('template-type');

			if (data_type === 'blocks' && !responsiveElementorSites.rstHasBlocksCount && !ResponsiveElementorSitesAdmin.syncBlocks) {
				$(document).trigger('rst-sync-blocks');
				ResponsiveElementorSitesAdmin.syncBlocks = true;
			}

			ResponsiveElementorSitesAdmin.type = data_type;
			ResponsiveElementorSitesAdmin._switchTo(data_type);
		},

		_switchTo: function (type) {
			if ('pages' == type) {
				ResponsiveElementorSitesAdmin._initSites();
				$rst_elscope.find('.dialog-lightbox-content').show();
				$rst_elscope.find('.responsive-blocks-category-inner-wrap').hide();
				$rst_elscope.find('.responsive-blocks-filter-inner-wrap').hide();
				$rst_elscope.find('.elementor-template-library-order').show();
			} else {
				ResponsiveElementorSitesAdmin._initBlocks();
				$rst_elscope.find('.dialog-lightbox-content-block').show();
				$rst_elscope.find('.responsive-blocks-category-inner-wrap').show();
				$rst_elscope.find('.responsive-blocks-filter-inner-wrap').show();
				$rst_elscope.find('.elementor-template-library-order').hide();
			}
			$rst_elscope.find('.responsive-sites-content-wrap').trigger('scroll');
		},

		_sync: function(event) {
			event.preventDefault();
			var button = $(this).find('.dashicons-update');

			if (button.hasClass('updating-message')) {
				return;
			}

			button.addClass('updating-message');
			$rst_elscope.find('#rst-sites-floating-notice-wrap-id').show().removeClass('error');
			$rst_elscope.find('#rst-sites-floating-notice-wrap-id .rst-sites-floating-notice').html('<span class="message">Syncing template library in the background. The process can take anywhere between 2 to 3 minutes. We will notify you once done.<span><button type="button" class="notice-dismiss"><span class="screen-reader-text">' + responsiveElementorProSites.dismiss_text + '</span></button>');
			$rst_elscope.find('#rst-sites-floating-notice-wrap-id').addClass('slide-in').removeClass('refreshed-notice');

			// Update Blocks and Sites Library.
			$.ajax({
				url: responsiveElementorProSites.ajaxurl,
				type: 'POST',
				data: {
					action: 'responsive-ready-sites-update-templates-library',
					_ajax_nonce: responsiveElementorProSites._ajax_nonce,
				},
			})
			.fail(function (jqXHR) {
				console.log(jqXHR);
			})
			.done(function (response) {
				if (response.success) {
					if ('updated' === response.data) {
						$rst_elscope.find('#rst-sites-floating-notice-wrap-id').addClass('refreshed-notice').find('.rst-sites-floating-notice').html('<span class="message">' + responsiveElementorProSites.syncCompleteMessage + '</span><button type="button" class="notice-dismiss"><span class="screen-reader-text">' + responsiveElementorProSites.dismiss_text + '</span></button>');
						button.removeClass('updating-message');
						console.log('Already sync all the sites and blocks.');
					} else {

						// Get Blocks Count.
						$.ajax({
							url: responsiveElementorProSites.ajaxurl,
							type: 'POST',
							data: {
								action: 'rst-blocks-requests-count',
								_ajax_nonce: responsiveElementorProSites._ajax_nonce,
							},
							beforeSend: function () {
								console.groupCollapsed('Updating Blocks');
								console.log('Updating Blocks');
							},
						})
						.fail(function (jqXHR) {
							console.log(jqXHR, 'error');
							console.error(jqXHR.status + jqXHR.statusText, 'Blocks Count Request Failed!', jqXHR);
							console.groupEnd('Updating Blocks');
						})
						.done(function (response) {
							console.log(response);
							if (response.success) {
								var total = response.data;

								for (let i = 1; i <= total; i++) {
									ResponsiveSitesAjaxQueue.add({
										url: responsiveElementorProSites.ajaxurl,
										type: 'POST',
										data: {
											action: 'rst-import-blocks',
											page_no: i,
											_ajax_nonce: responsiveElementorProSites._ajax_nonce,
										},
										beforeSend: function () {
											console.groupCollapsed('Importing Blocks - Page ' + i);
											console.log('Importing Blocks - Page ' + i);
										},
										success: function (response) {
											console.log(response);
											console.groupEnd('Importing Blocks - Page ' + i);
										}
									});
								}
								// Run the AJAX queue.
								ResponsiveSitesAjaxQueue.run();
							} else {
								console.error(response.data, 'Blocks Count Request Failed!');
							}
						});

						// Get Sites Count.
						$.ajax({
							url: responsiveElementorProSites.ajaxurl,
							type: 'POST',
							data: {
								action: 'responsive-sites-get-sites-request-count',
								_ajax_nonce: responsiveElementorProSites._ajax_nonce,
							},
						})
						.fail(function (jqXHR) {
							console.log(jqXHR);
						})
						.done(function (response) {
							if (response.success) {
								var total = response.data;

								for (let i = 1; i <= total; i++) {
									ResponsiveSitesAjaxQueue.add({
										url: responsiveElementorProSites.ajaxurl,
										type: 'POST',
										data: {
											action: 'responsive-ready-sites-import-sites',
											page_no: i,
											_ajax_nonce: responsiveElementorProSites._ajax_nonce,
										},
										success: function (result) {

											if (i === total && responsiveElementorProSites.syncCompleteMessage) {

												$.ajax({
													url: responsiveElementorProSites.ajaxurl,
													type: 'POST',
													data: {
														action: 'responsive-ready-sites-update-templates-library-complete',
														_ajax_nonce: responsiveElementorProSites._ajax_nonce,
													},
												}).done(function (response) {
													button.removeClass('updating-message');
													$rst_elscope.find('#rst-sites-floating-notice-wrap-id').addClass('refreshed-notice').find('.rst-sites-floating-notice').html('<span class="message">' + responsiveElementorProSites.syncCompleteMessage + ' Please refresh the window to access templates.' + '</span><button type="button" class="notice-dismiss"><span class="screen-reader-text">' + responsiveElementorProSites.dismiss_text + '</span></button>');
												});
											}
										}
									});
								}

								// Run the AJAX queue.
								ResponsiveSitesAjaxQueue.run();
							}
						});
					}
				}
			});
		},


		_dismiss: function() {
			$(this).closest('.rst-sites-floating-notice-wrap').removeClass('slide-in');
			$(this).closest('.rst-sites-floating-notice-wrap').addClass('slide-out');

			setTimeout(function () {
				$(this).closest('.rst-sites-floating-notice-wrap').removeClass('slide-out');
			}, 200);

			$('#rst-sites-floating-notice-wrap-id').toggle();
		},

		_preview: function (e) {

			if (ResponsiveElementorSitesAdmin.processing) {
				return;
			}

			let step = $(this).attr('data-step');

			if ( 'pages' === ResponsiveElementorSitesAdmin.type && ( 1 == step || 'search' === step ) ) {
				ResponsiveElementorSitesAdmin.actual_site_id = $(this).closest('.responsive-theme').data('actual-site-id');
				ResponsiveElementorSitesAdmin.requiredPlugins = $(this).closest('.responsive-theme').data('required-plugins');
				ResponsiveElementorSitesAdmin.wpforms_path = $(this).closest('.responsive-theme').data('wpforms-path');
				ResponsiveElementorSitesAdmin.site_url = $(this).closest('.responsive-theme').data('site-url');
				ResponsiveElementorSitesAdmin.requiredProPlugins = $(this).closest('.responsive-theme').data('required-pro-plugins');
			}
			
			ResponsiveElementorSitesAdmin.site_id = $(this).closest('.responsive-theme').data('site-id');
			ResponsiveElementorSitesAdmin.page_id = $(this).closest('.responsive-theme').data('template-id');
			ResponsiveElementorSitesAdmin.block_id = $(this).closest('.responsive-theme').data('block-id');
			
			if ( 'search' === step ) {
				ResponsiveElementorSitesAdmin.page_id = $(this).closest('.responsive-theme').data('page-id');
				ResponsiveElementorSitesAdmin.step = 'search';
			}

			if ( 'pages' === ResponsiveElementorSitesAdmin.type ) {
				if ($(this).closest('.responsive-theme').hasClass('site-type-pro')) {
					ResponsiveElementorSitesAdmin.templateType = 'pro'
				} else {
					ResponsiveElementorSitesAdmin.templateType = 'free'
				}
			} else {
				ResponsiveElementorSitesAdmin.blockType = $(this).closest('.responsive-theme').data('demo-type')
			}

			$rst_elscope.find('.back-to-layout').css('visibility', 'visible');
			$rst_elscope.find('.back-to-layout').css('opacity', '1');

			$rst_elscope.find('.rst-template-library-toolbar').hide();
			$rst_elscope.find('.rst-sites-modal__header').removeClass('rst-preview-mode');

			if (1 == step) {
				$rst_elscope.find('.back-to-layout').attr('data-step', 2);
				$(document).trigger('responsive-sites__elementor-do-step-1');

			} else {
				$rst_elscope.find('.back-to-layout').attr('data-step', 3);
				$(document).trigger('responsive-sites__elementor-do-step-2');
			}
		},

		_pluginCheck: function (e, data) {

			if ('object' === typeof data) {
				if (undefined !== data) {
					if (ResponsiveElementorSitesAdmin.type == 'pages') {
						if (undefined !== ResponsiveElementorSitesAdmin.requiredPlugins) {
							ResponsiveElementorSitesAdmin._requiredPluginsMarkup(ResponsiveElementorSitesAdmin.requiredPlugins);
						}
					} else {
						if (undefined !== ResponsiveElementorSitesAdmin.requiredBlockPlugins) {
							ResponsiveElementorSitesAdmin._requiredPluginsMarkup(ResponsiveElementorSitesAdmin.requiredBlockPlugins);
						}
					}
				}
			}

		},

		_requiredPluginsMarkup: function (requiredPlugins) {

			if ('' === requiredPlugins) {
				return;
			}

			if (
				ResponsiveElementorSitesAdmin.type == 'pages' &&
				responsiveElementorProSites.default_page_builder_sites[ResponsiveElementorSitesAdmin.site_id]['demo_type'] != undefined &&
				responsiveElementorProSites.default_page_builder_sites[ResponsiveElementorSitesAdmin.site_id]['demo_type'] != 'free'
			) {

				if (!responsiveElementorProSites.license_status) {

					output = '<p class="rst-validate">' + responsiveElementorProSites.license_msg + '</p>';

					$rst_elscope.find('.required-plugins-list').html(output);
					$rst_elscope.find('.rst-tooltip-wrap').css('opacity', 1);
					$rst_elscope.find('.responsive-sites-tooltip').css('opacity', 1);

					/**
					 * Enable Demo Import Button
					 * @type number
					 */
					ResponsiveElementorSitesAdmin.requiredPlugins = [];
					ResponsiveElementorSitesAdmin.canImport = true;
					ResponsiveElementorSitesAdmin.canInsert = true;
					$rst_elscope.find('.responsive-sites-import-template-action > div').removeClass('disabled');
					return;
				}

			}

			if (
				ResponsiveElementorSitesAdmin.type == 'blocks' &&
				ResponsiveElementorSitesAdmin.demo_type != undefined &&
				ResponsiveElementorSitesAdmin.demo_type != 'free'
			) {

				if (!responsiveElementorProSites.license_status) {

					output = '<p class="rst-validate">' + responsiveElementorProSites.license_block_msg + '</p>';

					$rst_elscope.find('.required-plugins-list').html(output);
					$rst_elscope.find('.rst-tooltip-wrap').css('opacity', 1);
					$rst_elscope.find('.responsive-sites-tooltip').css('opacity', 1);

					/**
					 * Enable Demo Import Button
					 * @type number
					 */
					ResponsiveElementorSitesAdmin.requiredPlugins = [];
					ResponsiveElementorSitesAdmin.canImport = true;
					ResponsiveElementorSitesAdmin.canInsert = true;
					$rst_elscope.find('.responsive-sites-import-template-action > div').removeClass('disabled');
					return;
				}
			}

			// Required Required.
			$.ajax({
				url: responsiveElementorProSites.ajaxurl,
				type: 'POST',
				data: {
					action: 'responsive-ready-sites-required-plugins',
					_ajax_nonce: responsiveElementorProSites._ajax_nonce,
					required_plugins: requiredPlugins
				},
			})
				.fail(function (jqXHR) {
					console.groupEnd();
				})
				.done(function (response) {
					if (false === response.success) {

						$rst_elscope = $('#rst-sites-modal');
						$rst_elscope.find('#rst-sites-floating-notice-wrap-id').show().removeClass('error');
						$rst_elscope.find('#rst-sites-floating-notice-wrap-id .rst-sites-floating-notice').show().html('<span class="message">Insufficient Permission. Please contact your Super Admin to allow the install required plugin permissions.<span>');
						$rst_elscope.find('#rst-sites-floating-notice-wrap-id').addClass('error slide-in').removeClass('refreshed-notice');

					} else {
						var output = '';

						/**
						 * Count remaining plugins.
						 * @type number
						 */
						var remaining_plugins = 0;
						var required_plugins_markup = '';

						required_plugins = response.data['required_plugins'];

						if (response.data['required_plugins'].length) {
							$( response.data['required_plugins'] ).each(function (index, plugin) {
								output += '<li class="plugin-card plugin-card-' + plugin.slug + '" data-slug="' + plugin.slug + '" data-init="' + plugin.init + '" data-name="' + plugin.name + '">' + plugin.name + '</li>';
							});
						}

						/**
						 * Not Installed
						 *
						 * List of not installed required plugins.
						 */
						if (typeof required_plugins.notinstalled !== 'undefined') {

							// Add not have installed plugins count.
							remaining_plugins += parseInt(required_plugins.notinstalled.length);
							$(required_plugins.notinstalled).each(function (index, plugin) {
								if ('elementor' == plugin.slug) {
									return;
								}
								output += '<li class="plugin-card plugin-card-' + plugin.slug + '" data-slug="' + plugin.slug + '" data-init="' + plugin.init + '" data-name="' + plugin.name + '">' + plugin.name + '</li>';
							});
						}

						/**
						 * Inactive
						 *
						 * List of not inactive required plugins.
						 */
						if (typeof required_plugins.inactive !== 'undefined') {

							// Add inactive plugins count.
							remaining_plugins += parseInt(required_plugins.inactive.length);
							$(required_plugins.inactive).each(function (index, plugin) {
								if ('elementor' == plugin.slug) {
									return;
								}
								output += '<li class="plugin-card plugin-card-' + plugin.slug + '" data-slug="' + plugin.slug + '" data-init="' + plugin.init + '" data-name="' + plugin.name + '">' + plugin.name + '</li>';
							});
						}

						/**
						 * Active
						 *
						 * List of active required plugins.
						 */
						if (typeof required_plugins.active !== 'undefined') {
							
							$(required_plugins.active).each(function (index, plugin) {
								if ('elementor' == plugin.slug) {
									return;
								}
								output += '<li class="plugin-card plugin-card-' + plugin.slug + '" data-slug="' + plugin.slug + '" data-init="' + plugin.init + '" data-name="' + plugin.name + '">' + plugin.name + '</li>';
							});
						}

						/**
						 * Pro Plugins.
						 */
						if( 'pro' === ResponsiveElementorSitesAdmin.templateType ) {
							
							$(ResponsiveElementorSitesAdmin.requiredProPlugins).each(function (index, plugin) {
								if ('elementor' == plugin.slug) {
									return;
								}
								output += '<li class="plugin-card plugin-card-' + plugin.slug + '" data-slug="' + plugin.slug + '" data-init="' + plugin.init + '" data-name="' + plugin.name + '">' + plugin.name + '</li>';
							});
						}
						
						if ('' != output) {
							output = '<li class="plugin-card-head"><strong>' + responsiveElementorProSites.install_plugin_text + '</strong></li>' + output;
							$rst_elscope.find('.required-plugins-list').html(output);
							$rst_elscope.find('.rst-tooltip-wrap').css('opacity', 1);
							$rst_elscope.find('.responsive-sites-tooltip').css('opacity', 1);
						}

						if ( '' === output ) {
							output = '<li class="plugin-card-head"><strong>' + responsiveElementorProSites.noPlugins + '</strong></li>';
							$rst_elscope.find('.required-plugins-list').html(output);
							$rst_elscope.find('.plugin-card-head').css('border-bottom', 'none')
							$rst_elscope.find('.plugin-card-head').css('padding-bottom', '0')
							$rst_elscope.find('.rst-tooltip-wrap').css('opacity', 1);
							$rst_elscope.find('.responsive-sites-tooltip').css('opacity', 1);
						}


						/**
						 * Enable Demo Import Button
						 * @type number
						 */
						// ResponsiveElementorSitesAdmin.requiredPlugins = response.data['required_plugins'];
						ResponsiveElementorSitesAdmin.requiredPagePlugins = response.data['required_plugins'];
						ResponsiveElementorSitesAdmin.canImport = true;
						ResponsiveElementorSitesAdmin.canInsert = true;
						$rst_elscope.find('.responsive-sites-import-template-action > div').removeClass('disabled');
					}
				});
		},

		_step1: function (e) {
			
			if ('pages' == ResponsiveElementorSitesAdmin.type) {

				let passing_data = responsiveElementorProSites.default_page_builder_sites[ResponsiveElementorSitesAdmin.site_id]['pages'];

				var count = 0;
				var one_page = [];
				var one_page_id = '';

				for (key in passing_data) {
					count++;
					one_page = passing_data[key];
					one_page_id = passing_data[key]['page_id'];
				}

				if (count == 1) {
					// Logic for one page sites.
					ResponsiveElementorSitesAdmin.page_id = one_page_id;

					$rst_elscope.find('.back-to-layout').css('visibility', 'visible');
					$rst_elscope.find('.back-to-layout').css('opacity', '1');

					$rst_elscope.find('.back-to-layout').attr('data-step', 2);
					$(document).trigger('responsive-sites__elementor-do-step-2');

					return;
				}


				let single_template = wp.template('responsive-sites-list-search');
				passing_data['site_id'] = ResponsiveElementorSitesAdmin.site_id;
				pages_list = single_template(passing_data);
				$rst_elscope.find('.dialog-lightbox-content-block').hide();
				$rst_elscope.find('.responsive-sites-step-1-wrap').show();
				$rst_elscope.find('.responsive-preview-actions-wrap').remove();
				$rst_elscope.find('.theme-preview').hide();
				$rst_elscope.find('.theme-preview').html('');
				$rst_elscope.find('.rst-template-library-toolbar').show();
				$rst_elscope.find('.theme-preview-block').hide();
				$rst_elscope.find('.theme-preview-block').html('');
				$rst_elscope.find('.dialog-lightbox-content').show();
				$rst_elscope.find('.dialog-lightbox-content').html(pages_list);

				ResponsiveElementorSitesAdmin._loadLargeImages();

			} else {
				$rst_elscope.find('.dialog-lightbox-content').hide();
				$rst_elscope.find('.dialog-lightbox-content-block').hide();
				$rst_elscope.find('.dialog-lightbox-message').animate({ scrollTop: 0 }, 50);
				$rst_elscope.find('.theme-preview-block').show();
				$rst_elscope.find('.rst-template-library-toolbar').hide();
				$rst_elscope.find('.rst-sites-modal__header').addClass('rst-preview-mode');
				$rst_elscope.find('.theme-preview').hide();
				$rst_elscope.find('.theme-preview').html('');

				let import_template = wp.template('responsive-sites-elementor-preview');
				let import_template_header = wp.template('responsive-sites-elementor-preview-actions');

				let template_object = responsiveElementorProSites.default_page_builder_blocks.filter((e) => e.block_id == parseInt(ResponsiveElementorSitesAdmin.block_id) )

				preview_page_html = import_template(template_object[0]);

				$rst_elscope.find('.theme-preview-block').html(preview_page_html);
				$rst_elscope.find('.responsive-sites-step-1-wrap').hide();

				preview_action_html = import_template_header(template_object[0]);

				$rst_elscope.find('.elementor-templates-modal__header__items-area').append(preview_action_html);

				if ( $rst_elscope.find('.elementor-templates-modal__header__items-area .responsive-preview-actions-wrap').length > 1 ) {
					$rst_elscope.find('.elementor-templates-modal__header__items-area .responsive-preview-actions-wrap:not(:first)').remove();
				}
				
				ResponsiveElementorSitesAdmin._masonry();

				$(document).trigger('responsive-sites__elementor-plugin-check', { 'id': ResponsiveElementorSitesAdmin.block_id });
			}
		},

		_step2: function (e) {

			$rst_elscope.find('.dialog-lightbox-content').hide();
			$rst_elscope.find('.dialog-lightbox-message').animate({ scrollTop: 0 }, 50);
			$rst_elscope.find('.theme-preview').show();

			$rst_elscope.find('.rst-sites-modal__header').addClass('rst-preview-mode');

			if (undefined === ResponsiveElementorSitesAdmin.site_id) {
				return;
			}

			let import_template = wp.template('responsive-sites-elementor-preview');
			let import_template_header = wp.template('responsive-sites-elementor-preview-actions');

			let template_object = []

			if ( 'search' === ResponsiveElementorSitesAdmin.step ) {

				let pages = responsiveElementorProSites.default_page_builder_sites[ResponsiveElementorSitesAdmin.site_id]['pages']
				let page_index = pages.findIndex((e) => {return e.page_id == ResponsiveElementorSitesAdmin.page_id})
				template_object = responsiveElementorProSites.default_page_builder_sites[ResponsiveElementorSitesAdmin.site_id]['pages'][page_index];
			} else {
				template_object = responsiveElementorProSites.default_page_builder_sites[ResponsiveElementorSitesAdmin.site_id]['pages'][ResponsiveElementorSitesAdmin.page_id];
			}

			if ( template_object.pro_plugins.length > 0 ) {
				$('#rst-pro-template').addClass('rst-pro-template')
				$('#rst-pro-template').text('Pro')
			} else {
				$('#rst-pro-template').removeClass('rst-pro-template')
				$('#rst-pro-template').text('')
			}

			if (undefined === template_object) {
				return;
			}

			template_object['id'] = ResponsiveElementorSitesAdmin.site_id;

			preview_page_html = import_template(template_object);
			$rst_elscope.find('.theme-preview').html(preview_page_html);

			$rst_elscope.find('.responsive-sites-step-1-wrap').hide();

			preview_action_html = import_template_header(template_object);
			$rst_elscope.find('.elementor-templates-modal__header__items-area').append(preview_action_html);

			ResponsiveElementorSitesAdmin.page_id = template_object.page_id
			ResponsiveElementorSitesAdmin.step = undefined
			$(document).trigger('responsive-sites__elementor-plugin-check', { 'id': template_object.page_id });
		},

		_insert: function (e) {

			if (!ResponsiveElementorSitesAdmin.canInsert) {
				return;
			}

			ResponsiveElementorSitesAdmin.canInsert = false;

			$(this).addClass('installing');
			$(this).text('Importing... ');

			ResponsiveElementorSitesAdmin.action = 'insert';

			ResponsiveElementorSitesAdmin._bulkPluginInstallActivate();

		},

		_importTemplate: function() {
			if (!ResponsiveElementorSitesAdmin.canImport) {
				if ($(this).attr('data-demo-link') != undefined) {
					window.open($(this).attr('data-demo-link'), '_blank');
				}
				return;
			}

			ResponsiveElementorSitesAdmin.canImport = false;

			var str = (ResponsiveElementorSitesAdmin.type == 'pages') ? 'Template' : 'Block';

			$(this).addClass('installing');
			$(this).text('Saving ' + str + '...');

			ResponsiveElementorSitesAdmin.action = 'import';

			ResponsiveElementorSitesAdmin._bulkPluginInstallActivate();
		},

		_bulkPluginInstallActivate: function () {

			console.groupCollapsed('Bulk Plugin Install Process Started');

			// If has class the skip-plugins then,
			// Avoid installing 3rd party plugins.
			var not_installed = ResponsiveElementorSitesAdmin.requiredPagePlugins.notinstalled || '';
			var activate_plugins = ResponsiveElementorSitesAdmin.requiredPagePlugins.inactive || '';

			// First Install Bulk.
			if (not_installed.length > 0) {
				ResponsiveElementorSitesAdmin._installAllPlugins(not_installed);
			}

			// Second Activate Bulk.
			if (activate_plugins.length > 0) {
				ResponsiveElementorSitesAdmin._activateAllPlugins(activate_plugins);
			}

			if (activate_plugins.length <= 0 && not_installed.length <= 0) {
				ResponsiveElementorSitesAdmin._enableImport();
			}
		},

		/**
		 * Install All Plugins.
		 */
		 _installAllPlugins: function (not_installed) {

			$.each(not_installed, function (index, single_plugin) {

				// Add each plugin activate request in Ajax queue.
				// @see wp-admin/js/updates.js
				wp.updates.queue.push({
					action: 'install-plugin', // Required action.
					data: {
						slug: single_plugin.slug
					}
				});
			});

			// Required to set queue.
			wp.updates.queueChecker();
		},

		/**
		 * Activate All Plugins.
		 */
		 _activateAllPlugins: function (activate_plugins) {

			$.each(activate_plugins, function (index, single_plugin) {

				ResponsiveSitesAjaxQueue.add({
					url: responsiveElementorProSites.ajaxurl,
					type: 'POST',
					data: {
						'action': 'responsive-ready-sites-required-plugin-activate',
						'init': single_plugin.init,
						'_ajax_nonce': responsiveElementorProSites._ajax_nonce,
					},
					success: function (result) {

						if (result.success) {

							var pluginsList = ResponsiveElementorSitesAdmin.requiredPagePlugins.inactive;

							// Reset not installed plugins list.
							ResponsiveElementorSitesAdmin.requiredPlugins.inactive = ResponsiveElementorSitesAdmin._removePluginFromQueue(single_plugin.slug, pluginsList);

							// Enable Demo Import Button
							ResponsiveElementorSitesAdmin._enableImport();
						}
					}
				});
			});
			ResponsiveSitesAjaxQueue.run();
		},

		/**
		 * Remove plugin from the queue.
		 */
		 _removePluginFromQueue: function (removeItem, pluginsList) {
			return jQuery.grep(pluginsList, function (value) {
				return value.slug != removeItem;
			});
		},

		_enableImport: function () {

			console.groupEnd();
			let id = ResponsiveElementorSitesAdmin.block_id;
			let url = responsiveElementorProSites.blockSiteURL + 'pages/';
			if ( 'pages' == ResponsiveElementorSitesAdmin.type ) {
				id = ResponsiveElementorSitesAdmin.page_id
				url = ResponsiveElementorSitesAdmin.site_url + '/wp-json/wp/v2/pages/';
			}

			ResponsiveElementorSitesAdmin._importWPForm(ResponsiveElementorSitesAdmin.wpforms_path, function (form_response) {

				$.ajax({
					url: responsiveElementorProSites.ajaxurl,
					type: 'POST',
					data: {
						action: 'responsive-ready-sites-remote-request',
						url: url + id,
						_ajax_nonce: responsiveElementorProSites._ajax_nonce,
					},
					beforeSend: function () {
						console.groupCollapsed('Get Template Details.');
					},
				})
				.fail(function (jqXHR) {
					console.groupEnd();
				})
				.done(function (response) {
					console.groupEnd();

					if( response.success ) {
						ResponsiveElementorSitesAdmin.insertData = response.data;
						if ('insert' == ResponsiveElementorSitesAdmin.action) {
							ResponsiveElementorSitesAdmin._insertDemo(response.data);
						} else {
							ResponsiveElementorSitesAdmin._createTemplate(response.data);
						}
					}
				});
			});
		},

		_importWPForm: function (wpforms_url, callback) {

			if ( ! wpforms_url) {
				if (callback && typeof callback == "function") {
					callback('');
				}
				return;
			}

			$.ajax({
				url: responsiveElementorProSites.ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'responsive-ready-sites-import-wpforms',
					wpforms_path: wpforms_url,
					_ajax_nonce: responsiveElementorProSites._ajax_nonce,
				},
				beforeSend: function () {
					console.groupCollapsed('Importing WP Forms');
				},
			})
				.fail(function (jqXHR) {
					console.groupEnd();
				})
				.done(function (data) {

					// 1. Fail - Import WPForms Options.
					if (false === data.success) {
						console.groupEnd();
					} else {
						if (callback && typeof callback == "function") {
							callback(data);
						}
					}
				});
		},

		_createTemplate: function () {

			console.groupEnd();
			
			// Work with JSON page here
			$.ajax({
				url: responsiveElementorProSites.ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {
					'action': 'responsive-sites-create-template',
					'id' : (ResponsiveElementorSitesAdmin.type == 'pages') ? ResponsiveElementorSitesAdmin.page_id : ResponsiveElementorSitesAdmin.block_id,
					'title': ResponsiveElementorSitesAdmin.insertData?.title?.rendered || '',
					'type': ( 'blocks' == ResponsiveElementorSitesAdmin.type ) ? 'blocks' : 'pages',
					'_ajax_nonce': responsiveElementorProSites._ajax_nonce,
				},
				beforeSend: function () {
					console.groupCollapsed('Creating Template');
				}
			})
			.fail(function (jqXHR) {
				console.log(jqXHR);
			})
			.done(function (data) {
				ResponsiveElementorSitesAdmin._done(data);
			});
		},

		_done: function (data) {

			console.groupEnd('Process Done.');

			var str = (ResponsiveElementorSitesAdmin.type == 'pages') ? 'Template' : 'Block';
			$rst_elscope.find('.rst-import-elementor-template').removeClass('installing');
			$rst_elscope.find('.rst-import-elementor-template').attr('data-demo-link', data.data.link);
			setTimeout(function () {
				$rst_elscope.find('.rst-import-elementor-template').text('View Saved ' + str);
				$rst_elscope.find('.rst-import-elementor-template').addClass('action-done');
			}, 200);
		},

		_insertDemo: function (data) {

			if (undefined !== data && undefined !== data['post-meta']['_elementor_data']) {

				let templateModel = new Backbone.Model({
					getTitle() {
						return data['title']
					},
				});
				let page_content = JSON.parse(data['post-meta']['_elementor_data']);
				let page_settings = '';

				let api_url = responsiveElementorProSites.blockSiteURL + 'pages/' + ResponsiveElementorSitesAdmin.block_id;
				if ( 'pages' == ResponsiveElementorSitesAdmin.type ) {
					api_url = ResponsiveElementorSitesAdmin.site_url + '/wp-json/wp/v2/pages/' + ResponsiveElementorSitesAdmin.page_id;
				}

				$.ajax({
					url: responsiveElementorProSites.ajaxurl,
					type: 'POST',
					data: {
						action: 'responsive-ready-sites-elementor_page_import_process',
						id: elementor.config.document.id,
						url: api_url,
						_ajax_nonce: responsiveElementorProSites._ajax_nonce,
					},
					beforeSend: function () {
						console.groupCollapsed('Inserting Demo.');
					},
				})
					.fail(function (jqXHR) {
						console.groupEnd();
					})
					.done(function (response) {

						ResponsiveElementorSitesAdmin.processing = false;
						$rst_elscope.find('.responsive-sites-content-wrap').removeClass('processing');

						page_content = response.data;

						page_content = page_content.map(function (item) {
							item.id = Math.random().toString(36).substr(2, 7);
							return item;
						});

						console.groupEnd();
						if (undefined !== page_content && '' !== page_content) {

							if (undefined != $e && 'undefined' != typeof $e.internal) {

								elementor.channels.data.trigger('document/import', templateModel);

								elementor.getPreviewView().addChildModel(page_content, { at: ResponsiveElementorSitesAdmin.index } || {});

								elementor.channels.data.trigger('template:after:insert', {});

								$e.internal('document/save/set-is-modified', { status: true })
							} else {
								elementor.channels.data.trigger('document/import', templateModel);
								elementor.getPreviewView().addChildModel(page_content, { at: ResponsiveElementorSitesAdmin.index } || {});
								elementor.channels.data.trigger('template:after:insert', {});
								elementor.saver.setFlagEditorChange(true);
							}
						}
						ResponsiveElementorSitesAdmin.insertActionFlag = true;
						ResponsiveElementorSitesAdmin._close();
					});
			}
		},

		/**
		 * Installing Plugin
		 */
		 _pluginInstalling: function (event, args) {
			console.log('Installing Plugin - ' + args.slug);
		},

		/**
		 * Plugin Installation Error.
		 */
		 _installError: function (event, response) {
			console.log(response);
			console.log('Error Installing Plugin - ' + response.slug);
			console.log(response.errorMessage);
		},

		/**
		 * Install Success
		 */
		 _installSuccess: function (event, response) {

			event.preventDefault();

			// Transform the 'Install' button into an 'Activate' button.
			var $init = $('.plugin-card-' + response.slug).data('init');
			var $name = $('.plugin-card-' + response.slug).data('name');

			// Reset not installed plugins list.
			var pluginsList = ResponsiveElementorSitesAdmin.requiredPagePlugins.notinstalled;
			var curr_plugin = ResponsiveElementorSitesAdmin._getPluginFromQueue(response.slug, pluginsList);

			ResponsiveElementorSitesAdmin.requiredPagePlugins.notinstalled = ResponsiveElementorSitesAdmin._removePluginFromQueue(response.slug, pluginsList);


			// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
			setTimeout(function () {

				$.ajax({
					url: responsiveElementorProSites.ajaxurl,
					type: 'POST',
					data: {
						'action': 'responsive-ready-sites-required-plugin-activate',
						'init': curr_plugin.init,
						'_ajax_nonce': responsiveElementorProSites._ajax_nonce,
					},
				})
					.done(function (result) {

						if (result.success) {
							var pluginsList = ResponsiveElementorSitesAdmin.requiredPagePlugins.inactive;

							// Reset not installed plugins list.
							ResponsiveElementorSitesAdmin.requiredPagePlugins.inactive = ResponsiveElementorSitesAdmin._removePluginFromQueue(response.slug, pluginsList);

							// Enable Demo Import Button
							ResponsiveElementorSitesAdmin._enableImport();

						}
					});

			}, 1200);

		},

		/**
		 * Get plugin from the queue.
		 */
		_getPluginFromQueue: function (item, pluginsList) {

			var match = '';
			for (ind in pluginsList) {
				if (item == pluginsList[ind].slug) {
					match = pluginsList[ind];
				}
			}
			return match;
		},

		_changeType: function () {
			ResponsiveElementorSitesAdmin.siteType = $(this).val();
			$rst_elscope.find('#wp-filter-search-input').trigger('keyup');
		},

		_categoryChange: function () {
			ResponsiveElementorSitesAdmin.blockCategory = $(this).val();
			$rst_elscope.find('#wp-filter-search-input').trigger('keyup');
		},
		
		_blockColorChange: function() {
			ResponsiveElementorSitesAdmin.blockColor = $(this).val();
			$rst_elscope.find('#wp-filter-search-input').trigger('keyup');
		},

		_populate_block_color_filters: function() {
			let template = wp.template('rst-template-block-color-filters');
			let colorFilters = [ 'light', 'dark' ];
			$( '#elementor-template-block-color-filter' ).show().html( template( colorFilters ) );
		},

		_search: function () {

			let search_term = $(this).val() || '';
			search_term = search_term.toLowerCase();

			if ('pages' == ResponsiveElementorSitesAdmin.type) {

				var items = ResponsiveElementorSitesAdmin._getSearchedPages(search_term);

				if (search_term.length) {
					$(this).addClass('has-input');
					ResponsiveElementorSitesAdmin._addSites(items);
				} else {
					$(this).removeClass('has-input');
					ResponsiveElementorSitesAdmin._appendSites(responsiveElementorProSites.default_page_builder_sites);
				}
			} else {
				var items = ResponsiveElementorSitesAdmin._getSearchedBlocks(search_term);

				if (search_term.length) {
					$(this).addClass('has-input');
					ResponsiveElementorSitesAdmin._appendBlocks(items);
				} else {
					$(this).removeClass('has-input');
					ResponsiveElementorSitesAdmin._appendBlocks(responsiveElementorProSites.default_page_builder_blocks);
				}
			}
		},

		_getSearchedBlocks: function (search_term) {

			var items = [];

			if (search_term.length) {

				for (block_id in responsiveElementorProSites.default_page_builder_blocks) {

					var current_site = responsiveElementorProSites.default_page_builder_blocks[block_id];

					// Check in site title.
					if (current_site['title']['rendered']) {
						var site_title = ResponsiveElementorSitesAdmin._unescape_lower(current_site['title']['rendered']);

						if (site_title.toLowerCase().includes(search_term)) {
							items[block_id] = current_site;
							items[block_id]['type'] = 'site';
							items[block_id]['site_id'] = block_id;
						}
					}

				}
			}

			return items;
		},

		_getSearchedPages: function (search_term) {

			var items = [];
			search_term = search_term.toLowerCase();

			for (site_id in responsiveElementorProSites.default_page_builder_sites) {

				var current_site = responsiveElementorProSites.default_page_builder_sites[site_id];

				if ( current_site['allow_pages'] ) {

					// Check in page title.
					if (Object.keys(current_site['pages']).length) {
						var pages = current_site['pages'];
						for (page_id in pages) {
							// Check in site title.
							if (pages[page_id]['page_title']) {
								var page_title = ResponsiveElementorSitesAdmin._unescape_lower(pages[page_id]['page_title']);
								if (page_title.toLowerCase().includes(search_term)) {
									items[site_id] = pages[page_id];
									items[site_id]['type'] = current_site['demo_type'];
									items[site_id]['site_id'] = site_id;
									items[site_id]['actual_site_id'] = current_site['id'];
									items[site_id]['parent-site-name'] = current_site['title'] || '';
									items[site_id]['site_url'] = current_site['site_url'];
									items[site_id]['pages-count'] = 0;
									items[site_id]['required_plugins'] = current_site['required_plugins'];
									items[site_id]['required_pro_plugins'] = current_site['required_pro_plugins'];
									items[site_id]['wpforms_path'] = current_site['wpforms_path'];
								}
							}
						}
					}
				}
			}
			
			return items;
		},

		_unescape_lower: function (input_string) {
			input_string = $("<textarea/>").html(input_string).text()
			var input_string = ResponsiveElementorSitesAdmin._unescape(input_string);
			return input_string.toLowerCase();
		},

		_unescape: function (input_string) {
			var title = _.unescape(input_string);

			title = title.replace('&#8211;', '-');

			return title;
		},

		_addSites: function (data) {

			if (data) {
				let single_template = wp.template('responsive-sites-search');
				pages_list = single_template(data);
				$rst_elscope.find('.dialog-lightbox-content').html(pages_list);
				ResponsiveElementorSitesAdmin._loadLargeImages();

			} else {
				$rst_elscope.find('.dialog-lightbox-content').html(wp.template('responsive-sites-no-sites'));
			}
		},

	};

	/**
	 * Initialize ResponsiveElementorSitesAdmin
	 */
	$(function () {
		ResponsiveElementorSitesAdmin.init();
	});

})(jQuery);