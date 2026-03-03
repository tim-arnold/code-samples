# Custom ACF Blocks

Two custom Gutenberg blocks built with Advanced Custom Fields, registered via `block.json`.

## Portfolio Companies (`portfolio-companies/`)

A tabbed logo grid that displays portfolio companies organized by fund. Each company card shows a logo with an optional link and hover summary. Tabs switch between Fund 1 and Fund 2 with a fade transition.

- Keyboard-navigable tabs (arrow keys, Home/End)
- ARIA roles and attributes for screen readers
- Hover summaries shown on keyboard focus, hidden on mouse interaction

## Portfolio Grid (`portfolio-grid/`)

An interlocking card grid that displays portfolio companies in a staggered layout with alternating horizontal and vertical cards. Cards show a product image, logo, location, CEO, and description, with a hover state that reveals an alternate image.

- Cards are positioned on a CSS Grid using a repeating 6-card pattern
- AJAX "View more" loads additional cards without a page reload
- Grid positioning logic is shared between PHP (initial render) and JavaScript (dynamic cards)
- XSS prevention via `escapeHtml()` for all dynamically inserted text content