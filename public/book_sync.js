// book_sync.js
// Handles ISBN sync button and checkbox logic for both add and edit forms

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action*="books"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const coverUrlInput = document.getElementById('cover_image_url');
            if (coverUrlInput) {
                console.log('[DEBUG] cover_image_url at submit:', coverUrlInput.value);
            }
        });
    }
    const syncBtn = document.getElementById('sync-isbn-btn');
    const syncCheckbox = document.getElementById('sync_enabled');
    const isbnInput = document.getElementById('isbn');
    const titleInput = document.getElementById('title');
    const authorInput = document.getElementById('author');
    const descInput = document.getElementById('description');
    const coverInput = document.getElementById('cover_image');
    const coverUrlInput = document.getElementById('cover_image_url');
    const coverPreview = document.getElementById('cover_image_preview');
    let coverImageUrl = null;

    if (coverInput && coverPreview) {
        coverInput.addEventListener('change', function() {
            if (coverInput.files && coverInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    coverPreview.innerHTML = `<img src='${e.target.result}' alt='Cover Preview' style='max-width:120px;display:block;margin-bottom:8px;'>`;
                };
                reader.readAsDataURL(coverInput.files[0]);
            } else if (coverUrlInput && coverUrlInput.value) {
                // If file is cleared, show URL preview if present
                coverPreview.innerHTML = `<img src='${coverUrlInput.value}' alt='Cover Preview' style='max-width:120px;display:block;margin-bottom:8px;'>`;
            } else {
                coverPreview.innerHTML = '';
            }
        });
    }

    // Live preview for pasted cover URL
    if (coverUrlInput && coverPreview) {
        coverUrlInput.addEventListener('input', function() {
    console.log('[DEBUG] cover_image_url input changed:', coverUrlInput.value);
    if (coverUrlInput.value) {
        coverPreview.innerHTML = `<img src='${coverUrlInput.value}' alt='Cover Preview' style='max-width:120px;display:block;margin-bottom:8px;'>`;
    } else {
        coverPreview.innerHTML = '';
    }
});
    }

    if (syncBtn && isbnInput) {
        syncBtn.addEventListener('click', function () {
            if (!syncCheckbox.checked) {
                alert('Enable synchronization to fetch book data.');
                return;
            }
            const isbn = isbnInput.value.trim();
            if (!isbn) {
                alert('Please enter an ISBN number.');
                return;
            }
            syncBtn.disabled = true;
            syncBtn.innerText = 'Syncing...';
            fetch(`https://openlibrary.org/api/books?bibkeys=ISBN:${isbn}&format=json&jscmd=data`)
                .then(resp => resp.json())
                .then(data => {
                    const bookData = data[`ISBN:${isbn}`];
                    if (!bookData) {
                        alert('No data found for this ISBN.');
                        return;
                    }
                    if (titleInput && bookData.title) titleInput.value = bookData.title;
                    if (authorInput && bookData.authors && bookData.authors.length > 0) authorInput.value = bookData.authors.map(a => a.name).join(', ');
                    if (descInput && bookData.notes) descInput.value = typeof bookData.notes === 'string' ? bookData.notes : (bookData.notes.value || '');

                    // Hybrid cover approach: try main API, then Covers API
                    let coverUrl = bookData.cover && bookData.cover.large ? bookData.cover.large : `https://covers.openlibrary.org/b/isbn/${isbn}-L.jpg`;
                    const coverUrlInput = document.getElementById('cover_image_url');
                    const coverPreview = document.getElementById('cover_image_preview');
                    // Check if the cover actually exists (Covers API returns a placeholder if not found)
                    const img = new Image();
                    img.onload = function() {
                        // If width/height is small, it's likely a placeholder (1x1 or 42x65)
                        if ((img.width <= 1 && img.height <= 1) || (img.width === 42 && img.height === 65)) {
                            alert('No cover found for this ISBN. Please upload or paste a cover image link manually.');
                            if (coverUrlInput) {
                                coverUrlInput.value = '';
                                coverUrlInput.style.display = '';
                            }
                            if (coverPreview) {
                                coverPreview.innerHTML = '';
                            }
                        } else {
                            if (coverUrlInput) {
                                if (coverUrlInput) {
                                    coverUrlInput.value = coverUrl;
console.log('[DEBUG] cover_image_url set by sync:', coverUrlInput.value);
                                    coverUrlInput.style.display = '';
                                }
                            }
                            if (coverPreview) {
                                coverPreview.innerHTML = `<img src='${coverUrl}' alt='Cover Preview' style='max-width:120px;display:block;margin-bottom:8px;'>`;
                            }
                        }
                    };
                    img.onerror = function() {
                        alert('No cover found for this ISBN. Please upload or paste a cover image link manually.');
                        if (coverUrlInput) {
                            coverUrlInput.value = '';
                            coverUrlInput.style.display = '';
                        }
                        if (coverPreview) {
                            coverPreview.innerHTML = '';
                        }
                    };
                    img.src = coverUrl;
                })
                .catch(() => {
                    alert('Failed to fetch book data.');
                })
                .finally(() => {
                    syncBtn.disabled = false;
                    syncBtn.innerText = 'Sync';
                });
        });
    }

    if (syncCheckbox) {
        syncCheckbox.addEventListener('change', function () {
            if (!this.checked) {
                syncBtn.classList.add('disabled');
            } else {
                syncBtn.classList.remove('disabled');
            }
        });
    }
});
