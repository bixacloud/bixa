// public/js/advertisement.js
document.addEventListener('DOMContentLoaded', function() {
    loadAdvertisingSystem();
});

/**
 * Load the advertising system
 */
function loadAdvertisingSystem() {
    // Fetch ad slots first to create dynamic slots
    fetch('/api/ad-slots')
        .then(response => {
            // Kiểm tra xem response có phải là JSON không
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error(`Expected JSON, got ${contentType}`);
            }
            
            return response.json();
        })
        .then(slots => {
            // Process dynamic slots
            createDynamicSlots(slots);
            
            // Now fetch and load advertisements
            loadAdvertisements();
        })
        .catch(error => {
            console.error('Error loading ad slots:', error);
        });
}

/**
 * Create dynamic slots based on CSS selectors
 */
function createDynamicSlots(slots) {
    slots.forEach(slot => {
        if (slot.type === 'dynamic' && slot.selector && slot.is_active) {
            try {
                const elements = document.querySelectorAll(slot.selector);
                
                if (elements.length === 0) {
                    console.warn(`No elements found for selector: ${slot.selector}`);
                    return;
                }
                
                elements.forEach(el => {
                    const adContainer = document.createElement('div');
                    adContainer.id = 'ad-slot-' + slot.code;
                    adContainer.className = 'ad-slot';
                    
                    // Insert container at the appropriate position
                    switch(slot.position) {
                        case 'before': 
                            el.parentNode.insertBefore(adContainer, el); 
                            break;
                        case 'after': 
                            el.parentNode.insertBefore(adContainer, el.nextSibling); 
                            break;
                        case 'prepend': 
                            el.prepend(adContainer); 
                            break;
                        case 'append': 
                            el.append(adContainer); 
                            break;
                    }
                });
            } catch (error) {
                console.error(`Error creating dynamic slot ${slot.code}:`, error);
            }
        }
    });
}

/**
 * Load advertisements into slots
 */
function loadAdvertisements() {
    fetch('/api/advertisements')
        .then(response => {
            // Kiểm tra xem response có phải là JSON không
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error(`Expected JSON, got ${contentType}`);
            }
            
            return response.json();
        })
        .then(ads => {
            ads.forEach(ad => {
                if (ad.slot_position && ad.is_active) {
                    const slot = document.getElementById('ad-slot-' + ad.slot_position);
                    if (slot) {
                        // Insert ad content
                        slot.innerHTML = ad.html_content;
                        
                        // Add click tracking
                        addClickTracking(slot, ad.id);
                        
                        // Record impression
                        recordImpression(ad.id);
                    } else {
                        console.warn(`Ad slot not found for position: ${ad.slot_position}`);
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading advertisements:', error);
        });
}

/**
 * Add click tracking to all links within an ad
 */
function addClickTracking(container, adId) {
    const links = container.querySelectorAll('a');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            recordClick(adId);
            // Don't prevent default, let the user follow the link
        });
    });
}

/**
 * Record an impression for an ad
 */
function recordImpression(adId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch(`/api/advertisements/${adId}/impression`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).catch(error => {
        console.error('Error recording impression:', error);
    });
}

/**
 * Record a click for an ad
 */
function recordClick(adId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch(`/api/advertisements/${adId}/click`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).catch(error => {
        console.error('Error recording click:', error);
    });
}