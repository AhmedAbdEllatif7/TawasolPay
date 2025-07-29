<div class="message-input bg-white rounded-3 shadow-sm p-3">
    <!-- منطقة معاينة الملفات -->
    <div id="filePreviewContainer" class="file-preview-container d-none mb-3">
        <div class="d-flex flex-wrap gap-2 p-2 bg-light rounded-2">
            <div id="filePreviewList" class="d-flex flex-wrap gap-2 w-100"></div>
        </div>
    </div>

    <form wire:submit.prevent='sendMessage' class="d-flex gap-2" enctype="multipart/form-data">
        <input type="hidden" wire:model="conversationId">

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
                       wire:model="file"
                       class="d-none"
                       multiple>
            </label>
        </div>

        <button type="submit" class="btn btn-primary rounded-pill px-3">
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>
</div>

@push('styles')
<style>
.file-preview-item {
    position: relative;
    display: inline-block;
    margin: 5px;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-width: 120px;
}

.file-preview-image {
    width: 100px;
    height: 100px;
    object-fit: cover;
    display: block;
}

.file-preview-icon {
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    color: #6c757d;
    font-size: 2rem;
}

.file-preview-name {
    padding: 5px;
    font-size: 0.75rem;
    text-align: center;
    background: #f8f9fa;
    border-top: 1px solid #ddd;
    word-break: break-word;
    max-height: 40px;
    overflow: hidden;
}

.file-preview-remove {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.75rem;
    transition: background-color 0.2s;
}

.file-preview-remove:hover {
    background: rgba(220, 53, 69, 1);
}

.file-preview-container.d-none {
    display: none !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const previewContainer = document.getElementById('filePreviewContainer');
    const previewList = document.getElementById('filePreviewList');
    let selectedFiles = [];

    // استمع لتغييرات إدخال الملف
    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        
        // إضافة الملفات الجديدة إلى القائمة
        files.forEach(file => {
            if (!selectedFiles.find(f => f.name === file.name && f.size === file.size)) {
                selectedFiles.push(file);
                createFilePreview(file);
            }
        });
        
        updateFileInput();
        togglePreviewContainer();
    });

    function createFilePreview(file) {
        const previewItem = document.createElement('div');
        previewItem.className = 'file-preview-item';
        previewItem.dataset.fileName = file.name;
        previewItem.dataset.fileSize = file.size;

        // زر الحذف
        const removeBtn = document.createElement('button');
        removeBtn.className = 'file-preview-remove';
        removeBtn.innerHTML = '×';
        removeBtn.type = 'button';
        removeBtn.addEventListener('click', function() {
            removeFile(file);
        });

        // معاينة الملف
        if (file.type.startsWith('image/')) {
            // معاينة الصور
            const img = document.createElement('img');
            img.className = 'file-preview-image';
            
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
            
            previewItem.appendChild(img);
        } else {
            // أيقونة للملفات الأخرى
            const icon = document.createElement('div');
            icon.className = 'file-preview-icon';
            
            // تحديد الأيقونة حسب نوع الملف
            let iconClass = 'fas fa-file';
            if (file.type.includes('pdf')) {
                iconClass = 'fas fa-file-pdf';
            } else if (file.type.includes('word') || file.type.includes('document')) {
                iconClass = 'fas fa-file-word';
            } else if (file.type.includes('excel') || file.type.includes('spreadsheet')) {
                iconClass = 'fas fa-file-excel';
            } else if (file.type.includes('powerpoint') || file.type.includes('presentation')) {
                iconClass = 'fas fa-file-powerpoint';
            } else if (file.type.includes('text')) {
                iconClass = 'fas fa-file-alt';
            } else if (file.type.includes('zip') || file.type.includes('rar')) {
                iconClass = 'fas fa-file-archive';
            }
            
            icon.innerHTML = `<i class="${iconClass}"></i>`;
            previewItem.appendChild(icon);
        }

        // اسم الملف
        const fileName = document.createElement('div');
        fileName.className = 'file-preview-name';
        fileName.textContent = file.name.length > 15 ? file.name.substring(0, 12) + '...' : file.name;
        fileName.title = file.name;
        previewItem.appendChild(fileName);

        previewItem.appendChild(removeBtn);
        previewList.appendChild(previewItem);
    }

    function removeFile(fileToRemove) {
        // إزالة الملف من القائمة
        selectedFiles = selectedFiles.filter(file => 
            !(file.name === fileToRemove.name && file.size === fileToRemove.size)
        );
        
        // إزالة عنصر المعاينة
        const previewItem = previewList.querySelector(
            `[data-file-name="${fileToRemove.name}"][data-file-size="${fileToRemove.size}"]`
        );
        if (previewItem) {
            previewItem.remove();
        }
        
        updateFileInput();
        togglePreviewContainer();
    }

    function updateFileInput() {
        // إنشاء DataTransfer جديد لتحديث input
        const dt = new DataTransfer();
        selectedFiles.forEach(file => {
            dt.items.add(file);
        });
        fileInput.files = dt.files;
        
        // إطلاق حدث التغيير لـ Livewire
        fileInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function togglePreviewContainer() {
        if (selectedFiles.length > 0) {
            previewContainer.classList.remove('d-none');
        } else {
            previewContainer.classList.add('d-none');
        }
    }

    // تنظيف المعاينة عند إرسال النموذج
    document.querySelector('form').addEventListener('submit', function() {
        // يمكن إضافة منطق إضافي هنا إذا لزم الأمر
        // مثل إخفاء المعاينة بعد الإرسال الناجح
    });
});
</script>
@endpush

