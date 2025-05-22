@extends('layouts.master')

@section('title') CSS Grid Generator @endsection

@section('css')
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
    
    /* Grid controls */
    .grid-controls {
        margin-bottom: 1.5rem;
    }
    
    .grid-params {
        display: flex;
        justify-content: center;
        margin-bottom: 1.5rem;
        gap: 1rem;
    }
    
    .param-group {
        text-align: center;
    }
    
    .param-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .param-input {
        width: 100px;
        height: 50px;
        text-align: center;
        font-size: 1.2rem;
        background-color: #18181b;
        color: white;
        border: none;
        border-radius: 8px;
    }
    
    /* Grid container */
    .grid-container {
        margin-bottom: 2rem;
        padding: 1rem;
        min-height: 500px;
    }
    
    .grid-wrapper {
        display: grid;
        width: 100%;
        min-height: 480px;
        grid-template-columns: repeat(var(--grid-cols, 5), 1fr);
        grid-template-rows: repeat(var(--grid-rows, 5), 1fr);
        gap: var(--grid-gap, 8px);
    }
    
    /* Grid cells */
    .grid-cell {
        background-color: #f9f9f9;
        border: 1px dashed #e5e7eb;
        border-radius: 8px;
        position: relative;
    }
    
    /* Grid item */
    .grid-item {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #fff;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        font-weight: 500;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        z-index: 1;
        will-change: transform, width, height;
        transition: box-shadow 0.2s ease;
    }
    
    .grid-item.dragging {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        z-index: 10;
        opacity: 0.9;
        cursor: grabbing;
    }
    
    .grid-item.resizing {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        z-index: 10;
    }
    
    .grid-item.selected {
        background-color: #dbeafe;
        border-color: #93c5fd;
    }
    
    /* Add button */
    .add-btn {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background-color: #a1a1aa;
        color: white;
        border-radius: 8px;
        cursor: pointer;
        user-select: none;
    }
    
    .add-btn:hover {
        background-color: #71717a;
    }
    
    /* Remove button */
    .remove-btn {
        position: absolute;
        top: 0;
        right: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ef4444;
        color: white;
        border-radius: 0 8px 0 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        z-index: 2;
        user-select: none;
    }
    
    /* Corner resize handle */
    .corner-handle {
        position: absolute;
        right: 0;
        bottom: 0;
        width: 16px;
        height: 16px;
        cursor: se-resize;
        z-index: 2;
        display: block;
        border: 16px solid transparent;
        border-right-color: #000;
        border-bottom-color: #000;
        border-radius: 0 0 8px 0;
        opacity: 0.1;
        user-select: none;
    }
    
    /* Code output */
    .code-output {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    @media (max-width: 767.98px) {
        .code-output {
            flex-direction: column;
        }
    }
    
    .code-section {
        flex: 1;
    }
    
    .code-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 1rem;
        background-color: #18181b;
        color: white;
        border-radius: 0.25rem 0.25rem 0 0;
    }
    
    .code-content {
        padding: 1rem;
        background-color: #f9f9f9;
        border: 1px solid #e5e7eb;
        border-top: none;
        border-radius: 0 0 0.25rem 0.25rem;
        white-space: pre;
        overflow-x: auto;
        font-family: monospace;
        max-height: 300px;
        overflow-y: auto;
    }
    
    /* Buttons */
    .action-buttons {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
    }
    
    .reset-btn {
        padding: 0.5rem 1.5rem;
        background-color: transparent;
        border: 1px solid #71717a;
        border-radius: 999px;
        cursor: pointer;
    }
    
    .reset-btn:hover {
        background-color: #f4f4f5;
    }
    
    .copy-btn {
        background-color: #18181b;
        color: white;
        border: none;
        border-radius: 0.25rem;
        cursor: pointer;
        padding: 0.25rem 0.75rem;
    }
    
    .copy-btn:hover {
        background-color: #27272a;
    }
    
    /* Toast notification */
    .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #4ade80;
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        display: none;
        z-index: 1000;
    }
    
    .toast.show {
        display: block;
        animation: fadeIn 0.3s, fadeOut 0.3s 2.7s;
        animation-fill-mode: forwards;
    }
    
    .toast.error {
        background-color: #ef4444;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(20px); }
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Tools @endslot
        @slot('title') CSS Grid Generator @endslot
    @endcomponent

    <!-- Header Section -->
    <div class="header-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-4 header-title">CSS Grid Generator</h1>
            <p class="lead mb-4">Create custom CSS grid layouts easily by specifying the number of columns, rows, and gap size.</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">How to use CSS Grid Generator?</h5>
                        
                        <ol class="mb-4">
                            <li class="mb-2">Customize the number of columns, rows, and gaps to fit your needs.</li>
                            <li class="mb-2">Click the square with + sign to add a new element to the grid.</li>
                            <li class="mb-2">Resize the DIV using the handle in the bottom right corner.</li>
                            <li class="mb-2">Drag and drop the DIV to reposition it as desired.</li>
                            <li class="mb-2">Finally, copy the generated HTML and CSS code and paste it into your project.</li>
                        </ol>
                        
                        <div class="grid-controls">
                            <div class="grid-params">
                                <div class="param-group">
                                    <label>Columns</label>
                                    <input type="number" id="columns" class="param-input" value="5" min="1" max="12">
                                </div>
                                
                                <div class="param-group">
                                    <label>Rows</label>
                                    <input type="number" id="rows" class="param-input" value="5" min="1" max="12">
                                </div>
                                
                                <div class="param-group">
                                    <label>Gap(px)</label>
                                    <input type="number" id="gap" class="param-input" value="8" min="0" max="50">
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid-container">
                            <div id="gridWrapper" class="grid-wrapper"></div>
                        </div>
                        
                        <div class="action-buttons">
                            <button id="resetBtn" class="reset-btn">Reset</button>
                        </div>
                        
                        <div class="code-output mt-4">
                            <div class="code-section">
                                <div class="code-header">
                                    <span>HTML</span>
                                    <button id="copyHtmlBtn" class="copy-btn">Copy HTML</button>
                                </div>
                                <div id="htmlOutput" class="code-content"></div>
                            </div>
                            
                            <div class="code-section">
                                <div class="code-header">
                                    <span>CSS</span>
                                    <button id="copyCssBtn" class="copy-btn">Copy CSS</button>
                                </div>
                                <div id="cssOutput" class="code-content"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast notification -->
    <div id="toast" class="toast"></div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // DOM Elements
        const gridWrapper = document.getElementById('gridWrapper');
        const columnsInput = document.getElementById('columns');
        const rowsInput = document.getElementById('rows');
        const gapInput = document.getElementById('gap');
        const resetBtn = document.getElementById('resetBtn');
        const htmlOutput = document.getElementById('htmlOutput');
        const cssOutput = document.getElementById('cssOutput');
        const copyHtmlBtn = document.getElementById('copyHtmlBtn');
        const copyCssBtn = document.getElementById('copyCssBtn');
        const toast = document.getElementById('toast');
        
        // Grid state
        let gridItems = [];
        let gridCells = [];
        let itemCounter = 0;
        let columns = parseInt(columnsInput.value);
        let rows = parseInt(rowsInput.value);
        let gap = parseInt(gapInput.value);
        let selectedItem = null;
        let isDragging = false;
        let isResizing = false;
        let updatePending = false;
        
        // Initialize the grid
        initializeGrid();
        
        // Event listeners for grid controls
        // Use debounced input to prevent lag while typing
        columnsInput.addEventListener('input', debounce(updateGrid, 300));
        rowsInput.addEventListener('input', debounce(updateGrid, 300));
        gapInput.addEventListener('input', debounce(updateGrid, 300));
        resetBtn.addEventListener('click', resetGrid);
        copyHtmlBtn.addEventListener('click', () => copyToClipboard(htmlOutput.textContent, 'HTML'));
        copyCssBtn.addEventListener('click', () => copyToClipboard(cssOutput.textContent, 'CSS'));
        
        // Debounce function to limit rate of function calls
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }
        
        // Initialize the grid
        function initializeGrid() {
            columns = parseInt(columnsInput.value) || 5;
            rows = parseInt(rowsInput.value) || 5;
            gap = parseInt(gapInput.value) || 8;
            
            // Set CSS variables for grid
            gridWrapper.style.setProperty('--grid-cols', columns);
            gridWrapper.style.setProperty('--grid-rows', rows);
            gridWrapper.style.setProperty('--grid-gap', gap + 'px');
            
            // Clear existing cells
            gridWrapper.innerHTML = '';
            gridCells = [];
            
            // Create grid cells
            for (let row = 1; row <= rows; row++) {
                for (let col = 1; col <= columns; col++) {
                    createCell(row, col);
                }
            }
            
            // Add items back to the grid (if any)
            renderItems();
            
            // Update code output
            updateCodeOutput();
        }
        
        // Create a single grid cell
        function createCell(row, col) {
            const cell = document.createElement('div');
            cell.className = 'grid-cell';
            cell.dataset.row = row;
            cell.dataset.col = col;
            cell.style.gridRow = row;
            cell.style.gridColumn = col;
            
            gridWrapper.appendChild(cell);
            gridCells.push(cell);
        }
        
        // Check if a cell is occupied
        function isCellOccupied(row, col) {
            return gridItems.some(item => {
                const itemRow = parseInt(item.row);
                const itemCol = parseInt(item.col);
                const itemRowSpan = parseInt(item.rowSpan);
                const itemColSpan = parseInt(item.colSpan);
                
                return row >= itemRow && row < itemRow + itemRowSpan && 
                       col >= itemCol && col < itemCol + itemColSpan;
            });
        }
        
        // Add a new grid item
        function addItem(row, col) {
            itemCounter++;
            
            // Create item object
            const item = {
                id: itemCounter,
                row: row,
                col: col,
                rowSpan: 1,
                colSpan: 1
            };
            
            // Add to items array
            gridItems.push(item);
            
            // Render all items
            renderItems();
            
            // Update code output
            updateCodeOutput();
            
            // Select the new item
            selectItem(item.id);
        }
        
        // Render all grid items with optimized reflow
        function renderItems() {
            // Use a document fragment to batch DOM operations
            const fragment = document.createDocumentFragment();
            
            // Remove all existing items and add buttons
            document.querySelectorAll('.grid-item, .add-btn').forEach(elem => elem.remove());
            
            // Create a matrix to track occupied cells
            const occupiedCells = {};
            
            // Mark occupied cells
            gridItems.forEach(item => {
                const itemRow = parseInt(item.row);
                const itemCol = parseInt(item.col);
                const itemRowSpan = parseInt(item.rowSpan);
                const itemColSpan = parseInt(item.colSpan);
                
                for (let r = itemRow; r < itemRow + itemRowSpan; r++) {
                    for (let c = itemCol; c < itemCol + itemColSpan; c++) {
                        if (r <= rows && c <= columns) {
                            occupiedCells[`${r}_${c}`] = true;
                        }
                    }
                }
            });
            
            // Create "Add" buttons for empty cells first
            gridCells.forEach(cell => {
                const row = parseInt(cell.dataset.row);
                const col = parseInt(cell.dataset.col);
                
                if (!occupiedCells[`${row}_${col}`]) {
                    const addBtn = document.createElement('div');
                    addBtn.className = 'add-btn';
                    addBtn.textContent = '+';
                    
                    // Use event delegation for better performance
                    addBtn.addEventListener('click', function() {
                        addItem(row, col);
                    });
                    
                    cell.appendChild(addBtn);
                }
            });
            
            // Add items to their cells
            gridItems.forEach(item => {
                createItemElement(item);
            });
            
            // Batch update the grid
            requestAnimationFrame(() => {
                updatePending = false;
            });
        }
        
        // Create a grid item element
        function createItemElement(item) {
            // Find the target cell (top-left corner of the item)
            const cell = gridCells.find(cell => 
                parseInt(cell.dataset.row) === parseInt(item.row) && 
                parseInt(cell.dataset.col) === parseInt(item.col)
            );
            
            if (!cell) return;
            
            // Create item element
            const itemElem = document.createElement('div');
            itemElem.className = 'grid-item';
            if (selectedItem === item.id) {
                itemElem.classList.add('selected');
            }
            itemElem.textContent = item.id;
            itemElem.dataset.id = item.id;
            
            // Set size based on span
            const colSpan = parseInt(item.colSpan);
            const rowSpan = parseInt(item.rowSpan);
            itemElem.style.width = `calc(100% * ${colSpan} + ${gap * (colSpan - 1)}px)`;
            itemElem.style.height = `calc(100% * ${rowSpan} + ${gap * (rowSpan - 1)}px)`;
            
            // Add "X" button
            const removeBtn = document.createElement('div');
            removeBtn.className = 'remove-btn';
            removeBtn.textContent = 'X';
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                removeItem(item.id);
            });
            itemElem.appendChild(removeBtn);
            
            // Add corner resize handle
            const cornerHandle = document.createElement('div');
            cornerHandle.className = 'corner-handle';
            itemElem.appendChild(cornerHandle);
            
            // Add to DOM
            cell.appendChild(itemElem);
            
            // Make item selectable
            itemElem.addEventListener('click', (e) => {
                // Don't select if dragging or clicking controls
                if (isDragging || isResizing || 
                    e.target.classList.contains('remove-btn') || 
                    e.target.classList.contains('corner-handle')) {
                    return;
                }
                selectItem(item.id);
            });
            
            // Make item resizable
            initResizable(itemElem, item);
            
            // Make item draggable
            initDraggable(itemElem, item);
        }
        
        // Select an item
        function selectItem(id) {
            // Deselect previously selected item
            if (selectedItem) {
                const prevItem = document.querySelector(`.grid-item[data-id="${selectedItem}"]`);
                if (prevItem) prevItem.classList.remove('selected');
            }
            
            // Select new item
            selectedItem = id;
            const itemElem = document.querySelector(`.grid-item[data-id="${id}"]`);
            if (itemElem) itemElem.classList.add('selected');
        }
        
        // Remove an item
        function removeItem(id) {
            // Remove from array
            const index = gridItems.findIndex(item => item.id === id);
            if (index !== -1) {
                gridItems.splice(index, 1);
            }
            
            // If the removed item was selected, deselect it
            if (selectedItem === id) {
                selectedItem = null;
            }
            
            // Render items
            renderItems();
            
            // Update code output
            updateCodeOutput();
        }
        
        // Initialize resizable behavior with optimized performance
        function initResizable(itemElem, item) {
            const handle = itemElem.querySelector('.corner-handle');
            let startX, startY, startWidth, startHeight;
            let initialColSpan, initialRowSpan;
            let currentColSpan, currentRowSpan;
            let frameId = null;
            
            handle.addEventListener('mousedown', startResize, { passive: true });
            handle.addEventListener('touchstart', startResize, { passive: true });
            
            function startResize(e) {
                // Prevent default only if needed
                if (!e.target.closest('.remove-btn')) {
                    if (e.cancelable) e.preventDefault();
                }
                
                e.stopPropagation();
                isResizing = true;
                
                // Add dragging visual state
                itemElem.classList.add('resizing');
                
                // Get initial position and dimensions
                const rect = itemElem.getBoundingClientRect();
                startX = e.clientX || e.touches[0].clientX;
                startY = e.clientY || e.touches[0].clientY;
                startWidth = rect.width;
                startHeight = rect.height;
                initialColSpan = parseInt(item.colSpan);
                initialRowSpan = parseInt(item.rowSpan);
                currentColSpan = initialColSpan;
                currentRowSpan = initialRowSpan;
                
                // Add event listeners for resize using high-performance approach
                window.addEventListener('mousemove', resize, { passive: true });
                window.addEventListener('touchmove', resize, { passive: true });
                window.addEventListener('mouseup', stopResize);
                window.addEventListener('touchend', stopResize);
                window.addEventListener('touchcancel', stopResize);
            }
            
            function resize(e) {
                if (frameId) {
                    cancelAnimationFrame(frameId);
                }
                
                frameId = requestAnimationFrame(() => {
                    // Calculate new width and height
                    const clientX = e.clientX || e.touches[0].clientX;
                    const clientY = e.clientY || e.touches[0].clientY;
                    const deltaX = clientX - startX;
                    const deltaY = clientY - startY;
                    
                    // Get cell dimensions
                    const cellWidth = (gridWrapper.offsetWidth - gap * (columns - 1)) / columns;
                    const cellHeight = (gridWrapper.offsetHeight - gap * (rows - 1)) / rows;
                    
                    // Calculate new colSpan and rowSpan
                    const newColSpan = Math.max(1, Math.round((startWidth + deltaX) / (cellWidth + gap)));
                    const newRowSpan = Math.max(1, Math.round((startHeight + deltaY) / (cellHeight + gap)));
                    
                    // Limit to grid boundaries
                    const maxColSpan = columns - parseInt(item.col) + 1;
                    const maxRowSpan = rows - parseInt(item.row) + 1;
                    
                    currentColSpan = Math.min(newColSpan, maxColSpan);
                    currentRowSpan = Math.min(newRowSpan, maxRowSpan);
                    
                    // Update element dimensions with transform for better performance during resize
                    const newWidth = `calc(100% * ${currentColSpan} + ${gap * (currentColSpan - 1)}px)`;
                    const newHeight = `calc(100% * ${currentRowSpan} + ${gap * (currentRowSpan - 1)}px)`;
                    itemElem.style.width = newWidth;
                    itemElem.style.height = newHeight;
                    
                    frameId = null;
                });
            }
            
            function stopResize() {
                if (frameId) {
                    cancelAnimationFrame(frameId);
                    frameId = null;
                }
                
                isResizing = false;
                itemElem.classList.remove('resizing');
                
                // Remove event listeners
                window.removeEventListener('mousemove', resize);
                window.removeEventListener('touchmove', resize);
                window.removeEventListener('mouseup', stopResize);
                window.removeEventListener('touchend', stopResize);
                window.removeEventListener('touchcancel', stopResize);
                
                // Update item data only if span has changed
                if (currentColSpan !== initialColSpan || currentRowSpan !== initialRowSpan) {
                    item.colSpan = currentColSpan;
                    item.rowSpan = currentRowSpan;
                    
                    // Only re-render items if spans have changed
                    renderItems();
                    updateCodeOutput();
                }
            }
        }
        
        // Initialize draggable behavior with optimized performance
        function initDraggable(itemElem, item) {
            let startX, startY;
            let initialRow, initialCol;
            let currentRow, currentCol;
            let frameId = null;
            
            itemElem.addEventListener('mousedown', startDrag, { passive: true });
            itemElem.addEventListener('touchstart', startDrag, { passive: true });
            
            function startDrag(e) {
                // Ignore if clicking on remove button or resize handle
                if (e.target.closest('.remove-btn') || e.target.closest('.corner-handle')) {
                    return;
                }
                
                if (e.cancelable) e.preventDefault();
                
                isDragging = true;
                
                // Add dragging visual state
                itemElem.classList.add('dragging');
                
                // Get initial position
                startX = e.clientX || e.touches[0].clientX;
                startY = e.clientY || e.touches[0].clientY;
                initialRow = parseInt(item.row);
                initialCol = parseInt(item.col);
                currentRow = initialRow;
                currentCol = initialCol;
                
                // Add event listeners for drag
                window.addEventListener('mousemove', drag, { passive: true });
                window.addEventListener('touchmove', drag, { passive: true });
                window.addEventListener('mouseup', stopDrag);
                window.addEventListener('touchend', stopDrag);
                window.addEventListener('touchcancel', stopDrag);
                
                // Select this item
                selectItem(item.id);
            }
            
            function drag(e) {
                if (frameId) {
                    cancelAnimationFrame(frameId);
                }
                
                frameId = requestAnimationFrame(() => {
                    // Calculate new position
                    const clientX = e.clientX || e.touches[0].clientX;
                    const clientY = e.clientY || e.touches[0].clientY;
                    const deltaX = clientX - startX;
                    const deltaY = clientY - startY;
                    
                    // Get cell dimensions
                    const cellWidth = (gridWrapper.offsetWidth - gap * (columns - 1)) / columns;
                    const cellHeight = (gridWrapper.offsetHeight - gap * (rows - 1)) / rows;
                    
                    // Calculate new row and column
                    const colOffset = Math.round(deltaX / (cellWidth + gap));
                    const rowOffset = Math.round(deltaY / (cellHeight + gap));
                    
                    const newCol = Math.max(1, Math.min(columns - item.colSpan + 1, initialCol + colOffset));
                    const newRow = Math.max(1, Math.min(rows - item.rowSpan + 1, initialRow + rowOffset));
                    
                    // Only update if position has changed
                    if (newCol !== currentCol || newRow !== currentRow) {
                        currentCol = newCol;
                        currentRow = newRow;
                        
                        // Use transform for movement during drag (better performance)
                        const offsetX = (currentCol - initialCol) * (cellWidth + gap);
                        const offsetY = (currentRow - initialRow) * (cellHeight + gap);
                        itemElem.style.transform = `translate(${offsetX}px, ${offsetY}px)`;
                    }
                    
                    frameId = null;
                });
            }
            
            function stopDrag() {
                if (frameId) {
                    cancelAnimationFrame(frameId);
                    frameId = null;
                }
                
                isDragging = false;
                itemElem.classList.remove('dragging');
                itemElem.style.transform = '';
                
                // Remove event listeners
                window.removeEventListener('mousemove', drag);
                window.removeEventListener('touchmove', drag);
                window.removeEventListener('mouseup', stopDrag);
                window.removeEventListener('touchend', stopDrag);
                window.removeEventListener('touchcancel', stopDrag);
                
                // Update item position only if changed
                if (currentCol !== initialCol || currentRow !== initialRow) {
                    item.col = currentCol;
                    item.row = currentRow;
                    
                    // Render items and update code
                    renderItems();
                    updateCodeOutput();
                }
            }
        }
        
        // Update the grid dimensions
        function updateGrid() {
            if (updatePending) return;
            updatePending = true;
            
            // Use requestAnimationFrame to batch updates
            requestAnimationFrame(() => {
                initializeGrid();
                updatePending = false;
            });
        }
        
        // Reset the grid
        function resetGrid() {
            // Clear items
            gridItems = [];
            itemCounter = 0;
            selectedItem = null;
            
            // Re-initialize grid
            initializeGrid();
            
            // Show toast notification
            showToast('Grid has been reset', 'success');
        }
        
       // Chỉ thay đổi phần hàm updateCodeOutput() để sử dụng cú pháp CSS Grid giống như trang web gốc

function updateCodeOutput() {
    if (updatePending) return;
    
    // Batch code updates to avoid layout thrashing
    requestAnimationFrame(() => {
        // HTML output
        let html = '<div class="parent">\n';
        gridItems.forEach(item => {
            html += `    <div class="div${item.id}"></div>\n`;
        });
        html += '</div>';
        
        // CSS output
        let css = '.parent {\n';
        css += '    display: grid;\n';
        css += `    grid-template-columns: repeat(${columns}, 1fr);\n`;
        css += `    grid-template-rows: repeat(${rows}, 1fr);\n`;
        css += `    gap: ${gap}px;\n`;
        css += '}\n\n';
        
        gridItems.forEach(item => {
            const rowStart = parseInt(item.row);
            const colStart = parseInt(item.col);
            const rowSpan = parseInt(item.rowSpan);
            const colSpan = parseInt(item.colSpan);
            
            css += `.div${item.id} {\n`;
            
           
            if (colSpan > 1) {
                css += `    grid-column: span ${colSpan} / span ${colSpan};\n`;
            } else {
                css += `    grid-column-start: ${colStart};\n`;
            }
            
            if (rowSpan > 1) {
                css += `    grid-row: span ${rowSpan} / span ${rowSpan};\n`;
            } else {
                css += `    grid-row-start: ${rowStart};\n`;
            }
            
         
            if (colStart > 1 && colSpan > 1) {
                css += `    grid-column-start: ${colStart};\n`;
            }
            
            
            if (rowStart > 1 && rowSpan > 1) {
                css += `    grid-row-start: ${rowStart};\n`;
            }
            
            css += '}\n\n';
        });
        
        // Update output elements
        htmlOutput.textContent = html;
        cssOutput.textContent = css;
    });
}
        
        // Toast notification function
        function showToast(message, type = 'success') {
            toast.textContent = message;
            toast.className = 'toast';
            if (type === 'error') toast.classList.add('error');
            
            // Remove existing animation
            toast.style.animation = 'none';
            toast.offsetHeight; // Trigger reflow
            toast.style.animation = null;
            
            toast.classList.add('show');
            
            // Auto hide after 3 seconds
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
        
        // Copy to clipboard with improved feedback
        function copyToClipboard(text, type) {
            navigator.clipboard.writeText(text).then(() => {
                showToast(`${type} copied to clipboard!`, 'success');
            }).catch(err => {
                console.error('Failed to copy: ', err);
                showToast(`Failed to copy ${type}`, 'error');
            });
        }
    });
</script>
@endsection