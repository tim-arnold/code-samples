# Custom Templates

## single-team_member.php

Single view for a team member profile. Displays a hero image, subtitle/credentials (WYSIWYG), bio content, email, and social media links (Facebook, LinkedIn, Instagram, YouTube, X, Bluesky). Includes prev/next navigation between team members using `menu_order` since WordPress's built-in adjacent post functions don't support custom ordering.

## archive-team_member.php

Archive page that displays all team members in a card grid, grouped by ACF category. Sections render in a defined order (Investment Team, Advisors, VC-in-Residence), with any additional categories appended automatically. Members can belong to multiple categories. Handles both current ACF checkbox format and legacy single-select values.