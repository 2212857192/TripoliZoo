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

    /* ── Steps bar ── */
    .steps-bar {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
        background: white;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1rem 1.5rem;
        box-shadow: var(--card-shadow);
    }

    .step-item {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
    }

    .step-circle {
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 900; font-size: .9rem;
        flex-shrink: 0;
        border: 2px solid var(--border);
        color: var(--text-muted);
        background: var(--bg-color);
        transition: all .3s;
    }

    .step-item.active .step-circle {
        background: var(--primary-gradient);
        border-color: transparent; color: white;
        box-shadow: 0 4px 12px rgba(45,90,39,.3);
    }
    .step-item.done .step-circle {
        background: #DCFCE7; border-color: #86EFAC; color: #166534;
    }

    .step-label { font-size: .82rem; font-weight: 700; color: var(--text-muted); }
    .step-item.active .step-label { color: #1e3a1e; font-weight: 800; }
    .step-item.done .step-label   { color: #166534; }

    .step-divider {
        flex: 0 0 40px; height: 2px;
        background: var(--border); margin: 0 8px; border-radius: 2px;
    }

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

    .animal-preview-emoji {
        width: 50px; height: 50px;
        background: white; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem;
        box-shadow: 0 4px 12px rgba(0,0,0,.06);
        flex-shrink: 0;
    }
    .animal-preview-info h4 {
        font-size: .95rem; font-weight: 800; color: #1e3a1e; margin: 0 0 2px;
    }
    .animal-preview-info p {
        font-size: .78rem; color: var(--text-muted); font-weight: 600; margin: 0; font-style: italic;
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

    .writing-tips {
        background: #FFFBF5;
        border: 1px solid #FED7AA;
        border-radius: 10px; padding: 12px 14px; margin-top: 1rem;
    }
    .writing-tips p {
        font-size: .78rem; color: #92400E; font-weight: 700; margin: 0 0 6px;
        display: flex; align-items: center; gap: 6px;
    }
    .writing-tips ul {
        padding-right: 16px; margin: 0;
        display: flex; flex-direction: column; gap: 4px;
    }
    .writing-tips li { font-size: .76rem; color: #92400E; font-weight: 600; }

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

<a href="/admin/animals" class="page-back">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    العودة إلى قائمة المحتوى التعريفي
</a>

{{-- ── Steps bar ── --}}
<div class="steps-bar">
    <div class="step-item active" id="step1-item">
        <div class="step-circle" id="step1-circle">1</div>
        <span class="step-label">اختيار الحيوان</span>
    </div>
    <div class="step-divider"></div>
    <div class="step-item" id="step2-item">
        <div class="step-circle" id="step2-circle">2</div>
        <span class="step-label">كتابة الوصف</span>
    </div>
    <div class="step-divider"></div>
    <div class="step-item" id="step3-item">
        <div class="step-circle" id="step3-circle">3</div>
        <span class="step-label">إضافة الصورة</span>
    </div>
    <div class="step-divider"></div>
    <div class="step-item" id="step4-item">
        <div class="step-circle" id="step4-circle">4</div>
        <span class="step-label">الحفظ والنشر</span>
    </div>
</div>

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
            <select id="animalSelect" onchange="onAnimalChange()">
                <option value="">— اختر حيواناً من القائمة —</option>
                <option value="1" data-emoji="🦁" data-sci="Panthera leo">الأسد الإفريقي</option>
                <option value="2" data-emoji="🐘" data-sci="Elephas maximus">الفيل الآسيوي</option>
                <option value="3" data-emoji="🐯" data-sci="Panthera tigris">النمر البنغالي</option>
                <option value="4" data-emoji="🦒" data-sci="Giraffa camelopardalis">الزرافة</option>
                <option value="5" data-emoji="🐊" data-sci="Crocodylus niloticus">التمساح النيلي</option>
            </select>
            <svg class="select-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </div>

        <div class="animal-preview" id="animalPreview">
            <div class="animal-preview-emoji" id="previewEmoji">🦁</div>
            <div class="animal-preview-info">
                <h4 id="previewName">—</h4>
                <p id="previewSci">—</p>
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
            class="desc-textarea"
            placeholder="مثال: الأسد الإفريقي من أكبر القطط البرية في العالم، يعيش في مجموعات تُعرف بـ (الفخر)، ويتميز الذكر بعُرفه الكثيف..."
            oninput="onDescInput()"
            rows="6"
        ></textarea>
        <div class="char-count">الأحرف: <span id="charCount">0</span> / 600</div>

        <div class="writing-tips">
            <p>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                نصائح لكتابة وصف جذاب:
            </p>
            <ul>
                <li>ابدأ بمعلومة مثيرة أو حقيقة غريبة عن الحيوان</li>
                <li>اذكر موطنه الأصلي، طبيعته، وسلوكه الاجتماعي</li>
                <li>يُفضّل أن يكون الوصف بين 100 و300 حرف لسهولة القراءة</li>
            </ul>
        </div>
    </div>
</div>

{{-- ── Bottom Row (Image + Completion + Actions) ── --}}
<div class="bottom-row">

    {{-- Image Upload --}}
    <div class="bottom-card">
        <div class="bottom-card-head">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <h3>صورة الحيوان <span style="color:var(--text-muted);font-weight:600;font-size:.75rem;">(اختياري)</span></h3>
        </div>
        <div class="bottom-card-body">
            <div class="upload-zone" id="uploadZone">
                <input type="file" id="imgInput" accept="image/*" onchange="previewImg(this)">
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
            <button class="btn-save" id="btnSave" onclick="submitForm()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                حفظ المحتوى التعريفي
            </button>
            <a href="/admin/animals" class="btn-discard">إلغاء والتراجع</a>
        </div>
    </div>

</div>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script>
    const animalData = {
        '1': { name: 'الأسد الإفريقي',  emoji: '🦁', sci: 'Panthera leo' },
        '2': { name: 'الفيل الآسيوي',   emoji: '🐘', sci: 'Elephas maximus' },
        '3': { name: 'النمر البنغالي',  emoji: '🐯', sci: 'Panthera tigris' },
        '4': { name: 'الزرافة',          emoji: '🦒', sci: 'Giraffa camelopardalis' },
        '5': { name: 'التمساح النيلي',  emoji: '🐊', sci: 'Crocodylus niloticus' },
    };

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    /* ── Animal change ── */
    function onAnimalChange() {
        const val = document.getElementById('animalSelect').value;
        const preview = document.getElementById('animalPreview');
        if (val && animalData[val]) {
            const d = animalData[val];
            document.getElementById('previewEmoji').textContent = d.emoji;
            document.getElementById('previewName').textContent  = d.name;
            document.getElementById('previewSci').textContent   = d.sci;
            preview.classList.add('show');
        } else {
            preview.classList.remove('show');
        }
        updateSteps();
    }

    /* ── Description counter ── */
    function onDescInput() {
        const len = document.getElementById('desc').value.length;
        document.getElementById('charCount').textContent = len;
        updateSteps();
    }

    /* ── Image preview ── */
    function previewImg(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('imgPreview').src = e.target.result;
                document.getElementById('imgPreviewWrap').classList.add('show');
                document.getElementById('uploadZone').style.display = 'none';
                updateSteps();
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImg() {
        document.getElementById('imgPreview').src = '';
        document.getElementById('imgPreviewWrap').classList.remove('show');
        document.getElementById('uploadZone').style.display = 'block';
        document.getElementById('imgInput').value = '';
        updateSteps();
    }

    /* ── Drag & drop ── */
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

    /* ── Steps updater ── */
    function updateSteps() {
        const hasAnimal = !!document.getElementById('animalSelect').value;
        const hasDesc   = document.getElementById('desc').value.trim().length >= 20;
        const hasImg    = document.getElementById('imgPreviewWrap').classList.contains('show');

        const doneIcon = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>`;

        const steps = [
            { id: 'step1', done: hasAnimal,            active: !hasAnimal },
            { id: 'step2', done: hasAnimal && hasDesc,  active: hasAnimal && !hasDesc },
            { id: 'step3', done: hasDesc && hasImg,     active: hasDesc && !hasImg },
            { id: 'step4', done: false,                 active: hasAnimal && hasDesc },
        ];

        steps.forEach((s, i) => {
            const item   = document.getElementById(s.id + '-item');
            const circle = document.getElementById(s.id + '-circle');
            item.className = 'step-item ' + (s.done ? 'done' : s.active ? 'active' : '');
            circle.innerHTML = s.done ? doneIcon : (i + 1);
        });
    }

    /* ── Submit ── */
    function submitForm() {
        const animal = document.getElementById('animalSelect').value;
        const desc   = document.getElementById('desc').value.trim();

        if (!animal) {
            showToast('⚠️ يجب اختيار الحيوان أولاً');
            return;
        }
        if (desc.length < 20) {
            showToast('⚠️ يجب كتابة وصف تعريفي (20 حرف على الأقل)');
            document.getElementById('desc').focus();
            return;
        }

        const btn = document.getElementById('btnSave');
        btn.disabled = true; btn.style.opacity = '.7';
        btn.innerHTML = '⏳ جاري الحفظ...';

        setTimeout(() => {
            showToast('✅ تمت إضافة المحتوى التعريفي بنجاح');
            btn.innerHTML = '✅ تم الحفظ بنجاح!';
            setTimeout(() => { window.location.href = '/admin/animals'; }, 1200);
        }, 900);
    }

    updateSteps();
</script>
@endsection
