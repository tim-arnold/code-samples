<?php
/**
 * Portfolio Companies Block Template
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param int $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'portfolio-companies-' . $block['id'];
if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'portfolio-companies-grid';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
if( !empty($block['align']) ) {
    $className .= ' align' . $block['align'];
}

// Get ACF fields
$portfolio_companies = get_field('portfolio_companies');

// Separate companies by fund
$fund1_companies = [];
$fund2_companies = [];
if($portfolio_companies) {
    foreach($portfolio_companies as $company) {
        if($company['fund_assignment'] === 'fund1') {
            $fund1_companies[] = $company;
        } else if($company['fund_assignment'] === 'fund2') {
            $fund2_companies[] = $company;
        }
    }
}

?>

<section class="portfolio-companies-section" id="<?php echo esc_attr($id); ?>-section">

    <!-- Fund 1 Companies -->
    <div 
        class="fund-content fund1-content <?php echo esc_attr($className); ?>" 
        data-fund="fund1"
        role="tabpanel"
        id="fund1-panel"
        aria-labelledby="fund1-tab-header"
        tabindex="0"
    >
        <?php if($fund1_companies): ?>
            <?php foreach($fund1_companies as $index => $company): ?>
                <?php if($company['company_link']): ?>
                    <a href="<?php echo esc_url($company['company_link']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Visit <?php echo esc_attr($company['company_name']); ?> website" class="portfolio-company-card portfolio-company-link" tabindex="0" aria-describedby="<?php echo $company['company_summary'] ? 'company-summary-' . $index . '-fund1' : ''; ?>">
                        <?php if($company['company_logo']): ?>
                            <div class="company-logo">
                                <?php echo wp_get_attachment_image($company['company_logo']['ID'], 'medium', false, ['alt' => $company['company_logo']['alt'] ?: $company['company_name'] . ' logo']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if($company['company_summary']): ?>
                            <div class="company-summary" id="company-summary-<?php echo $index; ?>-fund1" role="tooltip" aria-live="polite">
                                <p><?php echo esc_html($company['company_summary']); ?></p>
                            </div>
                        <?php endif; ?>
                    </a>
                <?php else: ?>
                    <div class="portfolio-company-card" tabindex="0" aria-describedby="<?php echo $company['company_summary'] ? 'company-summary-' . $index . '-fund1' : ''; ?>">
                        <?php if($company['company_logo']): ?>
                            <div class="company-logo">
                                <?php echo wp_get_attachment_image($company['company_logo']['ID'], 'medium', false, ['alt' => $company['company_logo']['alt'] ?: $company['company_name'] . ' logo']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if($company['company_summary']): ?>
                            <div class="company-summary" id="company-summary-<?php echo $index; ?>-fund1" role="tooltip" aria-live="polite">
                                <p><?php echo esc_html($company['company_summary']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-companies">No companies found for Fund 1.</p>
        <?php endif; ?>
    </div>

    <!-- Fund 2 Companies -->
    <div 
        class="fund-content fund2-content <?php echo esc_attr($className); ?>" 
        data-fund="fund2" 
        style="opacity: 0; display: none;"
        role="tabpanel"
        id="fund2-panel"
        aria-labelledby="fund2-tab-header"
        tabindex="0"
        aria-hidden="true"
    >
        <?php if($fund2_companies): ?>
            <?php foreach($fund2_companies as $index => $company): ?>
                <?php if($company['company_link']): ?>
                    <a href="<?php echo esc_url($company['company_link']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Visit <?php echo esc_attr($company['company_name']); ?> website" class="portfolio-company-card portfolio-company-link" tabindex="0" aria-describedby="<?php echo $company['company_summary'] ? 'company-summary-' . $index . '-fund2' : ''; ?>">
                        <?php if($company['company_logo']): ?>
                            <div class="company-logo">
                                <?php echo wp_get_attachment_image($company['company_logo']['ID'], 'medium', false, ['alt' => $company['company_logo']['alt'] ?: $company['company_name'] . ' logo']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if($company['company_summary']): ?>
                            <div class="company-summary" id="company-summary-<?php echo $index; ?>-fund2" role="tooltip" aria-live="polite">
                                <p><?php echo esc_html($company['company_summary']); ?></p>
                            </div>
                        <?php endif; ?>
                    </a>
                <?php else: ?>
                    <div class="portfolio-company-card" tabindex="0" aria-describedby="<?php echo $company['company_summary'] ? 'company-summary-' . $index . '-fund2' : ''; ?>">
                        <?php if($company['company_logo']): ?>
                            <div class="company-logo">
                                <?php echo wp_get_attachment_image($company['company_logo']['ID'], 'medium', false, ['alt' => $company['company_logo']['alt'] ?: $company['company_name'] . ' logo']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if($company['company_summary']): ?>
                            <div class="company-summary" id="company-summary-<?php echo $index; ?>-fund2" role="tooltip" aria-live="polite">
                                <p><?php echo esc_html($company['company_summary']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-companies">No companies found for Fund 2.</p>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const fundContents = document.querySelectorAll('.fund-content');
    
    function switchTab(activeButton) {
        const fund = activeButton.getAttribute('data-fund');
        const selectedContent = document.querySelector('.fund-content[data-fund="' + fund + '"]');
        
        // Don't do anything if clicking the active tab
        if (activeButton.classList.contains('active')) {
            return;
        }
        
        // Update button states and ARIA attributes
        tabButtons.forEach(btn => {
            btn.classList.remove('active');
            btn.setAttribute('aria-selected', 'false');
            btn.setAttribute('tabindex', '-1');
        });
        
        // Set active button
        activeButton.classList.add('active');
        activeButton.setAttribute('aria-selected', 'true');
        activeButton.setAttribute('tabindex', '0');
        
        // Find currently visible content
        const currentContent = document.querySelector('.fund-content[style*="opacity: 1"], .fund-content:not([style*="display: none"]):not([style*="opacity: 0"])');
        
        if (currentContent) {
            // Update ARIA for current content
            currentContent.setAttribute('aria-hidden', 'true');
            
            // Fade out current content
            currentContent.style.opacity = '0';
            
            setTimeout(function() {
                // Hide current content and show selected content
                currentContent.style.display = 'none';
                
                if (selectedContent) {
                    selectedContent.style.display = 'grid';
                    selectedContent.style.opacity = '0';
                    selectedContent.setAttribute('aria-hidden', 'false');
                    
                    // Fade in new content
                    setTimeout(function() {
                        selectedContent.style.opacity = '1';
                    }, 50);
                }
            }, 400); // Match CSS transition duration
        } else {
            // First load or no current content - just show selected
            if (selectedContent) {
                selectedContent.style.display = 'grid';
                selectedContent.style.opacity = '1';
                selectedContent.setAttribute('aria-hidden', 'false');
            }
        }
    }
    
    // Click handlers
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            switchTab(this);
        });
        
        // Keyboard navigation
        button.addEventListener('keydown', function(e) {
            let targetButton = null;
            
            switch(e.key) {
                case 'ArrowLeft':
                case 'ArrowUp':
                    e.preventDefault();
                    targetButton = this.previousElementSibling || tabButtons[tabButtons.length - 1];
                    break;
                case 'ArrowRight':
                case 'ArrowDown':
                    e.preventDefault();
                    targetButton = this.nextElementSibling || tabButtons[0];
                    break;
                case 'Home':
                    e.preventDefault();
                    targetButton = tabButtons[0];
                    break;
                case 'End':
                    e.preventDefault();
                    targetButton = tabButtons[tabButtons.length - 1];
                    break;
                case 'Enter':
                case ' ':
                    e.preventDefault();
                    switchTab(this);
                    return;
            }
            
            if (targetButton) {
                targetButton.focus();
                switchTab(targetButton);
            }
        });
    });
    
    // Show summary on keyboard focus only (not mouse clicks)
    let isMouseDown = false;
    
    document.addEventListener('mousedown', function() {
        isMouseDown = true;
    });
    
    document.addEventListener('keydown', function() {
        isMouseDown = false;
    });
    
    const companyCards = document.querySelectorAll('.portfolio-company-card');
    companyCards.forEach(card => {
        card.addEventListener('focus', function() {
            // Only show summary if focus came from keyboard, not mouse
            if (!isMouseDown) {
                const summary = this.querySelector('.company-summary');
                if (summary) {
                    summary.style.opacity = '1';
                }
            }
        });
        
        card.addEventListener('blur', function() {
            const summary = this.querySelector('.company-summary');
            if (summary) {
                summary.style.opacity = '0';
            }
        });
    });
});
</script>