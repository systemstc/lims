<head>
    <base href="../../../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{ asset('frontAssets/textiles_logo_200.png') }}">
    <!-- Page Title  -->
    <title>@yield('title', 'LIMS | Textiles Committee Laboratories')</title>
    <!-- StyleSheets  -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('backAssets/css/dashlite.css') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('backAssets/css/theme.css') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hyperformula/dist/hyperformula.full.min.js"></script>
    <script src="{{ asset('backAssets/js/jquery.js') }}"></script>

    <script>
        /**
         * SummernoteTableFormulaEngine
         * Provides Excel-like formula support for standard HTML tables in Summernote.
         */
        window.SummernoteTableFormulaEngine = {
            hf: null,
            sheetName: 'Sheet1',

            init: function() {
                if (this.hf) return true;
                try {
                    this.hf = HyperFormula.buildEmpty({
                        licenseKey: 'gpl-v3'
                    });
                    this.hf.addSheet(this.sheetName);
                    return true;
                } catch (e) {
                    console.error('SummernoteTableFormulaEngine: HF Init Error', e);
                    return false;
                }
            },

            calculate: function(container) {
                if (!container) return;
                try {
                    if (!this.init()) return;

                    let sheetId = this.hf.getSheetId(this.sheetName);
                    if (sheetId === undefined) {
                        this.hf.addSheet(this.sheetName);
                        sheetId = this.hf.getSheetId(this.sheetName);
                    }

                    const tables = container.querySelectorAll('table');
                    if (tables.length === 0) return;

                    this.hf.clearSheet(sheetId);

                    tables.forEach((table, tableIndex) => {
                        this.processTable(table, tableIndex, sheetId);
                    });
                } catch (error) {
                    console.error('SummernoteTableFormulaEngine: Calculation Error', error);
                }
            },

            processTable: function(table, tableIndex, sheetId) {
                const rows = table.querySelectorAll('tr');
                const rowOffset = tableIndex * 1000;

                // 1. Map data and detect formulas
                rows.forEach((tr, rowIndex) => {
                    const cells = tr.querySelectorAll('td, th');
                    cells.forEach((td, colIndex) => {
                        let content = td.innerText.trim();
                        let currentFormula = td.getAttribute('data-formula') || "";
                        let lastResult = td.getAttribute('data-last-result') || "";
                        let formulaToUse = null;

                        if (content.startsWith('=')) {
                            formulaToUse = content;
                        } else if (currentFormula) {
                            if (content == lastResult || content === "") {
                                formulaToUse = currentFormula;
                            } else {
                                td.removeAttribute('data-formula');
                                td.removeAttribute('data-last-result');
                            }
                        }

                        const addr = {
                            sheet: sheetId,
                            row: rowOffset + rowIndex,
                            col: colIndex
                        };
                        if (formulaToUse) {
                            this.hf.setCellContents(addr, formulaToUse);
                            td.setAttribute('data-formula', formulaToUse);
                        } else {
                            const numStr = content.replace(/,/g, '');
                            const num = parseFloat(numStr);
                            this.hf.setCellContents(addr, (numStr !== "" && !isNaN(num)) ? num :
                                content);
                        }
                    });
                });

                // 2. Update results
                rows.forEach((tr, rowIndex) => {
                    const cells = tr.querySelectorAll('td, th');
                    cells.forEach((td, colIndex) => {
                        const addr = {
                            sheet: sheetId,
                            row: rowOffset + rowIndex,
                            col: colIndex
                        };
                        const result = this.hf.getCellValue(addr);
                        const formula = this.hf.getCellFormula(addr);

                        if (formula) {
                            let displayResult = result;
                            if (typeof result === 'number') {
                                displayResult = Math.round(result * 100) / 100;
                            } else if (result && result.message) {
                                displayResult = '#ERROR!';
                            }
                            td.innerText = (displayResult === null || displayResult === undefined) ?
                                '' : displayResult;
                            td.setAttribute('data-last-result', td.innerText);
                        }
                    });
                });
            },

            initSummernote: function(selector) {
                const self = this;
                const $elements = $(selector);
                if ($elements.length === 0) return;

                $elements.each(function() {
                    const $el = $(this);
                    if ($el.next().hasClass('note-editor')) return;

                    $el.summernote({
                        height: 400,
                        placeholder: 'Enter content...',
                        toolbar: [
                            ['font', ['bold', 'underline', 'clear']],
                            // ['fontname', ['fontname']],
                            ['subscript', ['subscript', 'superscript']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['table', ['table']],
                            ['insert', ['link', 'picture', 'video']],
                            ['view', ['fullscreen', 'codeview', 'help']],
                            ['custom', ['calculate', 'insertFormulaTable']]
                        ],
                        buttons: {
                            calculate: function(context) {
                                return $.summernote.ui.button({
                                    contents: '<i class="ni ni-calculator"></i> Calculate',
                                    title: 'Calculate Formulas',
                                    container: 'body',
                                    click: function() {
                                        self.calculate(context.layoutInfo.editable[
                                            0]);
                                    }
                                }).render();
                            },
                            insertFormulaTable: function(context) {
                                return $.summernote.ui.button({
                                    contents: '<i class="ni ni-table-view"></i> Formula Table',
                                    title: 'Insert Formula Table',
                                    container: 'body',
                                    click: function() {
                                        const html =
                                            '<table class="table table-bordered formula-table" style="width: 100%;"><tbody>' +
                                            '<tr><td>10</td><td>20</td><td>=A1+B1</td></tr>' +
                                            '<tr><td>30</td><td>40</td><td>=A2+B2</td></tr>' +
                                            '<tr><td>=SUM(A1:A2)</td><td>=SUM(B1:B2)</td><td>=SUM(C1:C2)</td></tr>' +
                                            '</tbody></table><p><br></p>';
                                        context.invoke('editor.pasteHTML', html);
                                        setTimeout(() => self.calculate(context
                                            .layoutInfo.editable[0]), 100);
                                    }
                                }).render();
                            }
                        },
                        callbacks: {
                            onInit: function() {
                                try {
                                    const $editable = $(this).next().find('.note-editable');
                                    if ($editable.length) self.calculate($editable[0]);
                                } catch (e) {
                                    console.warn('Summernote onInit Error', e);
                                }
                            },
                            onBlur: function() {
                                try {
                                    const $editable = $(this).next().find('.note-editable');
                                    if ($editable.length) self.calculate($editable[0]);
                                } catch (e) {
                                    console.warn('Summernote onBlur Error', e);
                                }
                            }
                        }
                    });
                });
            }
        };

        $(document).ready(function() {
            if (window.SummernoteTableFormulaEngine) {
                window.SummernoteTableFormulaEngine.initSummernote('.summernote-basic');
            }
        });
    </script>

    <style>
        #global-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(189, 180, 180, 0.9);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .loader {
            position: relative;
            width: 80px;
            height: 80px;
            animation: rotate 1.5s linear infinite;
        }

        .dot {
            position: absolute;
            width: 18px;
            height: 18px;
            border-radius: 50%;
        }

        .dot1 {
            background: #f52601;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        .dot2 {
            background: #3F41D1;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loader-text {
            margin-top: 18px;
            font-size: 18px;
            font-weight: 600;
            color: #3F41D1;
            letter-spacing: 1px;
            opacity: 0.8;
        }
    </style>

</head>
