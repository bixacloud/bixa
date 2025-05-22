@extends('layouts.master')

@section('title') Color Tools @endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.21.0/dist/sweetalert2.min.css">
<style>
    /* Base Styles */
    :root {
        --primary: #4338ca;
        --primary-dark: #312e81;
        --primary-light: #6366f1;
        --accent: #ff5722;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #3b82f6;
        --border-color: #ced4da;
        --dark-bg: #1e293b;
        --light-bg: #f8f9fa;
    }
    
    /* Header Section */
    .header-section {
        background: linear-gradient(to right, var(--primary), var(--primary-dark));
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .header-title {
        color: white !important;
        font-weight: 700;
    }
    
    /* Advanced Color Picker */
    .advanced-color-picker {
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }
    
    .color-picker-header {
        background-color: var(--dark-bg);
        color: white;
        padding: 1rem;
        font-weight: 500;
    }
    
    .color-picker-content {
        display: flex;
        flex-direction: column;
        padding: 1.5rem;
        background-color: white;
    }
    
    @media (min-width: 992px) {
        .color-picker-content {
            flex-direction: row;
            gap: 2rem;
        }
        
        .color-picker-left {
            flex: 1;
        }
        
        .color-picker-right {
            flex: 1;
        }
    }
    
    .color-wheel-container {
        position: relative;
        width: 100%;
        max-width: 300px;
        height: auto;
        margin: 0 auto 1.5rem;
    }
    
    #colorWheel {
        width: 100%;
        height: auto;
        border-radius: 50%;
        cursor: crosshair;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .color-picker-pointer {
        position: absolute;
        width: 14px;
        height: 14px;
        background-color: transparent;
        border: 2px solid white;
        border-radius: 50%;
        box-shadow: 0 0 4px rgba(0, 0, 0, 0.5);
        pointer-events: none;
        transform: translate(-50%, -50%);
        top: 50%;
        left: 50%;
    }
    
    .color-input-area {
        margin-top: 1rem;
    }
    
    .color-selected-display {
        height: 120px;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        overflow: hidden;
        position: relative;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .color-main-display {
        height: 70%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .color-text-display {
        height: 30%;
        background-color: #000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .color-text-sample {
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        background-color: rgba(255, 255, 255, 0.85);
        font-weight: 500;
    }
    
    .color-text-white {
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        color: white;
        font-weight: 500;
    }
    
    .color-demo-shadow {
        position: absolute;
        bottom: 10px;
        right: 10px;
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.5);
    }
    
    .color-values-display {
        background-color: var(--light-bg);
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        font-family: monospace;
    }
    
    .color-value-line {
        display: flex;
        margin-bottom: 0.5rem;
    }
    
    .color-value-name {
        width: 80px;
        font-weight: bold;
    }
    
    .color-value-data {
        flex: 1;
    }
    
    .color-values-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
    }
    
    .color-values-table td {
        padding: 0.5rem;
        border-top: 1px solid var(--border-color);
    }
    
    .color-values-table tr:first-child td {
        border-top: none;
    }
    
    /* Color Variations */
    .color-variations {
        display: flex;
        flex-direction: column;
        margin-top: 1rem;
    }
    
    .color-variation-row {
        display: flex;
        height: 30px;
    }
    
    .color-variation-label {
        width: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--light-bg);
        border-right: 1px solid var(--border-color);
        font-size: 0.75rem;
    }
    
    .color-variation-swatch {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: monospace;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .color-variation-swatch:hover {
        transform: scale(1.05);
        z-index: 1;
    }
    
    /* Original Color Picker */
    .color-section {
        margin-bottom: 2rem;
    }
    
    .color-picker {
        height: 50px;
        width: 100%;
        padding: 0;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        cursor: pointer;
    }
    
    .color-preview {
        height: 100px;
        margin-bottom: 1.25rem;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .color-value {
        padding: 0.25rem 0.75rem;
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 0.25rem;
        color: #000;
        font-family: monospace;
        font-size: 0.875rem;
        transition: all 0.3s;
    }
    
    /* Color Swatch */
    .color-swatch {
        height: 40px;
        width: 100%;
        margin-bottom: 0.625rem;
        border-radius: 0.25rem;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .color-swatch:hover {
        transform: scale(1.05);
    }
    
    .color-swatch-label {
        position: absolute;
        bottom: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        font-size: 0.625rem;
        padding: 0.125rem 0.375rem;
        border-radius: 0 0 0.25rem 0;
    }
    
    /* Color Mixer */
    .color-mixer-container {
        border: 1px solid var(--border-color);
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1.25rem;
        background-color: white;
    }
    
    /* Gradient Preview */
    .gradient-preview {
        height: 150px;
        margin-bottom: 1.25rem;
        border-radius: 0.375rem;
        position: relative;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
        gap: 0.625rem;
    }
    
    /* Borderless Copy Button Style */
    .copy-btn {
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        color: #4338ca !important;
        padding: 6px 8px !important;
        transition: transform 0.2s, color 0.2s;
        border-radius: 4px;
    }
    
    .copy-btn:hover {
        color: #312e81 !important;
        transform: scale(1.1);
        background-color: rgba(99, 102, 241, 0.1) !important;
    }
    
    .copy-btn:focus, 
    .copy-btn:active {
        outline: none !important;
        box-shadow: none !important;
    }
    
    .copy-btn i {
        font-size: 18px;
    }
    
    /* Animation for copy success */
    @keyframes copiedAnimation {
        0% { transform: scale(1); color: #4338ca; }
        50% { transform: scale(1.2); color: #10b981; }
        100% { transform: scale(1); color: #4338ca; }
    }
    
    .copied-animation {
        animation: copiedAnimation 0.8s ease;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
        }
        
        .action-buttons .btn {
            margin-bottom: 0.625rem;
        }
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Tools @endslot
        @slot('title') Color Tools @endslot
    @endcomponent

    <!-- Header Section -->
    <div class="header-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-4 header-title">Color Tools</h1>
            <p class="lead mb-4">Convert, mix, and analyze colors with our comprehensive color tools</p>
        </div>
    </div>

    <div class="container">
        <!-- Advanced Color Picker -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="advanced-color-picker">
                    <div class="color-picker-header">
                        <i class="bx bx-palette me-2"></i>
                        Advanced Color Picker
                    </div>
                    <div class="color-picker-content">
                        <div class="color-picker-left">
                            <h5 class="mb-3">Pick a Color:</h5>
                            
                            <div class="color-wheel-container">
                                <canvas id="colorWheel" width="300" height="300"></canvas>
                                <div id="colorPickerPointer" class="color-picker-pointer"></div>
                            </div>
                            
                            <div class="color-input-area">
                                <label for="htmlColorInput" class="form-label">Or Enter a Color:</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="htmlColorInput" placeholder="Color name, HEX, RGB, HSL..." value="#ff0000">
                                    <button class="btn btn-primary" id="htmlColorInputBtn">OK</button>
                                </div>
                                
                                <label for="html5ColorPicker" class="form-label">Or Use HTML5 Color Picker:</label>
                                <input type="color" class="form-control form-control-color w-100" id="html5ColorPicker" value="#ff0000">
                            </div>
                        </div>
                        
                        <div class="color-picker-right">
                            <h5 class="mb-3">Selected Color:</h5>
                            
                            <div class="color-selected-display" id="colorDemo" style="background-color: #ff0000;">
                                <div class="color-main-display">
                                    <div class="color-text-sample" id="colorBlackText">Black Text</div>
                                    <div class="color-demo-shadow" id="colorShadow">Shadow</div>
                                </div>
                                <div class="color-text-display">
                                    <div class="color-text-white" id="colorWhiteText">White Text</div>
                                </div>
                            </div>
                            
                            <div class="color-values-display">
                                <div class="color-value-line">
                                    <div class="color-value-name">HEX:</div>
                                    <div class="color-value-data" id="advancedHexValue">#ff0000</div>
                                </div>
                                <div class="color-value-line">
                                    <div class="color-value-name">RGB:</div>
                                    <div class="color-value-data" id="advancedRgbValue">rgb(255, 0, 0)</div>
                                </div>
                                <div class="color-value-line">
                                    <div class="color-value-name">HSL:</div>
                                    <div class="color-value-data" id="advancedHslValue">hsl(0, 100%, 50%)</div>
                                </div>
                            </div>
                            
                            <h5 class="mt-4 mb-3">Lighter / Darker:</h5>
                            <div class="color-variations" id="colorVariations">
                                <!-- Lighter colors (10 rows) -->
                                <div class="color-variation-row">
                                    <div class="color-variation-label">100%</div>
                                    <div class="color-variation-swatch" style="background-color: #ffffff;" data-color="#ffffff">#ffffff</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">90%</div>
                                    <div class="color-variation-swatch" style="background-color: #ffe6e6;" data-color="#ffe6e6">#ffe6e6</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">80%</div>
                                    <div class="color-variation-swatch" style="background-color: #ffcccc;" data-color="#ffcccc">#ffcccc</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">70%</div>
                                    <div class="color-variation-swatch" style="background-color: #ffb3b3;" data-color="#ffb3b3">#ffb3b3</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">60%</div>
                                    <div class="color-variation-swatch" style="background-color: #ff9999;" data-color="#ff9999">#ff9999</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">50%</div>
                                    <div class="color-variation-swatch" style="background-color: #ff8080;" data-color="#ff8080">#ff8080</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">40%</div>
                                    <div class="color-variation-swatch" style="background-color: #ff6666;" data-color="#ff6666">#ff6666</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">30%</div>
                                    <div class="color-variation-swatch" style="background-color: #ff4d4d;" data-color="#ff4d4d">#ff4d4d</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">20%</div>
                                    <div class="color-variation-swatch" style="background-color: #ff3333;" data-color="#ff3333">#ff3333</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">10%</div>
                                    <div class="color-variation-swatch" style="background-color: #ff1a1a;" data-color="#ff1a1a">#ff1a1a</div>
                                </div>
                                
                                <!-- Original color -->
                                <div class="color-variation-row">
                                    <div class="color-variation-label">0%</div>
                                    <div class="color-variation-swatch" style="background-color: #ff0000;" data-color="#ff0000">#ff0000</div>
                                </div>
                                
                                <!-- Darker colors (10 rows) -->
                                <div class="color-variation-row">
                                    <div class="color-variation-label">10%</div>
                                    <div class="color-variation-swatch" style="background-color: #e60000;" data-color="#e60000">#e60000</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">20%</div>
                                    <div class="color-variation-swatch" style="background-color: #cc0000;" data-color="#cc0000">#cc0000</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">30%</div>
                                    <div class="color-variation-swatch" style="background-color: #b30000;" data-color="#b30000">#b30000</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">40%</div>
                                    <div class="color-variation-swatch" style="background-color: #990000;" data-color="#990000">#990000</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">50%</div>
                                    <div class="color-variation-swatch" style="background-color: #800000;" data-color="#800000">#800000</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">60%</div>
                                    <div class="color-variation-swatch" style="background-color: #660000;" data-color="#660000">#660000</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">70%</div>
                                    <div class="color-variation-swatch" style="background-color: #4d0000;" data-color="#4d0000">#4d0000</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">80%</div>
                                    <div class="color-variation-swatch" style="background-color: #330000;" data-color="#330000">#330000</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">90%</div>
                                    <div class="color-variation-swatch" style="background-color: #1a0000;" data-color="#1a0000">#1a0000</div>
                                </div>
                                <div class="color-variation-row">
                                    <div class="color-variation-label">100%</div>
                                    <div class="color-variation-swatch" style="background-color: #000000;" data-color="#000000">#000000</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="row">
            <!-- Color Picker and Converter -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="bx bx-palette me-2"></i>
                        Color Picker & Converter
                    </div>
                    <div class="card-body">
                        <div class="color-section">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label for="colorPicker" class="form-label fw-bold">Select Color:</label>
                                    <input type="color" id="colorPicker" class="color-picker" value="#4338ca">
                                </div>
                                <div class="col-md-4">
                                    <label for="colorInput" class="form-label fw-bold">Enter Color:</label>
                                    <input type="text" id="colorInput" class="form-control" placeholder="e.g. #4338ca or rgb(67, 56, 202)" value="#4338ca">
                                </div>
                            </div>
                            
                            <div id="colorPreview" class="color-preview" style="background-color: #4338ca;">
                                <div class="color-value">#4338ca</div>
                            </div>
                            
                            <h5 class="mt-4 mb-3">Color Values:</h5>
                            <table class="color-values-table">
                                <tr>
                                    <td width="30%"><strong>HEX:</strong></td>
                                    <td id="hexValue">#4338ca</td>
                                    <td width="10%">
                                        <button type="button" class="copy-btn" data-value="hex">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>RGB:</strong></td>
                                    <td id="rgbValue">rgb(67, 56, 202)</td>
                                    <td>
                                        <button type="button" class="copy-btn" data-value="rgb">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>HSL:</strong></td>
                                    <td id="hslValue">hsl(245, 58%, 51%)</td>
                                    <td>
                                        <button type="button" class="copy-btn" data-value="hsl">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>HSV:</strong></td>
                                    <td id="hsvValue">hsv(245, 72%, 79%)</td>
                                    <td>
                                        <button type="button" class="copy-btn" data-value="hsv">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>CMYK:</strong></td>
                                    <td id="cmykValue">cmyk(67%, 72%, 0%, 21%)</td>
                                    <td>
                                        <button type="button" class="copy-btn" data-value="cmyk">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td id="nameValue">Indigo</td>
                                    <td>
                                        <button type="button" class="copy-btn" data-value="name">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="card bg-light mt-4">
                            <div class="card-body">
                                <h5 class="mb-3"><i class="bx bx-lightbulb text-primary me-2"></i> Color Properties</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Brightness:</strong> <span id="brightnessValue">38%</span></p>
                                        <p><strong>Is Dark:</strong> <span id="isDarkValue">Yes</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Luminance:</strong> <span id="luminanceValue">0.12</span></p>
                                        <p><strong>WCAG Contrast:</strong> <span id="contrastValue">4.5:1</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Color Schemes -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="bx bx-palette me-2"></i>
                        Color Schemes
                    </div>
                    <div class="card-body">
                        <h5 class="mb-3">Complementary:</h5>
                        <div class="row mb-4" id="complementary">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        
                        <h5 class="mb-3">Triadic:</h5>
                        <div class="row mb-4" id="triadic">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        
                        <h5 class="mb-3">Tetradic:</h5>
                        <div class="row mb-4" id="tetradic">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        
                        <h5 class="mb-3">Analogous:</h5>
                        <div class="row mb-4" id="analogous">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        
                        <h5 class="mb-3">Monochromatic:</h5>
                        <div class="row" id="monochromatic">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Color Mixer and Gradient -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="bx bx-credit-card-front me-2"></i>
                        Color Mixer
                    </div>
                    <div class="card-body">
                        <div class="color-mixer-container">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="colorMixer1" class="form-label fw-bold">Color 1:</label>
                                    <div class="input-group">
                                        <input type="color" id="colorMixer1" class="form-control form-control-color" value="#4338ca">
                                        <input type="text" id="colorMixer1Text" class="form-control" value="#4338ca">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="colorMixer2" class="form-label fw-bold">Color 2:</label>
                                    <div class="input-group">
                                        <input type="color" id="colorMixer2" class="form-control form-control-color" value="#ff5722">
                                        <input type="text" id="colorMixer2Text" class="form-control" value="#ff5722">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="mixRatio" class="form-label fw-bold">Mix Ratio: <span id="mixRatioValue">50%</span></label>
                                    <input type="range" class="form-range" id="mixRatio" min="0" max="100" value="50">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Result:</label>
                                    <div id="mixResult" class="color-preview" style="background-color: #a14976;">
                                        <div class="color-value">#a14976</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="action-buttons">
                                <button id="copyMixResult" class="btn btn-primary">
                                    <i class="bx bx-copy me-1"></i> Copy Result
                                </button>
                                <button id="swapColors" class="btn btn-secondary">
                                    <i class="bx bx-transfer me-1"></i> Swap Colors
                                </button>
                            </div>
                        </div>
                        
                        <div class="card bg-light mt-4">
                            <div class="card-body">
                                <h5><i class="bx bx-info-circle text-primary me-2"></i> About Color Mixer</h5>
                                <p>This tool allows you to mix two colors together with a customizable ratio. Use the slider to adjust the proportion of each color in the final mix.</p>
                                <p>Applications include:</p>
                                <ul>
                                    <li>Creating color transitions for UI/UX design</li>
                                    <li>Blending colors for digital art</li>
                                    <li>Finding intermediate colors for gradients</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gradient Generator -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="bx bx-gradient me-2"></i>
                        Gradient Generator
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="gradientStart" class="form-label fw-bold">Start Color:</label>
                                <div class="input-group">
                                    <input type="color" id="gradientStart" class="form-control form-control-color" value="#4338ca">
                                    <input type="text" id="gradientStartText" class="form-control" value="#4338ca">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="gradientEnd" class="form-label fw-bold">End Color:</label>
                                <div class="input-group">
                                    <input type="color" id="gradientEnd" class="form-control form-control-color" value="#ff5722">
                                    <input type="text" id="gradientEndText" class="form-control" value="#ff5722">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="gradientType" class="form-label fw-bold">Gradient Type:</label>
                                <select class="form-select" id="gradientType">
                                    <option value="linear">Linear</option>
                                    <option value="radial">Radial</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="gradientAngle" class="form-label fw-bold">Angle: <span id="gradientAngleValue">90°</span></label>
                                <input type="range" class="form-range" id="gradientAngle" min="0" max="360" value="90">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Preview:</label>
                                <div id="gradientPreview" class="gradient-preview" style="background: linear-gradient(90deg, #4338ca, #ff5722);"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="gradientCode" class="form-label fw-bold">CSS Code:</label>
                                <div class="input-group">
                                    <input type="text" id="gradientCode" class="form-control" value="background: linear-gradient(90deg, #4338ca, #ff5722);" readonly>
                                    <button class="btn btn-primary" id="copyGradientCode">
                                        <i class="bx bx-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.21.0/dist/sweetalert2.all.min.js"></script>


<!-- TinyColor Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinycolor/1.6.0/tinycolor.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Main variables
    let currentColor = tinycolor("#4338ca");
    
    // ----- Advanced Color Picker ----- 
    const html5ColorPicker = document.getElementById('html5ColorPicker');
    const htmlColorInput = document.getElementById('htmlColorInput');
    const htmlColorInputBtn = document.getElementById('htmlColorInputBtn');
    const colorDemo = document.getElementById('colorDemo');
    const colorPickerPointer = document.getElementById('colorPickerPointer');

    // Initialize with default color
    initializeColorPicker("#ff0000");
    
    // Initialize color wheel
    initColorWheel();

    // HTML5 Color Picker Event
    html5ColorPicker.addEventListener('input', function(e) {
        const newColor = e.target.value;
        updateAllColorDisplays(newColor);
    });
    
    // Manual color input event
    htmlColorInputBtn.addEventListener('click', function() {
        const inputColor = htmlColorInput.value;
        if (isValidColor(inputColor)) {
            updateAllColorDisplays(inputColor);
        } else {
            showAlert('Invalid color format', 'error');
        }
    });
    
    // Enter key in input
    htmlColorInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            htmlColorInputBtn.click();
        }
    });
    
    // Click on color variation swatch
    document.querySelectorAll('.color-variation-swatch').forEach(function(swatch) {
        swatch.addEventListener('click', function() {
            const colorValue = this.getAttribute('data-color');
            copyToClipboard(colorValue);
        });
    });
    
    // ----- Standard Color Picker ----- 
    // Initialize with default values
    updateOriginalColorPicker('#4338ca');
    updateMixedColor();
    updateGradient();
    
    // Color Picker Events
    document.getElementById('colorPicker').addEventListener('input', function(e) {
        const newColor = e.target.value;
        updateOriginalColorPicker(newColor);
        updateAllColorDisplays(newColor);
    });
    
    document.getElementById('colorInput').addEventListener('input', function(e) {
        const inputColor = e.target.value;
        if (isValidColor(inputColor)) {
            document.getElementById('colorPicker').value = tinycolor(inputColor).toHexString();
            updateOriginalColorPicker(inputColor);
            updateAllColorDisplays(inputColor);
        }
    });
    
    // Setup copy buttons with proper handling
    setupCopyButtons();
    
    // Color Mixer Events
    document.getElementById('colorMixer1').addEventListener('input', function(e) {
        document.getElementById('colorMixer1Text').value = e.target.value;
        updateMixedColor();
    });
    
    document.getElementById('colorMixer2').addEventListener('input', function(e) {
        document.getElementById('colorMixer2Text').value = e.target.value;
        updateMixedColor();
    });
    
    document.getElementById('colorMixer1Text').addEventListener('input', function(e) {
        const color = e.target.value;
        if (isValidColor(color)) {
            document.getElementById('colorMixer1').value = tinycolor(color).toHexString();
            updateMixedColor();
        }
    });
    
    document.getElementById('colorMixer2Text').addEventListener('input', function(e) {
        const color = e.target.value;
        if (isValidColor(color)) {
            document.getElementById('colorMixer2').value = tinycolor(color).toHexString();
            updateMixedColor();
        }
    });
    
    document.getElementById('mixRatio').addEventListener('input', function(e) {
        document.getElementById('mixRatioValue').textContent = e.target.value + '%';
        updateMixedColor();
    });
    
    document.getElementById('copyMixResult').addEventListener('click', function(e) {
        e.preventDefault();
        const mixResult = document.querySelector('#mixResult .color-value').textContent;
        copyToClipboard(mixResult, this);
        return false;
    });
    
    document.getElementById('swapColors').addEventListener('click', function() {
        const color1 = document.getElementById('colorMixer1').value;
        const color2 = document.getElementById('colorMixer2').value;
        
        document.getElementById('colorMixer1').value = color2;
        document.getElementById('colorMixer1Text').value = color2;
        document.getElementById('colorMixer2').value = color1;
        document.getElementById('colorMixer2Text').value = color1;
        
        updateMixedColor();
    });
    
    // Gradient Generator Events
    document.getElementById('gradientStart').addEventListener('input', function(e) {
        document.getElementById('gradientStartText').value = e.target.value;
        updateGradient();
    });
    
    document.getElementById('gradientEnd').addEventListener('input', function(e) {
        document.getElementById('gradientEndText').value = e.target.value;
        updateGradient();
    });
    
    document.getElementById('gradientStartText').addEventListener('input', function(e) {
        const color = e.target.value;
        if (isValidColor(color)) {
            document.getElementById('gradientStart').value = tinycolor(color).toHexString();
            updateGradient();
        }
    });
    
    document.getElementById('gradientEndText').addEventListener('input', function(e) {
        const color = e.target.value;
        if (isValidColor(color)) {
            document.getElementById('gradientEnd').value = tinycolor(color).toHexString();
            updateGradient();
        }
    });
    
    document.getElementById('gradientType').addEventListener('change', updateGradient);
    
    document.getElementById('gradientAngle').addEventListener('input', function(e) {
        document.getElementById('gradientAngleValue').textContent = e.target.value + '°';
        updateGradient();
    });
    
    document.getElementById('copyGradientCode').addEventListener('click', function(e) {
        e.preventDefault();
        const gradientCode = document.getElementById('gradientCode').value;
        copyToClipboard(gradientCode, this);
        return false;
    });
    
    // Setup all copy buttons with proper event handling
    function setupCopyButtons() {
        // Get all copy buttons
        const copyButtons = document.querySelectorAll('.copy-btn');
        
        // Add event listener to each button
        copyButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Prevent default action
                e.preventDefault();
                e.stopPropagation();
                
                // Get the data value
                const type = this.getAttribute('data-value');
                let valueToCopy = '';
                
                // Determine what to copy based on button type
                switch(type) {
                    case 'hex':
                        valueToCopy = document.getElementById('hexValue').textContent;
                        break;
                    case 'rgb':
                        valueToCopy = document.getElementById('rgbValue').textContent;
                        break;
                    case 'hsl':
                        valueToCopy = document.getElementById('hslValue').textContent;
                        break;
                    case 'hsv':
                        valueToCopy = document.getElementById('hsvValue').textContent;
                        break;
                    case 'cmyk':
                        valueToCopy = document.getElementById('cmykValue').textContent;
                        break;
                    case 'name':
                        valueToCopy = document.getElementById('nameValue').textContent;
                        break;
                }
                
                // Copy to clipboard
                copyToClipboard(valueToCopy, this);
                
                // Prevent default action
                return false;
            });
        });
        
        // Also setup color swatches to be copyable
        document.querySelectorAll('.color-swatch').forEach(swatch => {
            swatch.addEventListener('click', function() {
                const colorValue = this.getAttribute('data-color');
                copyToClipboard(colorValue);
            });
        });
    }
    
    // Color Swatch Click Event (for Color Schemes)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('color-swatch') || e.target.classList.contains('color-swatch-label')) {
            const swatch = e.target.classList.contains('color-swatch') ? e.target : e.target.parentElement;
            const colorValue = swatch.getAttribute('data-color');
            copyToClipboard(colorValue);
        }
    });
    
    // ----- COLOR WHEEL FUNCTIONS -----
    
    // Initialize color wheel
    function initColorWheel() {
        const canvas = document.getElementById('colorWheel');
        const pointer = document.getElementById('colorPickerPointer');
        
        // Ensure canvas exists
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = Math.min(centerX, centerY) - 5;
        
        // Draw color wheel
        drawColorWheel(ctx, centerX, centerY, radius);
        
        // Handle click events on canvas
        canvas.addEventListener('click', function(e) {
            const rect = canvas.getBoundingClientRect();
            // Calculate click position relative to canvas
            const x = (e.clientX - rect.left) * (canvas.width / rect.width);
            const y = (e.clientY - rect.top) * (canvas.height / rect.height);
            
            // Calculate distance from center
            const dx = x - centerX;
            const dy = y - centerY;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            // Only select color if click is within the circle
            if (distance <= radius) {
                // Move pointer to click position
                pointer.style.left = `${x / canvas.width * 100}%`;
                pointer.style.top = `${y / canvas.height * 100}%`;
                
                // Get color from click position
                const imageData = ctx.getImageData(x, y, 1, 1).data;
                const color = `rgb(${imageData[0]}, ${imageData[1]}, ${imageData[2]})`;
                
                // Update selected color
                updateAllColorDisplays(color);
            }
        });
    }

    // Draw color wheel function
    function drawColorWheel(ctx, centerX, centerY, radius) {
        // Clear canvas
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        
        // Draw color circle
        for (let angle = 0; angle < 360; angle += 0.1) {
            const startAngle = (angle - 0.5) * Math.PI / 180;
            const endAngle = (angle + 0.5) * Math.PI / 180;
            
            // Convert angle to Hue (HSL)
            const hue = angle;
            
            // Draw each part of the color wheel
            for (let saturation = 0; saturation <= 100; saturation += 1) {
                // Calculate length from center based on saturation
                const sat = saturation / 100;
                const innerRadius = radius * sat;
                
                // Color based on hue and saturation (fixed lightness)
                ctx.fillStyle = `hsl(${hue}, ${saturation}%, 50%)`;
                
                // Draw a small arc segment
                ctx.beginPath();
                ctx.arc(centerX, centerY, innerRadius, startAngle, endAngle);
                ctx.arc(centerX, centerY, innerRadius + (radius / 100), endAngle, startAngle, true);
                ctx.closePath();
                ctx.fill();
            }
        }
    }
    
    /**
     * Update color wheel pointer position based on current color
     */
    function updateColorWheelPointer(color) {
        const canvas = document.getElementById('colorWheel');
        const pointer = document.getElementById('colorPickerPointer');
        
        if (!canvas || !pointer) return;
        
        const hsl = color.toHsl();
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = Math.min(centerX, centerY) - 5;
        
        // Calculate position based on hue and saturation
        const hueRad = hsl.h * Math.PI / 180;
        const sat = hsl.s;
        
        // Calculate x, y coordinates from center
        const x = centerX + Math.cos(hueRad) * radius * sat;
        const y = centerY + Math.sin(hueRad) * radius * sat;
        
        // Set pointer position
        pointer.style.left = `${x / canvas.width * 100}%`;
        pointer.style.top = `${y / canvas.height * 100}%`;
    }
    
    // ----- HELPER FUNCTIONS -----

    /**
     * Check if string is a valid color
     */
    function isValidColor(color) {
        return tinycolor(color).isValid();
    }
    
    /**
     * Initialize color picker with default color
     */
    function initializeColorPicker(color) {
        updateAllColorDisplays(color);
    }
    
    /**
     * Update all color displays in both sections
     */
    function updateAllColorDisplays(color) {
        // Convert to tinycolor object
        currentColor = tinycolor(color);
        
        if (!currentColor.isValid()) {
            return;
        }
        
        // Update advanced color picker
        updateAdvancedColorPicker(currentColor);
        
        // Update standard color picker
        updateOriginalColorPicker(currentColor);
        
        // Update color wheel pointer position
        updateColorWheelPointer(currentColor);
    }
    
    /**
     * Update advanced color picker display
     */
    function updateAdvancedColorPicker(color) {
        if (!color.isValid()) return;
        
        const hexColor = color.toHexString();
        
        // Update inputs
        html5ColorPicker.value = hexColor;
        htmlColorInput.value = hexColor;
        
        // Update preview
        colorDemo.style.backgroundColor = hexColor;
        
        // Update color values
        document.getElementById('advancedHexValue').textContent = hexColor;
        document.getElementById('advancedRgbValue').textContent = color.toRgbString();
        document.getElementById('advancedHslValue').textContent = color.toHslString();
        
        // Update text samples
        const isDark = color.isDark();
        document.getElementById('colorBlackText').style.color = 'black';
        document.getElementById('colorWhiteText').style.color = 'white';
        document.getElementById('colorShadow').style.backgroundColor = hexColor;
        
        // Create and update color variations
        updateColorVariations(color);
    }
    
    /**
     * Update color variations (lighter/darker)
     */
    function updateColorVariations(color) {
        const variationSwatches = document.querySelectorAll('.color-variation-swatch');
        
        // Create 10 lighter variations
        for (let i = 0; i < 10; i++) {
            const amount = (10 - i) * 10;
            const lighter = tinycolor.mix('#ffffff', color, amount);
            const hexCode = lighter.toHexString();
            
            variationSwatches[i].style.backgroundColor = hexCode;
            variationSwatches[i].textContent = hexCode;
            variationSwatches[i].setAttribute('data-color', hexCode);
            variationSwatches[i].style.color = lighter.isDark() ? 'white' : 'black';
        }
        
        // Original color
        variationSwatches[10].style.backgroundColor = color.toHexString();
        variationSwatches[10].textContent = color.toHexString();
        variationSwatches[10].setAttribute('data-color', color.toHexString());
        variationSwatches[10].style.color = color.isDark() ? 'white' : 'black';
        
        // Create 10 darker variations
        for (let i = 0; i < 10; i++) {
            const amount = (i + 1) * 10;
            const darker = tinycolor.mix('#000000', color, 100 - amount);
            const hexCode = darker.toHexString();
            
            variationSwatches[i + 11].style.backgroundColor = hexCode;
            variationSwatches[i + 11].textContent = hexCode;
            variationSwatches[i + 11].setAttribute('data-color', hexCode);
            variationSwatches[i + 11].style.color = darker.isDark() ? 'white' : 'black';
        }
    }
    
    /**
     * Update standard color picker
     */
    function updateOriginalColorPicker(color) {
        const tColor = typeof color === 'object' ? color : tinycolor(color);
        
        if (!tColor.isValid()) {
            return;
        }
        
        // Update input
        document.getElementById('colorPicker').value = tColor.toHexString();
        document.getElementById('colorInput').value = tColor.toHexString();
        
        // Update preview
        const colorPreview = document.getElementById('colorPreview');
        colorPreview.style.backgroundColor = tColor.toHexString();
        colorPreview.querySelector('.color-value').textContent = tColor.toHexString();
        
        // Update preview text color based on brightness
        colorPreview.querySelector('.color-value').style.color = tColor.isDark() ? '#fff' : '#000';
        
        // Update color values
        document.getElementById('hexValue').textContent = tColor.toHexString();
        document.getElementById('rgbValue').textContent = tColor.toRgbString();
        document.getElementById('hslValue').textContent = tColor.toHslString();
        
        // Calculate HSV
        const hsv = tColor.toHsv();
        document.getElementById('hsvValue').textContent = `hsv(${Math.round(hsv.h)}, ${Math.round(hsv.s * 100)}%, ${Math.round(hsv.v * 100)}%)`;
        
        // Calculate CMYK
        const rgb = tColor.toRgb();
        let c = 1 - (rgb.r / 255);
        let m = 1 - (rgb.g / 255);
        let y = 1 - (rgb.b / 255);
        let k = Math.min(c, m, y);
        
        if (k === 1) {
            c = m = y = 0;
        } else {
            c = (c - k) / (1 - k);
            m = (m - k) / (1 - k);
            y = (y - k) / (1 - k);
        }
        
        document.getElementById('cmykValue').textContent = `cmyk(${Math.round(c * 100)}%, ${Math.round(m * 100)}%, ${Math.round(y * 100)}%, ${Math.round(k * 100)}%)`;
        
        // Get approximate color name
        const colorName = getColorName(tColor);
        document.getElementById('nameValue').textContent = colorName;
        
        // Color properties
        const brightness = tColor.getBrightness();
        document.getElementById('brightnessValue').textContent = Math.round(brightness / 255 * 100) + '%';
        document.getElementById('isDarkValue').textContent = tColor.isDark() ? 'Yes' : 'No';
        
        const luminance = tColor.getLuminance();
        document.getElementById('luminanceValue').textContent = luminance.toFixed(2);
        
        // WCAG contrast with white
        const contrastWithWhite = tinycolor.readability(tColor, "#ffffff").toFixed(1);
        document.getElementById('contrastValue').textContent = contrastWithWhite + ':1';
        
        // Update color schemes
        updateColorSchemes(tColor);
    }
    
    /**
     * Get approximate color name
     */
    function getColorName(color) {
        const names = {
            "#ff0000": "Red",
            "#00ff00": "Green",
            "#0000ff": "Blue",
            "#ffff00": "Yellow",
            "#00ffff": "Cyan",
            "#ff00ff": "Magenta",
            "#c0c0c0": "Silver",
            "#808080": "Gray",
            "#800000": "Maroon",
            "#808000": "Olive",
            "#008000": "Dark Green",
            "#800080": "Purple",
            "#008080": "Teal",
            "#000080": "Navy",
            "#ffffff": "White",
            "#000000": "Black",
            "#ffa500": "Orange",
            "#ffc0cb": "Pink",
            "#4b0082": "Indigo",
            "#a52a2a": "Brown"
        };
        
        let closestColorName = "Custom";
        let closestDistance = Infinity;
        
        const targetRgb = color.toRgb();
        
        for (const [hex, name] of Object.entries(names)) {
            const namedColor = tinycolor(hex).toRgb();
            const distance = Math.sqrt(
                Math.pow(targetRgb.r - namedColor.r, 2) +
                Math.pow(targetRgb.g - namedColor.g, 2) +
                Math.pow(targetRgb.b - namedColor.b, 2)
            );
            
            if (distance < closestDistance) {
                closestDistance = distance;
                closestColorName = name;
            }
        }
        
        // If distance is too high, return "Custom"
        return closestDistance < 50 ? closestColorName : "Custom";
    }
    
    /**
     * Update color schemes for selected color
     */
    function updateColorSchemes(color) {
        // Complementary
        const complementary = [color.toHexString(), color.complement().toHexString()];
        updateColorSwatches('complementary', complementary, 6);
        
        // Triadic
        const triadic = color.triad().map(c => c.toHexString());
        updateColorSwatches('triadic', triadic, 4);
        
        // Tetradic
        const tetradic = color.tetrad().map(c => c.toHexString());
        updateColorSwatches('tetradic', tetradic, 3);
        
        // Analogous
        const analogous = color.analogous(6).map(c => c.toHexString());
        updateColorSwatches('analogous', analogous, 2);
        
        // Monochromatic
        const monochromatic = color.monochromatic(6).map(c => c.toHexString());
        updateColorSwatches('monochromatic', monochromatic, 2);
    }
    
    /**
     * Update color swatches for a scheme
     */
    function updateColorSwatches(containerId, colors, colSize) {
        const container = document.getElementById(containerId);
        let html = '';
        
        colors.forEach(color => {
            const textColor = tinycolor(color).isDark() ? 'white' : 'black';
            html += `
            <div class="col-md-${colSize} col-6">
                <div class="color-swatch" style="background-color: ${color}; color: ${textColor};" data-color="${color}">
                    <div class="color-swatch-label">${color}</div>
                </div>
            </div>`;
        });
        
        container.innerHTML = html;
    }
    
    /**
     * Update mixed color based on current settings
     */
    function updateMixedColor() {
        const color1 = tinycolor(document.getElementById('colorMixer1').value);
        const color2 = tinycolor(document.getElementById('colorMixer2').value);
        const ratio = document.getElementById('mixRatio').value / 100;
        
        // Mix colors
        const mixed = tinycolor.mix(color1, color2, ratio * 100);
        
        // Update result display
        const mixResult = document.getElementById('mixResult');
        mixResult.style.backgroundColor = mixed.toHexString();
        mixResult.querySelector('.color-value').textContent = mixed.toHexString();
        
        // Update text color based on brightness
        mixResult.querySelector('.color-value').style.color = mixed.isDark() ? '#fff' : '#000';
    }
    
    /**
     * Update gradient preview and code based on current settings
     */
    function updateGradient() {
        const startColor = document.getElementById('gradientStart').value;
        const endColor = document.getElementById('gradientEnd').value;
        const type = document.getElementById('gradientType').value;
        const angle = document.getElementById('gradientAngle').value;
        
        let gradientCSS = '';
        
        if (type === 'linear') {
            gradientCSS = `background: linear-gradient(${angle}deg, ${startColor}, ${endColor});`;
            document.getElementById('gradientPreview').style.background = `linear-gradient(${angle}deg, ${startColor}, ${endColor})`;
        } else { // radial
            gradientCSS = `background: radial-gradient(circle, ${startColor}, ${endColor});`;
            document.getElementById('gradientPreview').style.background = `radial-gradient(circle, ${startColor}, ${endColor})`;
        }
        
        document.getElementById('gradientCode').value = gradientCSS;
    }
    
    /**
     * Copy text to clipboard with visual feedback
     */
    function copyToClipboard(text, button = null) {
        // Use modern clipboard API when available
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    showSuccessAnimation(button);
                    showAlert(`Copied: ${text}`);
                })
                .catch(err => {
                    console.error("Copy failed:", err);
                    fallbackCopyMethod(text, button);
                });
        } else {
            // Fallback for older browsers
            fallbackCopyMethod(text, button);
        }
    }
    
    /**
     * Fallback copy method for browsers without clipboard API
     */
    function fallbackCopyMethod(text, button) {
        try {
            // Create temporary element
            const textarea = document.createElement('textarea');
            
            // Set its value and attributes
            textarea.value = text;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            textarea.style.left = '-9999px';
            
            // Add to DOM, select and copy
            document.body.appendChild(textarea);
            textarea.select();
            const success = document.execCommand('copy');
            
            // Remove element
            document.body.removeChild(textarea);
            
            // Show feedback
            if (success) {
                showSuccessAnimation(button);
                showAlert(`Copied: ${text}`);
            } else {
                showAlert('Copy failed. Please try again.', 'error');
            }
        } catch (err) {
            console.error("Fallback copy error:", err);
            showAlert('Copy failed. Please try again.', 'error');
        }
    }
    
    /**
     * Show success animation for a button
     */
    function showSuccessAnimation(button) {
        if (!button) return;
        
        // Store original content
        const originalContent = button.innerHTML;
        
        // Show checkmark icon
        button.innerHTML = '<i class="bx bx-check"></i>';
        
        // Add animation class
        button.classList.add('copied-animation');
        
        // Reset after animation
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.classList.remove('copied-animation');
        }, 800);
    }
    
    /**
     * Display a SweetAlert notification
     */
    function showAlert(message, type = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        
        Toast.fire({
            icon: type,
            title: message
        });
    }
});
</script>
@endsection