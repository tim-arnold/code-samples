# W Fund Global CTAs

A WordPress plugin for managing global call-to-action sections with ACF fields.

## Features

- **ACF Integration**: Rich field interface for managing CTAs
- **Visual Management**: Color scheme selector, toggles, and intuitive UI
- **Flexible Configuration**: Up to 5 CTAs with priority ordering
- **Per-Page Control**: Toggle individual CTA color schemes on/off per page
- **Accessible Markup**: Semantic HTML with ARIA labels and screen reader support
- **Easy Customization**: Override styles with theme CSS or reposition with hooks

## Requirements

- WordPress 5.0+
- PHP 7.4+
- Advanced Custom Fields (ACF) plugin

## Installation

1. Upload plugin files to `/wp-content/plugins/wfund-global-ctas/`
2. Activate the plugin through WordPress admin
3. Navigate to **Global CTAs** in the admin sidebar to configure

## Configuration

### Adding CTAs

1. Go to **Global CTAs** in the WordPress admin sidebar
2. Click **Add CTA Section**
3. Configure:
   - **Enabled**: Toggle CTA on/off
   - **Heading**: Main CTA text
   - **Color Scheme**: Black or Red
   - **Display Order**: Priority (lower = first)
   - **Links**: Up to 3 action buttons per CTA

### Per-Page Visibility

Individual pages and posts have `show_black_cta` and `show_red_cta` toggle fields, allowing editors to hide specific CTA color schemes on a per-page basis. Archive pages always display all enabled CTAs.

## Customization

### CSS Override

The plugin outputs semantic class names you can target in your theme CSS:

```css
.cta-section {
    /* Wrapper for each CTA section */
}

.cta-section.cta-black {
    /* Black color scheme variant */
}

.cta-section.cta-red {
    /* Red color scheme variant */
}

.cta-heading {
    /* CTA heading text */
}

.cta-link {
    /* Individual action link */
}
```

### Template Override

The plugin renders via the `primer_before_footer` hook. To reposition:

```php
// Remove default placement
remove_action( 'primer_before_footer', array( WFund_Global_CTAs::get_instance(), 'render_cta_sections' ) );

// Add to custom location
add_action( 'your_custom_hook', array( WFund_Global_CTAs::get_instance(), 'render_cta_sections' ) );
```

## File Structure

```
wfund-global-ctas/
├── wfund-global-ctas.php      # Main plugin file (rendering, hooks)
├── includes/
│   └── class-cta-fields.php   # ACF field registration and options page
├── acf-json/                  # ACF local JSON sync
└── README.md
```