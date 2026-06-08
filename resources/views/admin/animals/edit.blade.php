@extends($__layout ?? 'admin.layout')
@section('title', 'تعديل المحتوى التعريفي | Tripoli Zoo')
@section('page_title', 'تعديل المحتوى التعريفي')

@php
$animals = [
    '1' => ['name'=>'الأسد الإفريقي',  'sci'=>'Panthera leo',           'emoji'=>'🦁', 'code'=>'L-01',
             'desc'=>'الأسد الإفريقي من أكبر القطط البرية في العالم. يعيش في مجموعات تُعرف بـ (الفخر). يتميز الذكر بعُرفه الكثيف الذي يزداد قتامةً مع التقدم في السن. يصل وزنه إلى 190 كجم ويمكنه الجري بسرعة تصل إلى 80 كم/ساعة لمسافات قصيرة.',
             'img'=>'/zoo_lion.png'],
    '2' => ['name'=>'الفيل الآسيوي',   'sci'=>'Elephas maximus',         'emoji'=>'🐘', 'code'=>'E-04',
             'desc'=>'الفيل الآسيوي أصغر حجماً من الأفريقي، ويتميز بأذنين أصغر ورأس أكثر تحدباً. يُعدّ من أكثر الحيوانات ذكاءً في العالم، ويمتلك ذاكرة استثنائية.',
             'img'=>'/zoo_elephant.png'],
    '3' => ['name'=>'النمر البنغالي',  'sci'=>'Panthera tigris',         'emoji'=>'🐯', 'code'=>'T-02',
             'desc'=>'النمر البنغالي أكبر أنواع القطط وأقواها. يسبح جيداً ويجيد تسلق الأشجار. يُهدَّد بالانقراض بسبب الصيد الجائر وفقدان موطنه.',
             'img'=>''],
];
$animal = $animals[$id ?? '1'] ?? $animals['1'];
@endphp

@section('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg,#1e3a1e 0%,#2d5a27 100%);
        --card-shadow: 0 10px 30px -10px rgba(0,0,0,.07);
    }

    .page-back {
        display:inline-flex; align-items:center; gap:8px;
        color:var(--text-muted); text-decoration:none;
        font-weight:700; font-size:.9rem; margin-bottom:1.6rem; transition:color .2s;
    }
    .page-back:hover { color:var(--orange); }

    /* ── Section card ── */
    .section-card {
        background:white; border:1px solid var(--border);
        border-radius:20px; overflow:hidden;
        box-shadow:var(--card-shadow); margin-bottom:1.4rem;
        transition:box-shadow .3s;
    }
    .section-card:hover { box-shadow:0 15px 35px -10px rgba(45,90,39,.08); }

    .section-head {
        padding:1.2rem 1.6rem; border-bottom:1px solid var(--border);
        display:flex; align-items:center; gap:12px;
        background:linear-gradient(to left,rgba(45,90,39,.02),transparent);
    }

    .section-num {
        width:30px; height:30px; background:var(--primary-gradient);
        color:white; border-radius:8px;
        display:flex; align-items:center; justify-content:center;
        font-weight:900; font-size:.85rem; flex-shrink:0;
    }

    .section-head-text h3 { font-size:1rem; font-weight:900; color:#1e3a1e; margin:0 0 2px; }
    .section-head-text p  { font-size:.78rem; color:var(--text-muted); font-weight:600; margin:0; }

    .section-body { padding:1.6rem; }

    /* ── Animal identity block (read-only) ── */
    .animal-identity {
        display:flex; align-items:center; gap:1.4rem;
    }

    .animal-avatar {
        width:80px; height:80px; border-radius:16px; flex-shrink:0;
        overflow:hidden; background:linear-gradient(135deg,#FFF7ED,#FFEDD5);
        display:flex; align-items:center; justify-content:center;
        border:2px solid var(--border); box-shadow:0 6px 16px rgba(0,0,0,.07);
    }
    .animal-avatar img  { width:100%; height:100%; object-fit:cover; }
    .animal-avatar span { font-size:2.8rem; }

    .animal-identity-info h3 { font-size:1.15rem; font-weight:900; color:#1e3a1e; margin:0 0 3px; }
    .animal-identity-info p  { font-size:.85rem; color:var(--text-muted); font-weight:600; margin:0 0 10px; font-style:italic; }

    .code-pill {
        display:inline-flex; align-items:center; gap:5px;
        padding:4px 12px; border-radius:50px;
        background:rgba(45,90,39,.07); color:#1e3a1e;
        border:1px solid rgba(45,90,39,.15);
        font-size:.78rem; font-weight:800;
    }

    .readonly-note {
        margin-top:1rem; padding:10px 14px;
        background:#F8FAFC; border:1px solid var(--border);
        border-radius:10px; font-size:.78rem; color:var(--text-muted);
        font-weight:700; display:flex; align-items:center; gap:8px;
    }

    /* ── Description textarea ── */
    .desc-textarea {
        width:100%;
        padding:14px 16px;
        border:2px solid var(--border);
        border-radius:12px;
        font-family:'Cairo',sans-serif;
        font-size:.92rem; line-height:1.7; color:var(--text-main);
        resize:vertical; min-height:160px; outline:none;
        transition:all .2s; background:white;
    }
    .desc-textarea:focus {
        border-color:var(--orange);
        box-shadow:0 0 0 4px rgba(232,101,26,.06);
    }

    .char-count {
        display:flex; justify-content:flex-end;
        margin-top:6px; font-size:.78rem; color:var(--text-muted); font-weight:700;
    }
    .char-count span { color:var(--orange); }

    .writing-tips {
        background:#FFFBF5; border:1px solid #FED7AA;
        border-radius:10px; padding:12px 14px; margin-top:1rem;
    }
    .writing-tips p {
        font-size:.78rem; color:#92400E; font-weight:700; margin:0 0 6px;
        display:flex; align-items:center; gap:6px;
    }
    .writing-tips ul { padding-right:16px; margin:0; display:flex; flex-direction:column; gap:4px; }
    .writing-tips li { font-size:.76rem; color:#92400E; font-weight:600; }

    /* ── Bottom row ── */
    .bottom-row { display:flex; flex-direction:column; gap:1.4rem; }

    .bottom-card {
        background:white; border:1px solid var(--border);
        border-radius:20px; overflow:hidden; box-shadow:var(--card-shadow);
    }

    .bottom-card-head {
        padding:1rem 1.4rem; border-bottom:1px solid var(--border);
        background:#FAFBFC; display:flex; align-items:center; gap:8px;
    }
    .bottom-card-head h3 { font-size:.92rem; font-weight:800; color:var(--text-main); margin:0; }
    .bottom-card-body    { padding:1.2rem 1.4rem; }

    /* ── Image upload ── */
    .current-img-wrap {
        border-radius:14px; overflow:hidden; border:1.5px solid var(--border);
        margin-bottom:1rem; position:relative;
    }
    .current-img-wrap img  { width:100%; max-height:200px; object-fit:cover; display:block; }
    .current-img-label {
        position:absolute; bottom:0; left:0; right:0;
        padding:8px 12px; background:rgba(0,0,0,.45);
        color:white; font-size:.75rem; font-weight:700;
        backdrop-filter:blur(4px);
    }

    .upload-zone {
        border:2.5px dashed var(--border); border-radius:14px;
        padding:1.4rem 1rem; text-align:center; cursor:pointer;
        transition:all .3s; background:#FAFBFC; position:relative;
    }
    .upload-zone:hover { border-color:var(--orange); background:#FFFBF8; }
    .upload-zone.dragover { border-color:#2d5a27; background:rgba(45,90,39,.04); }
    .upload-zone input[type="file"] { position:absolute; inset:0; opacity:0; cursor:pointer; }

    .upload-zone-icon {
        width:42px; height:42px;
        background:rgba(232,101,26,.08); color:var(--orange);
        border-radius:12px; display:flex; align-items:center; justify-content:center;
        margin:0 auto 8px;
    }
    .upload-zone h4 { font-size:.85rem; font-weight:800; color:var(--text-main); margin:0 0 3px; }
    .upload-zone p  { font-size:.73rem; color:var(--text-muted); font-weight:600; margin:0; }

    .img-preview-wrap { display:none; border-radius:12px; overflow:hidden; border:1.5px solid var(--border); position:relative; }
    .img-preview-wrap.show { display:block; }
    .img-preview-wrap img  { width:100%; height:140px; object-fit:cover; display:block; }
    .img-remove-btn {
        position:absolute; top:8px; left:8px;
        width:28px; height:28px;
        background:rgba(239,68,68,.9); color:white;
        border:none; border-radius:6px; font-size:1rem; cursor:pointer;
        display:flex; align-items:center; justify-content:center; transition:all .2s;
    }
    .img-remove-btn:hover { background:#DC2626; }

    /* ── Action buttons ── */
    .btn-save {
        width:100%; padding:14px;
        background:var(--primary-gradient); color:white; border:none;
        border-radius:12px; font-family:'Cairo',sans-serif; font-weight:800; font-size:1rem;
        cursor:pointer; transition:all .3s;
        display:flex; align-items:center; justify-content:center; gap:8px;
        box-shadow:0 6px 18px rgba(30,58,30,.25); margin-bottom:10px;
    }
    .btn-save:hover { transform:translateY(-2px); box-shadow:0 10px 24px rgba(30,58,30,.35); }

    .btn-view {
        width:100%; padding:11px; margin-bottom:10px;
        background:rgba(45,90,39,.07); color:#1e3a1e;
        border:1.5px solid rgba(45,90,39,.2); border-radius:12px;
        font-family:'Cairo',sans-serif; font-weight:700; font-size:.9rem;
        cursor:pointer; transition:all .2s;
        text-align:center; text-decoration:none; display:block;
        display:flex; align-items:center; justify-content:center; gap:8px;
    }
    .btn-view:hover { background:rgba(45,90,39,.12); }

    .btn-discard {
        width:100%; padding:11px;
        background:var(--bg-color); color:var(--text-muted);
        border:1.5px solid var(--border); border-radius:12px;
        font-family:'Cairo',sans-serif; font-weight:700; font-size:.9rem;
        cursor:pointer; transition:all .2s;
        text-align:center; text-decoration:none; display:block;
    }
    .btn-discard:hover { background:#E2E8F0; color:var(--text-main); }

    /* ── Toast ── */
    .toast {
        position:fixed; bottom:2rem; left:50%;
        transform:translateX(-50%) translateY(80px);
        background:#1E293B; color:white;
        padding:12px 24px; border-radius:50px;
        font-weight:700; font-size:.9rem; z-index:9999;
        transition:transform .4s cubic-bezier(.4,0,.2,1); white-space:nowrap;
    }
    .toast.show { transform:translateX(-50%) translateY(0); }
</style>
@endsection

@section('content')

<a href="/admin/animals/{{ $id ?? 1 }}" class="page-back">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    العودة لعرض المحتوى التعريفي
</a>

{{-- ── 1. Animal (read-only) ── --}}
<div class="section-card">
    <div class="section-head">
        <div class="section-num">1</div>
        <div class="section-head-text">
            <h3>الحيوان المحدد</h3>
            <p>لا يمكن تغيير الحيوان في وضع التعديل — بإمكانك تعديل الوصف والصورة فقط</p>
        </div>
    </div>
    <div class="section-body">
        <div class="animal-identity">
            <div class="animal-avatar">
                @if($animal['img'])
                    <img src="{{ $animal['img'] }}" alt="{{ $animal['name'] }}"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                    <span style="display:none">{{ $animal['emoji'] }}</span>
                @else
                    <span>{{ $animal['emoji'] }}</span>
                @endif
            </div>
            <div class="animal-identity-info">
                <h3>{{ $animal['name'] }}</h3>
                <p>{{ $animal['sci'] }}</p>
                <span class="code-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    رمز الحيوان: {{ $animal['code'] }}
                </span>
            </div>
        </div>
        <div class="readonly-note">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            هذا الحيوان مسجّل مسبقاً في النظام — يمكنك تعديل وصفه وصورته أدناه
        </div>
    </div>
</div>

{{-- ── 2. Description ── --}}
<div class="section-card">
    <div class="section-head">
        <div class="section-num">2</div>
        <div class="section-head-text">
            <h3>تعديل الوصف التعريفي</h3>
            <p>هذا الوصف يظهر للزوار عند مسح رمز QR الخاص بالحيوان</p>
        </div>
    </div>
    <div class="section-body">
        <textarea
            id="desc"
            class="desc-textarea"
            oninput="onDescInput()"
            rows="6"
        >{{ $animal['desc'] }}</textarea>
        <div class="char-count">الأحرف: <span id="charCount">{{ mb_strlen($animal['desc']) }}</span> / 600</div>

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

{{-- ── Bottom: Image + Actions ── --}}
<div class="bottom-row">

    {{-- Image upload --}}
    <div class="bottom-card">
        <div class="bottom-card-head">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <h3>صورة الحيوان <span style="color:var(--text-muted);font-weight:600;font-size:.75rem;">(اختياري — اترك فارغاً للإبقاء على الحالية)</span></h3>
        </div>
        <div class="bottom-card-body">

            {{-- Current image --}}
            @if($animal['img'])
            <div class="current-img-wrap" id="currentImgWrap">
                <img src="{{ $animal['img'] }}" alt="{{ $animal['name'] }}"
                     onerror="this.parentElement.style.display='none'">
                <div class="current-img-label">📷 الصورة الحالية</div>
            </div>
            @endif

            {{-- Upload zone --}}
            <div class="upload-zone" id="uploadZone">
                <input type="file" id="imgInput" accept="image/*" onchange="previewImg(this)">
                <div class="upload-zone-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                </div>
                <h4>رفع صورة جديدة</h4>
                <p>اسحب وأفلت أو انقر للاختيار</p>
                <p style="margin-top:4px;">PNG أو JPG حتى 5 ميجابايت</p>
            </div>

            {{-- New image preview --}}
            <div class="img-preview-wrap" id="imgPreviewWrap" style="margin-top:10px;">
                <img id="imgPreview" src="" alt="معاينة الصورة الجديدة">
                <button class="img-remove-btn" onclick="removeImg()" title="إلغاء الصورة الجديدة">×</button>
            </div>

        </div>
    </div>

    {{-- Actions --}}
    <div class="bottom-card">
        <div class="bottom-card-head">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            <h3>حفظ التعديلات</h3>
        </div>
        <div class="bottom-card-body">
            <button class="btn-save" id="btnSave" onclick="submitForm()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                حفظ التعديلات
            </button>
            <a href="/admin/animals/{{ $id ?? 1 }}" class="btn-view">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                عرض بدون حفظ
            </a>
            <a href="/admin/animals" class="btn-discard">إلغاء والعودة للقائمة</a>
        </div>
    </div>

</div>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script>
    /* ── Description counter ── */
    function onDescInput() {
        document.getElementById('charCount').textContent = document.getElementById('desc').value.length;
    }

    /* ── Image preview ── */
    function previewImg(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('imgPreview').src = e.target.result;
                document.getElementById('imgPreviewWrap').classList.add('show');
                document.getElementById('uploadZone').style.display = 'none';
                // Hide current image
                const cur = document.getElementById('currentImgWrap');
                if (cur) cur.style.opacity = '.4';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImg() {
        document.getElementById('imgPreview').src = '';
        document.getElementById('imgPreviewWrap').classList.remove('show');
        document.getElementById('uploadZone').style.display = 'block';
        document.getElementById('imgInput').value = '';
        const cur = document.getElementById('currentImgWrap');
        if (cur) cur.style.opacity = '1';
    }

    /* ── Drag & drop ── */
    const uploadZone = document.getElementById('uploadZone');
    uploadZone.addEventListener('dragover',  e => { e.preventDefault(); uploadZone.classList.add('dragover'); });
    uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));
    uploadZone.addEventListener('drop', e => {
        e.preventDefault(); uploadZone.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            const dt = new DataTransfer(); dt.items.add(file);
            const inp = document.getElementById('imgInput'); inp.files = dt.files;
            previewImg(inp);
        }
    });

    /* ── Toast ── */
    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg; t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    /* ── Submit ── */
    function submitForm() {
        const desc = document.getElementById('desc').value.trim();
        if (desc.length < 20) {
            showToast('⚠️ يجب كتابة وصف تعريفي (20 حرف على الأقل)');
            document.getElementById('desc').focus();
            return;
        }

        const btn = document.getElementById('btnSave');
        btn.disabled = true; btn.style.opacity = '.7';
        btn.innerHTML = '⏳ جاري الحفظ...';

        setTimeout(() => {
            showToast('✅ تم حفظ التعديلات بنجاح');
            btn.innerHTML = '✅ تم الحفظ!';
            setTimeout(() => { window.location.href = '/admin/animals/{{ $id ?? 1 }}'; }, 1200);
        }, 900);
    }
</script>
@endsection
