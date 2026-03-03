/**
 * Portfolio Grid Load More functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    const loadMoreButtons = document.querySelectorAll('.glacier-more');
    
    loadMoreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const portfolioGrid = this.closest('.glacier-card-wrapper');
            if (!portfolioGrid) return;
            
            // Get data from button attributes
            const postId = this.dataset.postId;
            const nonce = this.dataset.nonce;
            const ajaxUrl = this.dataset.ajaxUrl;
            const totalCards = parseInt(this.dataset.totalCards);
            const loadedCards = parseInt(this.dataset.loadedCards);
            
            // Don't load if we already have all cards
            if (loadedCards >= totalCards) {
                this.style.display = 'none';
                return;
            }
            
            // Load more cards via AJAX
            loadMoreCards(portfolioGrid, this, postId, nonce, ajaxUrl, loadedCards);
        });
    });
});

/**
 * Load more portfolio cards via AJAX
 */
function loadMoreCards(container, button, postId, nonce, ajaxUrl, currentCount) {
    // Add loading state
    const originalText = button.querySelector('p').textContent;
    button.querySelector('p').textContent = 'Loading...';
    button.disabled = true;
    
    // Prepare AJAX request
    const formData = new FormData();
    formData.append('action', 'load_more_portfolio');
    formData.append('post_id', postId);
    formData.append('offset', currentCount);
    formData.append('per_page', 6);
    formData.append('nonce', nonce);

    fetch(ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Insert new cards before the load more button
            data.data.cards.forEach((cardData, index) => {
                const cardElement = createCardElement(cardData, currentCount + index);
                container.insertBefore(cardElement, button);
            });
            
            // Update counts and grid sizing
            const newCardCount = container.querySelectorAll('.glacier-card:not(.glacier-more)').length;
            updateGridRows(container, newCardCount);
            repositionLoadMoreButton(button, newCardCount);
            
            // Update button data
            button.dataset.loadedCards = newCardCount;
            
            // Hide button if no more cards
            if (!data.data.has_more) {
                button.style.display = 'none';
                
                // If we have a complete set of 6 cards, add empty placeholder
                if (newCardCount % 6 === 0) {
                    const placeholderPosition = calculateCardPosition(newCardCount);
                    if (placeholderPosition) {
                        const placeholderDiv = document.createElement('div');
                        placeholderDiv.className = 'glacier-card';
                        placeholderDiv.style.cssText = `grid-area: ${placeholderPosition.grid_area};`;
                        container.insertBefore(placeholderDiv, button);
                    }
                }
            } else {
                button.querySelector('p').textContent = originalText;
                button.disabled = false;
            }
        } else {
            button.querySelector('p').textContent = 'Error loading';
        }
    })
    .catch(error => {
        button.querySelector('p').textContent = 'Error loading';
    });
}

/**
 * Create a card element from ACF card data
 */
function createCardElement(cardData, index) {
    const position = calculateCardPosition(index);
    
    const cardDiv = document.createElement('div');
    cardDiv.className = `glacier-card ${position.classes}`;
    cardDiv.style.cssText = `grid-area: ${position.grid_area};`;
    
    // Build card HTML with ACF field structure
    cardDiv.innerHTML = `
        <div class="text-container">
            <div class="arrow"></div>
            ${cardData.location ? `<div class="location-label">${escapeHtml(cardData.location)}</div>` : ''}
            ${cardData.logo ? `<div class="logo"><figure><img src="${cardData.logo.url}" alt="${escapeHtml(cardData.logo.alt || 'Logo')}"></figure></div>` : ''}
            <div class="card-content">
                ${cardData.ceo_name ? `<p class="ceo">${escapeHtml(cardData.ceo_name)}</p>` : ''}
                ${cardData.description ? `<p class="description">${escapeHtml(cardData.description)}</p>` : ''}
            </div>
        </div>
        <div class="image-container">
            ${cardData.product_image ? `<figure class="product"><img src="${cardData.product_image.url}" alt="${escapeHtml(cardData.product_image.alt || 'Product')}"></figure>` : ''}
            ${cardData.hover_image ? `<figure class="hovered"><img src="${cardData.hover_image.url}" alt="${escapeHtml(cardData.hover_image.alt || 'Hover')}"></figure>` : ''}
        </div>
    `;
    
    return cardDiv;
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Calculate card position based on index
 */
function calculateCardPosition(index) {
    const basePattern = [
        {class: 'divpre', type: 'grid-r', row_start: 1, row_span: 2, col_start: 3, col_span: 1},
        {class: 'div1', type: 'grid-l', row_start: 2, row_span: 1, col_start: 1, col_span: 2},
        {class: 'div2', type: 'grid-l', row_start: 3, row_span: 1, col_start: 1, col_span: 2},
        {class: 'div3', type: 'grid-r', row_start: 3, row_span: 2, col_start: 3, col_span: 1},
        {class: 'div4', type: 'grid-l', row_start: 4, row_span: 1, col_start: 1, col_span: 2},
        {class: 'div5', type: 'grid-l', row_start: 5, row_span: 1, col_start: 1, col_span: 2}
    ];
    
    // Special positioning for card 6 (first extra card) - vertical card spanning 2 rows
    if (index === 6) {
        return {
            classes: 'divpre_1 grid-r',
            grid_area: '5 / 3 / 7 / 4', // Row 5-6, spans 2 rows (vertical)
            is_grid_l: false,
            has_arrow_in_text: false
        };
    }
    
    // Special positioning for card 7 (second extra card) - horizontal card at row 6
    if (index === 7) {
        return {
            classes: 'div1_1 grid-l', 
            grid_area: '6 / 1 / 7 / 3', // Row 6 only, spans 1 row (horizontal)
            is_grid_l: true,
            has_arrow_in_text: true
        };
    }
    
    // For cards 0-5, use the original pattern
    if (index < 6) {
        const pattern = basePattern[index];
        return {
            classes: pattern.class + ' ' + pattern.type,
            grid_area: `${pattern.row_start} / ${pattern.col_start} / ${pattern.row_start + pattern.row_span} / ${pattern.col_start + pattern.col_span}`,
            is_grid_l: pattern.type === 'grid-l',
            has_arrow_in_text: [1, 4].includes(index)
        };
    }
    
    // For cards 8+, use the full pattern logic
    const setNumber = Math.floor(index / 6);
    const positionInSet = index % 6;
    
    if (positionInSet >= basePattern.length) {
        return null;
    }
    
    const pattern = basePattern[positionInSet];
    const rowOffset = setNumber * 4; // Back to original offset for full sets
    const adjustedRowStart = pattern.row_start + rowOffset;
    
    const dynamicClass = pattern.class + (setNumber > 0 ? '_' + setNumber : '');
    
    return {
        classes: dynamicClass + ' ' + pattern.type,
        grid_area: `${adjustedRowStart} / ${pattern.col_start} / ${adjustedRowStart + pattern.row_span} / ${pattern.col_start + pattern.col_span}`,
        is_grid_l: pattern.type === 'grid-l',
        has_arrow_in_text: [1, 4].includes(positionInSet)
    };
}


/**
 * Update grid template rows to accommodate new cards
 */
function updateGridRows(container, cardCount) {
    // Always use the simple calculation: 5 rows for first 6 cards, then 1 extra row per additional card
    let totalRows = 5; // Base 5 rows for first 6 cards
    
    if (cardCount > 6) {
        totalRows = 6; // Just 1 additional row for cards 7+
    }
    
    container.style.gridTemplateRows = `repeat(${totalRows}, 1fr)`;
}

/**
 * Reposition the load more button to follow the grid pattern
 */
function repositionLoadMoreButton(button, cardCount) {
    const buttonPosition = calculateCardPosition(cardCount);
    if (buttonPosition) {
        button.style.gridArea = buttonPosition.grid_area;
    } else {
        // Fallback positioning
        const finalRow = 1 + (Math.ceil(cardCount / 6) * 4);
        button.style.gridArea = `${finalRow} / 1 / ${finalRow + 1} / 3`;
    }
}