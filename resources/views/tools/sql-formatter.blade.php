@extends('layouts.master')

@section('title') SQL Formatter @endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.21.0/dist/sweetalert2.min.css">
<style>
    .header-section {
        background: linear-gradient(to right, #4338ca, #312e81);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0.25rem;
    }
    
    .header-title {
        color: white !important;
    }
    
    #sqlEditor, #formattedOutput {
        width: 100%;
        height: 300px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    .settings-container {
        margin: 15px 0;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 4px;
    }
    
    .action-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        gap: 10px;
    }
    
    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
        }
        
        .action-buttons .btn {
            margin-bottom: 10px;
        }
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Tools @endslot
        @slot('title') SQL Formatter @endslot
    @endcomponent

    <!-- Header Section -->
    <div class="header-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-4 header-title">SQL Formatter</h1>
            <p class="lead mb-4">Format and beautify your SQL queries for better readability</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="bx bx-data me-2"></i>
                        SQL Query Formatter
                    </div>
                    <div class="card-body">
                        <!-- Input Section -->
                        <div class="mb-3">
                            <label for="sqlEditor" class="form-label fw-bold">Enter your SQL query:</label>
                            <div id="sqlEditor"></div>
                        </div>
                        
                        <!-- Settings -->
                        <div class="settings-container">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="indentSize" class="form-label">Indent Size:</label>
                                        <select class="form-select" id="indentSize">
                                            <option value="2">2 spaces</option>
                                            <option value="4" selected>4 spaces</option>
                                            <option value="8">8 spaces</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="maxColumnLength" class="form-label">Max Column Length:</label>
                                        <input type="number" class="form-control" id="maxColumnLength" value="50">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="uppercase" checked>
                                        <label class="form-check-label" for="uppercase">Uppercase SQL keywords</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="highlightParams" checked>
                                        <label class="form-check-label" for="highlightParams">Highlight parameters</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <label for="sqlDialect" class="form-label">SQL Dialect:</label>
                                    <select class="form-select" id="sqlDialect">
                                        <option value="standard" selected>Standard SQL</option>
                                        <option value="mysql">MySQL</option>
                                        <option value="postgresql">PostgreSQL</option>
                                        <option value="oracle">Oracle</option>
                                        <option value="sqlserver">SQL Server</option>
                                    </select>
                                </div>
                                <div class="col-md-9">
                                    <button id="formatButton" class="btn btn-primary mt-4">
                                        <i class="bx bx-paint me-1"></i> Format SQL
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Output Section -->
                        <div class="mt-4">
                            <label for="formattedOutput" class="form-label fw-bold">Formatted SQL:</label>
                            <div id="formattedOutput"></div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <div>
                                <button class="btn btn-success" id="copyButton">
                                    <i class="bx bx-copy me-1"></i> Copy Formatted SQL
                                </button>
                            </div>
                            <div>
                                <button class="btn btn-secondary" id="clearButton">
                                    <i class="bx bx-trash me-1"></i> Clear
                                </button>
                            </div>
                        </div>
                        
                        <!-- Info Section -->
                        <div class="card bg-light mt-4">
                            <div class="card-body">
                                <h5><i class="bx bx-info-circle text-primary me-2"></i> About SQL Formatter</h5>
                                <p>This tool helps you format SQL queries to make them more readable and easier to maintain.</p>
                                <p>Features:</p>
                                <ul>
                                    <li>Format SQL queries with proper indentation and line breaks</li>
                                    <li>Support for multiple SQL dialects (Standard, MySQL, PostgreSQL, etc.)</li>
                                    <li>Customize formatting options (indent size, uppercase keywords, etc.)</li>
                                    <li>Syntax highlighting for better readability</li>
                                </ul>
                                <p><strong>Example SQL:</strong> Try formatting a complex query with JOIN, WHERE, and GROUP BY clauses to see the difference.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<!-- Ace Editor -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/ace.js"></script>

<!-- SQL Formatter -->
<script src="https://cdn.jsdelivr.net/npm/sql-formatter@12.2.3/dist/sql-formatter.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.21.0/dist/sweetalert2.all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize input editor
    const inputEditor = ace.edit("sqlEditor");
    inputEditor.setTheme("ace/theme/xcode");
    inputEditor.session.setMode("ace/mode/sql");
    inputEditor.setFontSize(14);
    inputEditor.setShowPrintMargin(false);
    inputEditor.session.setUseWrapMode(true);
    inputEditor.setOptions({
        enableBasicAutocompletion: true,
        enableLiveAutocompletion: true
    });
    
    // Initialize output editor
    const outputEditor = ace.edit("formattedOutput");
    outputEditor.setTheme("ace/theme/monokai");
    outputEditor.session.setMode("ace/mode/sql");
    outputEditor.setFontSize(14);
    outputEditor.setShowPrintMargin(false);
    outputEditor.session.setUseWrapMode(true);
    outputEditor.setReadOnly(true);
    
    // Set example SQL
    inputEditor.setValue(`SELECT c.customer_id, c.name, c.email, c.phone, 
a.street, a.city, a.state, a.postal_code, a.country,
COUNT(o.order_id) as total_orders, 
SUM(p.amount) as total_spent,
MAX(o.order_date) as last_order_date
FROM customers c
JOIN addresses a ON c.customer_id = a.customer_id
LEFT JOIN orders o ON c.customer_id = o.customer_id
LEFT JOIN payments p ON o.order_id = p.order_id
WHERE c.status = 'active' 
AND a.is_primary = true
AND (o.order_date >= '2023-01-01' OR o.order_date IS NULL)
GROUP BY c.customer_id, c.name, c.email, c.phone, a.street, a.city, a.state, a.postal_code, a.country
HAVING COUNT(o.order_id) > 0 OR COUNT(o.order_id) = 0
ORDER BY total_spent DESC, c.name ASC
LIMIT 100;`, -1);
    
    // Format Button
    document.getElementById('formatButton').addEventListener('click', function() {
        const sql = inputEditor.getValue();
        if (!sql.trim()) {
            showAlert('Please enter SQL query first', 'error');
            return;
        }
        
        const indentSize = parseInt(document.getElementById('indentSize').value);
        const maxColumnLength = parseInt(document.getElementById('maxColumnLength').value);
        const uppercase = document.getElementById('uppercase').checked;
        const dialect = document.getElementById('sqlDialect').value;
        
        try {
            // Format SQL using sql-formatter
            const formatted = sqlFormatter.format(sql, {
                language: dialect,
                indent: ' '.repeat(indentSize),
                uppercase: uppercase,
                linesBetweenQueries: 2,
                maxColumnLength: maxColumnLength
            });
            
            outputEditor.setValue(formatted, -1);
            showAlert('SQL formatted successfully!');
        } catch (error) {
            showAlert('Error formatting SQL: ' + error.message, 'error');
        }
    });
    
    // Copy Button
    document.getElementById('copyButton').addEventListener('click', function() {
        const formattedSql = outputEditor.getValue();
        if (!formattedSql.trim()) {
            showAlert('No formatted SQL to copy!', 'error');
            return;
        }
        
        copyToClipboard(formattedSql);
    });
    
    // Clear Button
    document.getElementById('clearButton').addEventListener('click', function() {
        inputEditor.setValue('');
        outputEditor.setValue('');
        showAlert('Editors cleared!');
    });
    
    /**
     * Display a SweetAlert notification
     * @param {string} message - The message to display
     * @param {string} type - The type of notification (success or error)
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
    
    /**
     * Copy text to clipboard
     * @param {string} text - Text to copy
     */
    function copyToClipboard(text) {
        // Use modern Clipboard API when available
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    showAlert('Copied to clipboard!');
                })
                .catch(err => {
                    fallbackCopyToClipboard(text);
                });
        } else {
            // Fallback for older browsers
            fallbackCopyToClipboard(text);
        }
    }
    
    /**
     * Fallback method for copying to clipboard
     * @param {string} text - Text to copy
     */
    function fallbackCopyToClipboard(text) {
        try {
            // Create temporary element
            const textarea = document.createElement('textarea');
            
            // Set attributes and styling
            textarea.value = text;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'fixed';
            textarea.style.top = '0';
            textarea.style.left = '0';
            textarea.style.opacity = '0';
            textarea.style.pointerEvents = 'none';
            
            // Add to DOM
            document.body.appendChild(textarea);
            
            // Select and copy
            textarea.select();
            textarea.setSelectionRange(0, 99999);
            const successful = document.execCommand('copy');
            
            // Log result
            if (successful) {
                showAlert('Copied to clipboard!');
            } else {
                showAlert('Copy failed. Please try again.', 'error');
            }
            
            // Clean up
            document.body.removeChild(textarea);
        } catch (err) {
            showAlert('Copy failed. Please try again.', 'error');
        }
    }
    
    // Format on load to show example
    document.getElementById('formatButton').click();
});
</script>
@endsection