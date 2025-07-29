<div class="message-input bg-white rounded-3 shadow-sm p-3">
    <form wire:submit.prevent='sendMessage' class="d-flex flex-column gap-2" enctype="multipart/form-data" id="messageForm">
        <input type="hidden" wire:model.defer="conversationId">

        <div class="flex-grow-1 d-flex align-items-center gap-2">
            <input type="text"
                   wire:model.defer="message"
                   class="form-control rounded-pill"
                   placeholder="اكتب رسالتك هنا..."
                   required>

            <label for="fileInput" class="btn btn-light rounded-circle" title="إرفاق ملف">
                <i class="fas fa-paperclip"></i>
                <input type="file" 
                       id="fileInput"
                       class="d-none"
                       multiple
                       accept="image/*,application/pdf"
                       wire:model.defer="files">
            </label>
        </div>

        <div id="filePreview" class="d-flex flex-wrap gap-2 mt-2" style="display: none;"></div>

        <button type="submit" class="btn btn-primary rounded-pill px-3 align-self-end">
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const form = document.getElementById('messageForm');
    let cachedFiles = [];

    function previewFiles(files) {
        filePreview.innerHTML = ''; 
        filePreview.style.display = files.length ? 'flex' : 'none';

        files.forEach((file, index) => {
            const fileContainer = document.createElement('div');
            fileContainer.className = 'position-relative';
            fileContainer.style.width = '100px';
            fileContainer.style.height = '100px';

            const removeButton = document.createElement('button');
            removeButton.innerHTML = '&times;';
            removeButton.className = 'btn btn-danger btn-sm rounded-circle position-absolute';
            removeButton.style.top = '5px';
            removeButton.style.right = '5px';
            removeButton.style.zIndex = '10';
            removeButton.onclick = () => removeFile(index);

            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '8px';
                fileContainer.appendChild(img);
            } else if (file.type === 'application/pdf') {
                const pdfIcon = document.createElement('div');
                pdfIcon.innerHTML = '<i class="fas fa-file-pdf fa-3x"></i><br>' + file.name;
                pdfIcon.style.textAlign = 'center';
                pdfIcon.style.padding = '10px';
                pdfIcon.style.background = '#f8f9fa';
                pdfIcon.style.borderRadius = '8px';
                fileContainer.appendChild(pdfIcon);
            }

            fileContainer.appendChild(removeButton);
            filePreview.appendChild(fileContainer);
        });
    }

    fileInput.addEventListener('change', function () {
        cachedFiles = Array.from(this.files);
        previewFiles(cachedFiles);
    });

    function removeFile(index) {
        cachedFiles.splice(index, 1); // حذف من الكاش فقط
        previewFiles(cachedFiles);    // إعادة عرض المتبقي

        // ما بنحدث input.files هنا ولا بنرسل شيء لـ Livewire
    }

    form.addEventListener('submit', function () {
        const dt = new DataTransfer();
        cachedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files; // تحديث input قبل الإرسال فقط
    });
});
</script>
