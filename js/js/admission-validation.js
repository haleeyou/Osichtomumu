// admission-validation.js

document.getElementById("admissionForm").addEventListener("submit", function(e) {
    const maxFileSize = 2 * 1024 * 1024; // 2MB

    // Passport validation
    let passport = document.getElementById("passport").files[0];
    if (passport) {
        let ext = passport.name.split('.').pop().toLowerCase();
        if (!["jpg", "jpeg", "png"].includes(ext)) {
            alert("❌ Passport must be a JPG or PNG file.");
            e.preventDefault();
            return;
        }
        if (passport.size > maxFileSize) {
            alert("❌ Passport file size exceeds 2MB.");
            e.preventDefault();
            return;
        }
    }

    // O'Level validation
    let olevel = document.getElementById("olevel").files[0];
    if (olevel) {
        let ext = olevel.name.split('.').pop().toLowerCase();
        if (ext !== "pdf") {
            alert("❌ O'Level Certificate must be a PDF file.");
            e.preventDefault();
            return;
        }
        if (olevel.size > maxFileSize) {
            alert("❌ O'Level file size exceeds 2MB.");
            e.preventDefault();
            return;
        }
    }

    // Other certificate validation (optional)
    let othercert = document.getElementById("othercert").files[0];
    if (othercert) {
        let ext = othercert.name.split('.').pop().toLowerCase();
        if (ext !== "pdf") {
            alert("❌ Other Certificate must be a PDF file.");
            e.preventDefault();
            return;
        }
        if (othercert.size > maxFileSize) {
            alert("❌ Other Certificate file size exceeds 2MB.");
            e.preventDefault();
            return;
        }
    }
});
