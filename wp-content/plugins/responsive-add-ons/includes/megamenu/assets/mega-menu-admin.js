import React, { useState, useEffect, useRef } from "react";
import ReactDOM from 'react-dom';
const { TextControl, Button, ButtonGroup, ToggleControl, SelectControl, RangeControl, ColorPalette, TextareaControl } = wp.components;
const { __ } = wp.i18n;
import apiFetch from '@wordpress/api-fetch';

const App = ({ id, depth, menuTitle }) => {
	const [isLoading, setIsLoading] = useState(true)
	const [options, setOptions] = useState({})
	const [tab, setTab] = useState('general');
	const [storeSettings, setStoreSettings] = useState(__('Save Changes', 'responsive-addons-pro'))
	const modalBodyRef = useRef(null);
	const url = 'responsive_pro/v1/rpro_mega_menu'
	const colors = [
		{ name: __('red','responsive-addons-pro'), color: '#f00' },
		{ name: __('white','responsive-addons-pro'), color: '#fff' },
		{ name: __('blue','responsive-addons-pro'), color: '#00f' },
	];

	const openMediaLibrary = (uploadState) => {
		const { wp } = window;
		if (!wp || !wp.media) {
			return;
		}

		const mediaLibrary = wp.media({
			title: __('Select or Upload Media', 'responsive-addons-pro'),
			button: {
				text: __('Select','responsive-addons-pro'),
			},
			multiple: false, // Set this to true if you want to allow multiple selections
		});

		// Callback for when an image is selected
		mediaLibrary.on('select', () => {
			const attachment = mediaLibrary.state().get('selection').first().toJSON();

			if ('resp_menu_item_icon_type_upload' === uploadState) {
				setOptions(prevOptions => ({
					...prevOptions,
					resp_menu_item_icon_type_upload: {
						...prevOptions.resp_menu_item_icon_type_upload,
						value: attachment.url,
					},
				}));
			}
			if ('resp_background_image' === uploadState) {
				setOptions(prevOptions => ({
					...prevOptions,
					resp_background_image: {
						...prevOptions.resp_background_image,
						value: attachment.url,
					},
				}));
			}
			if ('resp_submenu_item_icon_type_upload' === uploadState) {
				setOptions(prevOptions => ({
					...prevOptions,
					resp_submenu_item_icon_type_upload: {
						...prevOptions.resp_submenu_item_icon_type_upload,
						value: attachment.url,
					},
				}));
			}
		});

		// Open the media library
		mediaLibrary.open();
	};

	useEffect(() => {
		setIsLoading(true);
		apiFetch({
			path: `${url}/${id}`,
		}).then(data => {
			setOptions(data)
			setIsLoading(false)
		})
		if (modalBodyRef.current && modalBodyRef.current.scrollTop > 0) {
			modalBodyRef.current.scrollTop = 0;
		}
	}, [id]);

	function handleModalClose() {
		let closemodal = document.getElementById('responsive-pro-modal-app')
		closemodal.style.display = 'none'
		setTab('general')
	}

	const saveSettings = () => {
		setStoreSettings(__('Saving Settings...', 'responsive-addons-pro'))
		apiFetch({
			path: url,
			method: "POST",
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': rpro_megamenu.nonce,
			},
			body: JSON.stringify({ id, options })
		}).then(data => {
			setStoreSettings('Settings Saved');
			setTimeout(() => {
				setStoreSettings('Save Changes');
			}, 1500);
		})
	}

	const handleSubmit = () => {
		saveSettings()
	}

	return (
		<div>
			<div className="resp-pro-megamenu-modal-content">
				{isLoading && <p>Loading....</p>}
				{!isLoading && <>
					<div className="resp-pro-megamenu-modal-header">
						<span className="resp-pro-megamenu-modal-close" onClick={handleModalClose} >&times;</span>
						<div className="heading-items-wrapper">
							<div className="heading-items-wrapper-item heading-item-megamenu-settings">
								<p className="megamenu-header-heading no-margin-top no-margin-bottom">{__('Mega Menu Settings', 'responsive-pro')}</p>
								<p className="megamenu-header-desc no-margin-top no-margin-bottom">{__('Editing:', 'responsive-addons-pro')} <span id="resp-megamenu-page-title">{menuTitle}</span></p>
							</div>
							<div className={`heading-items-wrapper-item heading-item-megamenu-general-settings ${'general' === tab ? 'active-settings-tab' : ''} pointer-cursor`} onClick={() => { setTab('general') }}>
								<p className="megamenu-header-heading">{__('General Settings', 'responsive-addons-pro')}</p>
							</div>
							<div className={`heading-items-wrapper-item heading-item-megamenu-design-settings ${'design' === tab ? 'active-settings-tab' : ''} pointer-cursor`} onClick={() => setTab('design')}>
								<p className="megamenu-header-heading">{__('Design', 'responsive-addons-pro')}</p>
							</div>
						</div>
					</div>
					<div className="resp-pro-megamenu-modal-body" ref={modalBodyRef}>
						<div id="resp-megamenu-general-tab-section" className="" style={'general' === tab ? { display: 'block' } : { display: 'none' }}>
							{'general' === tab &&
								<>
									{/* General Main Menu */}
									{parseInt(depth) === 0 &&
										<div className="resp-pro-megamenu-depth-0">
											<div className="resp-pro-megamenu-setting-general align-items-baseline resp-pro-megamenu-setting-margin-top">
												<p className="resp-pro-megamenu-setting-heading resp-pro-megamenu-setting-heading-with-control">{__('Enable Mega menu','responsive-addons-pro')}</p>
												<ToggleControl
													checked={options.resp_enable_megamenu.value == '' ? false : true}
													onChange={(event) => {
														setOptions(prevOptions => ({
															...prevOptions,
															resp_enable_megamenu: {
																...prevOptions.resp_enable_megamenu,
																value: event,
															},
														}));
													}}
												/>
											</div>
											{options.resp_enable_megamenu.value == true &&
												<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
													<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Mega Menu Width', 'responsive-addons-pro')}</p>
													<SelectControl
														value={options.resp_megamenu_width.value}
														options={[
															{ label: __('Content','responsive-addons-pro'), value: 'content' },
															{ label: __('Menu Container Width','responsive-addons-pro'), value: 'menu-container' },
															{ label: __('Full Width','responsive-addons-pro'), value: 'full' },
															{ label: __('Full Width Stretched','responsive-addons-pro'), value: 'full-stretched' },
															{ label: __('Custom Width','responsive-addons-pro'), value: 'custom' },
														]}
														onChange={(event) => {
															setOptions(prevOptions => ({
																...prevOptions,
																resp_megamenu_width: {
																	...prevOptions.resp_megamenu_width,
																	value: event,
																},
															}));
														}}
														__nextHasNoMarginBottom
													/>
												</div>
											}
											{options.resp_megamenu_width.value == 'custom' &&
												<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
													<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Custom Width (px)','responsive-addons-pro')}</p>
													<RangeControl
														value={options.resp_megamenu_custom_width.value == '' ? options.resp_megamenu_custom_width.default_value : options.resp_megamenu_custom_width.value}
														min={1}
														max={1920}
														onChange={(event) => {
															setOptions(prevOptions => ({
																...prevOptions,
																resp_megamenu_custom_width: {
																	...prevOptions.resp_megamenu_custom_width,
																	value: event,
																},
															}));
														}}
														allowReset={true}
														resetFallbackValue={options.resp_megamenu_custom_width.default_value}
													/>
												</div>
											}
											<hr className="resp-pro-megamenu-hr" />
											<p className="resp-pro-megamenu-setting-heading resp-pro-megamenu-setting-margin-top">{__('Menu Item Icon','responsive-addons-pro')}</p>
											<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
												<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Icon','responsive-addons-pro')}</p>
												<ButtonGroup>
													<Button icon={'remove'} className={options.resp_menu_item_icon_type.value == 'none' ? 'is-pressed' : ''}
														onClick={() => {
															setOptions(prevOptions => ({
																...prevOptions,
																resp_menu_item_icon_type: {
																	...prevOptions.resp_menu_item_icon_type,
																	value: 'none',
																},
															}));
														}}
													></Button>
													<Button icon={'upload'} className={options.resp_menu_item_icon_type.value == 'upload' ? 'is-pressed' : ''}
														onClick={() => {
															setOptions(prevOptions => ({
																...prevOptions,
																resp_menu_item_icon_type: {
																	...prevOptions.resp_menu_item_icon_type,
																	value: 'upload',
																},
															}));
														}}
													></Button>
												</ButtonGroup>
											</div>
											{options.resp_menu_item_icon_type.value == 'upload' &&
												<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
													<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Upload Icon','responsive-addons-pro')}</p>
													<div className="resp-pro-megamenu-upload-wrapper">
														<Button variant="secondary" onClick={() => openMediaLibrary('resp_menu_item_icon_type_upload')}>{__('Select Icon', 'responsive-addons-pro')}</Button>
														{options.resp_menu_item_icon_type_upload.value != '' && (
															<div>
																<img style={{ marginTop: "10px", marginBottom: "10px" }} width="200" src={options.resp_menu_item_icon_type_upload.value} alt="Highlight Label Icon" />
																<Button className="display-block" variant="secondary" onClick={() => {
																	setOptions(prevOptions => ({
																		...prevOptions,
																		resp_menu_item_icon_type_upload: {
																			...prevOptions.resp_menu_item_icon_type_upload,
																			value: '',
																		},
																	}))
																}}>{__('Remove Icon', 'responsive-addons-pro')}</Button>
															</div>
														)}
													</div>
												</div>
											}
											{options.resp_menu_item_icon_type.value != 'none' &&
												<>
													<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
														<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Icon Position', 'responsive-addons-pro')}</p>
														<ButtonGroup>
															<Button icon={'editor-alignleft'} className={options.resp_menu_item_icon_position.value != '' && options.resp_menu_item_icon_position.value != 'right' ? 'is-pressed' : ''}
																onClick={() => {
																	setOptions(prevOptions => ({
																		...prevOptions,
																		resp_menu_item_icon_position: {
																			...prevOptions.resp_menu_item_icon_position,
																			value: 'left',
																		},
																	}));
																}}
															></Button>
															<Button icon={'editor-alignright'} className={options.resp_menu_item_icon_position.value != '' && options.resp_menu_item_icon_position.value != 'left' ? 'is-pressed' : ''}
																onClick={() => {
																	setOptions(prevOptions => ({
																		...prevOptions,
																		resp_menu_item_icon_position: {
																			...prevOptions.resp_menu_item_icon_position,
																			value: 'right',
																		},
																	}));
																}}></Button>
														</ButtonGroup>
													</div>
													<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
														<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Icon Size (px)', 'responsive-addons-pro')}</p>
														<RangeControl
															value={parseInt(options.resp_menu_item_icon_size.value)}
															min={1}
															max={100}
															onChange={(event) => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_menu_item_icon_size: {
																		...prevOptions.resp_menu_item_icon_size,
																		value: event,
																	},
																}));
															}}
															allowReset={true}
															resetFallbackValue={20}
														/>
													</div>
													<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
														<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Icon Spacing (px)', 'responsive-addons-pro')}</p>
														<RangeControl
															value={parseInt(options.resp_menu_item_icon_spacing.value)}
															min={1}
															max={100}
															onChange={(event) => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_menu_item_icon_spacing: {
																		...prevOptions.resp_menu_item_icon_spacing,
																		value: event,
																	},
																}));
															}}
															allowReset={true}
															resetFallbackValue={5}
														/>
													</div>
												</>
											}
											<hr className="resp-pro-megamenu-hr" />
											<p className="resp-pro-megamenu-setting-heading resp-pro-megamenu-setting-margin-top">{__('Highlight Labels', 'responsive-addons-pro')}</p>
											<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
												<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Highlight Label', 'responsive-addons-pro')}</p>
												<TextControl
													value={options.resp_highlight_label.value}
													autoComplete='off'
													onChange={(event) => {
														setOptions(prevOptions => ({
															...prevOptions,
															resp_highlight_label: {
																...prevOptions.resp_highlight_label,
																value: event,
															},
														}));
													}}
												/>
											</div>
										</div>
									}
									{/* General SubMenu */}
									{parseInt(depth) > 0 &&
										<>
											<div className="resp-pro-megamenu-depth-0">
												<div className="resp-pro-megamenu-setting-general align-items-baseline resp-pro-megamenu-setting-margin-top">
													<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Set as Heading', 'responsive-addons-pro')}</p>
													<ToggleControl
														checked={options.resp_set_heading.value == '' ? false : true}
														onChange={(event) => {
															setOptions(prevOptions => ({
																...prevOptions,
																resp_set_heading: {
																	...prevOptions.resp_set_heading,
																	value: event,
																},
															}));
														}}
													/>
												</div>
												<div className="resp-pro-megamenu-setting-general align-items-baseline resp-pro-megamenu-setting-margin-top">
													<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Hide Menu Label', 'responsive-addons-pro')}</p>
													<ToggleControl
														checked={options.resp_hide_menu_label.value == '' ? false : true}
														onChange={(event) => {
															setOptions(prevOptions => ({
																...prevOptions,
																resp_hide_menu_label: {
																	...prevOptions.resp_hide_menu_label,
																	value: event,
																},
															}));
														}}
													/>
												</div>
												{!options.resp_hide_menu_label.value &&
													<div className="resp-pro-megamenu-setting-general align-items-baseline resp-pro-megamenu-setting-margin-top">
														<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Disable Link','responsive-addons-pro')}</p>
														<ToggleControl
															checked={options.resp_disable_link.value == '' ? false : true}
															onChange={(event) => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_disable_link: {
																		...prevOptions.resp_disable_link,
																		value: event,
																	},
																}));
															}}
														/>
													</div>
												}
												<hr className="resp-pro-megamenu-hr" />
												<p className="resp-pro-megamenu-setting-heading resp-pro-megamenu-setting-margin-top">{__('Menu Item Icon', 'responsive-addons-pro')}</p>
												<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
													<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Icon', 'responsive-addons-pro')}</p>
													<ButtonGroup>
														<Button icon={'remove'} className={options.resp_submenu_item_icon_type.value == 'none' ? 'is-pressed' : ''}
															onClick={() => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_submenu_item_icon_type: {
																		...prevOptions.resp_submenu_item_icon_type,
																		value: 'none',
																	},
																}));
															}}
														></Button>
														<Button icon={'upload'} className={options.resp_submenu_item_icon_type.value == 'upload' ? 'is-pressed' : ''}
															onClick={() => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_submenu_item_icon_type: {
																		...prevOptions.resp_submenu_item_icon_type,
																		value: 'upload',
																	},
																}));
															}}
														></Button>
													</ButtonGroup>
												</div>
												
												{options.resp_submenu_item_icon_type.value == 'upload' &&
													<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
														<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Upload Icon', 'responsive-addons-pro')}</p>
														<div className="resp-pro-megamenu-upload-wrapper">
															<Button variant="secondary" onClick={() => openMediaLibrary('resp_submenu_item_icon_type_upload')}>{__('Select Icon', 'responsive-addons-pro')}</Button>
															{options.resp_submenu_item_icon_type_upload.value != '' && (
																<div>
																	<img style={{ marginTop: "10px", marginBottom: "10px" }} width="200" src={options.resp_submenu_item_icon_type_upload.value} alt="SubMenu Item Icon" />
																	<Button className="display-block" variant="secondary" onClick={() => {
																		setOptions(prevOptions => ({
																			...prevOptions,
																			resp_submenu_item_icon_type_upload: {
																				...prevOptions.resp_submenu_item_icon_type_upload,
																				value: '',
																			},
																		}))
																	}}>{__('Remove Icon', 'responsive-addons-pro')}</Button>
																</div>
															)}
														</div>
													</div>
												}
												{options.resp_submenu_item_icon_type.value != 'none' &&
													<>
														<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
															<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Icon Position', 'responsive-addons-pro')}</p>
															<ButtonGroup>
																<Button icon={'editor-alignleft'} className={options.resp_submenu_item_icon_position.value != '' && options.resp_submenu_item_icon_position.value != 'right' ? 'is-pressed' : ''}
																	onClick={() => {
																		setOptions(prevOptions => ({
																			...prevOptions,
																			resp_submenu_item_icon_position: {
																				...prevOptions.resp_submenu_item_icon_position,
																				value: 'left',
																			},
																		}));
																	}}
																></Button>
																<Button icon={'editor-alignright'} className={options.resp_submenu_item_icon_position.value != '' && options.resp_submenu_item_icon_position.value != 'left' ? 'is-pressed' : ''}
																	onClick={() => {
																		setOptions(prevOptions => ({
																			...prevOptions,
																			resp_submenu_item_icon_position: {
																				...prevOptions.resp_submenu_item_icon_position,
																				value: 'right',
																			},
																		}));
																	}}></Button>
															</ButtonGroup>
														</div>
														<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
															<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Icon Size (px)', 'responsive-addons-pro')}</p>
															<RangeControl
																value={parseInt(options.resp_submenu_item_icon_size.value)}
																min={1}
																max={100}
																onChange={(event) => {
																	setOptions(prevOptions => ({
																		...prevOptions,
																		resp_submenu_item_icon_size: {
																			...prevOptions.resp_submenu_item_icon_size,
																			value: event,
																		},
																	}));
																}}
																allowReset={true}
																resetFallbackValue={20}
															/>
														</div>
														<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
															<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Icon Spacing (px)', 'responsive-addons-pro')}</p>
															<RangeControl
																value={parseInt(options.resp_submenu_item_icon_spacing.value)}
																min={1}
																max={100}
																onChange={(event) => {
																	setOptions(prevOptions => ({
																		...prevOptions,
																		resp_submenu_item_icon_spacing: {
																			...prevOptions.resp_submenu_item_icon_spacing,
																			value: event,
																		},
																	}));
																}}
																allowReset={true}
																resetFallbackValue={5}
															/>
														</div>
													</>
												}
												<hr className="resp-pro-megamenu-hr" />
												<p className="resp-pro-megamenu-setting-heading resp-pro-megamenu-setting-margin-top">{__('Content Source', 'responsive-addons-pro')}</p>
												<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
													<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Content Source', 'responsive-addons-pro')}</p>
													<SelectControl
														value={options.resp_content_source.value}
														options={[
															{ label: __('Default', 'responsive-addons-pro'), value: 'default' },
															{ label: __('Custom Text', 'responsive-addons-pro'), value: 'custom_text' },
														]}
														onChange={(event) => {
															setOptions(prevOptions => ({
																...prevOptions,
																resp_content_source: {
																	...prevOptions.resp_content_source,
																	value: event,
																},
															}));
														}}
														__nextHasNoMarginBottom
													/>
												</div>
												{options.resp_content_source.value == 'custom_text' &&
													<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
														<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Custom Text', 'responsive-addons-pro')}</p>
														<TextareaControl
															value={options.resp_custom_text.value}
															onChange={(event) => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_custom_text: {
																		...prevOptions.resp_custom_text,
																		value: event,
																	},
																}));
															}}
														/>
													</div>
												}
												<hr className="resp-pro-megamenu-hr" />
												<p className="resp-pro-megamenu-setting-heading resp-pro-megamenu-setting-margin-top">{__('Highlight Labels', 'responsive-addons-pro')}</p>
												<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
													<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Highlight Label', 'responsive-addons-pro')}</p>
													<TextControl
														value={options.resp_submenu_highlight_label.value}
														autoComplete='off'
														onChange={(event) => {
															setOptions(prevOptions => ({
																...prevOptions,
																resp_submenu_highlight_label: {
																	...prevOptions.resp_submenu_highlight_label,
																	value: event,
																},
															}));
														}}
													/>
												</div>
											</div>
										</>
									}
								</>
							}
						</div>
						<div id="resp-megamenu-design-tab-section" className="" style={'design' === tab ? { display: 'block' } : { display: 'none' }}>
							{'design' === tab &&
								<>
									{parseInt(depth) === 0 &&
										<>
											<div className="resp-pro-megamenu-depth-0">
												{options.resp_enable_megamenu.value && <>
													<p className="resp-pro-megamenu-setting-heading resp-pro-megamenu-setting-margin-top">{__('Background Type', 'responsive-addons-pro')}</p>
													<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
														<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Color', 'responsive-addons-pro')}</p>
														<ColorPalette
															colors={colors}
															value={options.resp_background_color.value == '' ? options.resp_background_color.default_value : options.resp_background_color.value}
															onChange={(event) => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_background_color: {
																		...prevOptions.resp_background_color,
																		value: event,
																	},
																}));
															}}
															enableAlpha={true}
														/>
													</div>
													<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
														<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Background Image', 'responsive-addons-pro')}</p>
														<div className="resp-pro-megamenu-upload-wrapper">
															<Button variant="secondary" onClick={() => openMediaLibrary('resp_background_image')}>{__('Select Image', 'responsive-addons-pro')}</Button>
															{options.resp_background_image.value != 'none' && (
																<div>
																	<img style={{ marginTop: "10px", marginBottom: "10px" }} width="200" src={options.resp_background_image.value} alt="Background Image" />
																	<Button className="display-block" variant="secondary" onClick={() => {
																		setOptions(prevOptions => ({
																			...prevOptions,
																			resp_background_image: {
																				...prevOptions.resp_background_image,
																				value: 'none',
																			},
																		}))
																	}}>{__('Remove Image', 'responsive-addons-pro')}</Button>
																</div>
															)}
														</div>
													</div>
													<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
														<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Image Position', 'responsive-addons-pro')}</p>
														<SelectControl
															value={options.resp_background_image_position.value == '' ? options.resp_background_image_position.default_value : options.resp_background_image_position.value}
															options={[
																{ label: __('Left Top', 'responsive-addons-pro'), value: 'left top' },
																{ label: __('Left Center', 'responsive-addons-pro'), value: 'left center' },
																{ label: __('Left Bottom', 'responsive-addons-pro'), value: 'left bottom' },
																{ label: __('Right Top', 'responsive-addons-pro'), value: 'right top' },
																{ label: __('Right Center', 'responsive-addons-pro'), value: 'right center' },
																{ label: __('Right Bottom', 'responsive-addons-pro'), value: 'right bottom' },
																{ label: __('Center Top', 'responsive-addons-pro'), value: 'center top' },
																{ label: __('Center Center', 'responsive-addons-pro'), value: 'center center' },
																{ label: __('Center Bottom', 'responsive-addons-pro'), value: 'center bottom' },
															]}
															onChange={(event) => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_background_image_position: {
																		...prevOptions.resp_background_image_position,
																		value: event,
																	},
																}));
															}}
															__nextHasNoMarginBottom
														/>
													</div>
													<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
														<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Size', 'responsive-addons-pro')}</p>
														<SelectControl
															value={options.resp_background_image_size.value == '' ? options.resp_background_image_size.default_value : options.resp_background_image_size.value}
															options={[
																{ label: __('Auto', 'responsive-addons-pro'), value: 'auto' },
																{ label: __('Cover', 'responsive-addons-pro'), value: 'cover' },
																{ label: __('Contain', 'responsive-addons-pro'), value: 'contain' },
															]}
															onChange={(event) => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_background_image_size: {
																		...prevOptions.resp_background_image_size,
																		value: event,
																	},
																}));
															}}
															__nextHasNoMarginBottom
														/>
													</div>
													<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
														<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Repeat', 'responsive-addons-pro')}</p>
														<SelectControl
															value={options.resp_background_image_repeat.value == '' ? options.resp_background_image_repeat.default_value : options.resp_background_image_repeat.value}
															options={[
																{ label: __('No Repeat', 'responsive-addons-pro'), value: 'no-repeat' },
																{ label: __('Repeat Horizontally', 'responsive-addons-pro'), value: 'repeat-x' },
																{ label: __('Repeat Vertically', 'responsive-addons-pro'), value: 'repeat-y' },
															]}
															onChange={(event) => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_background_image_repeat: {
																		...prevOptions.resp_background_image_repeat,
																		value: event,
																	},
																}));
															}}
															__nextHasNoMarginBottom
														/>
													</div>
													<hr className="resp-pro-megamenu-hr" />
													<p className="resp-pro-megamenu-setting-heading resp-pro-megamenu-setting-margin-top">{__('Colors', 'responsive-addons-pro')}</p>
													<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
														<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Text/Link', 'responsive-addons-pro')}</p>
														<ColorPalette
															colors={colors}
															value={options.resp_color_text_or_link.value}
															onChange={(event) => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_color_text_or_link: {
																		...prevOptions.resp_color_text_or_link,
																		value: event,
																	},
																}));
															}}
															enableAlpha={true}
														/>
													</div>
													<hr className="resp-pro-megamenu-hr" />
												</>
												}
												<p className="resp-pro-megamenu-setting-heading resp-pro-megamenu-setting-margin-top">{__('Highlight Labels', 'responsive-addons-pro')}</p>
												<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
													<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Label Position', 'responsive-addons-pro')}</p>
													<ButtonGroup>
														<Button icon={'editor-alignleft'} className={options.resp_highlight_position.value != '' && options.resp_highlight_position.value != 'right' ? 'is-pressed' : ''}
															onClick={() => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_highlight_position: {
																		...prevOptions.resp_highlight_position,
																		value: 'left',
																	},
																}));
															}}
														></Button>
														<Button icon={'editor-alignright'} className={options.resp_highlight_position.value != '' && options.resp_highlight_position.value != 'left' ? 'is-pressed' : ''}
															onClick={() => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_highlight_position: {
																		...prevOptions.resp_highlight_position,
																		value: 'right',
																	},
																}));
															}}></Button>
													</ButtonGroup>
												</div>
												<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
													<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Label Text Color', 'responsive-addons-pro')}</p>
													<ColorPalette
														colors={colors}
														value={options.resp_highlight_color.value}
														onChange={(event) => {
															setOptions(prevOptions => ({
																...prevOptions,
																resp_highlight_color: {
																	...prevOptions.resp_highlight_color,
																	value: event,
																},
															}));
														}}
														enableAlpha={true}
													/>
												</div>
												<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
													<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Background Color', 'responsive-addons-pro')}</p>
													<ColorPalette
														colors={colors}
														value={options.resp_highlight_bg_color.value}
														onChange={(event) => {
															setOptions(prevOptions => ({
																...prevOptions,
																resp_highlight_bg_color: {
																	...prevOptions.resp_highlight_bg_color,
																	value: event,
																},
															}));
														}}
														enableAlpha={true}
													/>
												</div>
											</div>
										</>
									}
									{/* Design SubMenu */}
									{parseInt(depth) > 0 &&
										<>
											<div className="resp-pro-megamenu-depth-0">
												{ options.resp_set_heading.value == true &&
													<>
														<p className="resp-pro-megamenu-setting-heading resp-pro-megamenu-setting-margin-top">{__('MegaMenu', 'responsive-addons-pro')}</p>
														<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
															<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Separator Color', 'responsive-addons-pro')}</p>
															<ColorPalette
																colors={colors}
																value={options.resp_set_heading_separator_color.value}
																onChange={(event) => {
																	setOptions(prevOptions => ({
																		...prevOptions,
																		resp_set_heading_separator_color: {
																			...prevOptions.resp_set_heading_separator_color,
																			value: event,
																		},
																	}));
																}}
																enableAlpha={true}
															/>
														</div>
														<hr className="resp-pro-megamenu-hr" />
													</>
												}
												<p className="resp-pro-megamenu-setting-heading resp-pro-megamenu-setting-margin-top">{__('Highlight Labels', 'responsive-addons-pro')}</p>
												<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
													<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Label Position', 'responsive-addons-pro')}</p>
													<ButtonGroup>
														<Button icon={'editor-alignleft'} className={options.resp_submenu_highlight_label_position.value != '' && options.resp_submenu_highlight_label_position.value != 'right' ? 'is-pressed' : ''}
															onClick={() => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_submenu_highlight_label_position: {
																		...prevOptions.resp_submenu_highlight_label_position,
																		value: 'left',
																	},
																}));
															}}
														></Button>
														<Button icon={'editor-alignright'} className={options.resp_submenu_highlight_label_position.value != '' && options.resp_submenu_highlight_label_position.value != 'left' ? 'is-pressed' : ''}
															onClick={() => {
																setOptions(prevOptions => ({
																	...prevOptions,
																	resp_submenu_highlight_label_position: {
																		...prevOptions.resp_submenu_highlight_label_position,
																		value: 'right',
																	},
																}));
															}}></Button>
													</ButtonGroup>
												</div>
												<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
													<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Label Text Color', 'responsive-addons-pro')}</p>
													<ColorPalette
														colors={colors}
														value={options.resp_submenu_highlight_label_color.value}
														onChange={(event) => {
															setOptions(prevOptions => ({
																...prevOptions,
																resp_submenu_highlight_label_color: {
																	...prevOptions.resp_submenu_highlight_label_color,
																	value: event,
																},
															}));
														}}
														enableAlpha={true}
													/>
												</div>
												<div className="resp-pro-megamenu-setting-general align-items-flex-start resp-pro-megamenu-setting-margin-top">
													<p className="resp-pro-megamenu-setting-subheading resp-pro-megamenu-setting-heading-with-control">{__('Background Color', 'responsive-addons-pro')}</p>
													<ColorPalette
														colors={colors}
														value={options.resp_submenu_highlight_label_bg_color.value}
														onChange={(event) => {
															setOptions(prevOptions => ({
																...prevOptions,
																resp_submenu_highlight_label_bg_color: {
																	...prevOptions.resp_submenu_highlight_label_bg_color,
																	value: event,
																},
															}));
														}}
														enableAlpha={true}
													/>
												</div>
											</div>
										</>
									}
								</>
							}
						</div>
					</div>
					<div className="resp-pro-megamenu-modal-footer">
						<button onClick={handleSubmit} className="button button-primary">
							{storeSettings == 'Settings Saved' ? <><span className="dashicons dashicons-saved"></span> </> : '' }
							{storeSettings}
						</button>
					</div>
				</>}
			</div>
		</div>
	);
}

var $ = jQuery.noConflict()
$(document).ready(function () {
	// Get the modal
	var modal = $(".resp-pro-megamenu-modal");

	// Get the button that opens the modal
	var btn = $(".resp-pro-megamenu-button");

	// When the user clicks on the button, open the modal
	btn.click(function (event) {
		modal.css("display", "block");
		modal.attr('data-depth', event.target.dataset.depth)
		modal.attr('data-id', event.target.dataset.menuId)
		modal.attr('data-menu-title', event.target.dataset.menuTitle)
		$('#resp-megamenu-page-title').text(event.target.dataset.menuTitle)

		const containerElement = document.getElementById('responsive-pro-modal-app');
		if (typeof containerElement !== 'undefined' && containerElement !== null) {
			const root = ReactDOM.createRoot(containerElement);
			root.render(<App id={event.target.dataset.menuId} depth={event.target.dataset.depth} menuTitle={event.target.dataset.menuTitle} />);
		}

	});

});