(function(window, document, $, undefined) {
    "use strict";

    var REACrossSiteCopy = {
        //Initializing properties and methods
        init: function(e) {
            REACrossSiteCopy.globalVars();
            REACrossSiteCopy.loadreaCrossSiteCopyLocalStorage();
            REACrossSiteCopy.loadContextMenuGroupsHooks();
        },

        setElementId: function(elements) {
            return (
                elements.forEach(function(item) {
                    (item.id = elementorCommon.helpers.getUniqueId()),
                    0 < item.elements.length &&
                        REACrossSiteCopy.setElementId(item.elements);
                }),
                elements
            );
        },
        globalVars: function(e) {
            window.lc_ajax_url = rael_cs_copy.ajax_url;
            window.lc_ajax_nonce = rael_cs_copy.nonce;
            window.lc_key = rael_cs_copy.front_key;
        },
        loadreaCrossSiteCopyLocalStorage: function() {
            reaCrossSiteCopyLocalStorage.init({
				iframeUrl: "https://api.cyberchimps.com/reaLCP/",
                initCallback: function() {
                    // if need any callback
                },
            });
        },
        loadContextMenuGroupsHooks: function() {
            elementor.hooks.addFilter(
                "elements/section/contextMenuGroups",
                function(groups, element) {
                    return REACrossSiteCopy.prepareMenuItem(groups, element);
                }
            );

            elementor.hooks.addFilter(
                "elements/widget/contextMenuGroups",
                function(groups, element) {
                    return REACrossSiteCopy.prepareMenuItem(groups, element);
                }
            );

            elementor.hooks.addFilter(
                "elements/column/contextMenuGroups",
                function(groups, element) {
                    return REACrossSiteCopy.prepareMenuItem(groups, element);
                }
            );

            elementor.hooks.addFilter(
                "elements/container/contextMenuGroups",
                function(groups, element) {
                    return REACrossSiteCopy.prepareMenuItem(groups, element);
                }
            );
        },
        prepareMenuItem: function(groups, element) {
            var index = _.findIndex(groups, function(element) {
                return "clipboard" === element.name;
            });
            groups.splice(index + 1, 0, {
                name: "rael-cs-copy-paste",
                actions: [{
                        name: "rael-cs-copy",
                        title: "REA Copy",
                        icon: "eicon-copy",
                        callback: function() {
                            REACrossSiteCopy.reaCSCopy(element);
                        },
                    },
                    {
                        name: "rael-cs-paste",
                        title: "REA Paste",
                        icon: "eicon-clone",
                        callback: function() {
                            REACrossSiteCopy.reaCSPaste(element);
                        },
                    },
                ],
            });

            return groups;
        },
        reaCSCopy: function(e) {
            var data = {
                elType: e.model.attributes.elType,
                eletype: e.model.get("widgetType"),
                modelJson: e.model.toJSON(),
            };

            const params = {};
            params[data.elType] = data;

            reaCrossSiteCopyLocalStorage.setItem(
                "front_copy_data",
                JSON.stringify(params)
            );
        },

        reaCSPaste: function(selectedContainer) {
            reaCrossSiteCopyLocalStorage.getItem(
                "front_copy_data",
                function(frontContent) {
                    var containerElType = selectedContainer.model.get("elType");
                    var frontData = JSON.parse(frontContent.value);
                    var frontDataKey = Object.keys(frontData)[0];

                    var elementData = frontData[frontDataKey];
                    var elementType, elementModel, encodedElementModel;

                    if (typeof elementData.modelJson == "undefined") {
                        elementType = elementData.elType;
                        elementModel = elementData;
                    } else {
                        elementType = elementData.modelJson.elType;
                        elementModel = elementData.modelJson;
                    }

                    encodedElementModel = JSON.stringify(elementModel);

                    const hasImageFiles = /\.(jpg|png|jpeg|gif|svg)/gi.test(
                        encodedElementModel
                    );
                    const importModel = {
                        elType: elementType,
                        settings: elementModel.settings,
                    };

                    var importContainer = null;
                    var importOption = {
                        index: 0
                    };

                    if (elementType == "section") {
                        importModel.elements = REACrossSiteCopy.setElementId(
                            elementModel.elements
                        );
                        importContainer = elementor.getPreviewContainer();
                    } else if (elementType == "column") {
                        importModel.elements = REACrossSiteCopy.setElementId(
                            elementModel.elements
                        );
                        if ("section" === containerElType) {
                            importContainer = selectedContainer.getContainer();
                        }
                        else if ("column" === containerElType) {
                            importContainer = selectedContainer.getContainer().parent;
                            importOption.index = selectedContainer.getOption("_index") + 1;
                        } else if ("widget" === containerElType) {
                            importContainer = selectedContainer.getContainer().parent.parent;
                            importOption.index =
                                selectedContainer
                                .getContainer()
                                .parent.view.getOption("_index") + 1;
                        } else {
                            console.log("not match REA CS Copy ElType");
                            return;
                        }
                    } else if (elementType == "widget") {
                        (importModel.widgetType = elementData.eletype),
                        (importContainer = selectedContainer.getContainer());

                        if ("section" === containerElType) {
                            importContainer = selectedContainer.children
                                .findByIndex(0)
                                .getContainer();
                        } else if ("column" === containerElType) {
                            importContainer = importContainer =
                                selectedContainer.getContainer();
                        } else if ("widget" === containerElType) {
                            importContainer = selectedContainer.getContainer().parent;
                            importOption.index = selectedContainer.getOption("_index") + 1;
                        } else if ("container" === containerElType) {
                            importContainer = selectedContainer.getContainer();
                        } else {
                            console.log("not match REA CS Copy ElType");
                            return;
                        }
                    } else if (elementType === "container") {
                        importModel.elements = REACrossSiteCopy.setElementId(
                            elementModel.elements
                        );
                        importContainer = elementor.getPreviewContainer();
                    }

                    var importedContainer = $e.run("document/elements/create", {
                        model: importModel,
                        container: importContainer,
                        options: importOption,
                    });

                    if (hasImageFiles) {
                        $.ajax({
                            url: lc_ajax_url,
                            method: "POST",
                            data: {
                                action: "rael_elementor_import_rael_cs_copy_assets_files",
                                data: encodedElementModel,
                                security: lc_ajax_nonce,
                            },
                            beforeSend: function() {
                                importedContainer.view.$el.append(
                                    '<div id="rael-cs-copy-importing-images-loader">Importing Images..</div>'
                                );
                            },
                        }).done(function(response) {
                            if (response.success) {
                                const data = response.data[0];
                                importModel.elType = data.elType;
                                importModel.settings = data.settings;

                                if ("widget" === data.elType) {
                                    importModel.widgetType = data.widgetType;
                                } else {
                                    importModel.elements = data.elements;
                                }

                                setTimeout(function() {
                                    $e.run("document/elements/delete", {
                                        container: importedContainer,
                                    });
                                    $e.run("document/elements/create", {
                                        model: importModel,
                                        container: importContainer,
                                        options: importOption,
                                    });
                                }, 800);

                                $("#rael-cs-copy-importing-images-loader").remove();
                            }
                        });
                    }
                }
            );
        },
    };
    REACrossSiteCopy.init();
})(window, document, jQuery, reaCrossSiteCopyLocalStorage);
