
// Script to generate PWA icons from SVG logo
// This is a simple example - you would typically use a proper image processing library

const fs = require('fs');
const { createCanvas } = require('canvas');

const sizes = [72, 96, 128, 144, 152, 192, 384, 512];
const colors = {
    primary: '#2563eb',
    white: '#ffffff'
};

// Simple icon generator function
function generateIcon(size) {
    const canvas = createCanvas(size, size);
    const ctx = canvas.getContext('2d');
    
    // Background
    ctx.fillStyle = colors.primary;
    ctx.fillRect(0, 0, size, size);
    
    // Icon (simple chat bubble)
    ctx.fillStyle = colors.white;
    ctx.beginPath();
    
    const centerX = size / 2;
    const centerY = size / 2;
    const radius = size * 0.3;
    
    // Main circle
    ctx.arc(centerX, centerY - size * 0.05, radius, 0, 2 * Math.PI);
    ctx.fill();
    
    // Speech bubble tail
    ctx.beginPath();
    ctx.moveTo(centerX - radius * 0.3, centerY + radius * 0.4);
    ctx.lineTo(centerX - radius * 0.6, centerY + radius * 0.8);
    ctx.lineTo(centerX - radius * 0.1, centerY + radius * 0.6);
    ctx.closePath();
    ctx.fill();
    
    // Save as PNG
    const buffer = canvas.toBuffer('image/png');
    fs.writeFileSync(`icon-${size}x${size}.png`, buffer);
    console.log(`Generated icon-${size}x${size}.png`);
}

// Generate all icon sizes
sizes.forEach(generateIcon);

console.log('All PWA icons generated successfully!');
