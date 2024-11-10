// script.js

// Select the typing text element
const typingText = document.getElementById("typing-text");

// Function to play the typing animation
function playAnimation() {
  typingText.style.animationPlayState = "running";
}

// Function to pause the typing animation
function pauseAnimation() {
  typingText.style.animationPlayState = "paused";
}

// Function to restart the typing animation
function restartAnimation() {
  typingText.style.animation = "none"; // Reset animation
  void typingText.offsetWidth; // Trigger reflow to restart animation
  typingText.style.animation = "typing 3s steps(20) infinite, blink 0.5s step-end infinite alternate";
}
