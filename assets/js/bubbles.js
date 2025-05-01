document.addEventListener('DOMContentLoaded', function() {
    const bubblesContainer = document.querySelector('.bubbles-container');
    const bubbleCount = 15; // Increase number of bubbles
    
    for (let i = 0; i < bubbleCount; i++) {
        const bubble = document.createElement('div');
        bubble.classList.add('bubble');
        
        // Randomize bubble properties
        const size = Math.random() * 30 + 10;
        const left = Math.random() * 100;
        const delay = Math.random() * 5;
        const duration = Math.random() * 10 + 10;
        
        bubble.style.width = `${size}px`;
        bubble.style.height = `${size}px`;
        bubble.style.left = `${left}%`;
        bubble.style.animationDelay = `${delay}s`;
        bubble.style.animationDuration = `${duration}s`;
        bubble.style.opacity = Math.random() * 0.5 + 0.2;
        
        bubblesContainer.appendChild(bubble);
    }
});
