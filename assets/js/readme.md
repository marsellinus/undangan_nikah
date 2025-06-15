# JavaScript Directory

This directory contains all JavaScript files used in the wedding invitation website. Note that some JavaScript code may be embedded directly in the index.php file.

## JavaScript Functionality:

The wedding invitation website includes the following JavaScript functionality:

1. **Audio Controls**
   - Play/pause background music
   - Toggle icon and visual state

2. **Countdown Timer**
   - Calculate and display days, hours, minutes, and seconds until the wedding
   - Update in real-time
   - Show special message when wedding day arrives

3. **RSVP Form Handling**
   - Show/hide guest count field based on attendance selection
   - Form validation
   - Submission handling

4. **Copy to Clipboard**
   - Copy bank account numbers with a single click
   - Show confirmation messages

5. **Smooth Scrolling**
   - Smooth navigation between sections when clicking anchor links

6. **Message Carousel**
   - Navigate through guest messages
   - Auto-rotation functionality

7. **Animations**
   - AOS (Animate On Scroll) integration
   - Custom animation triggers

8. **Lightbox**
   - Image gallery lightbox functionality
   - Navigation between gallery images

## Extending JavaScript:

When adding new JavaScript functionality:

1. **Organization**:
   - Use modular approach with clearly named functions
   - Group related functionality together
   - Add appropriate comments

2. **Best Practices**:
   - Use event delegation where appropriate
   - Check if elements exist before accessing them
   - Handle all potential errors gracefully
   - Use const and let instead of var
   - Consider browser compatibility

3. **Performance**:
   - Avoid DOM manipulation in loops
   - Minimize reflows and repaints
   - Use debounce/throttle for scroll/resize events
   - Cache DOM elements when accessed repeatedly

## Example Code Structure:

```javascript
// Feature module pattern
const featureName = (function() {
  // Private variables
  const elements = {
    button: document.querySelector('.selector'),
    container: document.querySelector('.container')
  };
  
  // Private methods
  function privateMethod() {
    // Implementation details
  }
  
  // Event handlers
  function handleEvent(e) {
    // Handle the event
  }
  
  // Initialize
  function init() {
    if (!elements.button || !elements.container) return;
    
    elements.button.addEventListener('click', handleEvent);
    // Other initialization code
  }
  
  // Public API
  return {
    init: init
  };
})();

// Initialize on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
  featureName.init();
});
```

## Third-Party Libraries:

The wedding invitation uses the following JavaScript libraries:

1. **AOS** (Animate On Scroll) - For scroll-triggered animations
2. **Lightbox2** - For image gallery functionality
3. **jQuery** (Optional) - For DOM manipulation and some third-party plugins

When adding new libraries, consider:
- File size and performance impact
- Browser compatibility
- License compatibility
- Integration with existing code
