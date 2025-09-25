// Simple animation for cards
document.querySelectorAll('.card').forEach(card => {
  card.addEventListener('mouseenter', () => {
    card.style.transition = "all 0.5s ease";
  });
});
