document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("videoModal");
    const btn = document.getElementById("videoDemoBtn");
    const closeBtn = document.querySelector(".close-modal");

    btn.addEventListener("click", function (e) {
        e.preventDefault();
        modal.style.display = "block";
    });

    closeBtn.addEventListener("click", function () {
        modal.style.display = "none";
        const video = document.getElementById("demoVideo");
        video.pause(); // Stop video when modal closes
    });

    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
            const video = document.getElementById("demoVideo");
            video.pause();
        }
    });
});
