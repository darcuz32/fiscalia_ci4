// https://techlaboratory.net/jquery-smartwizard

// ============================================================================
// DOCUMENT CONFIGURATION - LOADED FROM SERVER
// ============================================================================

let DOCUMENT_CONFIGURATION = {
    documents: {},
    requirements: {},
    extraFields: {}
};

// ============================================================================
// CONFIGURATION LOOKUPS & HELPERS
// ============================================================================

// Lookup maps populated after config loads (O(1) access instead of array scans)
let reqByKey = {};     // panel key (e.g. 'identificacion', 'domicilio_key') -> requirement entry
let reqByTitle = {};   // display title (e.g. 'Identificación') -> requirement entry

var showRegimen = false;

/**
 * Build lookup maps from DOCUMENT_CONFIGURATION.
 * Called once after configuration is loaded from server.
 * Each entry keeps the same shape: {key, instrucciones, requisito, articulo, accion, ambos, selfie, tipos}
 */
function buildLookups() {
    reqByKey = {};
    reqByTitle = {};

    for (const req of Object.values(DOCUMENT_CONFIGURATION.requirements)) {
        const subtypeNames = (req.subtypes || []).map(st => typeof st === 'string' ? st : st.name);
        const initialAmbos = req.subtypes.length === 1 && typeof req.subtypes[0] === 'object'
            ? (req.subtypes[0].requiresBothSides || false)
            : false;

        const entry = {
            key: req.key,
            instrucciones: req.instructions,
            requisito: req.title,
            articulo: req.article,
            accion: req.action,
            ambos: initialAmbos,
            selfie: req.requiresSelfie,
            allowPdf: req.allowPdf !== undefined ? req.allowPdf : false,
            tipos: subtypeNames,
            _subtypes: req.subtypes // keep originals for updateAmbos
        };

        reqByKey[req.key] = entry;
        reqByTitle[req.title] = entry;

        // Register legacy DB titles so correction flow can match old records
        if (req.dbTitles) {
            for (const dbTitle of req.dbTitles) {
                reqByTitle[dbTitle] = entry;
            }
        }

        if (req.alternative) {
            const altSubtypes = (req.alternative.subtypes || []).map(st => typeof st === 'string' ? st : st.name);
            const altEntry = {
                key: req.alternative.key,
                instrucciones: req.alternative.instructions,
                requisito: req.alternative.title,
                articulo: req.article,
                accion: req.action,
                ambos: req.alternative.requiresBothSides || false,
                selfie: false,
                allowPdf: req.alternative.allowPdf !== undefined ? req.alternative.allowPdf : false,
                tipos: altSubtypes,
                _subtypes: req.alternative.subtypes
            };
            reqByKey[req.alternative.key] = altEntry;
            reqByTitle[req.alternative.title] = altEntry;
        }
    }
}

/**
 * Get requirement titles for a document type (for display/validation).
 * Returns array where each element is a string or an array of strings (for "or" groups like vehicleDoc).
 */
function getDocReqTitles(docName) {
    const doc = DOCUMENT_CONFIGURATION.documents[docName];
    if (!doc) return [];
    return doc.requirements.map(reqKey => {
        const req = DOCUMENT_CONFIGURATION.requirements[reqKey];
        if (req.alternative) return [req.title, req.alternative.title];
        return req.title;
    });
}

/**
 * Get extra fields config for a document type.
 * Returns array of {titulo, type, key, required, opciones}.
 */
function getDocExtraFields(docName) {
    const doc = DOCUMENT_CONFIGURATION.documents[docName];
    if (!doc) return [];
    return doc.extraFields.map(fieldKey => {
        const field = DOCUMENT_CONFIGURATION.extraFields[fieldKey];
        return {
            titulo: field.title,
            type: field.type,
            key: field.key,
            required: field.required,
            opciones: field.options || undefined
        };
    });
}

/**
 * Check if a DB requisito string belongs to a given config requirement entry.
 * Handles renamed requirements by resolving through reqByTitle (which includes dbTitles aliases).
 * @param {string} dbRequisito - The requisito string from the DB (e.g. "Comprobante de Pago | Pagafacil")
 * @param {string} configTitle - The config requirement title to match against (e.g. "Pago en línea directo en esta página")
 * @param {string} [suffix] - Optional suffix like " (Frente)", " (Reverso)", " (Selfie)"
 * @returns {boolean}
 */
function matchesDbRequisito(dbRequisito, configTitle, suffix) {
    const dbTipo = dbRequisito.split("|")[0].trim();
    const target = suffix ? configTitle + suffix : configTitle;
    if (dbTipo === target) return true;
    // Resolve DB tipo through reqByTitle lookup (handles dbTitles aliases)
    const baseTipo = dbTipo.replace(" (Frente)", "").replace(" (Reverso)", "").replace(" (Selfie)", "");
    const entry = reqByTitle[baseTipo];
    if (!entry) return false;
    // Verify the resolved entry actually maps to the requested config title
    if (entry.requisito !== configTitle) return false;
    // If suffix specified, verify the dbTipo actually has that suffix
    if (suffix) return dbTipo === baseTipo + suffix;
    // No suffix: verify the dbTipo has no suffix either
    return dbTipo === baseTipo;
}

/**
 * Find a DB requisito that belongs to a given config requirement entry.
 * @param {Array} dbRequisitos - Array of DB requisito objects from constanciaCorregir.requisitos
 * @param {string} configTitle - The config requirement title
 * @param {string} [suffix] - Optional suffix like " (Frente)", " (Reverso)", " (Selfie)"
 * @returns {object|undefined}
 */
function findDbRequisito(dbRequisitos, configTitle, suffix) {
    return dbRequisitos.find(x => matchesDbRequisito(x.requisito, configTitle, suffix));
}

/**
 * Update the ambos (requiresBothSides) state for a requirement based on selected subtype.
 */
function updateAmbos(panelKey, subtypeName) {
    const entry = reqByKey[panelKey];
    if (!entry || !entry._subtypes) return;

    const subtype = entry._subtypes.find(st =>
        (typeof st === 'string' ? st : st.name) === subtypeName
    );
    entry.ambos = typeof subtype === 'object' ? (subtype.requiresBothSides || false) : false;
}

// ============================================================================
// CONFIGURATION FETCHING FROM SERVER
// ============================================================================

async function initializeDocumentConfiguration() {
    if (typeof base_url === 'undefined') {
        console.warn('base_url not defined, using fallback configuration');
        return;
    }

    try {
        const response = await fetch(base_url + 'constancias/get_configuration', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (response.ok) {
            const serverConfig = await response.json();
            Object.assign(DOCUMENT_CONFIGURATION, serverConfig);
            console.log('Configuration loaded from server');
            buildLookups();
        } else {
            console.warn('Failed to fetch configuration from server, using fallback');
        }
    } catch (error) {
        console.warn('Error fetching document configuration:', error, '- using fallback configuration');
    }
}

// ============================================================================
// DOCUMENT GENERATION FUNCTIONS
// ============================================================================

function generateDocumentRadioButtons() {
    let html = '<div class="row">';
    const documents = Object.keys(DOCUMENT_CONFIGURATION.documents);
    const halfLength = Math.ceil(documents.length / 2);

    // First column
    html += '<div class="col-md-6">';
    documents.slice(0, halfLength).forEach((docName, index) => {
        const doc = DOCUMENT_CONFIGURATION.documents[docName];
        html += `
            <div class="form-check check-option">
                <input class="form-check-input" type="radio" name="extravio" id="extravio${index + 1}" value="${docName}">
                <label class="form-check-label" for="extravio${index + 1}">
                    ${doc.displayName}
                </label>
            </div>`;
    });
    html += '</div>';

    // Second column
    html += '<div class="col-md-6">';
    documents.slice(halfLength).forEach((docName, index) => {
        const doc = DOCUMENT_CONFIGURATION.documents[docName];
        html += `
            <div class="form-check check-option">
                <input class="form-check-input" type="radio" name="extravio" id="extravio${halfLength + index + 1}" value="${docName}">
                <label class="form-check-label" for="extravio${halfLength + index + 1}">
                    ${doc.displayName}
                </label>
            </div>`;
    });
    html += '</div>';

    html += '</div>';
    return html;
}

function generateAccordionRequisitos() {
    let html = '';
    let collapseIndex = 1;

    const allRequirements = Object.keys(DOCUMENT_CONFIGURATION.requirements);

    // Generate accordion cards for each unique requirement
    allRequirements.forEach(reqKey => {
        const requirement = DOCUMENT_CONFIGURATION.requirements[reqKey];
        if (!requirement) return;

        // Generate unique IDs for this requirement
        const headingId = `heading${collapseIndex}`;
        const collapseId = `collapse${collapseIndex}`;
        const panelId = requirement.panelId || requirement.key;

        // Determine if this should be expanded by default (first item)
        const isExpanded = collapseIndex === 1;
        const showClass = isExpanded ? 'show' : '';
        const expandedAttr = isExpanded ? 'true' : 'false';
        const collapsedClass = isExpanded ? '' : 'collapsed';

        html += `
            <div class="card panelRequisito" id="${panelId}">
                <div class="card-header" id="${headingId}">
                    <h2 class="mb-0">
                        <button class="btn btn-link ${collapsedClass}" type="button" data-toggle="collapse" data-target="#${collapseId}" aria-expanded="${expandedAttr}" aria-controls="${collapseId}">
                            ${requirement.title}
                        </button>
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-check completed" viewBox="0 0 16 16">
                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-x not-completed" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                    </h2>
                </div>
                <div id="${collapseId}" class="collapse ${showClass}" aria-labelledby="${headingId}" data-parent="#accordionRequisitos">
                    <div class="card-body"></div>
                </div>
            </div>`;

        // Handle alternative requirements (like vehicleDoc having factura or TarjetaCirculacion)
        if (requirement.alternative) {
            collapseIndex++;
            const altHeadingId = `heading${collapseIndex}`;
            const altCollapseId = `collapse${collapseIndex}`;
            const altPanelId = requirement.alternative.key;

            html += `
                <div class="card panelRequisito" id="${altPanelId}">
                    <div class="card-header" id="${altHeadingId}">
                        <h2 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#${altCollapseId}" aria-expanded="false" aria-controls="${altCollapseId}">
                                ${requirement.alternative.title}
                            </button>
                        </h2>
                    </div>
                    <div id="${altCollapseId}" class="collapse" aria-labelledby="${altHeadingId}" data-parent="#accordionRequisitos">
                        <div class="card-body"></div>
                    </div>
                </div>`;
        }

        collapseIndex++;
    });

    return html;
}



// Correccion
let constanciaCorregir = undefined;
let requisitosCorregir = undefined;
let requisitosCorregidos = undefined;

var idPhotoContainer = '';

document.addEventListener("DOMContentLoaded", function () {
    const fechaNacimientoInput = document.getElementById("fecha_nacimiento");

    if (fechaNacimientoInput) {
        // Calculate the maximum date (18 years ago from today)
        const today = new Date();
        const minDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());

        // Format the date as YYYY-MM-DD
        const formattedMinDate = minDate.toISOString().split("T")[0];

        // Set the max attribute dynamically
        fechaNacimientoInput.setAttribute("max", formattedMinDate);
    }
});

$(document).ready(function () {
    // Initialize configuration from server
    initializeDocumentConfiguration().then(() => {
        console.log('Document configuration initialized');

        // Generate document radio buttons dynamically
        const documentContainer = $('#documentOptionsContainer');
        documentContainer.html(generateDocumentRadioButtons());
        initializeDocumentSelection();

        // Generate accordion requisitos dynamically
        const accordionContainer = $('#accordionRequisitos');
        accordionContainer.html(generateAccordionRequisitos());

        // Add event listeners for accordion expansion to fix SmartWizard height
        $(document).on('show.bs.collapse', '#accordionRequisitos .collapse', function() {
            // Small delay to ensure content is rendered before height calculation
            setTimeout(() => {
                const wizardId = correccionConstancia ? '#smartwizardcorreccion' : '#smartwizard';
                $(wizardId).smartWizard("fixHeight");
            }, 100);
        });
        
        $(document).on('shown.bs.collapse', '#accordionRequisitos .collapse', function() {
            // Fix height again after accordion is fully shown
            const wizardId = correccionConstancia ? '#smartwizardcorreccion' : '#smartwizard';
            $(wizardId).smartWizard("fixHeight");
        });
        
        $(document).on('hide.bs.collapse', '#accordionRequisitos .collapse', function() {
            setTimeout(() => {
                const wizardId = correccionConstancia ? '#smartwizardcorreccion' : '#smartwizard';
                $(wizardId).smartWizard("fixHeight");
            }, 100);
        });

        const initWizardId = correccionConstancia ? '#smartwizardcorreccion' : '#smartwizard';
        $(initWizardId).smartWizard("fixHeight");

        if (!correccionConstancia) llenarPanelesRequisitos(correccionConstancia);
    }).catch(error => {
        console.error('Failed to initialize configuration:', error);
    });

    $('#smartwizard').smartWizard({
        theme: 'arrows', // theme for the wizard, related css need to include for other than default theme
        enableUrlHash: false, // Enable selection of the step based on url hash
        lang: {
            next: 'Siguiente',
            previous: 'Anterior'
        },
        autoAdjustHeight: true,
        contentHeight: 'auto', // Allow content to determine height
        contentWidth: '100%',
        backButtonSupport: true,
        useURLhash: false,
        showStepURLhash: false
    });

    // Add event handlers for step changes to fix height issues
    function fixSmartWizardHeight() {
        setTimeout(() => {
            const activeStep = document.querySelector('.tab-pane.active');
            const tabContent = document.querySelector('.tab-content');
            if (activeStep && tabContent) {
                const stepHeight = activeStep.offsetHeight;
                tabContent.style.height = stepHeight + 'px';
                console.log("SmartWizard height adjusted to:", stepHeight);
            }
        }, 100);
    }

    $('#smartwizard').on('stepChanged', function(e, anchorObject, stepIndex, stepDirection) {
        console.log("Step changed to:", stepIndex);
        fixSmartWizardHeight();
    });

    $('#smartwizard').on('stepShown', function(e, anchorObject, stepIndex, stepDirection) {
        console.log("Step shown:", stepIndex);
        fixSmartWizardHeight();
    });

    // Handle accordion expansion/collapse in mobile
    $(document).on('shown.bs.collapse hidden.bs.collapse', '.accordion .collapse', function() {
        console.log("Accordion toggled");
        fixSmartWizardHeight();
    });

    // General height fix for any tab pane activation
    $(document).on('shown.bs.tab', '[data-toggle="tab"]', function() {
        fixSmartWizardHeight();
    });

    // Also fix height on window resize for mobile responsiveness
    $(window).on('resize', function() {
        fixSmartWizardHeight();
    });


    function initializeDocumentSelection(){
        $('#otro-container').hide();
        $('input[name="extravio"]').change(function () {
            if ($(this).val() === 'Otro') {
                $('#otro-container').show();
                $('#otro').focus();
            } else {
                $('#otro-container').hide();
            }

            const extravio = $('input[name="extravio"]:checked').val();
            $('#documentoExtraviado').text(extravio);

            const requisitos = getDocReqTitles($(this).val());
            $('#panelListaRequisitos').show();
            $('#listaRequisitos').html('');
            requisitos.forEach(requisito => {
                if (Array.isArray(requisito)) {

                    $('#listaRequisitos').append('<li>' + requisito.join(" o ") + '</li>');
                } else {

                    // if (requisito == comprobantePagoPagaFacil.titulo) {
                    //     $('#listaRequisitos').append('<li>' + requisito + '&nbsp;&nbsp;  </li>');
                    // } else {
                        $('#listaRequisitos').append('<li>' + requisito + '</li>');
                    //}
                }
            });

            $('#camposExtra').html('');
            const extraData = getDocExtraFields($(this).val());
            extraData.forEach(campo => {
                let htmlCampo = '';
                if (campo.type == "text") {
                    htmlCampo += '<div class="col-md-12 mt-2">';
                    htmlCampo += '<label for="' + campo.key + '">' + campo.titulo + '</label>';
                    htmlCampo += '<input type="' + campo.type + '" class="form-control" name="' + campo.key + '" ' + (campo.required ? 'required' : '') + '>';
                    htmlCampo += '</div>';
                } else if (campo.type == "radio") {
                    htmlCampo += '<div class="col-md-12 mt-2">';
                    htmlCampo += '<label for="' + campo.key + '">' + campo.titulo + '</label>';
                    htmlCampo += '<br/>';

                    campo.opciones.forEach(opcion => {
                        htmlCampo += '<div class="form-check form-check-inline">';
                        htmlCampo += '<input type="' + campo.type + '" class="form-check-input" name="' + campo.key + '" id="' + campo.key + '-' + opcion + '" value="' + opcion + '">';
                        htmlCampo += '<label class="form-check-label" for="' + campo.key + '-' + opcion + '">' + opcion + '</label>';
                        htmlCampo += '</div>';
                    });

                    htmlCampo += '</div>';
                }else if (campo.type == "select") {
                    htmlCampo += '<div class="col-md-12 mt-2">';
                    htmlCampo += '<label for="' + campo.key + '">' + campo.titulo + '</label>';
                    htmlCampo += '<select class="form-control chosen-select" name="' + campo.key + '">';

                    htmlCampo += '<option value="" selected>Seleccione el Estado emisor</option>';
                    campo.opciones.forEach(opcion => {
                        htmlCampo += '<option value="' + opcion + '">' + opcion + '</option>';
                    });

                    htmlCampo += '</select>';
                    htmlCampo += '</div>';
                }
                $('#camposExtra').append(htmlCampo);
            });

            showRegimen = DOCUMENT_CONFIGURATION.documents[$(this).val()].showRegimen;

            $('.options-regimen').attr('style', showRegimen ? '' : 'display: none !important;');

            $('#smartwizard').smartWizard("fixHeight");
        });
    }

    let requisitosValidacionCompleta = false;
    $("#smartwizard").on("leaveStep", function (e, anchorObject, currentStepIndex, nextStepIndex, stepDirection) {
        $('.sw-btn-next').show();

        requisitosValidacionCompleta = false;
        if (nextStepIndex < currentStepIndex) {
            return true;
        }

        console.log('Leaving step:', currentStepIndex);

        if (currentStepIndex === 0) { // De extraviado a requisitos
            const extravio = $('input[name="extravio"]:checked').val();
            if (!extravio) {
                toastr.error('Seleccione el documento que se extravió.');
                return false;
            }

            if (extravio === 'Otro') {
                const otro = $('#otro').val();
                if (otro.trim() === '') {
                    toastr.error('Especifique el documento que se extravió.');
                    return false;
                }
            }

            let extraCamposValidos = true;
            const extraData = getDocExtraFields(extravio);
            extraData.forEach(campo => {
                if (campo.required) {
                    if (campo.type == "text") {
                        if ($('input[name="' + campo.key + '"]').val() == "") {
                            toastr.error('Llene el campo ' + campo.titulo);
                            extraCamposValidos = false;
                        }
                    } else if (campo.type == "radio") {
                        if ($('input[name="' + campo.key + '"]:checked').length == 0) {
                            toastr.error('Llene el campo ' + campo.titulo);
                            extraCamposValidos = false;
                        }
                    } else if (campo.type == "select") {
                        if ($('select[name="' + campo.key + '"] option:selected').length == 0 ||
                            $('select[name="' + campo.key + '"] option:selected').val().length == 0) {
                            toastr.error('Llene el campo ' + campo.titulo);
                            extraCamposValidos = false;
                        }
                    }
                }
            });

            if (!extraCamposValidos) {
                return false;
            }

            const requisitos = getDocReqTitles(extravio);
            const tipos = requisitos.filter(r => !Array.isArray(r)).map(t => reqByTitle[t]).filter(Boolean);

            $('.panelRequisito').hide();

            let counter = 0;
            tipos.filter(obj => obj.accion == 'Selecciona').forEach(tipo => {
                counter++;
                $('#' + tipo.key).show();
                $('#' + tipo.key).find('.btn-link').html(counter + '. ' + tipo.accion + ' ' + tipo.articulo + ' ' + tipo.requisito);
            });

            const grupos = requisitos.filter(x => Array.isArray(x));

            grupos.forEach(grupo => {
                counter++;
                const tiposGrupos = grupo.map(t => reqByTitle[t]).filter(Boolean);
                const tipoGrupo = tiposGrupos.filter(obj => obj.accion == 'Selecciona')[0];

                tiposGrupos.forEach(tipoGrupoActual => {
                    if ($('#' + tipoGrupo.key).find('.card-body').find('.btns-photo-container[data-key="' + tipoGrupoActual.key + '"]').length == 0) {
                        $('#' + tipoGrupo.key).find('.card-body').append($('#' + tipoGrupoActual.key).find('.card-body').html());
                        $('#' + tipoGrupoActual.key).find('.card-body').html("");
                    }
                });

                $('#' + tipoGrupo.key).show();
                $('#' + tipoGrupo.key).find('.btn-link').html(counter + '. ' + tiposGrupos.map(x => x.accion + ' ' + x.articulo + ' ' + x.requisito).join(" y/o "));
            });



            $('input[name="identificacion"]').parent().show();
            $('input[name="identificacion"][value="' + $('input[name="extravio"]:checked').val() + '"]').parent().hide();
            $('input[name="identificacion"][value="' + $('input[name="extravio"]:checked').val() + '"]').prop("checked", false);

            if ($('input[name="identificacion"]:checked').val() === undefined) {
                $('#identificacion').find('.completed').hide();
            }

            requisitos.forEach(requisito => {
                const tipo = reqByTitle[requisito];
                if (tipo && tipo.accion == 'Selecciona') {
                    //console.log(tipo.key, '=>', tipo.tipos.length);
                    checkComplete(tipo.key);
                }

            });
        } else if (currentStepIndex === 1) { // De requisitos a datos
                //=======================>
                requisitosValidacionCompleta = true;
                const requisitos = getDocReqTitles($('input[name="extravio"]:checked').val());

                let valid = true;
                requisitos.forEach(requisito => {
                    if (Array.isArray(requisito)) {
                        let mensajeFaltantes = [];
                        requisito.forEach(req => {
                            let mensajeFaltante = "";
                            const tipo = reqByTitle[req];
                            if (tipo && tipo.accion == 'Selecciona') {
                                if (tipo.ambos) {
                                    const photoContainerFrente = $('#photoResult' + tipo.key + 'frente');
                                    if (photoContainerFrente.html().trim() === '' && tipo.tipos.length > 0 && !checkComplete(tipo.key)) {
                                        mensajeFaltante += 'Debe tomar una foto de ' + tipo.requisito + ' (Frente) o suba un PDF';
                                        valid = false;
                                    }

                                    const photoContainerReverso = $('#photoResult' + tipo.key + 'reverso');
                                    if (photoContainerReverso.html().trim() === '' && tipo.tipos.length > 0 && !checkComplete(tipo.key)) {
                                        if (mensajeFaltante != "") mensajeFaltante += ' y ';
                                        mensajeFaltante += 'Debe tomar una foto de ' + tipo.requisito + ' (Reverso) o suba un PDF';
                                        valid = false;
                                    }
                                } else {
                                    const photoContainer = $('#photoResult' + tipo.key);
                                    if (photoContainer.html().trim() === '' && tipo.tipos.length > 0 && !checkComplete(tipo.key)) {
                                        if (mensajeFaltante != "") mensajeFaltante += ' y ';
                                        mensajeFaltante += 'Debe tomar una foto de ' + tipo.requisito + '  o suba un PDF';
                                        valid = false;
                                    }
                                }

                                if (tipo.selfie) {
                                    const photoContainerSelfie = $('#photoResult' + tipo.key + 'selfie');
                                    if (photoContainerSelfie.html().trim() === '' && tipo.tipos.length > 0 && !checkComplete(tipo.key)) {
                                        if (mensajeFaltante != "") mensajeFaltante += ' y ';
                                        mensajeFaltante += 'Debe tomar una selfie con el documento';
                                        valid = false;
                                    }
                                }

                                if (tipo.tipos.length > 1) {
                                    const selected = $('input[name="' + tipo.key + '"]:checked').val();
                                    if (!selected) {
                                        if (mensajeFaltante != "") mensajeFaltante += ' y ';
                                        mensajeFaltante += 'Seleccione el tipo de ' + tipo.requisito;
                                        valid = false;
                                    }
                                }
                            }

                            if (mensajeFaltante != "") mensajeFaltantes.push(mensajeFaltante);
                        });

                        if (mensajeFaltantes.length > 0) {
                            toastr.error(mensajeFaltantes.join(' o '));
                        }
                    } else {
                        const tipo = reqByTitle[requisito];
                        if (tipo && tipo.accion == 'Selecciona') {
                            //console.log(tipo.key, '=>', tipo.tipos.length);
                            if (tipo.ambos) {
                                const photoContainerFrente = $('#photoResult' + tipo.key + 'frente');
                                if (photoContainerFrente.html().trim() === '' && tipo.tipos.length > 0 && !checkComplete(tipo.key)) {
                                    toastr.error('Debe tomar una foto de ' + tipo.requisito + ' (Frente) o suba un PDF');
                                    valid = false;
                                }

                                const photoContainerReverso = $('#photoResult' + tipo.key + 'reverso');
                                if (photoContainerReverso.html().trim() === '' && tipo.tipos.length > 0 && !checkComplete(tipo.key)) {
                                    toastr.error('Debe tomar una foto de ' + tipo.requisito + ' (Reverso) o suba un PDF');
                                    valid = false;
                                }
                            } else {
                                const photoContainer = $('#photoResult' + tipo.key);
                                if (photoContainer.html().trim() === '' && tipo.tipos.length > 0 && !checkComplete(tipo.key)) {
                                    toastr.error('Debe tomar una foto de ' + tipo.requisito + '  o suba un PDF');
                                    valid = false;
                                }
                            }

                            if (tipo.selfie) {
                                const photoContainerSelfie = $('#photoResult' + tipo.key + 'selfie');
                                if (photoContainerSelfie.html().trim() === '' && tipo.tipos.length > 0 && !checkComplete(tipo.key)) {
                                    toastr.error('Debe tomar una selfie con el documento');
                                    valid = false;
                                }
                            }

                            if (tipo.tipos.length > 1) {
                                const selected = $('input[name="' + tipo.key + '"]:checked').val();
                                if (!selected) {
                                    toastr.error('Seleccione el tipo de ' + tipo.requisito);
                                    valid = false;
                                }
                            }
                        }
                    }

                });
                //=======================>

                const extravio = $('input[name="extravio"]:checked').val();

                if (extravio === 'Licencia de Conducir') {
                    $('#divTipoLicencia').show();
                    $('#divTipoLicencia').find('input').prop('required', true);
                } else {
                    $('#divTipoLicencia').hide();
                    $('#divTipoLicencia').find('input').prop('required', false);
                }

                if (extravio === 'Pasaporte de menor de edad' || extravio === 'Visa de menor de edad') {
                    $('#lblNombre').html('Nombre completo del padre o madre del menor');
                    $('#lblFechaNacimiento').html('Fecha de nacimiento del padre o madre del menor');

                    $('#nombre_menor_group').show();
                    $('#nombre_menor').prop('required', true);
                    $('#fecha_nacimiento_menor_group').show();
                    $('#fecha_nacimiento_menor').prop('required', true);
                }else{
                    $('#lblNombre').html('Nombre completo');
                    $('#lblFechaNacimiento').html('Fecha de nacimiento');

                    $('#nombre_menor_group').hide();
                    $('#nombre_menor').prop('required', false);
                    $('#fecha_nacimiento_menor_group').hide();
                    $('#fecha_nacimiento_menor').prop('required', false);
                }

                return valid;
        } else if (currentStepIndex === 2) { // De datos a pago
            // Validate required form fields
            let valid = true;
            const requiredFields = $('#formConstanciaHechos').find('[required]');
            requiredFields.each(function () {
                if (!$(this).val()) {
                    toastr.error('Por favor complete todos los campos requeridos.');
                    valid = false;
                    return false; // Break out of each loop
                }
            });

            // Validate fecha_nacimiento maxDate
            const fechaNacimiento = $('#fecha_nacimiento').val();
            const maxDate = new Date();
            maxDate.setFullYear(maxDate.getFullYear() - 18);
            if (fechaNacimiento && new Date(fechaNacimiento) > maxDate) {
                toastr.error('La fecha de nacimiento debe ser mayor de 18 años.');
                valid = false;
            }

            //$('.sw-btn-next').hide();
            return valid;
        }

        setTimeout(() => {
            $('#smartwizard').smartWizard("fixHeight");
        }, 100);
    });

    $('#btnTerminar').click(function () {
        window.location.reload();
    });

    function llenarPanelesRequisitos(correccion) {
        if (correccion && requisitosCorregir.map(x => { return x.replace(" (Selfie)", "") }).includes("Identificación")) {
            let identificacionReqType = reqByKey['identificacion'];
            if (identificacionReqType) {
                const subtipoObj = constanciaCorregir.requisitos.find(obj => {
                    const entry = reqByTitle[(obj.tipo).trim()];
                    return entry && entry.key === 'identificacion';
                });
                if (subtipoObj) updateAmbos('identificacion', subtipoObj.subtipo);
            }
        }
        Object.values(reqByKey).forEach(tipo => {
            const panelRequisitos = $('#' + tipo.key).find('.card-body');
            //let html = `${correccion ? "Suba una foto que se pueda distinguir" : tipo.instrucciones}<br/><br/>`;
            let html = `${tipo.instrucciones}<br/><br/>`;

            if (tipo.tipos.length !== 1 && !correccion) {
                html += `<div class="row">
                <div class="col-6">`;
                let counter = 0;
                tipo.tipos.forEach(tipo1 => {
                    counter++;
                    html += `<div class="form-check check-option">
                                <input class="form-check-input inputTipo" type="radio" name="${tipo.key}" id="${tipo.key}${counter}" value="${tipo1}">
                                <label class="form-check-label" for="${tipo.key}${counter}">
                                    ${tipo1}
                                </label>
                            </div>`;
                });
                html += `</div>`;
            }

            let display = (tipo.tipos.length !== 1 && !correccionConstancia) ? "none" : "";
            html += `<div class="col-12 btns-photo-container" data-key="${tipo.key}" id="buttonsPhotoContainer${tipo.key}" style="display:${display};">`;

            html += `<div class="row" >`;

            if (!tipo.ambos) {
                let requisito = constanciaCorregir ? findDbRequisito(constanciaCorregir.requisitos, tipo.requisito) : undefined;

                if (correccion && (tipo.key === 'Pago' || tipo.key === 'payment' || tipo.key === 'Pago_fake') && (!requisito || requisito.valido != "Si")) {
                    html += `<div class="col-12">
                                <h4 class="text-center mb-4">Pago de la Constancia</h4>
                                <p class="text-center">El costo de la constancia de hechos es de <strong>$125.00 MXN</strong></p>
                                
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5>Selecciona tu método de pago</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="payment_method" id="payment_card" value="card" checked>
                                            <label class="form-check-label" for="payment_card">
                                                <strong>💳 Tarjeta de Crédito/Débito, Apple Pay o Google Pay</strong>
                                                <small class="d-block text-muted">Pago inmediato con tarjeta (Visa, Mastercard, American Express) o mediante Apple Pay/Google Pay</small>
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="payment_method" id="payment_oxxo" value="oxxo">
                                            <label class="form-check-label" for="payment_oxxo">
                                                <strong>🏪 OXXO</strong>
                                                <small class="d-block text-muted">Genera una ficha y paga en efectivo en cualquier tienda OXXO</small>
                                            </label>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="payment_method" id="payment_upload" value="upload">
                                            <label class="form-check-label" for="payment_upload">
                                                <strong>📄 Subir comprobante de pago</strong>
                                                <small class="d-block text-muted">Si ya realizaste el pago, sube tu comprobante aquí.</small>
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <!-- Stripe Payment Section -->
                                <div id="stripe-payment-section" class="card mb-4">
                                    <div class="card-body text-center">
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-center align-items-center mb-3">
                                                <img src="${base_url}assets/images/logo_fiscalia.png" alt="Logo" style="height: 50px; margin-right: 15px;">
                                                <h5 class="mb-0">🔒 Pago Seguro</h5>
                                            </div>
                                            <p class="text-muted mb-2">Procesa tu pago de manera rápida y 100% seguro sin salir de esta página.</p>
                                            <div class="d-flex justify-content-center align-items-center mb-3">
                                                <span class="text-muted mr-2">Powered by</span>
                                                <img src="${base_url}assets/images/stripe_logo.png" alt="Stripe Logo" style="height: 30px; margin-left: 5px;">
                                            </div>
                                            <small class="text-muted">🛡️ Protegido con encriptación de nivel bancario SSL</small>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mx-auto">
                                                <div class="payment-summary p-3 bg-light rounded mb-3 border">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span>Constancia de Hechos:</span>
                                                        <span>$125.00 MXN</span>
                                                    </div>
                                                    <hr class="my-2">
                                                    <div class="d-flex justify-content-between font-weight-bold">
                                                        <span>Total a pagar:</span>
                                                        <span class="text-primary">$125.00 MXN</span>
                                                    </div>
                                                </div>
                                                <div class="text-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-check-circle text-success"></i> Pago procesado al instante<br>
                                                        <i class="fas fa-shield-alt text-success"></i> Tus datos están completamente protegidos
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload Payment Receipt Section -->
                                <div id="upload-payment-section" class="card mb-4" style="display: none;">
                                    <div class="card-header">
                                        <h5>Subir Comprobante de Pago</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>Envíanos el comprobante de pago:</p>
                                        <div class="alert alert-info">
                                            <strong>Recuerda:</strong> Si hiciste el pago en línea o en oxxo previamente, por favor sube tu comprobante aquí.
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-primary btn-block btnRequisitos" id="btnPagoContainer" data-photo-container="photoResult${tipo.key}">
                                                    📷 Tomar Foto
                                                </button>
                                            </div>
                                            ${tipo.allowPdf ? `<div class="col-md-6">
                                                <button type="button" class="btn btn-primary btn-block btnRequisitosPdf" id="btnPagoPdfContainer" data-photo-container="photoResult${tipo.key}">
                                                    📄 Subir PDF
                                                </button>
                                            </div>` : ''}
                                        </div>
                                        <div id="photoResult${tipo.key}" class="mt-3" data-key="${tipo.key}"></div>
                                    </div>
                                </div>
                            </div>`;
                } else {

                    if (!correccion || (!requisito || requisito.valido != "Si")) {
                        html += `<div class="col-6">`;
                        html += ` <button type="button" class="btn btn-primary btnRequisitos" id="btn${tipo.key}" data-photo-container="photoResult${tipo.key}">Tomar foto</button>`;
                        html += `</div>`;
                    }

                    if (!correccion || (!requisito || requisito.valido != "Si")) {

                        if (tipo.allowPdf) {
                            html += `<div class="col-6">`;
                            html += `<button type="button" class="btn btn-primary btnRequisitosPdf" id="btnPagoPdf" data-photo-container="photoResult${tipo.key}">Subir PDF</button>`;
                            html += `</div>`;
                        }


                        html += `<div class="col-12">`;
                        html += ` <div id="photoResult${tipo.key}" class="mt-3" data-key="${tipo.key}" style="overflow: auto;"></div>`;
                        html += `</div>`;
                    }
                }
            } else {
                let requisitoFrente = constanciaCorregir ? findDbRequisito(constanciaCorregir.requisitos, tipo.requisito, ' (Frente)') : undefined;
                if (!correccion || (!requisitoFrente || requisitoFrente.valido != "Si")) {
                    html += `<div class="col-6">`;
                    html += ` <button type="button" class="btn btn-primary btnRequisitos" id="btn${tipo.key}frente" data-photo-container="photoResult${tipo.key}frente">Tomar foto (Frente)</button>
                    <div id="photoResult${tipo.key}frente" class="mt-3" data-key="${tipo.key}"></div>`;

                    html += `</div>`;
                }

                let requisitoReverso = constanciaCorregir ? findDbRequisito(constanciaCorregir.requisitos, tipo.requisito, ' (Reverso)') : undefined;
                if (!correccion || (!requisitoReverso || requisitoReverso.valido != "Si")) {
                    html += `<div class="col-6">`;

                    html += ` <button type="button" class="btn btn-primary btnRequisitos" id="btn${tipo.key}reverso" data-photo-container="photoResult${tipo.key}reverso">Tomar foto (Reverso)</button>
                                <div id="photoResult${tipo.key}reverso" class="mt-3" data-key="${tipo.key}"></div>`;

                    html += `</div>`;
                }

                if (tipo.allowPdf && (!correccion ||
                    (requisitoFrente && requisitoFrente.valido == "No" && requisitoReverso && requisitoReverso.valido == "No"))) {

                    html += `<div class="col-6">`;
                    html += `<button type="button" class="btn btn-primary btnRequisitosPdf" id="btn${tipo.key}Pdf" data-photo-container="photoResult${tipo.key}">Subir PDF</button>`;
                    html += `</div>`;
                }

                if (!correccion ||
                    (requisitoFrente && requisitoFrente.valido == "No") ||
                    (requisitoReverso && requisitoReverso.valido == "No")) {

                    html += `<div class="col-12">`;
                    html += ` <div id="photoResult${tipo.key}" class="mt-3" data-key="${tipo.key}" style="overflow: auto;"></div>`;
                    html += `</div>`;
                }
            }

            let requisitoSelfie = constanciaCorregir ? findDbRequisito(constanciaCorregir.requisitos, tipo.requisito, ' (Selfie)') : undefined;
            if (tipo.selfie && (!correccion || (requisitoSelfie && requisitoSelfie.valido == "No"))) {
                html += `<div class="col-12 text-center">`;
                html += `Como medida de seguridad, por favor mándanos una foto de tu rostro y tu identificación de la siguiente manera: <br>
                <img src="${base_url}assets/images/Selfie_Ejemplo.jpeg" alt="Foto" style="width: 100%; height: auto; max-width: 500px;"></img> <br>
                <button type="button" class="btn btn-primary btnRequisitos" style="margin-top: 10px;" id="btn${tipo.key}selfie" data-photo-container="photoResult${tipo.key}selfie">Tomar selfie</button>`;
                html += `</div>`;

                html += `<div class="col-12">`;
                html += ` <div id="photoResult${tipo.key}selfie" class="mt-3" data-key="identificacion" style="overflow: auto;"></div>`;
                html += `</div>`;
            }

            html += `       </div>`;
            html += `   </div>`;
            html += ` </div>`;
            panelRequisitos.html(html);
        });

        $('.inputTipo').change(function () {
            let html = ``;
            if ($(this).attr("name") == "identificacion") {
                let identificacionReqType = reqByKey['identificacion'];
                let originalAmbos = identificacionReqType ? identificacionReqType.ambos : false;

                const selectedSubtype = $('input[name="identificacion"]:checked').val();
                if (identificacionReqType && selectedSubtype) {
                    updateAmbos('identificacion', selectedSubtype);
                }

                if (originalAmbos != (identificacionReqType ? identificacionReqType.ambos : false)) {
                    html += `<div class="row">`;

                    html += '<div class="col-12 mb-3" id="msg' + $(this).attr("name") + '">';
                    html += ``;
                    html += `</div>`;

                    if (!(identificacionReqType ? identificacionReqType.ambos : false)) {
                        html += `<div class="col-6">`;
                        html += ` <button type="button" class="btn btn-primary btnRequisitos" id="btnidentificacion" data-photo-container="photoResultidentificacion">Tomar foto</button>`;
                        html += `</div>`;
                    } else {
                        html += `<div class="col-6">`;
                        html += ` <button type="button" class="btn btn-primary btnRequisitos" id="btnidentificacionfrente" data-photo-container="photoResultidentificacionfrente">Tomar foto (Frente)</button>
                        <div id="photoResultidentificacionfrente" class="mt-3" data-key="identificacion"></div>`;

                        html += `</div>
                                <div class="col-6">`;

                        html += ` <button type="button" class="btn btn-primary btnRequisitos" id="btnidentificacionreverso" data-photo-container="photoResultidentificacionreverso">Tomar foto (Reverso)</button>
                                    <div id="photoResultidentificacionreverso" class="mt-3" data-key="identificacion"></div>`;

                        html += `</div>`;
                    }

                    html += `<div class="col-6"></div>`;

                    if (identificacionReqType && identificacionReqType.allowPdf) {
                        html += `<div class="col-6">`;
                        html += `<button type="button" class="btn btn-primary btnRequisitosPdf" id="btnidentificacionPdf" data-photo-container="photoResultidentificacion">Subir PDF</button>`;
                        html += `</div>`;
                    }

                    html += `<div class="col-12">`;
                    html += ` <div id="photoResultidentificacion" class="mt-3" data-key="identificacion" style="overflow: auto;"></div>`;
                    html += `</div>`;

                    if (identificacionReqType && identificacionReqType.selfie) {
                        html += `<div class="col-12 text-center">`;
                        html += `Como medida de seguridad, por favor mándanos una foto de tu rostro y tu identificación de la siguiente manera: <br>
                <img src="${base_url}assets/images/Selfie_Ejemplo.jpeg" alt="Foto" style="width: 100%; height: auto; max-width: 500px;"></img> <br>
                <button type="button" class="btn btn-primary btnRequisitos" id="btnidentificacionselfie" data-photo-container="photoResultidentificacionselfie">Tomar selfie</button>`;
                        html += `</div>`;

                        html += `<div class="col-12">`;
                        html += ` <div id="photoResultidentificacionselfie" class="mt-3" data-key="identificacion" style="overflow: auto;"></div>`;
                        html += `</div>`;
                    }

                    html += `       </div>`;
                    $('#buttonsPhotoContaineridentificacion').html(html);
                }

                let htmlMsg = "";
                if ($('input[name="identificacion"]:checked').val() == 'Credencial de Elector') {
                    htmlMsg += `<p class="text-muted credit" style="color:#fff">Solo serán válidas las credenciales de elector expedidas a partir del 2019 y que tengan los tres códigos QR en el reverso</p>`;
                    htmlMsg += `<img src="${base_url}assets/images/INE_Ejemplo.jpeg" alt="Foto" style="width: 100%; height: auto; max-width: 500px;"></img>`;
                }
                $('#msg' + $(this).attr("name")).html(htmlMsg);


            } else if ($(this).attr("name") == "identification_parents") {
                let identificacionParentsReqType = reqByKey['identification_parents'];
                let originalAmbos = identificacionParentsReqType ? identificacionParentsReqType.ambos : false;

                const selectedSubtype = $('input[name="identification_parents"]:checked').val();
                if (identificacionParentsReqType && selectedSubtype) {
                    updateAmbos('identification_parents', selectedSubtype);
                }

                if (originalAmbos != (identificacionParentsReqType ? identificacionParentsReqType.ambos : false)) {
                    html += `<div class="row">`;

                    html += '<div class="col-12 mb-3" id="msg' + $(this).attr("name") + '">';
                    html += ``;
                    html += `</div>`;

                    if (!(identificacionParentsReqType ? identificacionParentsReqType.ambos : false)) {
                        html += `<div class="col-6">`;
                        html += ` <button type="button" class="btn btn-primary btnRequisitos" id="btnidentification_parents" data-photo-container="photoResultidentification_parents">Tomar foto</button>`;
                        html += `</div>`;
                    } else {
                        html += `<div class="col-6">`;
                        html += ` <button type="button" class="btn btn-primary btnRequisitos" id="btnidentification_parentsfrente" data-photo-container="photoResultidentification_parentsfrente">Tomar foto (Frente)</button>
                        <div id="photoResultidentification_parentsfrente" class="mt-3" data-key="identification_parents"></div>`;

                        html += `</div>
                                <div class="col-6">`;

                        html += ` <button type="button" class="btn btn-primary btnRequisitos" id="btnidentification_parentsreverso" data-photo-container="photoResultidentification_parentsreverso">Tomar foto (Reverso)</button>
                                    <div id="photoResultidentification_parentsreverso" class="mt-3" data-key="identification_parents"></div>`;

                        html += `</div>`;
                    }

                    html += `<div class="col-6">`;
                    html += `</div>`;

                    if (identificacionParentsReqType && identificacionParentsReqType.allowPdf) {
                        html += `<div class="col-6">`;
                        html += `<button type="button" class="btn btn-primary btnRequisitosPdf" id="btnidentification_parentsPdf" data-photo-container="photoResultidentification_parents">Subir PDF</button>`;
                        html += `</div>`;
                    }

                    html += `<div class="col-12">`;
                    html += ` <div id="photoResultidentification_parents" class="mt-3" data-key="identification_parents" style="overflow: auto;"></div>`;
                    html += `</div>`;

                    if (identificacionParentsReqType && identificacionParentsReqType.selfie) {
                        html += `<div class="col-12 text-center">`;
                        html += `Como medida de seguridad, por favor mándanos una foto de tu rostro y tu identificación de la siguiente manera: <br>
                <img src="${base_url}assets/images/Selfie_Ejemplo.jpeg" alt="Foto" style="width: 100%; height: auto; max-width: 500px;"></img> <br>
                <button type="button" class="btn btn-primary btnRequisitos" id="btnidentification_parentsselfie" data-photo-container="photoResultidentification_parentsselfie">Tomar selfie</button>`;
                        html += `</div>`;

                        html += `<div class="col-12">`;
                        html += ` <div id="photoResultidentification_parentsselfie" class="mt-3" data-key="identification_parents" style="overflow: auto;"></div>`;
                        html += `</div>`;
                    }

                    html += `       </div>`;
                    $('#buttonsPhotoContaineridentification_parents').html(html);
                }

                let htmlMsg = "";
                console.log('=====> ' + $(this).attr("name") + ' - ' + $('input[name="identification_parents"]:checked').val());
                if ($('input[name="identification_parents"]:checked').val() == 'Credencial de Elector') {
                    htmlMsg += `<p class="text-muted credit" style="color:#fff">Solo serán válidas las credenciales de elector expedidas a partir del 2019 y que tengan los tres códigos QR en el reverso</p>`;
                    htmlMsg += `<img src="${base_url}assets/images/INE_Ejemplo.jpeg" alt="Foto" style="width: 100%; height: auto; max-width: 500px;"></img>`;
                }
                $('#msg' + $(this).attr("name")).html(htmlMsg);

            } else {
                // General case for any other requirement type that might have subtypes with different requiresBothSides
                const requirementKey = $(this).attr("name");
                const selectedSubtype = $('input[name="' + requirementKey + '"]:checked').val();

                if (selectedSubtype) {
                    // Update ambos based on the selected subtype for any requirement
                    updateAmbos(requirementKey, selectedSubtype);
                }
            }

            $('#buttonsPhotoContainer' + $(this).attr("name")).show();

            let photoResult = $('#' + $(this).closest('.row').find('.btnRequisitos').data('photo-container'));
            let key = photoResult.data('key');
            checkComplete(key);
            setTimeout(() => {
                const wizardId = correccionConstancia ? '#smartwizardcorreccion' : '#smartwizard';
                $(wizardId).smartWizard("fixHeight");
            }, 100);
        });

        // Fix SmartWizard height after all panels are populated
        setTimeout(() => {
            const wizardId = correccionConstancia ? '#smartwizardcorreccion' : '#smartwizard';
            $(wizardId).smartWizard("fixHeight");
        }, 200);

    }

    function checkComplete(key) {
        let panel = $('#buttonsPhotoContainer' + key + "").closest('.panelRequisito');
        if (panel.length === 0) return;
        key = panel.attr("id");
        let requisitosValidos = [];
        $('#' + key).find('.btns-photo-container').each(function () {
            let keyRequisito = $(this).data("key");
            let allCompleted = [];
            $('#buttonsPhotoContainer' + keyRequisito).find('.btnRequisitos, .btnRequisitosPdf').each(function () {
                if ($('#' + $(this).data("photo-container")).html() != "" && allCompleted.filter(obj => obj === $(this).data("photo-container")).length == 0) {
                    allCompleted.push($(this).data("photo-container"));
                }
            });

            let totalFotos = 1;
            let tipo = reqByKey[keyRequisito];
            if (tipo && tipo.selfie) totalFotos++;
            if (tipo && tipo.ambos && ($('#photoResult' + keyRequisito).html() == "" || $('#photoResult' + keyRequisito).html() == undefined)) totalFotos++;


            if (correccionConstancia) {
                totalFotos -= constanciaCorregir.requisitos.filter(x => {
                    const entry = reqByTitle[x.requisito.split("|")[0].trim().replace(" (Frente)", "").replace(" (Reverso)", "").replace(" (Selfie)", "")];
                    return entry && entry.key === tipo.key;
                }).filter(x => x.valido == "Si").length;
            }

            if (allCompleted.length >= totalFotos) {

                requisitosValidos.push(true);
            } else {
                requisitosValidos.push(false);
            }
        });

        if (requisitosValidos.filter(x => x).length >= 1) {

            $('#' + key).find('.completed').show();
            $('#' + key).find('.not-completed').hide();
            return true;
        } else {

            $('#' + key).find('.completed').hide();
            if (requisitosValidacionCompleta) {
                $('#' + key).find('.not-completed').show();
            } else {
                $('#' + key).find('.not-completed').hide();
            }
            return false;
        }
    }

    // =======
    $(document).delegate('.btnRequisitos', 'click', function () {
        idPhotoContainer = $(this).data('photo-container');
        $('#photoModal').modal('show');
    });

    $('#btnCloseModal').click(function () {
        $('#photoModal').modal('hide');
    });

    $(document).delegate('.btnRequisitosPdf', 'click', function () {
        idPhotoContainer = $(this).data('photo-container');

        $('#pdfFile').val('');
        $('#pdfPreview').html('');
        $('.custom-file-label').text('Selecciona un PDF');
        $('#pdfModal').modal('show');
    });

    $(document).delegate('#pdfFile', "change", function () {
        let fileName = $(this).val().split('\\');
        $('#pdfFile').next().text(fileName[fileName.length - 1]);
        const file = this.files[0];


        const fileReader = new FileReader();

        fileReader.onload = function (e) {
            const typedArray = new Uint8Array(e.target.result);

            // Load the PDF using PDF.js
            pdfjsLib.getDocument(typedArray).promise.then(pdf => {
                $('#pdfPreview').html(''); // Clear previous preview

                // Render the first page
                pdf.getPage(1).then(page => {
                    const viewport = page.getViewport({ scale: 1 });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');

                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    $('#pdfPreview').append(canvas);

                    $(canvas).css('width', '100%');

                    page.render({
                        canvasContext: context,
                        viewport: viewport
                    });
                });
            }).catch(error => {
                console.error('Error loading PDF:', error);
                alert('Failed to load PDF.');
            });
        };

        fileReader.readAsArrayBuffer(file);
    });

    $('#selectPDF').click(function (e) {
        if ($('#pdfFile').val() != '') {
            const file = $('#pdfFile')[0].files[0];
            console.log(idPhotoContainer);


            const fileReader = new FileReader();

            fileReader.onload = function (e) {
                const typedArray = new Uint8Array(e.target.result);
                const fileBase64 = e.target.result;

                // Load the PDF using PDF.js
                pdfjsLib.getDocument(typedArray).promise.then(pdf => {
                    $('#' + idPhotoContainer).html(''); // Clear previous preview

                    // Render the first page
                    pdf.getPage(1).then(page => {
                        const viewport = page.getViewport({ scale: 1 });
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');

                        canvas.width = viewport.width;
                        canvas.height = viewport.height;

                        $('#' + idPhotoContainer).append(canvas);

                        $(canvas).css('width', '100%');

                        page.render({
                            canvasContext: context,
                            viewport: viewport
                        });

                        let key = $('#' + idPhotoContainer).data('key');
                        checkComplete(key);

                        const fileReaderBase = new FileReader();

                        fileReaderBase.onload = function (e) {
                            const fileBase64 = e.target.result;
                            $('#' + idPhotoContainer + 'frente').html("");
                            $('#' + idPhotoContainer + 'reverso').html("");
                            $('#' + idPhotoContainer).find('canvas').data('file', fileBase64);

                            setTimeout(() => {
                                correccionConstancia ? $('#smartwizardcorreccion').smartWizard("fixHeight") : $('#smartwizard').smartWizard("fixHeight");
                            }, 500);
                            $('#pdfModal').modal('hide');
                        };

                        fileReaderBase.readAsDataURL(file);

                    });
                }).catch(error => {
                    console.error('Error loading PDF:', error);
                    alert('Failed to load PDF.');
                });
            };

            fileReader.readAsArrayBuffer(file);



        }
    });
    // ======

    let video = document.getElementById('video');
    let canvas = document.getElementById('canvas');

    $('#photoModal').on('shown.bs.modal', function () {
        $('#takePhoto').attr('disabled', true);
        $('#changeCamera').attr('disabled', true);
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function (stream) {
                video.srcObject = stream;
                video.setAttribute('playsinline', true); // Prevent full-screen on iOS
                video.setAttribute('webkit-playsinline', true); // Prevent full-screen on iOS
                video.play();
                $('#takePhoto').attr('disabled', false);
                $('#changeCamera').attr('disabled', false);
            })
            .catch(function (err) {
                console.log("An error occurred: " + err);
            });
    });

    $('#photoModal').on('hidden.bs.modal', function () {
        $('#takePhoto').attr('disabled', true);
        $('#changeCamera').attr('disabled', true);
        video.srcObject.getVideoTracks().forEach(track => track.stop());
    });

    $('#takePhoto').click(function () {
        let context = canvas.getContext('2d');

        // Get the video dimensions
        let videoWidth = video.videoWidth * 2; //1920;//video.videoWidth;
        let videoHeight = video.videoHeight * 2; //1080;//video.videoHeight;

        // Set the canvas dimensions to match the video dimensions
        canvas.width = videoWidth;
        canvas.height = videoHeight;

        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        let dataUrl = canvas.toDataURL('image/png');
        // $('#'+idPhotoContainer).html('<img src="' + dataUrl + '" alt="Foto" style="width: 100%; max-width: 300px;"> <br/>  <p class="text-muted credit" style="color:#fff">Si no está legible, puedes volver a tomarla</p>');

        $('#' + idPhotoContainer).html('<img src="' + dataUrl + '" alt="Foto" style="width: 100%; height: auto; max-width: 300px;"> <br/>  <p class="text-muted credit" style="color:#fff">Si no está legible, puedes volver a tomarla</p>');



        if (idPhotoContainer.includes("frente") || idPhotoContainer.includes("reverso")) {
            $('#' + idPhotoContainer.replace("frente", "").replace("reverso", "")).html("");
        }

        let key = $('#' + idPhotoContainer).data('key');

        checkComplete(key);

        $('#photo').attr('src', dataUrl);
        $('#photoModal').modal('hide');
        // Close the camera stream
        video.srcObject.getVideoTracks().forEach(track => track.stop());
        setTimeout(() => {
            correccionConstancia ? $('#smartwizardcorreccion').smartWizard("fixHeight") : $('#smartwizard').smartWizard("fixHeight");
        }, 500);
    });

    $('#changeCamera').click(function () {
        // Toggle camera to the other one
        let videoSource = video.srcObject;
        let tracks = videoSource.getTracks();

        // Stop all current tracks
        tracks.forEach(track => track.stop());

        // Check if the current camera is front or back
        let currentFacingMode = video.getAttribute('data-facing-mode') || 'environment';
        let nextFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';

        // Request the new camera stream
        navigator.mediaDevices.getUserMedia({
            video: { facingMode: nextFacingMode }
        }).then(newStream => {
            video.srcObject = newStream;
            video.setAttribute('playsinline', true);
            video.setAttribute('webkit-playsinline', true);
            video.setAttribute('data-facing-mode', nextFacingMode); // Save the current facing mode
            video.play();
        }).catch(function (err) {
            console.log("An error occurred: " + err);
        });
    });

    $('#formConstanciaHechos').submit(function (e) {
        e.preventDefault();

        if (showRegimen && $('input[name="regimen"]:checked').val() == "Persona Moral") {
            const photoContainer = $('#photoResultPoderNotarial');
            if (photoContainer.html().trim() === '') {
                toastr.error('Debe tomar una foto o subir el archivo del poder notarial');
                return false;
            }
        }

        let requisitos = [];
        Object.values(reqByKey).forEach(tipo => {
            let radioValue = $('input[name="' + tipo.key + '"]:checked').val();
            if (tipo.key == 'Pago') {
                radioValue = 'Pago';
            } else if (tipo.key == 'PoderNotarial') {
                radioValue = '';
            }
            if (tipo.ambos && $('#photoResult' + tipo.key + 'frente').html() != "" && $('#photoResult' + tipo.key + 'reverso').html() != "") {

                const photoContainerFrente = $('#photoResult' + tipo.key + 'frente');
                const photoFrente = photoContainerFrente.find('img').attr('src');
                if (photoFrente) {
                    requisitos.push({
                        tipo: tipo.requisito + ' (Frente)',
                        subtipo: radioValue,
                        foto: photoFrente
                    });
                }

                const photoContainerReverso = $('#photoResult' + tipo.key + 'reverso');
                const photoReverso = photoContainerReverso.find('img').attr('src');
                if (photoReverso) {
                    requisitos.push({
                        tipo: tipo.requisito + ' (Reverso)',
                        subtipo: radioValue,
                        foto: photoReverso
                    });
                }
            } else {

                const photoContainer = $('#photoResult' + tipo.key);
                const photo = photoContainer.find('img').attr('src');
                if (photo) {
                    requisitos.push({
                        tipo: tipo.requisito,
                        subtipo: radioValue,
                        foto: photo
                    });
                } else {
                    const pdf = photoContainer.find('canvas').data('file');
                    if (pdf) {
                        requisitos.push({
                            tipo: tipo.requisito,
                            subtipo: radioValue,
                            foto: pdf
                        });
                    }
                }
            }

            if (tipo.selfie) {
                const photoContainerSelfie = $('#photoResult' + tipo.key + 'selfie');
                const photo = photoContainerSelfie.find('img').attr('src');
                if (photo) {
                    requisitos.push({
                        tipo: tipo.requisito + ' (Selfie)',
                        subtipo: radioValue,
                        foto: photo
                    });
                }
            }
        });

        // Add pago photo manually
        const payment_method = $('input[name="payment_method"]:checked').val();
        console.log('Payment Method:', payment_method);
        if (payment_method == "upload") {
            const photoPagoContainer = $('#photoResultPago');
            if (photoPagoContainer.html().trim() === '') {
                toastr.error('Debe tomar una foto del comprobante de pago');
                return false;
            }

            const photoPago = photoPagoContainer.find('img').attr('src');
            if (photoPago) {
                requisitos.push({
                    tipo: 'Comprobante de Pago',
                    subtipo: 'Pago',
                    foto: photoPago
                });
            }else{
                const pdf = photoPagoContainer.find('canvas').data('file');
                if (pdf) {
                    requisitos.push({
                        tipo: 'Comprobante de Pago',
                        subtipo: 'Pago',
                        foto: pdf
                    });
                }
            }
        }


        const regimen = showRegimen ? $('input[name="regimen"]:checked').val() : "";
        let data = {
            payment_method: payment_method,
            extravio: $('input[name="extravio"]:checked').val(),
            nombre: $('#nombre').val(),

            nombre_menor: $('#nombre_menor').val(),
            fecha_nacimiento_menor: $('#fecha_nacimiento_menor').val(),

            email: $('#email').val(),
            telefono: $('#telefono').val(),
            fecha_nacimiento: $('#fecha_nacimiento').val(),
            domicilio: $('#domicilio').val(),
            municipio: $('#municipio').val(),
            fecha_extravio: $('#fecha_extravio').val(),
            tipo_licencia: $('input[name="tipo_licencia"]:checked').val(),
            tipo_identificacion: $('input[name="identificacion"]:checked').val(),
            regimen: regimen,
            razon_social: (regimen == "Persona Moral") ? $('#razon_social').val() : "",
            requisitos: requisitos
        };

        if(data.extravio == "Pasaporte de menor de edad" || data.extravio == "Visa de menor de edad") {
            data.tipo_identificacion = $('input[name="identification_parents"]:checked').val();
        }

        const extraData = getDocExtraFields($('input[name="extravio"]:checked').val());
        extraData.forEach(campo => {
            if (campo.type == "text") {
                data[campo.key] = $('input[name="' + campo.key + '"]').val();
            } else if (campo.type == "radio") {
                data[campo.key] = $('input[name="' + campo.key + '"]:checked').val();
            }else if (campo.type == "select") {
                data[campo.key] = $('select[name="' + campo.key + '"] option:selected').val();
            }
        });

        $('button[type="submit"]').prop('disabled', true).html('Enviando...');
        // Send data to server
        $("#overlay").fadeIn(300);
        $.ajax({
            type: 'POST',
            url: base_url + 'constancias/save',
            data: { data: JSON.stringify(data) },
            success: function (response) {
                try {
                    const obj = (response);
                    if (obj.success === false) {
                        toastr.error(obj.message);
                        $("#overlay").fadeOut(300);
                        $('button[type="submit"]').prop('disabled', false).html('Enviar');
                        // Show the error message
                        $('#error-message').show();
                        $('#error-message').html(obj.message);
                        return;
                    }

                    if (obj.success && (obj.payment_method === 'card' || obj.payment_method === 'oxxo')) {
                        // Proceed to Stripe payment (card or OXXO)
                        createStripeCheckoutSession(obj.folio_id, obj.folio, obj.payment_method, 'creation');
                    } else {
                        toastr.success('Gracias, en breve recibirá un correo de confirmación.');
                        $('button[type="submit"]').prop('disabled', true).html('Enviado').removeClass('btn-primary').addClass('btn-success');

                        $('#formConstanciaHechos').hide();
                        $('#smartwizard').hide();
                        $('.instrucciones').hide();
                        $('#message-container').show();
                        $('#fiscalia-title').html('Fiscalía Digital Coahuila');
                        $('#folio').html(obj.folio);

                        $('#btnTramitarOtra').hide();
                        if (regimen == "Persona Moral") {
                            $('#btnTramitarOtra').show();
                        }

                        setTimeout(() => {
                            $('#smartwizard').smartWizard("fixHeight");
                        }, 500);
                    }
                } catch (error) {
                    console.error(error);
                    toastr.error('Hubo un problema al guardar los datos. Intente de nuevo por favor.');

                    //$('#message-container').show();
                    //$('#folio').html(JSON.stringify(data));
                    $('#error-message').show();
                    $('#error-message').html(obj.message);

                    $('button[type="submit"]').prop('disabled', false).html('Enviar');
                    $("#overlay").fadeOut(300);
                    return;
                }
                $("#overlay").fadeOut(300);
            },
            error: function () {
                toastr.error('Hubo un problema al guardar los datos. Intente de nuevo.');
                $('button[type="submit"]').prop('disabled', false).html('Enviar');
                $("#overlay").fadeOut(300);
            }
        });

    });

    $('#btnTramitarOtra').click(function () {
        $('#formConstanciaHechos').show();
        $('#smartwizard').show();
        $('#message-container').hide();
        $('#folio').html("");
        // Reset placa fields
        $('[name="placa"]').val('');
        // Reset tipo_placa radio buttons
        $('input[name="tipo_placa"]').prop('checked', false);
        // Reset factura fields
        $('#photoResultfacturafrente').html('');
        $('#photoResultfacturareverso').html('');
        $('#photoResultTarjetaCirculacion').html('');
        $('#photoResultPago').html('');
        checkComplete('factura');
        checkComplete('TarjetaCirculacion');


        // Move the wizard to the first step
        $('#smartwizard').smartWizard("reset");
        $('#smartwizard').smartWizard("goToStep", 0);
        $('button[type="submit"]').prop('disabled', false).html('Enviar');
    });

    $('.btn-link').click(function () {
        setTimeout(() => {
            correccionConstancia ? $('#smartwizardcorreccion').smartWizard("fixHeight") : $('#smartwizard').smartWizard("fixHeight");
        }, 200);

    });



    $('.btn-toggle label').click(function () {
        $('.btn-toggle label').removeClass('active');
        $('.btn-toggle label').removeClass('btn-primary');
        $('.btn-toggle label').addClass('btn-secondary');
        $(this).removeClass('btn-secondary');
        $(this).addClass('btn-primary');
        $(this).addClass('active');
        $('.btn-toggle label').find('input').attr('checked', false);
        $(this).find('input').attr('checked', true);
        $('.btn-toggle label').find('input:checked').trigger('change');
    });

    $('input[name=regimen]').change(function () {
        if ($(this).is(':checked')) {
            if ($(this).val() == "Persona Moral") {
                $('.moral').removeClass('hidden-element');

                $('.moral').find('input').prop('required', true);
            } else {
                $('.moral').addClass('hidden-element');

                $('.moral').find('input').prop('required', false);
            }

            $('#smartwizard').smartWizard("fixHeight");
        }
    });


    $('#smartwizardcorreccion').smartWizard({
        theme: 'arrows', // theme for the wizard, related css need to include for other than default theme
        enableUrlHash: false, // Enable selection of the step based on url hash
        lang: {
            next: 'Siguiente',
            previous: 'Anterior'
        },
        autoAdjustHeight: true,

    });

    // Add event handlers for step changes in correction wizard to fix height issues
    function fixCorrectionWizardHeight() {
        setTimeout(() => {
            const activeStep = document.querySelector('#smartwizardcorreccion .tab-pane.active');
            const tabContent = document.querySelector('#smartwizardcorreccion .tab-content');
            if (activeStep && tabContent) {
                const stepHeight = activeStep.offsetHeight;
                tabContent.style.height = stepHeight + 'px';
                console.log("Correction wizard height adjusted to:", stepHeight);
            }
        }, 100);
    }

    $('#smartwizardcorreccion').on('stepChanged', function(e, anchorObject, stepIndex, stepDirection) {
        console.log("Correction step changed to:", stepIndex);
        fixCorrectionWizardHeight();
    });

    $('#smartwizardcorreccion').on('stepShown', function(e, anchorObject, stepIndex, stepDirection) {
        console.log("Correction step shown:", stepIndex);
        fixCorrectionWizardHeight();
    });

    $("#smartwizardcorreccion").on("leaveStep", function (e, anchorObject, currentStepIndex, nextStepIndex, stepDirection) {
        $('.sw-btn-next').show();
        if (nextStepIndex < currentStepIndex) {
            return true;
        }

        if (currentStepIndex === 0 && !$("#overlay").is(":visible")) {
            if ($('#formFolio')[0].reportValidity()) {
                $("#overlay").fadeIn(300);
                $('#btnRevisarFolio').prop('disabled', true).html('Espere...').removeClass('btn-primary').addClass('btn-success');
                $.ajax({
                    type: 'POST',
                    url: base_url + 'constancias/validarfolio',
                    data: { folio: $('#folio').val(), email: $('#email').val() },
                    success: function (response) {
                        try {
                            const obj = (response);
                            if (obj.success === false) {
                                toastr.error(obj.message);
                                $('#btnRevisarFolio').prop('disabled', false).html('Continuar');
                                $("#overlay").fadeOut(300);
                                return;
                            }
                            $('#registroFolio').hide();
                            $('#smartwizardcorreccion').show();
                            constanciaCorregir = obj.data;
                            requisitosCorregir = constanciaCorregir.requisitos.filter(obj => obj.valido == "No").map(x => { return x.tipo });

                            // Resolve DB tipo names to current config titles (handles renamed requirements)
                            const resolvedCorregir = requisitosCorregir.map(tipo => {
                                const entry = reqByTitle[tipo];
                                return entry ? entry.requisito : tipo;
                            });

                            requisitosCorregidos = getDocReqTitles(constanciaCorregir.extravio).filter(obj => {
                                if (Array.isArray(obj)) return false; // groups handled separately
                                return resolvedCorregir.map(x => x.replace(" (Selfie)", "")).includes(obj);
                            });

                            // Only include "or" groups that have at least one invalid member
                            requisitosCorregidosGrupo = getDocReqTitles(constanciaCorregir.extravio).filter(obj => {
                                if (!Array.isArray(obj)) return false;
                                return obj.some(title => resolvedCorregir.map(x => x.replace(" (Selfie)", "")).includes(title));
                            });

                            let html = `<div class="col-md-6">`;
                            html += `<p><strong>Extravío:</strong> <span>${constanciaCorregir.extravio}</span></p>`;
                            html += `</div>`
                            html += `<div class="col-md-6">`;
                            html += `<p><strong>Nombre:</strong> <span>${constanciaCorregir.nombre}</span></p>`;
                            html += `</div>`
                            html += `<div class="col-md-6">`;
                            html += `<p><strong>Correo:</strong> <span>${constanciaCorregir.email}</span></p>`;
                            html += `</div>`
                            html += `<div class="col-md-6">`;
                            html += `<p><strong>Teléfono:</strong> <span>${constanciaCorregir.telefono}</span></p>`;
                            html += `</div>`
                            html += `<div class="col-md-6">`;
                            html += `<p><strong>Status:</strong> <span>${constanciaCorregir.status}</span></p>`;
                            html += `</div>`
                            html += `<div class="col-md-6">`;
                            html += `<p><strong>Fecha registro:</strong> <span>${constanciaCorregir.fecharegistro}</span></p>`;
                            html += `</div>`
                            $('#infoConstancia').html(html);
                            $('#btnRevisarFolio').prop('disabled', false).html('Continuar');
                            $('#smartwizardcorreccion').smartWizard("next");
                            llenarPanelesRequisitos(correccionConstancia);
                        } catch (error) {
                            console.error(error);
                            toastr.error('Hubo un problema al validar los datos. Intente de nuevo por favor.');
                            $('#btnRevisarFolio').prop('disabled', false).html('Continuar');
                            return;
                        }
                        $("#overlay").fadeOut(300);
                    },
                    error: function () {
                        toastr.error('Hubo un problema al validar los datos. Intente de nuevo.');
                        $('#btnRevisarFolio').prop('disabled', false).html('Continuar');
                        $("#overlay").fadeOut(300);
                    }
                });
            }
            return false;
        } else if (currentStepIndex === 1) { // De extraviado a requisitos
            let tipos = requisitosCorregidos.map(t => reqByTitle[t]).filter(Boolean);

            $('.panelRequisito').hide();

            let counter = 0;
            tipos.forEach(tipo => {
                counter++;
                // Match DB requisito to config entry by resolving through reqByTitle (supports dbTitles aliases)
                const requisitoPorCorregir = constanciaCorregir.requisitos.find(obj => {
                    const entry = reqByTitle[(obj.tipo).trim()];
                    return entry && entry.key === tipo.key;
                });
                $('#' + tipo.key).show();
                $('#' + tipo.key).find('.collapse').addClass('show');
                $('#' + tipo.key).find('.btn-link').removeClass('collapsed').attr('aria-expanded', 'true');
                $('#' + tipo.key).find('.btn-link').html(counter + '. ' + (requisitoPorCorregir && requisitoPorCorregir.subtipo == "" ? requisitoPorCorregir.tipo : (requisitoPorCorregir ? requisitoPorCorregir.subtipo : tipo.requisito)));
            });


            console.log('requisitosCorregidosGrupo =>', requisitosCorregidosGrupo);
            requisitosCorregidosGrupo.forEach(grupo => {
                counter++;
                const tiposGrupos = grupo.map(t => reqByTitle[t]).filter(Boolean);
                const tipoGrupo = tiposGrupos.filter(obj => obj.accion == 'Selecciona')[0];

                tiposGrupos.forEach(tipoGrupoActual => {
                    if ($('#' + tipoGrupo.key).find('.card-body').find('.btns-photo-container[data-key="' + tipoGrupoActual.key + '"]').length == 0) {
                        $('#' + tipoGrupo.key).find('.card-body').append($('#' + tipoGrupoActual.key).find('.card-body').html());
                        $('#' + tipoGrupoActual.key).find('.card-body').html("");
                    }
                });

                $('#' + tipoGrupo.key).show();
                $('#' + tipoGrupo.key).find('.collapse').addClass('show');
                $('#' + tipoGrupo.key).find('.btn-link').removeClass('collapsed').attr('aria-expanded', 'true');
                $('#' + tipoGrupo.key).find('.btn-link').html(counter + '. ' + tiposGrupos.map(x => x.accion + ' ' + x.articulo + ' ' + x.requisito).join(" y/o "));
            });

            $('.sw-btn-next').hide();
        }
    });


    $('#formConstanciaHechosCorregir').submit(function (e) {
        e.preventDefault();

        let valid = true;
        requisitosCorregidos.forEach(requisito => {
            const tipo = reqByTitle[requisito];
            if (tipo) {
                const requisitoPorCorregir = constanciaCorregir.requisitos.find(obj => {
                    const entry = reqByTitle[(obj.tipo).trim()];
                    return entry && entry.key === tipo.key;
                });

                if (tipo.ambos) {
                    const photoContainerFrente = $('#photoResult' + tipo.key + 'frente');
                    if (photoContainerFrente.html() != null && photoContainerFrente.html().trim() === '' && tipo.tipos.length > 0 && !checkComplete(tipo.key)) {
                        toastr.error('Debe tomar una foto de ' + (requisitoPorCorregir.subtipo).trim() + ' (Frente) o suba un PDF');
                        valid = false;
                    }

                    const photoContainerReverso = $('#photoResult' + tipo.key + 'reverso');
                    if (photoContainerReverso.html() != null && photoContainerReverso.html().trim() === '' && tipo.tipos.length > 0 && !checkComplete(tipo.key)) {
                        toastr.error('Debe tomar una foto de ' + (requisitoPorCorregir.subtipo).trim() + ' (Reverso) o suba un PDF');
                        valid = false;
                    }
                } else {

                    let skipValidation = false;
                    if (tipo.key === 'Pago' || tipo.key === 'payment' || tipo.key === 'Pago_fake') {
                        const method = $('input[name="payment_method"]:checked').val();
                        if (method === 'card' || method === 'oxxo') {
                            skipValidation = true;
                        }
                    }

                    const photoContainer = $('#photoResult' + tipo.key);
                    if (!skipValidation && photoContainer.html() != null && photoContainer.html().trim() === '' && tipo.tipos.length > 0 && !checkComplete(tipo.key)) {
                        toastr.error('Debe tomar una foto de ' + (requisitoPorCorregir.subtipo).trim() + '  o suba un PDF');
                        valid = false;
                    }
                }

                if (tipo.selfie) {
                    const photoContainerSelfie = $('#photoResult' + tipo.key + 'selfie');
                    if (photoContainerSelfie.html() != null && photoContainerSelfie.html().trim() === '' && tipo.tipos.length > 0 && !checkComplete(tipo.key)) {
                        toastr.error('Debe tomar una selfie con el documento');
                        valid = false;
                    }
                }

            }

        });


        requisitosCorregidosGrupo.forEach(requisito => {
            let groupHasUpload = false;
            let mensajeFaltantes = [];
            requisito.forEach(req => {
                let mensajeFaltante = "";
                const tipo = reqByTitle[req];
                if (tipo && tipo.accion == 'Selecciona') {
                    if (tipo.ambos) {
                        const fHtml = $('#photoResult' + tipo.key + 'frente').html();
                        const rHtml = $('#photoResult' + tipo.key + 'reverso').html();
                        const mHtml = $('#photoResult' + tipo.key).html();

                        const frenteOk = fHtml != null && fHtml.trim() !== '';
                        const reversoOk = rHtml != null && rHtml.trim() !== '';
                        const mainOk = mHtml != null && mHtml.trim() !== '';

                        if ((frenteOk && reversoOk) || mainOk) {
                            groupHasUpload = true;
                        } else if (fHtml != null || rHtml != null) {
                            // Container exists but empty (invalid member)
                            if (!frenteOk && fHtml != null && tipo.tipos.length > 0) {
                                mensajeFaltante += 'Debe tomar una foto de ' + tipo.requisito + ' (Frente) o suba un PDF';
                            }
                            if (!reversoOk && rHtml != null && tipo.tipos.length > 0) {
                                if (mensajeFaltante != "") mensajeFaltante += ' y ';
                                mensajeFaltante += 'Debe tomar una foto de ' + tipo.requisito + ' (Reverso) o suba un PDF';
                            }
                        }
                    } else {
                        const photoContainer = $('#photoResult' + tipo.key);
                        if (photoContainer.html() != null && photoContainer.html().trim() !== '') {
                            groupHasUpload = true;
                        } else if (photoContainer.html() != null && photoContainer.html().trim() === '' && tipo.tipos.length > 0) {
                            mensajeFaltante += 'Debe tomar una foto de ' + tipo.requisito + '  o suba un PDF';
                        }
                    }

                    if (tipo.selfie) {
                        const photoContainerSelfie = $('#photoResult' + tipo.key + 'selfie');
                        if (photoContainerSelfie.html() != null && photoContainerSelfie.html().trim() === '' && tipo.tipos.length > 0) {
                            if (mensajeFaltante != "") mensajeFaltante += ' y ';
                            mensajeFaltante += 'Debe tomar una selfie con el documento';
                        }
                    }

                    if (tipo.tipos.length > 1) {
                        const selected = $('input[name="' + tipo.key + '"]:checked').val();
                        if (!selected) {
                            if (mensajeFaltante != "") mensajeFaltante += ' y ';
                            mensajeFaltante += 'Seleccione el tipo de ' + tipo.requisito;
                        }
                    }
                }

                if (mensajeFaltante != "") mensajeFaltantes.push(mensajeFaltante);
            });

            if (!groupHasUpload) {
                if (mensajeFaltantes.length > 0) {
                    toastr.error(mensajeFaltantes.join(' o '));
                }
                valid = false;
            }

        });

        if (!valid) {
            $('button[type="submit"]').prop('disabled', false).html('Enviar');
            return;
        }

        let requisitos = [];
        Object.values(reqByKey).forEach(tipo => {
            let requisitoPorCorregir = constanciaCorregir.requisitos.find(obj => {
                const entry = reqByTitle[(obj.tipo).trim()];
                return entry && entry.key === tipo.key;
            });

            if (tipo.ambos && $('#photoResult' + tipo.key + 'frente').html() != "" && $('#photoResult' + tipo.key + 'reverso').html() != "") {

                const photoContainerFrente = $('#photoResult' + tipo.key + 'frente');
                const photoFrente = photoContainerFrente.find('img').attr('src');
                if (photoFrente) {
                    requisitos.push({
                        tipo: tipo.requisito + ' (Frente)',
                        subtipo: requisitoPorCorregir ? (requisitoPorCorregir.subtipo).trim() : "",
                        foto: photoFrente
                    });
                }

                const photoContainerReverso = $('#photoResult' + tipo.key + 'reverso');
                const photoReverso = photoContainerReverso.find('img').attr('src');
                if (photoReverso) {
                    requisitos.push({
                        tipo: tipo.requisito + ' (Reverso)',
                        subtipo: requisitoPorCorregir ? (requisitoPorCorregir.subtipo).trim() : "",
                        foto: photoReverso
                    });
                }
            } else {

                const photoContainer = $('#photoResult' + tipo.key);
                const tipoName = requisitoPorCorregir ? (requisitoPorCorregir.tipo).trim() : tipo.requisito;
                const photo = photoContainer.find('img').attr('src');
                if (photo) {
                    requisitos.push({
                        tipo: tipoName,
                        subtipo: requisitoPorCorregir ? (requisitoPorCorregir.subtipo).trim() : "",
                        foto: photo
                    });
                } else {
                    const pdf = photoContainer.find('canvas').data('file');
                    if (pdf) {
                        requisitos.push({
                            tipo: tipoName,
                            subtipo: requisitoPorCorregir ? (requisitoPorCorregir.subtipo).trim() : "",
                            foto: pdf
                        });
                    }
                }
            }

            if (tipo.selfie) {
                const subtipo_obj = constanciaCorregir.requisitos.find(obj => {
                    const entry = reqByTitle[(obj.tipo).trim()];
                    return entry && entry.key === tipo.key;
                });
                if (subtipo_obj) {
                    subtipo = subtipo_obj.subtipo;
                    const photoContainerSelfie = $('#photoResult' + tipo.key + 'selfie');
                    const photo = photoContainerSelfie.find('img').attr('src');
                    if (photo) {
                        requisitos.push({
                            tipo: tipo.requisito + ' (Selfie)',
                            subtipo: subtipo,
                            foto: photo
                        });
                    }
                }
            }
        });

        let data = {
            Id: constanciaCorregir.Id,
            email: constanciaCorregir.email,
            requisitos: requisitos,
            payment_method: $('input[name="payment_method"]:checked').val()
        };

        // Send data to server
        $("#overlay").fadeIn(300);

        $('button[type="submit"]').prop('disabled', true).html('Enviando...');

        $.ajax({
            type: 'POST',
            url: base_url + 'constancias/savecorreccion',
            data: { data: JSON.stringify(data) },
            success: function (response) {
                try {
                    const obj = (response);
                    if (obj.success === false) {
                        toastr.error(obj.message);
                        $('button[type="submit"]').prop('disabled', false).html('Enviar');
                        $("#overlay").fadeOut(300);
                        return;
                    }

                    if (data.payment_method && (data.payment_method === 'card' || data.payment_method === 'oxxo')) {
                        const folioId = obj.folio_id || constanciaCorregir.Id;
                        const folioStr = obj.folio || $('#folio').val();
                        createStripeCheckoutSession(folioId, folioStr, data.payment_method, 'edition');
                        return;
                    }

                    showConfirmationEdition(obj.folio);
                } catch (error) {
                    console.error(error);
                    toastr.error('Hubo un problema al guardar los datos. Intente de nuevo por favor.');
                    $('button[type="submit"]').prop('disabled', false).html('Enviar');
                    $("#overlay").fadeOut(300);
                    return;
                }
                $("#overlay").fadeOut(300);
            },
            error: function () {
                toastr.error('Hubo un problema al guardar los datos. Intente de nuevo.');
                $('button[type="submit"]').prop('disabled', false).html('Enviar');
                $("#overlay").fadeOut(300);
            }
        });

    });


    $('input[name="extravio"]:checked').trigger("change");
});



function showConfirmationEdition(folio){
    toastr.success('Gracias, en breve recibirá un correo de confirmación.');
    $('button[type="submit"]').prop('disabled', true).html('Enviado').removeClass('btn-primary').addClass('btn-success');

    $('#formConstanciaHechosCorregir').hide();
    $('#smartwizardcorreccion').hide();
    $('.instrucciones').hide();
    $('#message-container').show();
    $('#fiscalia-title').html('Fiscalía Digital Coahuila');
    $('#folioGuardado').html(folio);
    setTimeout(() => {
        $('#smartwizardcorreccion').smartWizard("fixHeight");
    }, 500);
}

// ==== Payment methods ====>
$(document).on('change', 'input[name="payment_method"]', function (e) {
    const selectedMethod = e.target.value;
    //this.currentPaymentMethod = selectedMethod;
    togglePaymentSections(selectedMethod);
    updateSubmitButton(selectedMethod);
});

function togglePaymentSections(method) {
    const stripeSection = $('#stripe-payment-section');
    const uploadSection = $('#upload-payment-section');

    if (method === 'card' || method === 'oxxo') {
        stripeSection.slideDown();
        uploadSection.slideUp();
    } else if (method === 'upload') {
        stripeSection.slideUp();
        uploadSection.slideDown();
    }
}

function updateSubmitButton(method) {
    const submitBtn = $('.btnenviar');
    const submitText = submitBtn.find('.submit-text');

    if (method === 'card') {
        submitText.text('Continuar con el Pago');
        submitBtn.removeClass('btn-success').addClass('btn-primary');
    } else if (method === 'oxxo') {
        submitText.text('Generar Ficha OXXO');
        submitBtn.removeClass('btn-success').addClass('btn-primary');
    } else if (method === 'upload') {
        submitText.text('Enviar Constancia');
        submitBtn.removeClass('btn-primary').addClass('btn-success');
    }
}

function createStripeCheckoutSession(folioId, folio, paymentMethod, mode) {
    console.log('Creating Stripe checkout session for folio:', folio, 'Payment method:', paymentMethod);

    $.ajax({
        url: base_url + 'payment/create_checkout_session',
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            folio_id: folioId,
            mode: mode,
            payment_method: paymentMethod || 'card'
        }),
        success: (response) => {
            console.log('Stripe session response:', response);
            
            try {
                if(mode === 'edition' && response.type === 'any' && response.paid){
                    showConfirmationEdition(folio);
                    $("#overlay").fadeOut(300);
                }else if (response.type === 'card' && response.url) {
                    // Redirect to Stripe Checkout for card payments
                    toastr.success('Redirigiendo al pago seguro...');
                    setTimeout(() => {
                        window.location.href = response.url;
                    }, 1000);
                } else if (response.type === 'oxxo' && response.payment_intent_id) {
                    // Redirect to custom OXXO voucher page
                    toastr.success('Generando ficha OXXO...');
                    setTimeout(() => {
                        window.location.href = base_url + 'payment/oxxo_voucher/' + response.payment_intent_id;
                    }, 1000);
                } else if (response.error) {
                    toastr.error(response.error);
                    $('#error-message').show();
                    $('#error-message').html(response.error);
                    $('button[type="submit"]').prop('disabled', false).html('Enviar');
                    $("#overlay").fadeOut(300);
                } else {
                    const errorMsg = 'Respuesta inesperada del servidor';
                    toastr.error(errorMsg);
                    $('#error-message').show();
                    $('#error-message').html(errorMsg);
                    $('button[type="submit"]').prop('disabled', false).html('Enviar');
                    $("#overlay").fadeOut(300);
                }
            } catch (error) {
                console.error('Error processing payment response:', error);
                const errorMsg = error.message || 'Error al procesar la respuesta del pago';
                toastr.error(errorMsg);
                $('#error-message').show();
                $('#error-message').html(errorMsg);
                $('button[type="submit"]').prop('disabled', false).html('Enviar');
                $("#overlay").fadeOut(300);
            }
        },
        error: (xhr, status, error) => {
            console.error('Error creating Stripe session:', error);
            let errorMessage = 'Error al procesar el pago';
            try {
                const errorResponse = (xhr.responseText);
                errorMessage = errorResponse.error || errorMessage;
            } catch (e) {}
            
            toastr.error(errorMessage);
            $('#error-message').show();
            $('#error-message').html(errorMessage);
            $('button[type="submit"]').prop('disabled', false).html('Enviar');
            $("#overlay").fadeOut(300);
        },
        dataType: 'json'
    });
}