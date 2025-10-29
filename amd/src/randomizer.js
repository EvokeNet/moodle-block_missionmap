export const init = () => {
    const screen_size = window.screen.width;
    const map_image = document.querySelector('img');

    if (screen_size <= 600) {
        return;
    }

    if (map_image.complete) {
        randomize();
        initTooltips();
    } else {
        map_image.addEventListener('load', () => {
            randomize();
            initTooltips();
        });
        map_image.addEventListener('error', () => {
            window.console.log('Error while randomizing missions on the map');
        });
    }
};

const initTooltips = () => {
    // Initialize tooltips for mission descriptions
    const missionElements = document.querySelectorAll('.mission[data-toggle="tooltip"]');
    
    missionElements.forEach(element => {
        // Check if tooltip is already initialized
        if (!element.hasAttribute('data-tooltip-initialized')) {
            // Mark as initialized
            element.setAttribute('data-tooltip-initialized', 'true');
            
            // Always use custom tooltip implementation for better control
            initCustomTooltip(element);
        }
    });
    
    console.log('Tooltip initialization completed for', missionElements.length, 'elements');
};

const initCustomTooltip = (element) => {
    const title = element.getAttribute('title');
    if (!title) return;
    
    // Remove the title attribute to prevent native tooltip
    element.removeAttribute('title');
    
    // Create tooltip element
    const tooltip = document.createElement('div');
    tooltip.className = 'mission-tooltip';
    tooltip.innerHTML = title;
    tooltip.style.cssText = `
        position: fixed;
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 10000;
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease;
        max-width: 200px;
        word-wrap: break-word;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    `;
    
    document.body.appendChild(tooltip);
    
    let showTimeout, hideTimeout;
    
    element.addEventListener('mouseenter', () => {
        clearTimeout(hideTimeout);
        showTimeout = setTimeout(() => {
            const rect = element.getBoundingClientRect();
            
            // Make tooltip visible first to get dimensions
            tooltip.style.opacity = '1';
            tooltip.style.visibility = 'hidden'; // Hidden but taking up space
            
            // Position tooltip directly above the mission dot
            const centerX = rect.left + (rect.width / 2);
            const tooltipX = centerX - (tooltip.offsetWidth / 2);
            const tooltipY = rect.top - tooltip.offsetHeight - 2; // Very close gap - just 2px
            
            tooltip.style.left = tooltipX + 'px';
            tooltip.style.top = tooltipY + 'px';
            
            // Now make it visible
            tooltip.style.visibility = 'visible';
            
            console.log('Tooltip positioned at:', tooltip.style.left, tooltip.style.top);
            console.log('Mission dot at:', rect.left, rect.top);
            console.log('Mission center X:', centerX);
            console.log('Tooltip width:', tooltip.offsetWidth);
            console.log('Tooltip height:', tooltip.offsetHeight);
            console.log('Gap between tooltip and mission:', rect.top - tooltipY - tooltip.offsetHeight);
        }, 300);
    });
    
    element.addEventListener('mouseleave', () => {
        clearTimeout(showTimeout);
        hideTimeout = setTimeout(() => {
            tooltip.style.opacity = '0';
            setTimeout(() => {
                tooltip.style.visibility = 'hidden';
            }, 300);
        }, 100);
    });
    
    // Function to update tooltip position
    const updateTooltipPosition = () => {
        if (tooltip.style.visibility === 'visible') {
            const rect = element.getBoundingClientRect();
            const centerX = rect.left + (rect.width / 2);
            const tooltipX = centerX - (tooltip.offsetWidth / 2);
            const tooltipY = rect.top - tooltip.offsetHeight - 5;
            
            tooltip.style.left = tooltipX + 'px';
            tooltip.style.top = tooltipY + 'px';
        }
    };
    
    // Update position on scroll and resize
    window.addEventListener('scroll', updateTooltipPosition, { passive: true });
    window.addEventListener('resize', updateTooltipPosition, { passive: true });
    
    console.log('Custom scroll-aware tooltip initialized for:', title);
};

const randomize = () => {
    const chapters = document.querySelectorAll('.chapter');

    const seed = xmur3('avocadotoast');
    const rand = sfc32(seed(), seed(), seed(), seed());

    for (let i = 0; i < chapters.length; i++) {
        let chapter_width = chapters[i].offsetWidth;
        let chapter_height = chapters[i].offsetHeight;
        let missions = chapters[i].querySelectorAll('.mission');

        let division = chapter_width / missions.length;

        let perc_division = division / chapter_width;
        let offsetLeft = 0.05;

        for (let j = 0; j < missions.length; j++) {
            let mission_height = missions[j].offsetHeight;
            let height_discount = mission_height / chapter_height;

            let posx = offsetLeft * 100;
            var posy = (rand() * (1 - height_discount) * 100).toFixed();

            // Position the mission element
            missions[j].style.left = `${posx}%`;
            missions[j].style.top = `${posy}%`;
            missions[j].style.transform = `translate(-${posx}%, -${posy}%)`;
            
            // Also position the mission container if it exists
            const missionContainer = missions[j].closest('.mission-container');
            if (missionContainer) {
                missionContainer.style.position = 'absolute';
                missionContainer.style.left = `${posx}%`;
                missionContainer.style.top = `${posy}%`;
                missionContainer.style.transform = `translate(-${posx}%, -${posy}%)`;
            }
            
            offsetLeft += perc_division;
        }
    }
};

/* eslint-disable */
const xmur3 = (str) => {
    for (var i = 0, h = 1779033703 ^ str.length; i < str.length; i++)
        (h = Math.imul(h ^ str.charCodeAt(i), 3432918353)),
            (h = (h << 13) | (h >>> 19));
    return function () {
        h = Math.imul(h ^ (h >>> 16), 2246822507);
        h = Math.imul(h ^ (h >>> 13), 3266489909);
        return (h ^= h >>> 16) >>> 0;
    };
};

const sfc32 = (a, b, c, d) => {
    return function () {
        a >>>= 0;
        b >>>= 0;
        c >>>= 0;
        d >>>= 0;
        var t = (a + b) | 0;
        a = b ^ (b >>> 9);
        b = (c + (c << 3)) | 0;
        c = (c << 21) | (c >>> 11);
        d = (d + 1) | 0;
        t = (t + d) | 0;
        c = (c + t) | 0;
        return (t >>> 0) / 4294967296;
    };
};
