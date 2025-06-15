# CSS Directory

This directory contains all stylesheet files used in the wedding invitation website.

## Main CSS Files:

1. **main.css** - Primary stylesheet with custom styling for the wedding invitation

## CSS Structure and Components:

### Colors
The theme uses a carefully selected color palette:
- Gold/champagne: `#B08D57` - Primary accent color
- Elegant light: `#F8F5F2` - Background color
- Elegant dark: `#3A3A3A` - Text and footer color
- Rose: Various shades for highlights and accents

### Typography
The website uses Google Fonts:
- 'Great Vibes' - For decorative headings
- 'Cormorant Garamond' - For elegant body text
- 'Montserrat' - For clean, modern UI elements

### Key Components

1. **Invitation Card**
   ```css
   .invitation-card-inner {
     /* Styling for the envelope/invitation card */
   }
   ```

2. **Music Button**
   ```css
   .music-button {
     /* Styling for the music control button */
   }
   ```

3. **Buttons**
   ```css
   .btn-elegant {
     /* Styling for primary buttons */
   }
   ```

4. **Image Frames**
   ```css
   .img-frame {
     /* Styling for photo frames */
   }
   ```

5. **Decorative Elements**
   ```css
   .decorative-divider {
     /* Styling for section dividers */
   }
   ```

## Customization Instructions:

### Changing Colors
To modify the color scheme:
1. Locate the color variables at the top of the CSS file
2. Update the hex color codes
3. Test across the entire website to ensure consistent appearance

```css
:root {
  --gold-color: #B08D57; 
  --elegant-light: #F8F5F2;
  --elegant-dark: #3A3A3A;
  --rose-primary: #D8827E;
}
```

### Modifying Typography
To change fonts:
1. Update the Google Fonts import URL in the HTML head
2. Modify the font-family declarations in the CSS
3. Adjust font sizes and weights as needed

### Adding Custom Elements
When adding new CSS components:
1. Follow the existing naming conventions
2. Group related styles together
3. Add appropriate comments
4. Test responsiveness on multiple devices

## Best Practices:

1. **Mobile-First Approach**: The CSS uses a mobile-first approach with media queries for larger screens
2. **Browser Compatibility**: Styles are tested for Chrome, Firefox, Safari, and Edge
3. **Performance**: Minimize use of heavy animations and transitions for better performance
4. **Accessibility**: Ensure sufficient color contrast for readability
5. **Documentation**: Keep this README updated if you make significant changes

## Integration with Tailwind CSS:

This project uses Tailwind CSS for utility classes. Custom CSS in this directory extends or overrides Tailwind when necessary. When adding custom CSS, consider if Tailwind's utility classes could be used instead.
