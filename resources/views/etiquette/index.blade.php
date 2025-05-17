<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur d'Étiquettes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f0f0f0;
        }
        .etiquette-preview {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }
        .barcode-container {
            background: white;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
            transition: all 0.3s ease;
            max-width: none;
            width: 168px;
            height: 82px;
            overflow: visible;
            position: relative;
            border: 1px solid #ddd;
            margin: 0;
            box-sizing: border-box;
            resize: both;
        }
        .barcode-container::after {
            content: '';
            position: absolute;
            right: 3px;
            bottom: 3px;
            width: 10px;
            height: 10px;
            border-right: 2px solid #007bff;
            border-bottom: 2px solid #007bff;
            cursor: nwse-resize;
        }
        .draggable {
            position: absolute;
            cursor: move;
            user-select: none;
            border: 1px dashed transparent;
            padding: 0;
        }
        .draggable:hover {
            border-color: #007bff;
        }
        .dimensions-display {
            position: absolute;
            background: rgba(0, 123, 255, 0.8);
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 10px;
            pointer-events: none;
            display: none;
            z-index: 1000;
        }
        .draggable:hover .dimensions-display {
            display: block;
        }
        .draggable.logo-container {
            width: 30px;
            height: 30px;
            top: 45px;
            left: 25px;
        }
        .draggable.barcode-element {
            width: 142px;
            height: 52px;
            top: 5px;
            left: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .draggable.barcode-element #barcode-text {
            font-size: 10px;
            margin-top: 5px;
            word-break: break-all;
        }
        .draggable.price-container {
            width: 121px;
            height: 29px;
            top: 50px;
            left: 48px;
            text-align: center;
        }
        .draggable.price-container #prix-preview {
            font-weight: bold;
            font-size: 1.4rem;
            width: 100%;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .resize-handle {
            width: 10px;
            height: 10px;
            background-color: #007bff;
            position: absolute;
            border-radius: 50%;
            display: none;
        }
        .resize-handle.right {
            right: -5px;
            top: 50%;
            transform: translateY(-50%);
            cursor: ew-resize;
        }
        .resize-handle.bottom {
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            cursor: ns-resize;
        }
        .draggable:hover .resize-handle {
            display: block;
        }
        .barcode-container svg {
            max-width: 100%;
            height: auto;
        }
        .barcode-container img {
            max-width: 100%;
            height: auto;
        }
        .barcode-container .h4 {
            word-wrap: break-word;
            font-size: 1.2rem;
        }
        .logo-iu {
            width: 30px;
            height: 30px;
            object-fit: contain;
        }
        .barcode-element svg {
            width: 100% !important;
            height: 100%;
        }
        @media print {
            @page {
                margin: 0;
                size: auto;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .container, .row, .col-md-6, .etiquette-preview {
                margin: 0 !important;
                padding: 0 !important;
                width: auto !important;
            }
            .etiquette {
                page-break-inside: avoid;
                margin: 0 !important;
                padding: 0 !important;
            }
            .barcode-container {
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                display: block !important;
                width: 100% !important;
                resize: none !important;
            }
            .barcode-container::after {
                display: none !important;
            }
            .dimensions-display, .resize-handle {
                display: none !important;
            }
            #logo-dimensions, #barcode-dimensions, #price-dimensions {
                display: none !important;
            }
            .draggable {
                position: absolute !important;
                transform: none !important;
            }
            .barcode-container {
                position: relative !important;
            }
            /* Hide everything except the etiquette content */
            .container > *:not(.row),
            .row > .col-md-6:last-child,
            h1 {
                display: none !important;
            }
        }

        /* Styles pour la prévisualisation du format */
        .format-preview-container {
            text-align: center;
        }
        .paper-preview {
            position: relative;
            margin: 0 auto;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            transform-origin: top left;
            overflow: hidden;
        }
        .etiquette-preview-container {
            position: absolute;
            border: 1px dashed #007bff;
            background-color: rgba(0, 123, 255, 0.1);
        }
        .paper-preview::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 1px solid #ddd;
            pointer-events: none;
        }
        .format-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">ETTIQUETTE GENERATOR</h1>
        
        <div class="row">
            <!-- Zone de prévisualisation -->
            <div class="col-md-6">
                <div class="etiquette-preview p-4">
                    <div class="barcode-container" id="etiquette-container">
                        <div class="draggable logo-container">
                            <div class="dimensions-display" id="logo-dimensions"></div>
                            <img src="{{ asset('images/wamp.png') }}" alt="IU Logo" class="logo-iu me-2">
                            <div class="resize-handle right"></div>
                            <div class="resize-handle bottom"></div>
                        </div>
                        <div class="draggable barcode-element">
                            <div class="dimensions-display" id="barcode-dimensions"></div>
                            <svg id="barcode"></svg>
                            <div class="text-center" id="barcode-text" style="margin-top: -10px;"></div>

                            <div class="resize-handle right"></div>
                            <div class="resize-handle bottom"></div>
                        </div>
                        <div class="draggable price-container">
                            <div class="dimensions-display" id="price-dimensions"></div>
                            <div class="w-100 d-flex justify-content-center align-items-center">
                                <span class="h4 w-100" id="prix-preview" style="font-size: 1.8rem;">100.000 Fr</span>
                            </div>
                            <div class="resize-handle right"></div>
                            <div class="resize-handle bottom"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="col-md-6">
                <form id="etiquetteForm" class="bg-white p-4 rounded shadow-sm">
                    <div class="mb-3">
                        <label for="code" class="form-label">Code</label>
                        <input type="text" class="form-control" id="code" name="code" required>
                    </div>

                    <div class="mb-3">
                        <label for="prix" class="form-label">Prix</label>
                        <input type="number" class="form-control" id="prix" name="prix" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Format de page</label>
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-select" id="page_format" name="page_format">
                                    <option value="thermal_50x30" selected>50mm × 30mm</option>
                                    <option value="a4">A4</option>
                                    <option value="a5">A5</option>
                                    <option value="a6">A6</option>
                                    <option value="letter">Letter</option>
                                    <option value="legal">Legal</option>
                                    <optgroup label="Xprinter-235B">
                                        <option value="thermal_20x30">20mm × 30mm</option>
                                        <option value="thermal_30x20">30mm × 20mm</option>
                                        <option value="thermal_40x30">40mm × 30mm</option>
                                        <option value="thermal_58">Rouleau 58mm</option>
                                        <option value="thermal_80">Rouleau 80mm</option>
                                    </optgroup>
                                    <option value="custom">Personnalisé</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select" id="page_orientation" name="page_orientation">
                                    <option value="portrait">Portrait</option>
                                    <option value="landscape">Paysage</option>
                                </select>
                            </div>
                        </div>
                        <div id="custom_page_dimensions" class="row mt-2" style="display: none;">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="number" class="form-control" id="page_width" placeholder="Largeur">
                                    <span class="input-group-text">mm</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="number" class="form-control" id="page_height" placeholder="Hauteur">
                                    <span class="input-group-text">mm</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 text-muted">
                            <small>Estimation: <span id="labels_per_page">0</span> étiquettes par page</small>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="preview_format_btn">
                                <i class="bi bi-eye"></i> Prévisualiser le format
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nombre_impression" class="form-label">Nombre Impression</label>
                        <input type="number" class="form-control" id="nombre_impression" name="nombre_impression" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="largeur" class="form-label">Largeur (px)</label>
                            <input type="number" class="form-control" id="largeur" name="largeur" value="168" min="100" max="500">
                        </div>
                        <div class="col-md-6">
                            <label for="hauteur" class="form-label">Hauteur (px)</label>
                            <input type="number" class="form-control" id="hauteur" name="hauteur" value="82" min="30" max="500">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary" id="imprimer">Imprimer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('etiquette-container');
            const largeurInput = document.getElementById('largeur');
            const hauteurInput = document.getElementById('hauteur');
            const codeInput = document.getElementById('code');
            const prixInput = document.getElementById('prix');
            const prixPreview = document.getElementById('prix-preview');
            const pageFormatSelect = document.getElementById('page_format');
            const customDimensions = document.getElementById('custom_page_dimensions');
            const pageWidthInput = document.getElementById('page_width');
            const pageHeightInput = document.getElementById('page_height');

            // Configuration de interact.js pour les éléments déplaçables
            interact('.draggable').draggable({
                inertia: true,
                modifiers: [
                    interact.modifiers.restrictRect({
                        restriction: 'parent',
                        endOnly: true
                    })
                ],
                listeners: {
                    move: dragMoveListener
                }
            }).resizable({
                edges: { 
                    left: true, 
                    right: true, 
                    bottom: true, 
                    top: true 
                },
                restrictEdges: {
                    outer: 'parent',
                    endOnly: true,
                },
                restrictSize: {
                    min: { width: 20, height: 20 },
                    max: { width: 300, height: 200 }
                },
                inertia: true,
                listeners: {
                    move: function (event) {
                        const target = event.target;
                        let x = (parseFloat(target.getAttribute('data-x')) || 0);
                        let y = (parseFloat(target.getAttribute('data-y')) || 0);

                        // Mise à jour des dimensions
                        target.style.width = `${event.rect.width}px`;
                        target.style.height = `${event.rect.height}px`;

                        // Ajustement de la position si redimensionné depuis la gauche/haut
                        if (event.edges.left) {
                            x += event.deltaRect.left;
                        }
                        if (event.edges.top) {
                            y += event.deltaRect.top;
                        }

                        target.style.transform = `translate(${x}px, ${y}px)`;
                        target.setAttribute('data-x', x);
                        target.setAttribute('data-y', y);

                        // Ajustements spécifiques selon le type d'élément
                        if (target.classList.contains('barcode-element')) {
                            // Régénérer le code-barres avec les nouvelles dimensions
                            const codeValue = document.getElementById('code').value || 'CLEEP10FR';
                            JsBarcode("#barcode", codeValue, {
                                height: event.rect.height - 20, // Réduire la hauteur pour le texte
                                width: 2,
                                margin: 0,
                                displayValue: false
                            });
                        } else if (target.classList.contains('price-container')) {
                            // Ajuster la taille du texte du prix
                            adjustPriceTextSize();
                        }

                        // Mettre à jour l'affichage des dimensions
                        updateDimensionsDisplay(target, Math.round(event.rect.width), Math.round(event.rect.height));
                    }
                }
            });

            function dragMoveListener(event) {
                var target = event.target;
                var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

                target.style.transform = `translate(${x}px, ${y}px)`;
                target.setAttribute('data-x', x);
                target.setAttribute('data-y', y);
            }

            // Fonction pour mettre à jour les dimensions
            function updateDimensions() {
                const largeur = largeurInput.value;
                const hauteur = hauteurInput.value;
                container.style.width = `${largeur}px`;
                container.style.height = `${hauteur}px`;
                container.style.minWidth = 'auto';
                container.style.minHeight = 'auto';
                container.style.maxWidth = 'none';
                container.style.maxHeight = 'none';
            }

            // Fonction pour formater le prix
            function formatPrix(prix) {
                return new Intl.NumberFormat('fr-FR').format(prix) + ' Fr';
            }

            // Appliquer les dimensions initiales
            updateDimensions();

            // Écouter les changements de dimensions
            largeurInput.addEventListener('input', updateDimensions);
            hauteurInput.addEventListener('input', updateDimensions);

            // Modifier la fonction de génération initiale du code-barres
            function generateBarcode(value) {
                const barcodeElement = document.querySelector('.barcode-element');
                const height = barcodeElement ? parseInt(barcodeElement.style.height) || 100 : 100;
                const codeValue = value || 'CLEEP10FR';
                
                const barcodeWidth = barcodeElement.offsetWidth;
                JsBarcode("#barcode", codeValue, {
                    height: height,
                    width: barcodeWidth / codeValue.length,
                    margin: 0,
                    displayValue: false,
                    fontSize: 12
                });

                // Mettre à jour le texte sous le code-barres
                document.getElementById('barcode-text').textContent = codeValue;
            }

            // Générer le code-barres initial
            generateBarcode();

            // Écouter les changements du code en temps réel
            codeInput.addEventListener('input', function(e) {
                if(e.target.value.trim() !== '') {
                    generateBarcode(e.target.value);
                }
            });

            // Fonction pour ajuster la taille du texte du prix
            function adjustPriceTextSize() {
                const priceContainer = document.querySelector('.price-container');
                const prixPreview = document.getElementById('prix-preview');
                const containerWidth = priceContainer.offsetWidth;
                
                // Ajuster la taille de police en fonction de la largeur du conteneur
                if (containerWidth < 100) {
                    prixPreview.style.fontSize = '0.9rem';
                } else if (containerWidth < 140) {
                    prixPreview.style.fontSize = '1.3rem';
                } else if (containerWidth < 190) {
                    prixPreview.style.fontSize = '1.4rem';
                } else {
                    prixPreview.style.fontSize = '1.7rem';
                }
            }
            
            // Observer les changements de taille du conteneur de prix
            const priceResizeObserver = new ResizeObserver(entries => {
                adjustPriceTextSize();
            });
            
            // Observer le conteneur de prix
            priceResizeObserver.observe(document.querySelector('.price-container'));
            
            // Ajuster la taille du texte initialement
            adjustPriceTextSize();
            
            // Initialiser le prix avec la valeur par défaut formatée
            prixPreview.textContent = formatPrix(100000);
            
            // Ajouter un listener pour les changements de prix
            prixInput.addEventListener('input', function(e) {
                const valeur = e.target.value;
                if(valeur) {
                    prixPreview.textContent = formatPrix(valeur);
                    adjustPriceTextSize();
                } else {
                    prixPreview.textContent = '0 Fr';
                    adjustPriceTextSize();
                }
            });

            // Gestion de l'impression
            document.getElementById('etiquetteForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const nombreImpressions = document.getElementById('nombre_impression').value || 1;
                const pageFormat = document.getElementById('page_format').value;
                const pageOrientation = document.getElementById('page_orientation').value;
                const printWindow = window.open('', '_blank');
                
                // Vérifier si c'est une impression thermique
                const isThermalPrinter = pageFormat.includes('thermal_');
                
                let printContent = '<html><head><title>Étiquettes à imprimer</title>';
                
                // Copier tous les styles de la page actuelle
                const styles = Array.from(document.styleSheets)
                    .map(styleSheet => {
                        try {
                            return Array.from(styleSheet.cssRules)
                                .map(rule => rule.cssText)
                                .join('\n');
                        } catch (e) {
                            return '';
                        }
                    })
                    .join('\n');
                
                // Ajouter les styles pour le format de page
                let pageSize;
                
                if (pageFormat === 'custom') {
                    // Utiliser les dimensions personnalisées
                    const pageWidth = document.getElementById('page_width').value || 210;
                    const pageHeight = document.getElementById('page_height').value || 297;
                    pageSize = `${pageWidth}mm ${pageHeight}mm`;
                } else {
                    pageSize = pageFormat;
                }
                
                let pageStyleContent = `
                    @page {
                        size: ${pageSize} ${pageOrientation};
                        margin: 0;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                    }
                    .etiquette {
                        page-break-inside: avoid;
                    }
                `;
                
                // Styles spécifiques pour les imprimantes thermiques
                if (isThermalPrinter) {
                    pageStyleContent += `
                        .etiquette {
                            width: 100%;
                            height: auto;
                            overflow: hidden;
                        }
                        .barcode-container {
                            border: none !important;
                            box-shadow: none !important;
                            padding: 0 !important;
                        }
                        .barcode-element svg {
                            max-height: 40px;
                        }
                    `;
                }
                
                printContent += `<style>${styles}${pageStyleContent}</style>`;
                printContent += '</head><body>';

                // Cloner l'étiquette avec sa disposition exacte
                const etiquetteContainer = document.getElementById('etiquette-container');
                const draggables = etiquetteContainer.querySelectorAll('.draggable');
                
                for (let i = 0; i < nombreImpressions; i++) {
                    const etiquetteClone = etiquetteContainer.cloneNode(true);
                    
                    // Copier les positions exactes de chaque élément
                    etiquetteClone.querySelectorAll('.draggable').forEach((clone, index) => {
                        const original = draggables[index];
                        const rect = original.getBoundingClientRect();
                        const containerRect = etiquetteContainer.getBoundingClientRect();
                        
                        clone.style.left = (rect.left - containerRect.left) + 'px';
                        clone.style.top = (rect.top - containerRect.top) + 'px';
                        clone.style.width = rect.width + 'px';
                        clone.style.height = rect.height + 'px';
                    });
                    
                    printContent += `<div class="etiquette">${etiquetteClone.outerHTML}</div>`;
                    
                    // Ajouter un saut de ligne pour les formats thermiques
                    if (isThermalPrinter && i < nombreImpressions - 1) {
                        printContent += '<div style="page-break-after: always;"></div>';
                    }
                }
                
                printContent += '</body></html>';
                
                printWindow.document.write(printContent);
                printWindow.document.close();
                
                printWindow.onload = function() {
                    printWindow.print();
                    printWindow.onafterprint = function() {
                        printWindow.close();
                    };
                };
            });

            // Fonction pour mettre à jour l'affichage des dimensions
            function updateDimensionsDisplay(element, width, height) {
                let dimensionsElement;
                if (element.classList.contains('logo-container')) {
                    dimensionsElement = document.getElementById('logo-dimensions');
                } else if (element.classList.contains('barcode-element')) {
                    dimensionsElement = document.getElementById('barcode-dimensions');
                } else if (element.classList.contains('price-container')) {
                    dimensionsElement = document.getElementById('price-dimensions');
                }

                if (dimensionsElement) {
                    dimensionsElement.textContent = `${width}px × ${height}px`;
                }
            }

            // Fonction pour calculer le nombre d'étiquettes par page
            function updateLabelsPerPage() {
                const pageFormat = document.getElementById('page_format').value;
                const pageOrientation = document.getElementById('page_orientation').value;
                const labelWidth = parseInt(document.getElementById('largeur').value) || 200;
                const labelHeight = parseInt(document.getElementById('hauteur').value) || 100;
                
                // Dimensions des formats de page en millimètres
                const pageSizes = {
                    'a4': { width: 210, height: 297 },
                    'a5': { width: 148, height: 210 },
                    'a6': { width: 105, height: 148 },
                    'letter': { width: 215.9, height: 279.4 },
                    'legal': { width: 215.9, height: 355.6 },
                    // Formats pour Xprinter-235B
                    'thermal_20x30': { width: 20, height: 30 },
                    'thermal_30x20': { width: 30, height: 20 },
                    'thermal_40x30': { width: 40, height: 30 },
                    'thermal_50x30': { width: 50, height: 30 },
                    'thermal_58': { width: 58, height: 100 }, // Hauteur arbitraire pour le rouleau
                    'thermal_80': { width: 80, height: 100 }  // Hauteur arbitraire pour le rouleau
                };
                
                // Convertir les dimensions de l'étiquette de px en mm (approximatif: 1px = 0.26mm)
                const labelWidthMm = labelWidth * 0.26;
                const labelHeightMm = labelHeight * 0.26;
                
                // Obtenir les dimensions de la page
                let pageWidth, pageHeight;
                
                if (pageFormat === 'custom') {
                    // Utiliser les dimensions personnalisées
                    pageWidth = parseInt(document.getElementById('page_width').value) || 210;
                    pageHeight = parseInt(document.getElementById('page_height').value) || 297;
                } else {
                    // Utiliser les dimensions prédéfinies
                    pageWidth = pageSizes[pageFormat].width;
                    pageHeight = pageSizes[pageFormat].height;
                }
                
                // Inverser si orientation paysage
                if (pageOrientation === 'landscape') {
                    [pageWidth, pageHeight] = [pageHeight, pageWidth];
                }
                
                // Vérifier si c'est un format à rouleau continu
                const isContinuousRoll = pageFormat === 'thermal_58' || pageFormat === 'thermal_80';
                
                // Calculer combien d'étiquettes peuvent tenir sur la page
                let totalLabelsPerPage;
                
                if (isContinuousRoll) {
                    // Pour les rouleaux, calculer uniquement sur la largeur et afficher différemment
                    const labelsPerRow = Math.floor(pageWidth / labelWidthMm);
                    document.getElementById('labels_per_page').textContent = 
                        labelsPerRow > 0 ? `${labelsPerRow} étiquettes par ligne` : "L'étiquette est trop large";
                    return;
                } else {
                    // Pour les formats standards, calculer normalement
                    // On compte 5mm de marge sur chaque côté
                    const usableWidth = pageWidth - 10;
                    const usableHeight = pageHeight - 10;
                    
                    const labelsPerRow = Math.floor(usableWidth / labelWidthMm);
                    const labelsPerColumn = Math.floor(usableHeight / labelHeightMm);
                    
                    totalLabelsPerPage = labelsPerRow * labelsPerColumn;
                    
                    // Mettre à jour l'affichage
                    document.getElementById('labels_per_page').textContent = totalLabelsPerPage;
                }
            }

            // Initialiser l'affichage des dimensions pour chaque élément
            document.querySelectorAll('.draggable').forEach(element => {
                const width = element.offsetWidth;
                const height = element.offsetHeight;
                updateDimensionsDisplay(element, width, height);
            });
            
            // Calculer initialement le nombre d'étiquettes par page
            updateLabelsPerPage();
            
            // Ajouter des listeners pour les changements de format et dimensions
            document.getElementById('page_format').addEventListener('change', updateLabelsPerPage);
            document.getElementById('page_orientation').addEventListener('change', updateLabelsPerPage);
            document.getElementById('largeur').addEventListener('input', updateLabelsPerPage);
            document.getElementById('hauteur').addEventListener('input', updateLabelsPerPage);

            // Afficher/masquer les dimensions personnalisées selon le format sélectionné
            pageFormatSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customDimensions.style.display = 'flex';
                } else {
                    customDimensions.style.display = 'none';
                }
                updateLabelsPerPage();
            });

            // Ajouter des listeners pour les champs de dimensions personnalisées
            pageWidthInput.addEventListener('input', updateLabelsPerPage);
            pageHeightInput.addEventListener('input', updateLabelsPerPage);

            // Prévisualisation du format de papier
            const previewFormatBtn = document.getElementById('preview_format_btn');
            const formatPreviewModal = new bootstrap.Modal(document.getElementById('formatPreviewModal'));
            
            previewFormatBtn.addEventListener('click', function() {
                // Récupérer les informations de format
                const pageFormat = document.getElementById('page_format').value;
                const pageOrientation = document.getElementById('page_orientation').value;
                
                // Dimensions des formats de page en millimètres
                const pageSizes = {
                    'a4': { width: 210, height: 297, name: 'A4' },
                    'a5': { width: 148, height: 210, name: 'A5' },
                    'a6': { width: 105, height: 148, name: 'A6' },
                    'letter': { width: 215.9, height: 279.4, name: 'Letter' },
                    'legal': { width: 215.9, height: 355.6, name: 'Legal' },
                    'thermal_20x30': { width: 20, height: 30, name: 'Étiquette 20×30mm' },
                    'thermal_30x20': { width: 30, height: 20, name: 'Étiquette 30×20mm' },
                    'thermal_40x30': { width: 40, height: 30, name: 'Étiquette 40×30mm' },
                    'thermal_50x30': { width: 50, height: 30, name: 'Étiquette 50×30mm' },
                    'thermal_58': { width: 58, height: 100, name: 'Rouleau 58mm' },
                    'thermal_80': { width: 80, height: 100, name: 'Rouleau 80mm' }
                };
                
                // Obtenir les dimensions du papier
                let pageWidth, pageHeight, formatName;
                
                if (pageFormat === 'custom') {
                    // Utiliser les dimensions personnalisées
                    pageWidth = parseInt(document.getElementById('page_width').value) || 210;
                    pageHeight = parseInt(document.getElementById('page_height').value) || 297;
                    formatName = 'Personnalisé';
                } else {
                    // Utiliser les dimensions prédéfinies
                    pageWidth = pageSizes[pageFormat].width;
                    pageHeight = pageSizes[pageFormat].height;
                    formatName = pageSizes[pageFormat].name;
                }
                
                // Obtenir les dimensions de l'étiquette
                const labelWidth = parseInt(document.getElementById('largeur').value) || 200;
                const labelHeight = parseInt(document.getElementById('hauteur').value) || 100;
                
                // Convertir les dimensions de l'étiquette de px en mm (approximatif: 1px = 0.26mm)
                const labelWidthMm = labelWidth * 0.26;
                const labelHeightMm = labelHeight * 0.26;
                
                // Dimensions réelles à utiliser selon l'orientation
                let displayWidth = pageWidth;
                let displayHeight = pageHeight;
                let orientationName = 'Portrait';
                
                if (pageOrientation === 'landscape') {
                    displayWidth = pageHeight;
                    displayHeight = pageWidth;
                    orientationName = 'Paysage';
                }
                
                // Calculer le nombre d'étiquettes par page
                // On compte 5mm de marge sur chaque côté
                const usableWidth = displayWidth - 10;
                const usableHeight = displayHeight - 10;
                
                const labelsPerRow = Math.floor(usableWidth / labelWidthMm);
                const labelsPerColumn = Math.floor(usableHeight / labelHeightMm);
                
                const totalLabelsPerPage = labelsPerRow * labelsPerColumn;
                const labelsCountText = (pageFormat === 'thermal_58' || pageFormat === 'thermal_80') 
                    ? `${labelsPerRow} étiquettes par ligne` 
                    : `${totalLabelsPerPage} (${labelsPerRow} × ${labelsPerColumn})`;
                
                // Mettre à jour les informations dans la modal
                document.getElementById('preview-format-name').textContent = formatName;
                document.getElementById('preview-dimensions').textContent = `${displayWidth} × ${displayHeight} mm`;
                document.getElementById('preview-orientation').textContent = orientationName;
                document.getElementById('preview-labels-count').textContent = labelsCountText;
                
                // Échelle pour l'affichage (pour que le papier soit bien visible dans la modal)
                // Taille maximale de la prévisualisation en pixels
                const maxPreviewWidth = 500;
                const maxPreviewHeight = 400;
                
                // Calculer l'échelle pour s'adapter à l'espace disponible
                const scaleX = maxPreviewWidth / displayWidth;
                const scaleY = maxPreviewHeight / displayHeight;
                const scale = Math.min(scaleX, scaleY);
                
                // Appliquer les dimensions et l'échelle à la prévisualisation du papier
                const paperPreview = document.querySelector('.paper-preview');
                paperPreview.style.width = `${displayWidth * scale}px`;
                paperPreview.style.height = `${displayHeight * scale}px`;
                
                // Générer la grille d'étiquettes dans la prévisualisation
                const previewContainer = document.querySelector('.etiquette-preview-container');
                previewContainer.innerHTML = '';
                
                // Positionner la preview au centre
                previewContainer.style.left = '5mm';
                previewContainer.style.top = '5mm';
                previewContainer.style.width = `${usableWidth * scale}px`;
                previewContainer.style.height = `${usableHeight * scale}px`;
                
                // Créer une visualisation des étiquettes
                for (let row = 0; row < labelsPerColumn; row++) {
                    for (let col = 0; col < labelsPerRow; col++) {
                        const etiquette = document.createElement('div');
                        etiquette.className = 'preview-etiquette';
                        etiquette.style.position = 'absolute';
                        etiquette.style.left = `${col * labelWidthMm * scale}px`;
                        etiquette.style.top = `${row * labelHeightMm * scale}px`;
                        etiquette.style.width = `${labelWidthMm * scale}px`;
                        etiquette.style.height = `${labelHeightMm * scale}px`;
                        etiquette.style.border = '1px solid rgba(0, 123, 255, 0.5)';
                        etiquette.style.backgroundColor = 'rgba(0, 123, 255, 0.2)';
                        previewContainer.appendChild(etiquette);
                    }
                }
                
                // Afficher la modal
                formatPreviewModal.show();
            });

            // Rendre le conteneur principal redimensionnable
            interact('#etiquette-container').resizable({
                edges: { right: true, bottom: true },
                listeners: {
                    move: function(event) {
                        const target = event.target;
                        const width = event.rect.width;
                        const height = event.rect.height;
                        
                        // Mettre à jour les dimensions du conteneur
                        target.style.width = width + 'px';
                        target.style.height = height + 'px';
                        
                        // Mettre à jour les champs de formulaire
                        document.getElementById('largeur').value = Math.round(width);
                        document.getElementById('hauteur').value = Math.round(height);
                        
                        // Mettre à jour l'estimation d'étiquettes par page
                        updateLabelsPerPage();
                    }
                },
                modifiers: [
                    interact.modifiers.restrictSize({
                        min: { width: 100, height: 60 }
                    })
                ]
            });
        });
    </script>
    
    <!-- Modal de prévisualisation du format -->
    <div class="modal fade" id="formatPreviewModal" tabindex="-1" aria-labelledby="formatPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formatPreviewModalLabel">Prévisualisation du format</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="format-preview-container">
                        <div class="paper-preview">
                            <div class="etiquette-preview-container"></div>
                        </div>
                        <div class="format-info mt-3">
                            <p><strong>Format:</strong> <span id="preview-format-name"></span></p>
                            <p><strong>Dimensions:</strong> <span id="preview-dimensions"></span></p>
                            <p><strong>Orientation:</strong> <span id="preview-orientation"></span></p>
                            <p><strong>Étiquettes par page:</strong> <span id="preview-labels-count"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 