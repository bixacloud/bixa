// public/js/advertisement-debug.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 Ad system initializing...');
    loadAdvertisingSystem();
});

/**
 * Load the advertising system with enhanced debugging
 */
function loadAdvertisingSystem() {
    console.log('🔍 Fetching ad slots from /api/ad-slots');
    
    // Fetch ad slots first to create dynamic slots
    fetch('/api/ad-slots')
        .then(response => {
            console.log('🔍 Ad slots response status:', response.status);
            console.log('🔍 Ad slots response headers:', Object.fromEntries(response.headers.entries()));
            
            // Clone the response for debugging raw content
            return response.clone().text().then(rawText => {
                console.log('🔍 Raw response:', rawText.substring(0, 500) + (rawText.length > 500 ? '...' : ''));
                
                // Check if response is valid JSON
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${rawText.substring(0, 200)}`);
                }
                
                // Try to parse as JSON
                try {
                    return JSON.parse(rawText);
                } catch (e) {
                    console.error('🔍 JSON parse error:', e);
                    console.error('🔍 First 1000 characters of response:', rawText.substring(0, 1000));
                    throw new Error(`Invalid JSON response: ${e.message}`);
                }
            });
        })
        .then(slots => {
            console.log('🔍 Received ad slots:', slots);
            
            // Process dynamic slots
            createDynamicSlots(slots);
            
            // Now fetch and load advertisements
            loadAdvertisements();
        })
        .catch(error => {
            console.error('🔍 Error loading ad slots:', error);
            document.dispatchEvent(new CustomEvent('adSystemError', { 
                detail: { 
                    message: error.message, 
                    type: 'adSlots' 
                } 
            }));
        });
}

/**
 * Create dynamic slots based on CSS selectors
 */
function createDynamicSlots(slots) {
    console.log('🔍 Creating dynamic slots');
    
    slots.forEach(slot => {
        console.log(`🔍 Processing slot: ${slot.code}, Type: ${slot.type}, Selector: ${slot.selector}`);
        
        if (slot.type === 'dynamic' && slot.selector && slot.is_active) {
            try {
                const elements = document.querySelectorAll(slot.selector);
                console.log(`🔍 Found ${elements.length} elements for selector: ${slot.selector}`);
                
                if (elements.length === 0) {
                    console.warn(`🔍 No elements found for selector: ${slot.selector}`);
                    // Log all existing selectors for debugging
                    console.log('🔍 All available elements on page:', 
                        Array.from(document.querySelectorAll('*'))
                            .map(el => el.tagName + (el.id ? '#'+el.id : '') + 
                                 (el.className ? '.'+el.className.replace(/ /g, '.') : ''))
                            .slice(0, 50)
                    );
                    return;
                }
                
                elements.forEach((el, index) => {
                    console.log(`🔍 Creating ad container for element ${index+1}`);
                    
                    const adContainer = document.createElement('div');
                    adContainer.id = 'ad-slot-' + slot.code;
                    adContainer.className = 'ad-slot';
                    adContainer.setAttribute('data-debug', 'dynamic-slot-' + slot.code);
                    
                    // Insert container at the appropriate position
                    switch(slot.position) {
                        case 'before': 
                            el.parentNode.insertBefore(adContainer, el);
                            console.log(`🔍 Inserted before element`);
                            break;
                        case 'after': 
                            el.parentNode.insertBefore(adContainer, el.nextSibling);
                            console.log(`🔍 Inserted after element`);
                            break;
                        case 'prepend': 
                            el.prepend(adContainer);
                            console.log(`🔍 Prepended to element`);
                            break;
                        case 'append': 
                            el.append(adContainer);
                            console.log(`🔍 Appended to element`);
                            break;
                    }
                });
            } catch (error) {
                console.error(`🔍 Error creating dynamic slot ${slot.code}:`, error);
            }
        }
    });
}

/**
 * Load advertisements into slots
 */
function loadAdvertisements() {
    console.log('🔍 Fetching advertisements from /api/advertisements');
    
    fetch('/api/advertisements')
        .then(response => {
            console.log('🔍 Advertisements response status:', response.status);
            console.log('🔍 Advertisements response headers:', Object.fromEntries(response.headers.entries()));
            
            // Clone the response for debugging raw content
            return response.clone().text().then(rawText => {
                console.log('🔍 Raw advertisements response:', rawText.substring(0, 500) + (rawText.length > 500 ? '...' : ''));
                
                // Check if response is valid JSON
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${rawText.substring(0, 200)}`);
                }
                
                // Try to parse as JSON
                try {
                    return JSON.parse(rawText);
                } catch (e) {
                    console.error('🔍 JSON parse error:', e);
                    throw new Error(`Invalid JSON response: ${e.message}`);
                }
            });
        })
        .then(ads => {
            console.log('🔍 Received advertisements:', ads);
            
            ads.forEach(ad => {
                console.log(`🔍 Processing ad: ${ad.name}, Slot: ${ad.slot_position}, Active: ${ad.is_active}`);
                
                if (ad.slot_position && ad.is_active) {
                    const slotId = 'ad-slot-' + ad.slot_position;
                    const slot = document.getElementById(slotId);
                    
                    if (slot) {
                        console.log(`🔍 Found slot with ID: ${slotId}`);
                        
                        // Insert ad content
                        slot.innerHTML = ad.html_content;
                        
                        // Add click tracking
                        addClickTracking(slot, ad.id);
                        
                        // Record impression
                        recordImpression(ad.id);
                    } else {
                        console.warn(`🔍 Ad slot not found for position: ${ad.slot_position}`);
                        console.log('🔍 All available ad slots:', 
                            Array.from(document.querySelectorAll('.ad-slot')).map(el => el.id)
                        );
                    }
                }
            });
        })
        .catch(error => {
            console.error('🔍 Error loading advertisements:', error);
            document.dispatchEvent(new CustomEvent('adSystemError', { 
                detail: { 
                    message: error.message, 
                    type: 'advertisements' 
                } 
            }));
        });
}

/**
 * Add click tracking to all links within an ad
 */
function addClickTracking(container, adId) {
    const links = container.querySelectorAll('a');
    console.log(`🔍 Adding click tracking to ${links.length} links for ad ID: ${adId}`);
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            console.log(`🔍 Ad clicked: ${adId}`);
            recordClick(adId);
        });
    });
}

/**
 * Record an impression for an ad
 */
function recordImpression(adId) {
    console.log(`🔍 Recording impression for ad ID: ${adId}`);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    console.log(`🔍 CSRF Token: ${csrfToken ? 'Found' : 'Not found'}`);
    
    fetch(`/api/advertisements/${adId}/impression`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log(`🔍 Impression recorded response:`, response.status, response.statusText);
    })
    .catch(error => {
        console.error('🔍 Error recording impression:', error);
    });
}

/**
 * Record a click for an ad
 */
function recordClick(adId) {
    console.log(`🔍 Recording click for ad ID: ${adId}`);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch(`/api/advertisements/${adId}/click`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log(`🔍 Click recorded response:`, response.status, response.statusText);
    })
    .catch(error => {
        console.error('🔍 Error recording click:', error);
    });
}

// Create a visual debug panel
function createDebugPanel() {
    const panel = document.createElement('div');
    panel.style.position = 'fixed';
    panel.style.bottom = '0';
    panel.style.right = '0';
    panel.style.width = '300px';
    panel.style.height = 'auto';
    panel.style.backgroundColor = 'rgba(0,0,0,0.8)';
    panel.style.color = 'white';
    panel.style.padding = '10px';
    panel.style.zIndex = '9999';
    panel.style.fontSize = '12px';
    panel.style.fontFamily = 'monospace';
    panel.style.overflowY = 'auto';
    panel.style.maxHeight = '300px';
    panel.innerHTML = '<h3>Ad System Debug</h3><div id="adDebugContent"></div>';
    document.body.appendChild(panel);
    
    // Log events to panel
    const originalConsoleLog = console.log;
    const originalConsoleError = console.error;
    const originalConsoleWarn = console.warn;
    
    console.log = function(...args) {
        originalConsoleLog.apply(console, args);
        if (args[0] && typeof args[0] === 'string' && args[0].includes('🔍')) {
            appendToDebugPanel(args.join(' '));
        }
    };
    
    console.error = function(...args) {
        originalConsoleError.apply(console, args);
        if (args[0] && typeof args[0] === 'string' && args[0].includes('🔍')) {
            appendToDebugPanel('<span style="color:red">' + args.join(' ') + '</span>');
        }
    };
    
    console.warn = function(...args) {
        originalConsoleWarn.apply(console, args);
        if (args[0] && typeof args[0] === 'string' && args[0].includes('🔍')) {
            appendToDebugPanel('<span style="color:orange">' + args.join(' ') + '</span>');
        }
    };
}

function appendToDebugPanel(message) {
    const content = document.getElementById('adDebugContent');
    if (content) {
        content.innerHTML += `<div>${message}</div>`;
        content.scrollTop = content.scrollHeight;
    }
}

// Initialize debug panel
createDebugPanel();
