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

    .animal-identity-info h3 { font-size:1.15rem; font-weight:900; color:#1e3a1e; margin:0; }

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

    /* ── Single image slot ── */
    .image-slot { position:relative; }

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

    .img-preview-wrap {
        display:none; border-radius:14px; overflow:hidden;
        border:1.5px solid var(--border); position:relative;
        background:#FAFBFC;
    }
    .img-preview-wrap.show { display:block; }
    .img-preview-wrap img {
        width:100%; max-height:320px; height:auto;
        object-fit:contain; display:block; margin:0 auto;
    }

    .img-preview-actions {
        display:flex; gap:8px; padding:10px 12px;
        border-top:1px solid var(--border); background:white;
    }

    .img-change-btn {
        flex:1; padding:9px 12px;
        background:rgba(45,90,39,.08); color:#1e3a1e;
        border:1.5px solid rgba(45,90,39,.2); border-radius:10px;
        font-family:'Cairo',sans-serif; font-weight:700; font-size:.82rem;
        cursor:pointer; transition:all .2s;
        display:flex; align-items:center; justify-content:center; gap:6px;
    }
    .img-change-btn:hover { background:rgba(45,90,39,.14); }

    .img-remove-inline {
        padding:9px 14px;
        background:rgba(239,68,68,.08); color:#DC2626;
        border:1.5px solid rgba(239,68,68,.25); border-radius:10px;
        font-family:'Cairo',sans-serif; font-weight:700; font-size:.82rem;
        cursor:pointer; transition:all .2s;
    }
    .img-remove-inline:hover { background:rgba(239,68,68,.15); }

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
        <div class="animal-identity-info">
            <h3>{{ $animal['name'] }}</h3>
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
    </div>
</div>

{{-- ── Bottom: Image + Actions ── --}}
<div class="bottom-row">

    {{-- Image upload — single slot --}}
    <div class="bottom-card">
        <div class="bottom-card-head">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <h3>صورة الحيوان <span style="color:#DC2626;font-weight:800;font-size:.75rem;">*</span></h3>
        </div>
        <div class="bottom-card-body">
            <div class="image-slot">
                <input type="file" id="imgInput" accept="image/*" onchange="previewImg(this)" hidden>

                <div class="upload-zone" id="uploadZone" @if($animal['img']) style="display:none" @endif onclick="document.getElementById('imgInput').click()">
                    <div class="upload-zone-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                    </div>
                    <h4>اسحب وأفلت الصورة هنا</h4>
                    <p>أو انقر للاختيار من جهازك</p>
                    <p style="margin-top:4px;">PNG أو JPG حتى 5 ميجابايت</p>
                </div>

                <div class="img-preview-wrap @if($animal['img']) show @endif" id="imgPreviewWrap">
                    <img id="imgPreview" src="{{ $animal['img'] ?: '' }}" alt="{{ $animal['name'] }}">
                    <div class="img-preview-actions">
                        <button type="button" class="img-change-btn" onclick="document.getElementById('imgInput').click()">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            تغيير الصورة
                        </button>
                        <button type="button" class="img-remove-inline" onclick="removeImg()">حذف</button>
                    </div>
                </div>
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
    function hasImage() {
        const wrap = document.getElementById('imgPreviewWrap');
        if (!wrap.classList.contains('show')) return false;
        return !!document.getElementById('imgPreview').getAttribute('src');
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
        e.preventDefault(); uploadZone.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            const dt = new DataTransfer(); dt.items.add(file);
            const inp = document.getElementById('imgInput'); inp.files = dt.files;
            previewImg(inp);
        }
    });

    document.getElementById('imgPreviewWrap').addEventListener('dragover', e => { e.preventDefault(); });
    document.getElementById('imgPreviewWrap').addEventListener('drop', e => {
        e.preventDefault();
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            const dt = new DataTransfer(); dt.items.add(file);
            const inp = document.getElementById('imgInput'); inp.files = dt.files;
            previewImg(inp);
        }
    });

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg; t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    function submitForm() {
        const desc = document.getElementById('desc').value.trim();
        if (desc.length < 20) {
            showToast('⚠️ يجب كتابة وصف تعريفي (20 حرف على الأقل)');
            document.getElementById('desc').focus();
            return;
        }
        if (!hasImage()) {
            showToast('⚠️ يجب إضافة صورة للحيوان');
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
