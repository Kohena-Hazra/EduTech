// NAVBAR ACTIVE HIGHLIGHT
document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll('.admin-navbar ul li a');
    links.forEach(link => {
        link.addEventListener('click', () => {
            links.forEach(l => l.classList.remove('active-link'));
            link.classList.add('active-link');
        });
    });
});

// ADD LEARNING INPUT
function addLearn() {
    document.getElementById("learn-wrapper").insertAdjacentHTML(
        "beforeend",
        `<input type="text" name="what_learn[]" placeholder="Add learning point..." class="small-input">`
    );
}

// ADD REQUIREMENT INPUT
function addReq() {
    document.getElementById("req-wrapper").insertAdjacentHTML(
        "beforeend",
        `<input type="text" name="requirements[]" placeholder="Add requirement..." class="small-input">`
    );
}

// ADD MODULE BLOCK
function addModule() {
    let html = `
    <div class="curriculum-item">
        <input type="text" name="module_title[]" placeholder="Module Title" class="small-input" required>
        <input type="text" name="module_duration[]" placeholder="Duration" class="small-input" required>
        <textarea name="module_topics[]" placeholder="Topics covered" class="small-textarea" required></textarea>
        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">Remove</button>
    </div>`;

    document.getElementById("curriculum-wrapper").insertAdjacentHTML("beforeend", html);
}
