/**
 * Library Book Management System - Client-side JavaScript
 * Handles form validation and delete confirmation dialogs.
 */

document.addEventListener("DOMContentLoaded", function () {
    // Attach validation to book forms (both Add and Edit forms if present)
    const bookForm = document.getElementById("bookForm");
    if (bookForm) {
        bookForm.addEventListener("submit", function (event) {
            if (!validateBookForm(this)) {
                event.preventDefault(); // Stop form submission if invalid
            }
        });
    }

    // Attach confirmation dialog to all delete forms
    const deleteForms = document.querySelectorAll(".delete-form");
    deleteForms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
            const confirmed = confirm("Are you sure you want to delete this book? This action cannot be undone.");
            if (!confirmed) {
                event.preventDefault(); // Stop submission if canceled
            }
        });
    });
});

/**
 * Validates book form fields on client-side before submission
 * @param {HTMLFormElement} form
 * @returns {boolean}
 */
function validateBookForm(form) {
    const titleInput = form.querySelector("#title");
    const authorInput = form.querySelector("#author");
    const quantityInput = form.querySelector("#quantity");
    const isbnInput = form.querySelector("#isbn");
    const categoryInput = form.querySelector("#category");

    // 1. Validate Book Title (Required)
    if (titleInput) {
        const titleValue = titleInput.value.trim();
        if (titleValue === "") {
            alert("Please enter the book title.");
            titleInput.focus();
            return false;
        }
        if (titleValue.length > 150) {
            alert("Book title cannot exceed 150 characters.");
            titleInput.focus();
            return false;
        }
    }

    // 2. Validate Author (Required)
    if (authorInput) {
        const authorValue = authorInput.value.trim();
        if (authorValue === "") {
            alert("Please enter the author name.");
            authorInput.focus();
            return false;
        }
        if (authorValue.length > 100) {
            alert("Author name cannot exceed 100 characters.");
            authorInput.focus();
            return false;
        }
    }

    // 3. Validate ISBN (Optional, Max 20 chars)
    if (isbnInput) {
        const isbnValue = isbnInput.value.trim();
        if (isbnValue.length > 20) {
            alert("ISBN cannot exceed 20 characters.");
            isbnInput.focus();
            return false;
        }
    }

    // 4. Validate Category (Optional, Max 100 chars)
    if (categoryInput) {
        const categoryValue = categoryInput.value.trim();
        if (categoryValue.length > 100) {
            alert("Category cannot exceed 100 characters.");
            categoryInput.focus();
            return false;
        }
    }

    // 5. Validate Quantity (Required, Integer >= 1)
    if (quantityInput) {
        const quantityValue = parseInt(quantityInput.value.trim(), 10);
        if (isNaN(quantityValue) || quantityValue < 1) {
            alert("Quantity must be a valid positive number (at least 1).");
            quantityInput.focus();
            return false;
        }
    }

    return true; // Form is valid
}
