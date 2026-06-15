@extends($__layout ?? 'admin.layout')
@section('title', 'إضافة محتوى تعريفي | Tripoli Zoo')
@section('page_title', 'إضافة محتوى تعريفي جديد')

@section('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1e3a1e 0%, #2d5a27 100%);
        --accent-gradient:  linear-gradient(135deg, #E8651A 0%, #f97316 100%);
        --card-shadow:      0 10px 30px -10px rgba(0,0,0,0.07);
    }

    /* ── Back link ── */
    .page-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 1.6rem;
        transition: color .2s;
    }
    .page-back:hover { color: var(--orange); }

    /* ── Section card ── */
    .section-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        margin-bottom: 1.4rem;
        transition: box-shadow .3s;
    }
    .section-card:hover {
        box-shadow: 0 15px 35px -10px rgba(45,90,39,.08);
    }

    .section-head {
        padding: 1.2rem 1.6rem;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; gap: 12px;
        background: linear-gradient(to left, rgba(45,90,39,.02), transparent);
    }

    .section-num {
        width: 30px; height: 30px;
        background: var(--primary-gradient);
        color: white; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 900; font-size: .85rem; flex-shrink: 0;
    }

    .section-head-text h3 {
        font-size: 1rem; font-weight: 900; color: #1e3a1e; margin: 0 0 2px;
    }
    .section-head-text p {
        font-size: .78rem; color: var(--text-muted); font-weight: 600; margin: 0;
    }

    .section-body { padding: 1.6rem; }

    /* ── Animal selector ── */
    .animal-select-wrapper { position: relative; }

    .animal-select-wrapper select {
        width: 100%;
        padding: 14px 16px 14px 42px;
        border: 2px solid var(--border);
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-size: .95rem; font-weight: 700; color: var(--text-main);
        background: white; outline: none; cursor: pointer; appearance: none;
        transition: all .2s;
    }
    .animal-select-wrapper select:focus {
        border-color: #2d5a27;
        box-shadow: 0 0 0 4px rgba(45,90,39,.08);
    }

    .select-icon {
        position: absolute; left: 14px; top: 50%;
        transform: translateY(-50%); pointer-events: none; color: var(--text-muted);
    }
    .select-chevron {
        position: absolute; right: 14px; top: 50%;
        transform: translateY(-50%); pointer-events: none; color: var(--text-muted);
    }

    .animal-preview {
        display: none;
        margin-top: 1rem; padding: 1rem;
        background: linear-gradient(135deg, rgba(45,90,39,.04), rgba(45,90,39,.02));
        border: 1.5px solid rgba(45,90,39,.15);
        border-radius: 12px;
        align-items: center; gap: 12px;
    }
    .animal-preview.show { display: flex; }

    .animal-preview-info h4 {
        font-size: .95rem; font-weight: 800; color: #1e3a1e; margin: 0;
    }
    .preview-badge {
        margin-right: auto;
        padding: 4px 10px; background: #DCFCE7; color: #166534;
        border-radius: 50px; font-size: .72rem; font-weight: 800;
    }

    /* ── Description ── */
    .desc-textarea {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid var(--border);
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-size: .92rem; line-height: 1.7; color: var(--text-main);
        resize: vertical; min-height: 160px; outline: none;
        transition: all .2s; background: white;
    }
    .desc-textarea:focus {
        border-color: var(--orange);
        box-shadow: 0 0 0 4px rgba(232,101,26,.06);
    }

    .char-count {
        display: flex; justify-content: flex-end;
        margin-top: 6px; font-size: .78rem; color: var(--text-muted); font-weight: 700;
    }
    .char-count span { color: var(--orange); }

    /* ── Bottom row ── */
    .bottom-row {
        display: flex;
        flex-direction: column;
        gap: 1.4rem;
    }

    .bottom-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }

    .bottom-card-head {
        padding: 1rem 1.4rem;
        border-bottom: 1px solid var(--border);
        background: #FAFBFC;
        display: flex; align-items: center; gap: 8px;
    }
    .bottom-card-head h3 {
        font-size: .92rem; font-weight: 800; color: var(--text-main); margin: 0;
    }

    .bottom-card-body { padding: 1.2rem 1.4rem; }

    /* ── Image upload ── */
    .upload-zone {
        border: 2.5px dashed var(--border);
        border-radius: 14px; padding: 1.6rem 1rem;
        text-align: center; cursor: pointer;
        transition: all .3s; background: #FAFBFC; position: relative;
    }
    .upload-zone:hover { border-color: var(--orange); background: #FFFBF8; }
    .upload-zone.dragover { border-color: #2d5a27; background: rgba(45,90,39,.04); }
    .upload-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

    .upload-zone-icon {
        width: 46px; height: 46px;
        background: rgba(232,101,26,.08); color: var(--orange);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 10px;
    }
    .upload-zone h4 { font-size: .85rem; font-weight: 800; color: var(--text-main); margin: 0 0 4px; }
    .upload-zone p  { font-size: .73rem; color: var(--text-muted); font-weight: 600; margin: 0; }

    .img-preview-wrap { display: none; border-radius: 12px; overflow: hidden; border: 1.5px solid var(--border); position: relative; }
    .img-preview-wrap.show { display: block; }
    .img-preview-wrap img { width: 100%; height: 140px; object-fit: cover; display: block; }
    .img-remove-btn {
        position: absolute; top: 8px; left: 8px;
        width: 28px; height: 28px;
        background: rgba(239,68,68,.9); color: white;
        border: none; border-radius: 6px;
        font-size: 1rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all .2s;
    }
    .img-remove-btn:hover { background: #DC2626; }



    /* ── Actions ── */
    .btn-save {
        width: 100%; padding: 14px;
        background: var(--primary-gradient);
        color: white; border: none; border-radius: 12px;
        font-family: 'Cairo', sans-serif; font-weight: 800; font-size: 1rem;
        cursor: pointer; transition: all .3s;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        box-shadow: 0 6px 18px rgba(30,58,30,.25);
        margin-bottom: 10px;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(30,58,30,.35); }

    .btn-discard {
        width: 100%; padding: 11px;
        background: var(--bg-color); color: var(--text-muted);
        border: 1.5px solid var(--border); border-radius: 12px;
        font-family: 'Cairo', sans-serif; font-weight: 700; font-size: .9rem;
        cursor: pointer; transition: all .2s;
        text-align: center; text-decoration: none; display: block;
    }
    .btn-discard:hover { background: #E2E8F0; color: var(--text-main); }

    /* ── Toast ── */
    .toast {
        position: fixed; bottom: 2rem; left: 50%;
        transform: translateX(-50%) translateY(80px);
        background: #1E293B; color: white;
        padding: 12px 24px; border-radius: 50px;
        font-weight: 700; font-size: .9rem;
        z-index: 9999;
        transition: transform .4s cubic-bezier(.4,0,.2,1);
        white-space: nowrap;
    }
    .toast.show { transform: translateX(-50%) translateY(0); }

    @media (max-width: 900px) {
        .bottom-row { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')

<a href="{{ route('admin.animals.index') }}" class="page-back">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    العودة إلى قائمة المحتوى التعريفي
</a>

<form method="POST" action="{{ route('admin.animals.store') }}" enctype="multipart/form-data" id="createForm">
@csrf

{{-- ── Step 1: Animal ── --}}
<div class="section-card">
    <div class="section-head">
        <div class="section-num">1</div>
        <div class="section-head-text">
            <h3>اختر الحيوان المراد إضافة محتواه التعريفي</h3>
            <p>القائمة تعرض فقط الحيوانات المسجّلة في الحديقة التي لا يوجد لها محتوى تعريفي بعد</p>
        </div>
    </div>
    <div class="section-body">
        <div class="animal-select-wrapper">
            <svg class="select-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            <select id="animalSelect" name="animal_id" onchange="onAnimalChange()" required>
                <option value="">— اختر حيواناً من القائمة —</option>
                @forelse($animals as $animal)
                <option value="{{ $animal->id }}" @selected(old('animal_id') == $animal->id)>{{ $animal->displayLabel() }} ({{ $animal->code }})</option>
                @empty
                <option value="" disabled>لا توجد حيوانات بدون محتوى تعريفي</option>
                @endforelse
            </select>
            <svg class="select-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </div>

        <div class="animal-preview" id="animalPreview">
            <div class="animal-preview-info">
                <h4 id="previewName">—</h4>
            </div>
            <span class="preview-badge">✓ تم الاختيار</span>
        </div>
    </div>
</div>

{{-- ── Step 2: Description ── --}}
<div class="section-card">
    <div class="section-head">
        <div class="section-num">2</div>
        <div class="section-head-text">
            <h3>اكتب الوصف التعريفي للحيوان</h3>
            <p>هذا الوصف سيظهر للزوار عند مسح رمز QR الخاص بهذا الحيوان</p>
        </div>
    </div>
    <div class="section-body">
        <textarea
            id="desc"
            name="description"
            class="desc-textarea"
            placeholder="مثال: الأسد الإفريقي من أكبر القطط البرية في العالم..."
            oninput="onDescInput()"
            rows="6"
            required
        >{{ old('description') }}</textarea>
        <div class="char-count">الأحرف: <span id="charCount">0</span> / 600</div>

    </div>
</div>

{{-- ── Bottom Row (Image + Completion + Actions) ── --}}
<div class="bottom-row">

    {{-- Image Upload --}}
    <div class="bottom-card">
        <div class="bottom-card-head">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <h3>صورة الحيوان <span style="color:#DC2626;font-weight:800;font-size:.75rem;">*</span></h3>
        </div>
        <div class="bottom-card-body">
            <div class="upload-zone" id="uploadZone">
                <input type="file" id="imgInput" name="image" accept="image/*" onchange="previewImg(this)" required>
                <div class="upload-zone-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                </div>
                <h4>اسحب وأفلت الصورة هنا</h4>
                <p>أو انقر للاختيار من جهازك</p>
                <p style="margin-top:4px;">PNG أو JPG حتى 5 ميجابايت</p>
            </div>
            <div class="img-preview-wrap" id="imgPreviewWrap">
                <img id="imgPreview" src="" alt="معاينة الصورة">
                <button class="img-remove-btn" onclick="removeImg()" title="حذف الصورة">×</button>
            </div>
        </div>
    </div>


    {{-- Actions --}}
    <div class="bottom-card">
        <div class="bottom-card-head">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            <h3>حفظ ونشر المحتوى</h3>
        </div>
        <div class="bottom-card-body">
            <button type="submit" class="btn-save" id="btnSave">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                حفظ المحتوى التعريفي
            </button>
            <a href="{{ route('admin.animals.index') }}" class="btn-discard">إلغاء والتراجع</a>
        </div>
    </div>

</div>

</form>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script>
    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    function onAnimalChange() {
        const sel = document.getElementById('animalSelect');
        const preview = document.getElementById('animalPreview');
        if (sel.value) {
            document.getElementById('previewName').textContent = sel.options[sel.selectedIndex].text;
            preview.classList.add('show');
        } else {
            preview.classList.remove('show');
        }
    }

    function onDescInput() {
        document.getElementById('charCount').textContent = document.getElementById('desc').value.length;
    }

    function previewImg(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('imgPreview').src = e.target.result;
                document.getElementById('imgPreviewWrap').classList.add('show');
                document.getElementById('uploadZone').style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImg() {
        document.getElementById('imgPreview').src = '';
        document.getElementById('imgPreviewWrap').classList.remove('show');
        document.getElementById('uploadZone').style.display = 'block';
        document.getElementById('imgInput').value = '';
    }

    const uploadZone = document.getElementById('uploadZone');
    uploadZone.addEventListener('dragover',  e => { e.preventDefault(); uploadZone.classList.add('dragover'); });
    uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));
    uploadZone.addEventListener('drop', e => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            const dt  = new DataTransfer();
            dt.items.add(file);
            const inp = document.getElementById('imgInput');
            inp.files = dt.files;
            previewImg(inp);
        }
    });

    onDescInput();
    onAnimalChange();
</script>
@endsection
